var casper = require('casper').create();
var vktopic = new (require('vktopic'))(casper);
vktopic.auth(function() {
  vktopic.parseGroupTopics('board32220012', function(data) {
    console.log('parse complete');
    casper.then(function() {
      require('fs').write(vktopic.home + '/data/topics/' + data.id1 + '_' + data.id2, JSON.stringify(data));
      require('child_process').execFile('php', ['/home/user/ngn-env/projects/sboards/cmd.php', 'processTopic', data.id1 + '_' + data.id2], null, function(err, stdout, stderr) {
        console.log(stdout);
      });
    });
    casper.wait(500, function() {
      // finish
    });
  });
});
