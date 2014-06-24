var vkGroupSearch = new (require('vkGroupSearch'))(require('casper').create(), require('vkParserArgs'));
vkGroupSearch.keyword = {
  keyword: 'абсурд'
};
vkGroupSearch.auth(vkGroupSearch.startParse.bind(vkGroupSearch));