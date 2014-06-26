<?php

while(1) {
  $s = "-------------                     \n".date('i:H:s')."                     \n";
  foreach (SboardsCore::groupsReport() as $userId => $v) {
    $s .= "{$v['title']} ($userId): {$v['joined']}/{$v['requested']}/{$v['all']}             \n";
  }
  Cli::replaceOut($s);
  sleep(2);
}
