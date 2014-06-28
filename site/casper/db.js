var require = patchRequire(require);
require('mootools');

var parseJSON = function(str) {
  if (!str.replace(new RegExp('\\s', 'g'), '')) return '';
  try {
    var r = JSON.parse(str);
  } catch (e) {
    throw new Error(e.message + "\n" + str);
  }
  return r;
};

var phpcmd = new Class({
  initialize: function(casper, root) {
    this.casper = casper;
    this.root = root;
  },
  phpcmd: function(callback, params) {
    var result = false, done = false;
    var _params = [this.root + '/cmd.php'];
    params[0] = 'db/' + params[0];
    for (var i = 0; i < params.length; i++) _params.push(params[i]);
    require('child_process').execFile('php', _params, null, function(err, stdout, stderr) {
      done = true;
      result = parseJSON(stdout);
    });
    this.casper.waitFor(function() {
      return done;
    }, function() {
      callback(result);
    });
  }
});

module.exports = new Class({
  Extends: phpcmd,

  insert: function(onInsert, table, key, items) {
    require('fs').write(this.root + '/site/data/parsed/' + key, JSON.stringify(items));
    this.phpcmd(onInsert, ['insert', table, key]);
  },

  insertTopics: function(onInsert, table, key, items) {
    require('fs').write(this.root + '/site/data/parsed/' + key, JSON.stringify(items));
    this.phpcmd(onInsert, ['insertTopic', table, key]);
  },

  update: function(onUpdate, table, id, k, v) {
    this.phpcmd(onUpdate, ['update', table, id, k, v]);
  },

  updateBy: function(onUpdate, table, param, paramV, k, v) {
    this.phpcmd(onUpdate, ['updateBy', table, param, paramV, k, v]);
  },

  select: function(onSelect, table, what, conds) {
    this.phpcmd(onSelect, ['select', table, JSON.stringify(what), JSON.stringify(conds)]);
  },

  selectExportGroups: function(onSelect, table, what, conds) {
    this.phpcmd(onSelect, ['selectExportGroups', table, JSON.stringify(what), JSON.stringify(conds)]);
  },

  selectNotPassedKeywords: function(onSelect, table, what, conds, userId) {
    this.phpcmd(onSelect, ['selectNotPassedKeywords', table, JSON.stringify(what), JSON.stringify(conds), userId]);
  },

  joinSync: function(onComplete, ids, userId) {
    this.phpcmd(onComplete, ['joinSync', JSON.stringify(ids), userId]);
  }

});