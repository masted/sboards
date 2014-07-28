var require = patchRequire(require);
var vkParser = require('vkParser');

module.exports = new Class({
  Extends: vkParser,

  run: function() {
    this.auth(this.startUpdate.bind(this));
  },

  startUpdate: function() {
    // получаем топики, которые были обновлены
  }

});