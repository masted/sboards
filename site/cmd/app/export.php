<?php

/*
while (1) {
  foreach (SboardsCore::users() as $userId => $user) {
    output("export topics $userId");
    $p = SITE_PATH.'/casper/topic.js '.$userId;
    print `casperjs $p`;
  }
  sleep(60 * 60);
}
*/

$cmds = [];
foreach (SboardsCore::users() as $userId => $user) $cmds[] = 'sudo casperjs '.SITE_PATH.'/casper/topic.js '.$userId;
(new Tmux)->run($cmds);