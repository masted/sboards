var require = patchRequire(require);
require('mootools');

var parseJSON = function(str) {
  try {
    var r = JSON.parse(str);
  } catch (e) {
    throw new Error(e.message + "\n" + str);
  }
  return r;
};

module.exports = new Class({

  initialize: function(casper, root) {
    this.casper = casper;
    this.root = root;
  },

  insert: function(onInsert, table, key, items) {
    require('fs').write(this.root + '/site/data/parsed/' + key, JSON.stringify(items));
    require('child_process').execFile('php', [this.root + '/cmd.php', 'insert', table, key], null, function(err, stdout, stderr) {
      if (onInsert) onInsert();
    });
    this.casper.wait(500);
  },

  update: function(onUpdate, table, id, k, v) {
    require('child_process').execFile('php', [this.root + '/cmd.php', 'update', table, id, k, v], null, function(err, stdout, stderr) {
      if (onUpdate) onUpdate();
    });
    this.casper.wait(300);
  },

  updateBy: function(onUpdate, table, param, paramV, k, v) {
    require('child_process').execFile('php', [this.root + '/cmd.php', 'updateBy', table, param, paramV, k, v], null, function(err, stdout, stderr) {
      if (onUpdate) onUpdate();
    });
  },

  select: function(onSelect, table, what, conds) {
    require('child_process').execFile('php', [this.root + '/cmd.php', 'select', table, JSON.stringify(what), JSON.stringify(conds)], null, function(err, stdout, stderr) {
      if (onSelect) onSelect(parseJSON(stdout));
    });
    this.casper.wait(500);
  },

  selectExportGroups: function(onSelect, table, what, conds) {
    require('child_process').execFile('php', [this.root + '/cmd.php', 'selectExportGroups', table, JSON.stringify(what), JSON.stringify(conds)], null, function(err, stdout, stderr) {
      if (onSelect) onSelect(parseJSON(stdout));
    });
    this.casper.wait(500);
  },

  selectNotPassedKeywords: function(onSelect, table, what, conds, userId) {
    var a = [
      this.root + '/cmd.php', 'selectNotPassedKeywords', table, JSON.stringify(what), JSON.stringify(conds), userId
    ];
    require('child_process').execFile('php', a, null, function(err, stdout, stderr) {
      //console.debug(stdout);
      if (onSelect) onSelect(parseJSON(stdout));
    });
    this.casper.wait(500);
  },

  joinSync: function(onComplete, ids, userId) {
    require('child_process').execFile('php', [
      this.root + '/cmd.php', 'joinSync', JSON.stringify(ids), userId
    ], null, function(err, stdout, stderr) {
      onComplete();
    });
    this.casper.wait(1000);
  }

});