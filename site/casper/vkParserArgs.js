var require = patchRequire(require);
var args = require('system').args;
if (args[4] === undefined) throw new Error('param #2 (userId) is required');

var root = args[3].replace(new RegExp('(.*)/[^/]+/[^/]+'), '$1');

module.exports = {
  root: root,
  userId: args[4]
};