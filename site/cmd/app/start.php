<?php

(new Tmux)->notSplit(3)->run([
  'php '.WEBROOT_PATH.'/cmd.php app/check',
  'php '.WEBROOT_PATH.'/cmd.php app/report/workers',
  'php '.WEBROOT_PATH.'/cmd.php app/report/speed',
  'php '.WEBROOT_PATH.'/cmd.php app/report/per-user',
  'php '.WEBROOT_PATH.'/cmd.php app/sync',
  //'php '.WEBROOT_PATH.'/cmd.php app/report/search-keywords',
  //'php '.WEBROOT_PATH.'/cmd.php app/report/pre-search-keywords',
  'php '.WEBROOT_PATH.'/cmd.php app/report/passed-keywords',
  'htop'
]);