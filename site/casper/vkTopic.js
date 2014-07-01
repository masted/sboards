var require = patchRequire(require);
var vkParser = require('vkParser');

var parseDate = function(d) {
  var months = {
    'янв': 1,
    'фев': 2,
    'мар': 3,
    'апр': 4,
    'мая': 5,
    'июн': 6,
    'июл': 7,
    'авг': 8,
    'сен': 9,
    'окт': 10,
    'ноя': 11,
    'дек': 12
  };
  d = d.match(new RegExp('(.+) в (\\d+):(\\d+)'));
  var time = ' ' + d[2] + ':' + d[3] + ':00';
  var date = new Date();
  var yesterday = date.getFullYear() + '-' + date.getMonth() + '-' + date.getDay() + time;
  date.setDate(date.getDate() - 1);
  var today = date.getFullYear() + '-' + date.getMonth() + '-' + date.getDay() + time;
  var ddd;
  if (d[1] == 'сегодня') {
    ddd = today;
  } else if (d[1] == 'вчера') {
    ddd = yesterday;
  } else {
    d[1] = d[1].match(new RegExp('(\\d+) (\\S+) (\\d+)'));
    d[1][2] = months[d[1][2]];
    ddd = d[1][3] + '-' + d[1][2] + '-' + d[1][1] + time;
  }
  return ddd;
};

module.exports = new Class({
  Extends: vkParser,

  topicsLimit: 0,
  commentsScrollLimit: 100,
  disableRecursion: false,
  group: null,

  run: function() {
    this.auth(this.startExport.bind(this));
  },

  startExport: function() {
    /*
    // for debug
    this.group = {
      id: 29154907,
      title: 'DARINA Shopping - Китай, Украина'
    };
    this.parseTopic('/topic-29154907_25024971', true);
   */
    this.db().selectExportGroups(function(groups) {
      if (!groups.length) {
        this.log('there are no groups for export', 1);
        return;
      }
      this.group = groups[0];
      this.parseGroup();
    }.bind(this), 'vkGroups', '*', {
      addF: {
        userId: this.userId
      }
    });
  },
  nextGroup: function() {
    if (this.disableRecursion) return;
    this.log('processing next group', 1);
    this.startExport();
  },
  exportTopic: function(topic, last) {
    for (var i = 0; i < topic.comments.length; i++) {
      topic.comments[i].dateCreate = parseDate(topic.comments[i].dateCreate);
      topic.comments[i].author = topic.comments[i].author[1];
    }
    var comments = topic.comments;
    for (var i = 0; i < comments.length; i++) {
      comments[i].id1 = topic.id1;
      comments[i].id2 = topic.id2;
    }
    topic.author = topic.author[1];
    topic.dateCreate = comments[0].dateCreate;
    topic.dateUpdate = comments[comments.length - 1].dateCreate;
    delete topic.comments;
    this.db().insertTopics(function() {
      this.log('topic inserted');
      if (last) this.nextGroup();
    }.bind(this), 'vkTopics', 'topicExport' + topic.id1 + '_' + topic.id2, [topic]);
    this.db().insert(function() {
      this.log(comments.length + ' comments inserted');
    }.bind(this), 'vkTopicComments', 'commentExport' + topic.id1 + '_' + topic.id2, comments);
  },
  parseGroup: function() {
    this.parseGroupTopics(this.group);
  },
  parseGroupTopics: function() {
    if (!this.group) throw new Error('group is empty');
    var name = 'board' + this.group.id;
    console.log('parsing group [' + this.group.title + '] (' + this.group.id + ')');
    this.thenOpen('http://vk.com/' + name, function() {
      this.casper.wait(1, function() {
        var links = this.casper.evaluate(function() {
          var elements = __utils__.findAll('a.blst_title'); // find topic links
          var r = [];
          for (var i = 0; i < elements.length; i++) {
            r.push(elements[i].getAttribute('href'));
          }
          return r;
        });
        if (!links.length) {
          this.log('there are no links in group [' + this.group.title + ']', 1);
          this.db().update(function() {
            this.nextGroup();
          }.bind(this), 'vkGroups', this.group.id, 'dateExport', new Date().toISOString().replace(/T/, ' ').replace(/\..+/, ''));
          return;
        }
        var limit = this.topicsLimit || links.length;
        console.log('found ' + limit + ' topics');
        for (var i = 0; i < limit; i++) this.parseTopic(links[i], i == limit - 1);
      }.bind(this));
    }.bind(this));
  },
  parseTopic: function(path, last) {
    var ids = path.match(new RegExp('/topic-(\\d+)_(\\d+)'));
    this.thenOpen('http://vk.com' + path, function() {
      this.casper.wait(100, function() {
        this.log('start scroll');
        this.scrollTillBottom(function() {
          var r = this.casper.evaluate(function() {
            try {
              var authors = __utils__.findAll('.bp_post .bp_author'); // find topic links
              var comments = __utils__.findAll('.bp_post .bp_text'); // find topic links
              var dates = __utils__.findAll('.bp_post .bp_date'); // find topic links
              var title = __utils__.findAll('#wrap3 .bt_header a')[0].innerHTML;
              var eAuthor = __utils__.findAll('#wrap3 .bt_header .bt_author a');
              if (eAuthor.length) {
                eAuthor = eAuthor[0];
              } else {
                //return 123;
                eAuthor = __utils__.findAll('#wrap3 .bt_header .bt_author')[0];
              }
              //if (!eAuthor) return false;
              var r = {
                title: title,
                author: [eAuthor.getAttribute('href'), eAuthor.innerHTML],
                comments: []
              };
              for (var i = 0; i < authors.length; i++) {
                r.comments.push({
                  comment: comments[i].innerHTML,
                  author: [authors[i].getAttribute('href'), authors[i].innerHTML],
                  dateCreate: dates[i].innerHTML
                });
              }
              return r;
            } catch (e) {
              return e.message;
            }
          });
          r.id1 = ids[1];
          r.id2 = ids[2];
          if (r.id1 != this.group.id) throw new Error(r.id1 + ' != ' + this.group.id);
          this.exportTopic(r, last);
        }.bind(this), this.commentsScrollLimit);
      }.bind(this));
    }.bind(this));
  }
});
