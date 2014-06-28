<?php

$table = $_SERVER['argv'][2];
$id = $_SERVER['argv'][3];
$k = $_SERVER['argv'][4];
$v = $_SERVER['argv'][5];

$d = [$k => $v];
if ($k == 'requested') $d['dateRequest'] = dbCurTime();
if ($k == 'passed') $d['datePassed'] = dbCurTime();
db()->update($table, $id, $d);
