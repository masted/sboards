<?php

function a($n) {
  $requested = db()->selectCell('SELECT COUNT(*) FROM vkGroups WHERE dateRequest>?', dbCurTime(time() - 60 * $n));
  $searched = db()->selectCell('SELECT COUNT(*) FROM vkGroups WHERE dateCreate>?', dbCurTime(time() - 60 * $n));
  $exported = db()->selectCell('SELECT COUNT(*) FROM vkGroups WHERE dateExport>?', dbCurTime(time() - 60 * $n));
  return "speed per $n min: $searched/$requested/$exported -----------\n";
}
while (1) {
  $s = date('i:H:s')."     search/request/export                \n";
  $s .= a(1);
  $s .= a(5);
  $s .= a(10);
  Cli::replaceOut($s);
  sleep(5);
}
