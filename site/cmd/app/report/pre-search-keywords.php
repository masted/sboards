<?php

$lastTime = time();
while (1) {
  if (($r = db()->select('SELECT * FROM vkGroupsSearch WHERE dateCreate>?', dbCurTime($lastTime)))) {
    foreach ($r as $v) print $v['dateCreate'].": ".$v['keyword'].": ".$v['name']."\n";
  }
  $lastTime = time();
  sleep(5);
}
