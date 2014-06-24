<?php

class SboardsJsonSelect {

  protected $table, $what;

  function __construct() {
    $this->table = Misc::checkEmpty($_SERVER['argv'][2]);
    $this->what = json_decode(Misc::checkEmpty($_SERVER['argv'][3]));
  }

  protected function cond() {
    $conds = json_decode(Misc::checkEmpty($_SERVER['argv'][4]), true);
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
    return $cond;
  }

  protected function _basic($cond) {
    print json_encode(db()->select('SELECT '.implode(', ', $this->what).' FROM '.$this->table.$cond->all()));
  }

  function basic() {
    $this->_basic($this->cond());
  }

  function notPassedKeywords() {
    $userId = Misc::checkEmpty($_SERVER['argv'][5]);
    $r = db()->select('SELECT '.implode(', ', $this->what).' FROM '.$this->table.$this->cond()->all());
    db()->query("UPDATE $this->table SET userId=?d WHERE id IN (?a)", $userId, Arr::get($r, 'id'));
  }

  function exportGroups() {
    $cond = $this->cond();
    $cond->addRangeFilter('exportDate', 0, dbCurTime(time() - 7 * 60 * 60 * 24)); // только те, что были экспортированы не позже, чем 7 дней незед
    $this->_basic($cond);
  }

}