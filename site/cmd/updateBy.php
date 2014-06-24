<?php

$table = $_SERVER['argv'][2];
$param = $_SERVER['argv'][3];
$paramV = $_SERVER['argv'][4];
$k = $_SERVER['argv'][5];
$v = $_SERVER['argv'][6];

db()->query("UPDATE $table SET $k=$v WHERE $param='$paramV'");