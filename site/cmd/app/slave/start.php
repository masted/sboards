<?php

(new Tmux)->run([
  'php '.WEBROOT_PATH.'/cmd.php app/check',
  'php '.WEBROOT_PATH.'/cmd.php app/report/workers',
  'htop'
]);