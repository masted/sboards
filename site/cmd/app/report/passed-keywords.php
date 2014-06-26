<?php

$lastTime = time();
while (1) {
  if (($r = db()->select('SELECT * FROM vkKeywords WHERE datePassed>?', dbCurTime($lastTime)))) {
    foreach ($r as $v) print $v['datePassed'].": ".$v['keyword']."\n";
  }
  $lastTime = time();
  sleep(5);
}
