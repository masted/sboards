<?php

$_SERVER['argv'] = [
  '/home/user/ngn-env/projects/sboards/cmd.php',
  'db/selectNotPassedKeywords',
  'vkKeywords',
  '["id","keyword"]',
  '{"setLimit":1,"setOrder":"id","addF":{"userId":[0,"3"],"passed":0}}',
  '3'
];

$table = $_SERVER['argv'][2];
$what = json_decode($_SERVER['argv'][3]);
$conds = json_decode($_SERVER['argv'][4], true);
$userId = $_SERVER['argv'][5];
$cond = new DbCond();
foreach ($conds as $method => $param) {
  if (is_array($param)) {
    foreach ($param as $k => $v) call_user_func_array([$cond, $method], [$k, $v]);
  }
  else {
    call_user_func_array([$cond, $method], [$param]);
  }
}
$q = 'SELECT '.implode(', ', $what).' FROM '.$table.$cond->all();
$r = db()->select('SELECT '.implode(', ', $what).' FROM '.$table.$cond->all());
if (empty($r)) throw new Exception('empty result of query: '.$q);
db()->query("UPDATE $table SET userId=?d WHERE id IN (?a)", $userId, Arr::get($r, 'id'));
$r = json_encode($r);
print $r;
