<?php

$table = $_SERVER['argv'][2];
$what = json_decode($_SERVER['argv'][3]);
$conds = json_decode($_SERVER['argv'][4], true);
$userId = $_SERVER['argv'][5];
$cond = new DbCond();
foreach ($conds as $method => $param) {
  if (is_array($param)) {
    foreach ($param as $k => $v) call_user_func_array([$cond, $method], [$k, $v]);
  } else {
    call_user_func_array([$cond, $method], [$param]);
  }
}
$r = db()->select('SELECT '.implode(', ', $what).' FROM '.$table.$cond->all());
db()->query("UPDATE $table SET userId=?d WHERE id IN (?a)", $userId, Arr::get($r, 'id'));
print json_encode($r);