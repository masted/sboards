var require = patchRequire(require);
new (require('vkParser'))(require('casper').create(), 4).run();