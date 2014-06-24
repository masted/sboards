var require = patchRequire(require);
var vkParser = require('vkParser');

/**
 * находить группы в которых
 * - > 1000 подписчиков
 * - они закрытые
 * - наблюдать за статусом
 */
module.exports = new Class({
  Extends: vkParser,

  run: function() {
    this.auth(this.initKeyword.pass(this.startParse.bind(this), this));
  },

  // keyword selection
  keyword: null,
  initKeyword: function(onInit) {
    this.selectKeyword(function() {
      if (this.keyword.userId == 0) {
        this.assignKeyword(onInit);
      } else {
        onInit();
      }
    }.bind(this));
  },
  selectKeyword: function(callback) {
    this.db().selectNotPassedKeywords(function(keywords) {
      this.keyword = keywords[0];
      this.log('keyword selected: ' + this.keyword.keyword, 1);
      callback();
    }.bind(this), 'vkKeywords', ['id', 'keyword'], {
      setLimit: 1,
      setOrder: 'id',
      addF: {
        userId: [0, this.userId],
        passed: 0
      },
    }, this.userId);
  },
  assignKeyword: function(callback) {
    this.log('assigning keyword "' + this.keyword.keyword + '" assigned to ' + this.userId, 1);
    this.db().update(function() {
      this.log('keyword "' + this.keyword.keyword + '" assigned to ' + this.userId, 1);
      callback();
    }.bind(this), 'vkKeywords', this.keyword.id, 'userId', this.userId);
  },

  // groups parse & pre-save
  startParse: function() {
    this.auth(function() {
      this.db().select(function(groups) {
        this.casper.wait(1000, function(){
          if (groups.length) {
            this.log('saving pre-saved groups (' + groups.length + ')', 2);
            this.saveGroups(groups);
          } else {
            this.scrollAndPreSave(this.saveGroups.bind(this));
          }
        }.bind(this));
      }.bind(this), 'vkGroupsSearch', ['*'], {
        addF: {
          keyword: this.keyword.keyword
        }
      });
    }.bind(this));
  },
  scrollAndPreSave: function(onPreSave) {
    this.log('start scroll & pre-save', 2);
    var url = 'https://vk.com/search?c%5Bq%5D=' + this.keyword.keyword + '&c%5Bsection%5D=communities&c%5Btype%5D=1';
    this.thenOpen(url, function() {
      this.casper.wait(1, function() {
        var limit = 0;
        this.scrollTillBottom(function() {
          this.preSaveGroups(onPreSave);
        }.bind(this), limit);
      }.bind(this));
    }.bind(this));
  },
  getPageGroups: function() {
    return this.casper.evaluate(function() {
      var r = [];
      var links = __utils__.findAll('.results .groups_row .title a:first-child');
      var images = __utils__.findAll('.results .groups_row .img img');
      var infos = __utils__.findAll('.results .groups_row .info');
      if (images.length != links.length) throw new Error(images.length + ' != ' + links.length);
      if (infos.length != links.length) throw new Error(infos.length + ' != ' + links.length);
      for (var i = 0; i < links.length; i++) {
        var img = images[i].getAttribute('src');
        if (!img) throw new Error(i + ' bad: ' + links[i].innerHTML);
        var id;
        var pattern = '.*.vk\\.me/g(\\d+)/.*';
        var closed = infos[i].getElementsByTagName('div')[1].innerText == 'Закрытая группа';
        var link = links[i].getAttribute('href');
        var name = link.replace(new RegExp('/([^/]+)'), '$1');
        if (img.match(new RegExp(pattern))) {
          id = parseInt(img.replace(new RegExp(pattern), '$1'));
        } else if (name.match(new RegExp('club(\\d+)'))) {
          id = parseInt(name.replace(new RegExp('club(\\d+)'), '$1'));
        } else {
          id = false;
        }
        if (closed) {
          r.push({
            name: name,
            title: links[i].innerHTML.replace(/<\/?[^>]+>/gi, ''),
            img: img,
            id: id
          });
        }
      }
      return r;
    });
  },
  preSaveGroups: function(onPreSave) {
    var groups = this.getPageGroups();
    for (var i = 0; i < groups.length; i++) groups[i].keyword = this.keyword.keyword;
    this.db().insert(function() {
      this.log('pre-saved groups by keyword "' + this.keyword.keyword + '": ' + groups.length);
      onPreSave(groups);
    }.bind(this), 'vkGroupsSearch', 'groupsSearchPreSave' + this.keyword.id, groups);
  },

  // groups save
  saveGroups: function(groups) {
    // add some extra data to each item
    if (!groups.length) {
      this.db().update(function() {
        this.log('keyword "' + this.keyword.keyword + '" is empty', 1);
        this.next();
      }.bind(this), 'vkKeywords', this.keyword.id, 'passed', 1);
      return;
    }
    for (var i = 0; i < groups.length; i++) {
      groups[i].url = this.baseUrl + '/' + groups[i].name;
      groups[i].userId = this.userId;
    }
    new (require('linksQueue'))(this.casper, groups, function(item) {
      var m = this.casper.page.content.match(new RegExp('Groups\\.init\\(\\{"group_id":(\\d+)'));
      if (!m)
        throw new Error('group id not found on page');
      item.id = m[1];
      this.db().updateBy(function() {
      }, 'vkGroupsSearch', 'name', item.name, 'id', item.id);
    }.bind(this), function(item) {
      return !parseInt(item.id);
    }, function(items) {
      for (var i = 0; i < items.length; i++) items[i].keyword = this.keyword.keyword;
      this.log('groups to add: ' + items.length, 1);
      this.db().insert(function() {
        this.db().update(function() {
          this.log('keyword "' + this.keyword.keyword + '" passed', 1);
          this.next();
        }.bind(this), 'vkKeywords', this.keyword.id, 'passed', 1);
      }.bind(this), 'vkGroups', 'groupsSearch' + this.keyword.id, items);
    }.bind(this));
  },

  next: function() {
    this.run();
  }

});
