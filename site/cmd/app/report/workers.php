<?php

$workers = new SboardsWorkers;
while (1) {
  $r = $workers->getWorking();
  $s = "-------------                     \n".date('i:H:s')."                     \n";
  $s .= "workers --------------           \n";
  foreach ($r as $k => $v) $s .= str_pad($k.':', 10).implode(', ', $v)."                     \n";
  Cli::replaceOut($s);
  sleep(5);
}
