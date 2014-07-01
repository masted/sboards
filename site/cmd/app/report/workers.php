<?php

$workers = new SboardsWorkers;
while (1) {
  $working = $workers->getWorking();
  $s = "-------------                     \n".date('i:H:s')."                     \n";
  $s .= "workers ----------------------  \n";
  foreach ($working as $k => $v) $s .= str_pad($k.':', 10).implode(', ', $v)."                     \n";
  Cli::replaceOut($s);
  sleep(5);
}
