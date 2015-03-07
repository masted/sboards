<?php

class SboardsWorkers {

  protected $workers = ['search', 'join', 'topic'];

  function __construct() {
    $enabled = [
      'search' => (bool)db()->selectCell('SELECT COUNT(*) FROM vkKeywords WHERE userId=0 AND passed=0'),
      'join' => true,
      'topic' => true,
    ];
    $this->workers = array_filter($this->workers, function($v) use ($enabled) {
      return $enabled[$v];
    });
  }

  protected function getUsers($name) {
    if ($name == 'join') {
      return SboardsCore::groupsReport();
    }
    else {
      return SboardsCore::users();
    }
  }

  function check() {
    while (1) {
      foreach ($this->workers as $name) $this->_check($name);
      sleep(15);
    }
  }

  function stop() {
    foreach ($this->workers as $name) $this->_stop($name, $this->getUsers($name));
  }

  function install() {
    foreach ($this->workers as $name) {
      foreach (array_keys($this->getUsers($name)) as $userId) {
        $this->daemonInstaller($name, $userId)->install();
      }
    }
  }

  protected function daemonInstaller($name, $userId) {
    return new DaemonInstaller(PROJECT_KEY, "$name-$userId", [
      'bin'  => '/usr/bin/casperjs',
      'opts' => PROJECT_PATH."/casper/$name.js $userId"
    ]);
  }

  protected function _stop($action, $users) {
    foreach (array_keys($users) as $userId) {
      $name = $this->daemonInstaller($action, $userId)->name;
      print `sudo /etc/init.d/$name stop`;
    }
  }

  function _check($name) {
    $users = $this->getUsers($name);
    $s = '';
    foreach ($users as $userId => $user) {
      $daemon = (new DaemonInstaller(PROJECT_KEY, "$name-$userId", [
        'bin'  => '/usr/bin/casperjs',
        'opts' => PROJECT_PATH."/casper/$name.js $userId"
      ]));
      if ($this->checkUser($name, $user)) {
        $s .= "check $daemon->name                ";
        $checkResult = `sudo /etc/init.d/$daemon->name check`;
        if (strstr($checkResult, 'Starting')) print date('d.m.Y H:i:s').': '.$daemon->name." restarted\n";
      }
    }
  }

  protected function checkUser($name, $user) {
    $method = 'checkUser'.ucfirst($name);
    if (method_exists($this, $method)) return $this->$method($user);
    return true;
  }

  protected function checkUserSearch($user) {
    //db()->query('SELECT * FROM ');
    //return $user['requested'] < $user['all'];
  }

  protected function checkUserJoin($user) {
    return $user['requested'] < $user['all'];
  }

  protected function checkUserTopic($user) {
    return (bool)db()->selectCell('SELECT id FROM vkGroups WHERE userId=?d AND dateExport<? AND joined=1 LIMIT 1', $user['id'], dbCurTime(SboardsCore::exportTime()));
  }

  /**
   * Возвращает актуальных пользователей для всех воркеров
   *
   * @return array
   */
  function getActual() {
    $r = [];
    foreach ($this->workers as $name) {
      $r[$name] = [];
      $users = $this->getUsers($name);
      foreach ($users as $user) {
        $r[$name][$user['id']] = $this->checkUser($name, $user);
      }
    }
    return $r;
  }

  function getWorking() {
    $lines = explode("\n", `ps aux | grep sboards`);
    $r = [];
    foreach ($this->workers as $worker) $r[$worker] = [];
    foreach ($this->workers as $worker) {
      foreach ($lines as $line) {
        $p = '#'.PROJECT_KEY.'/site/casper/'.$worker.'\\.js (\d+)#';
        if (preg_match($p, $line, $m)) {
          $r[$worker][] = $m[1];
        }
      }
    }
    return $r;
  }

}