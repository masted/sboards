<?php

(new Tmux)->run([
  'php '.WEBROOT_PATH.'/cmd.php report',
  'casperjs ~/ngn-env/projects/sboards/site/casper/search.js 4',
  //'casperjs ~/ngn-env/projects/sboards/site/casper/join.js 4',
  //'casperjs ~/ngn-env/projects/sboards/site/casper/search.js 5',
  //'casperjs ~/ngn-env/projects/sboards/site/casper/join.js 5',
  //'casperjs ~/ngn-env/projects/sboards/site/casper/search.js 6',
  //'casperjs ~/ngn-env/projects/sboards/site/casper/join.js 6',
  'htop'
]);