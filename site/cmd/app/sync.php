<?php

while (1) {
  foreach (SboardsCore::users(true) as $userId => $user) {
    output("sync $userId");
    $p = PROJECT_PATH.'/casper/joinSync.js '.$userId;
    print `casperjs $p`;
  }
  sleep(60 * 30);
}
