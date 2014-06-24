<?php

$table = $_SERVER['argv'][2];
$what = json_decode($_SERVER['argv'][3]);
$what = is_array($what) ? implode(', ', $what) : $what;
$conds = json_decode($_SERVER['argv'][4], true);
$cond = new DbCond();
$cond->setOrder('id ASC');
foreach ($conds as $method => $param) {
  if (is_array($param)) {
    foreach ($param as $k => $v) {
      call_user_func_array([$cond, $method], [$k, $v]);
    }
  }
  else {
    call_user_func_array([$cond, $method], [$param]);
  }
}
print json_encode(db()->select('SELECT '.$what.' FROM '.$table.$cond->all()));