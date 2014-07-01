<?php

while(1) {
  $s = "-------------                     \n".date('i:H:s')."    export/join/request/all           \n";
  foreach (SboardsCore::groupsReport() as $userId => $v) {
    $s .= "{$v['title']} ($userId): {$v['exported']}/{$v['joined']}/{$v['requested']}/{$v['all']}             \n";
  }
  Cli::replaceOut($s);
  sleep(2);
}
