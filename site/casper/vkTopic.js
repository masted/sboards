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
}


module.exports = new Class({
  Extends: vkParser,
  group: null,
  run: function() {
    this.auth(this.startExport.bind(this));
  },
  startExport: function() {
    this.db().selectExportGroups(function(groups) {
      this.group = groups[0];
      this.parseGroup();
    }.bind(this), 'vkGroups', '*', {
      addF: {
        userId: this.userId
      }
    });
  },
  exportTopic: function(topic) {
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
    }.bind(this), 'vkTopics', 'topicExport' + topic.id1 + '_' + topic.id2, [topic]);
    this.db().insert(function() {
      this.log(comments.length + ' comments inserted');
    }.bind(this), 'vkTopicComments', 'commentExport' + topic.id1 + '_' + topic.id2, comments);
  },
  parseGroup: function() {
    this.parseGroupTopics(this.group, this.exportTopic.bind(this));
  },
  parseGroupTopics: function(group, onTopic, onComplete) {
    var name = 'board' + group.id;
    console.log('parsing group ' + group.name + 'topics');
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
        console.log('found ' + links.length + ' topics');
        for (var i = 0; i < links.length; i++) {
          this.parseTopic(group, links[i], i == links.length-1, onTopic, onComplete);
        }
      }.bind(this));
    }.bind(this));
  },
  parseTopic: function(group, path, last, onComplete, onLast) {
    var ids = path.match(new RegExp('/topic-(\\d+)_(\\d+)'));
    this.thenOpen('http://vk.com' + path, function() {
      this.casper.wait(100, function() {
        this.log('start scroll');
        this.scrollTillBottom(function() {
          var r = this.casper.evaluate(function() {
            var authors = __utils__.findAll('.bp_post .bp_author'); // find topic links
            var comments = __utils__.findAll('.bp_post .bp_text'); // find topic links
            var dates = __utils__.findAll('.bp_post .bp_date'); // find topic links
            var title = __utils__.findAll('#wrap3 .bt_header a')[0].innerHTML;
            var eAuthor = __utils__.findAll('#wrap3 .bt_header .bt_author a')[0];
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
          });
          r.id1 = ids[1];
          if (r.id1 != group.id) throw new Error(r.id1 + ' != ' + group.id);
          r.id2 = ids[2];
          onComplete(r);

        }.bind(this), 10);
      }.bind(this));
    }.bind(this));
  }
});
