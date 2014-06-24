<?php

$table = $_SERVER['argv'][2];
$id = $_SERVER['argv'][3];
$k = $_SERVER['argv'][4];
$v = $_SERVER['argv'][5];

db()->update($table, $id, [$k => $v]);
print "$table, $id, [$k => $v]";