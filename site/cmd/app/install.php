<?php

$install = function($action) {
  foreach (SboardsCore::users() as $userId => $user) {
    (new DaemonInstaller(PROJECT_KEY, "$action-$userId", [
      'bin' => '/usr/bin/casperjs',
      'opts' => SITE_PATH."/casper/$action.js $userId"
    ]))->install();
  }
};
$install('join');
$install('search');