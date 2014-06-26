var require = patchRequire(require);
var vkParser = require('vkParser');

module.exports = new Class({
  Extends: vkParser,
  groups: [],
  run: function() {
    this.auth(this.startExport.bind(this));
  },
  startExport: function() {
    this.db().selectExportGroups(function(groups) {
      //require('utils').dump(groups);
      //for (var i = 0; i < groups.length; i++) groups[i].url = this.baseUrl + '/' + groups[i].name;
      //this.groups = groups;
      //this.auth(this.parseCurrentGroup.bind(this));
      this.parseTopic('/topic-1127941_27230181', function(topic) {

        var comments = [];
        for (var i = 0; i < topic.authors.length; i++) {
          comments.push({
            id1: topic.id1,
            id2: topic.id2,
            author: topic.authors[i][1],
            comment: topic.comments[i],
          });
        }
        delete topic.authors;
        delete topic.comments;
        topic.author = topic.author[1];

        require('utils').dump(topic);
        return;

        this.db().insert(function() {
          this.log('topic inserted');
        }.bind(this), 'vkTopics', '1127941_27230181', [topic]);
        this.db().insert(function() {
          this.log(comments.length + ' comments inserted');
        }.bind(this), 'vkTopicComments', '1127941_27230181', comments);

      }.bind(this));
    }.bind(this), 'vkGroups', '*', {
      addF: {
        userId: this.userId
      }
    });
  },
  i: 0,
  parseCurrentGroup: function() {
    this.parseGroupTopics(this.groups[0], function(topic) {
      require('utils').dump(topic);
    });
  },
  parseGroupTopics: function(group, onTopic) {
    var name = 'board' + group.id;
    console.log('parsing group ' + group.name + 'topics');
    this.thenOpen('http://vk.com/' + name, function() {
      this.casper.wait(1, function() {
        //this.scrollTillBottom(function() {
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
          this.parseTopic(links[i], onTopic);
        }
        //}.bind(this));
      }.bind(this));
    }.bind(this));
  },
  parseTopic: function(path, onComplete) {
    var ids = path.match(new RegExp('/topic-(\\d+)_(\\d+)'));
    this.thenOpen('http://vk.com' + path, function() {
      this.casper.wait(100, function() {
        this.log('start scroll');
        this.scrollTillBottom(function() {
          var r = this.casper.evaluate(function() {
            var authors = __utils__.findAll('.bp_post .bp_author'); // find topic links
            var comments = __utils__.findAll('.bp_post .bp_text'); // find topic links
            var title = __utils__.findAll('#wrap3 .bt_header a')[0].innerHTML;
            var eAuthor = __utils__.findAll('#wrap3 .bt_header .bt_author a')[0];
            var r = {
              title: title,
              author: [eAuthor.getAttribute('href'), eAuthor.innerHTML],
              comments: [],
              authors: []
            };
            for (var i = 0; i < authors.length; i++) {
              r.comments.push(comments[i].innerHTML);
              r.authors.push([
                authors[i].getAttribute('href'), authors[i].innerHTML
              ]);
            }
            return r;
          });
          r.id1 = ids[1];
          r.id2 = ids[2];
          onComplete(r);
        }.bind(this), 50);
      }.bind(this));
    }.bind(this));
  }
});
