var require = patchRequire(require);
var rumax = require('rumax');

module.exports = new Class({
  Implements: rumax,

  i: 0,
  key: 'url',

  initialize: function(casper, items, onLink, testItem, onComplete) {
    this.casper = casper;
    this.items = items;
    if (!this.items.length) throw new Error('no items');
    this.onLink = onLink;
    if (testItem) this.testItem = testItem;
    this.onComplete = onComplete;
    this.proceessCurrentItem();
  },

  testItem: function() {
    return true;
  },

  proceessCurrentItem: function() {
    if (!this.testItem(this.items[this.i])) {
      this.log('item #' + this.i + ' skipped', 1);
      this.processNextItem();
      return;
    }
    this.log('open url "' + this.items[this.i][this.key] + '" for item #' + this.i, 1);
    //this.log(JSON.stringify(this.items[this.i]), 2);
    if (this.items[this.i][this.key] === undefined) throw new Error('no "' + this.key + '" key in item');
    this.thenOpen(this.items[this.i][this.key], function() {
      this.onLink(this.items[this.i]);
      this.processNextItem();
    }.bind(this));
  },

  processNextItem: function() {
    if (!this.items[this.i + 1]) {
      for (var i = 0; i < this.items.length; i++) delete this.items[i][this.key];
      if (this.onComplete) this.onComplete(this.items);
      console.log('end');
      return;
    }
    this.i++;
    this.proceessCurrentItem();
  }

});