<?php

$_SERVER['argv'] = array (
  0 => '/home/user/ngn-env/projects/sboards/cmd.php',
  1 => 'selectExportGroups',
  2 => 'vkGroups',
  3 => '"*"',
  4 => '{"addF":{"userId":"5"}}',
);
$table = $_SERVER['argv'][2];
$what = json_decode($_SERVER['argv'][3]);
$what = is_array($what) ? implode(', ', $what) : $what;
$conds = json_decode($_SERVER['argv'][4], true);
$cond = new DbCond();
$cond->setOrder('id ASC');
$cond->addF('joined', 1);
$cond->addRangeFilter('dateExport', '0000-00-00', dbCurTime(time() - 7 * 60 * 60 * 24)); // только те, что были экспортированы не позже, чем 7 дней незед
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