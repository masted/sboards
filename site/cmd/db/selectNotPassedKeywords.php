<?php

/*
$_SERVER['argv'] = array (
  0 => '/home/user/ngn-env/projects/sboards/cmd.php',
  1 => 'selectNotPassedKeywords',
  2 => 'vkKeywords',
  3 => '["id","keyword"]',
  4 => '{"setLimit":1,"setOrder":"id","addF":{"userId":[0,"10"],"passed":0}}',
  5 => '10',
);
*/

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
$r = json_encode($r);
print $r;
