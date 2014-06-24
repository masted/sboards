var require = patchRequire(require);
require('mootools');
var loglevel = require('loglevel');

module.exports = new Class({
  Implements: loglevel,

  logLevel: 1,

  thenOpen: function(url, callback) {
    this.log('open url: ' + url, 2);
    this.casper.thenOpen(url, function(page) {
      this.log('url opened: ' + url, 2);
      this.wrapCallback(callback, page);
    }.bind(this));
  },

  waitForSelector: function(selector, callback) {
    this.casper.waitForSelector(selector, function() {
      this.wrapCallback(callback);
    }.bind(this));
  },

  capture: function(top) {
    return;
    this.casper.capture('/home/user/ngn-env/rumax/web/captures/1.png', {
      top: 0,
      left: 0,
      width: 780,
      height: 500
    });
    this.log('CAPTURED', 3);
    require('child_process').execFile('run', ['rumax/ping'], null, function(err, stdout, stderr) {
      this.log('PINGED', 3);
    }.bind(this));
  },

  wrapCallback: function(callback, arg1) {
    this.capture();
    callback(arg1);
  }

});