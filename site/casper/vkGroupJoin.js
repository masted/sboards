var require = patchRequire(require);
var vkParserMobile = require('vkParserMobile');

module.exports = new Class({
  Extends: vkParserMobile,

  requesting: [],
  links: [],
  i: 0,

  run: function() {
    this.auth(function() {
      this.request();
    }.bind(this));
  },

  request: function() {
    this.initRequesting(function() {
      if (this.requesting[this.i] === undefined) {
        this.log('there are no groups for request. complete', 1);
        return;
      }
      this.requestUrl();
    }.bind(this));
  },

  requestedCount: 0,

  getGroupStatus: function() {
    return this.casper.evaluate(function() {
      var joinBlock = __utils__.findAll('#group_like_module');
      if (!joinBlock) return 5;
      if (!joinBlock.length) {
        return 5;
        closedPageBlock = __utils__.findAll('.closed_page');
        if (closedPageBlock && closedPageBlock[0].innerText.match('Вы добавлены в чёрный список')) {
          return 3;
        }
        return 4;
      }
      var text = joinBlock[0].innerText;
      if (text.match('Вы состоите в группе')) {
        return 2;
      } else if (text.match('Вы подали')) {
        return 1;
      } else if (text.match('Подать заявку')) {
        return 0;
      } else {
        return 5;
      }
    });
  },

  requestUrl: function() {
    this.thenOpen(this.requesting[this.i].url, function(page) {
      this.casper.wait(100, function() {
        var status = this.getGroupStatus();
        var titles = ['не в группе', 'уже подана заявка', 'уже в группе', 'в черном списке', 'неизвестная хуйня'];
        if (status === 5) {
          this.log(page.url + ' is private', 1);
          this.db().update(function() {
            this.requestNextUrl();
          }.bind(this), 'vkGroups', this.requesting[this.i].id, 'private', 1);
          return;
        }
        this.log(page.url + ' (' + titles[status] + '): ' + this.requesting[this.i].title.substring(0, 20), 1);
        if (status == 0) {
          this.casper.click('#group_like_module button');
          this.db().update(function() {
            this.requestedCount++;
            this.requestNextUrl();
          }.bind(this), 'vkGroups', this.requesting[this.i].id, 'requested', 1);
        } else if (status == 1) {
          this.db().update(function() {
            this.requestNextUrl();
          }.bind(this), 'vkGroups', this.requesting[this.i].id, 'requested', 1);
        } else {
          this.requestNextUrl();
        }
      }.bind(this));
    }.bind(this), function() {
      this.log('url failed. try next');
      this.requestNextUrl();
    }.bind(this));
  },

  requestNextUrl: function() {
    if (this.requesting[this.i + 1] === undefined) {
      this.log('groups requested: ' + this.requestedCount + '/' + this.requesting.length, 1);
      return;
    }
    this.i++;
    this.requestUrl();
  },

  initRequesting: function(callback) {
    this.db().select(function(requesting) {
      for (var i = 0; i < requesting.length; i++) {
        requesting[i].url = this.baseUrl + '/' + requesting[i].name;
      }
      this.requesting = requesting;
      this.log('groups for request: ' + requesting.length, 1);
      callback();
    }.bind(this), 'vkGroups', ['name', 'id', 'title'], {
      addF: {
        private: 0,
        requested: 0,
        joined: 0,
        userId: this.userId
      }
    });
  }

});
