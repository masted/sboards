<?php

while (1) {
  foreach (SboardsCore::users() as $userId => $user) {
    output("sync $userId");
    $p = SITE_PATH.'/casper/joinSync.js '.$userId;
    print `casperjs $p`;
  }
  sleep(60 * 30);
}
