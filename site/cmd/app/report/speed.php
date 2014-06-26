<?php

while (1) {
  $s = "-------------                     \n".date('i:H:s')."                     \n";
  $requested = db()->selectCell('SELECT COUNT(*) FROM vkGroups WHERE dateRequest>?', dbCurTime(time() - 60));
  $searched = db()->selectCell('SELECT COUNT(*) FROM vkGroups WHERE dateCreate>?', dbCurTime(time() - 60));
  $s .= "speed per min -----------           \nrequested: ".$requested.'             '. //
    "\n".'searched:  '.$searched."              \n";
  $requested = db()->selectCell('SELECT COUNT(*) FROM vkGroups WHERE dateRequest>?', dbCurTime(time() - 60 * 5));
  $searched = db()->selectCell('SELECT COUNT(*) FROM vkGroups WHERE dateCreate>?', dbCurTime(time() - 60 * 5));
  $s .= "speed per 5 min ---------           \nrequested: ".$requested.'             '. //
    "\n".'searched:  '.$searched."              \n";
  $requested = db()->selectCell('SELECT COUNT(*) FROM vkGroups WHERE dateRequest>?', dbCurTime(time() - 60 * 10));
  $searched = db()->selectCell('SELECT COUNT(*) FROM vkGroups WHERE dateCreate>?', dbCurTime(time() - 60 * 10));
  $s .= "speed per 10 min --------           \nrequested: ".$requested.'             '. //
    "\n".'searched:  '.$searched."              \n";
  Cli::replaceOut($s);
  sleep(5);
}
