var vkGroupSearch = require('vkGroupSearch');

var s = (new vkGroupSearch(require('casper').create()));

s.auth(function() {
  s.selectKeyword(function() {
    require('utils').dump(s.keyword);
  });
});


