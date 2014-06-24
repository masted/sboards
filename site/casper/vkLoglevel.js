var require = patchRequire(require);
var loglevel = require('loglevel');

module.exports = new Class({
  Extends: loglevel,

  logName: 'casper',
  logWriteLevel: 1,

  initialize: function() {
    this.logDir = require('system').args[3].replace(new RegExp('(.*)/[^/]+/[^/]+'), '$1') + '/logs';
  },

  log: function(text, level) {
    if (level === undefined) level = 0;
    this.parent(text, level);
    if (level <= this.logWriteLevel) require('fs').write(this.logDir + '/' + this.logName + '.log', this.userId + ': ' + text + "\n", 'a');
  }

});