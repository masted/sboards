<?php

class SboardsWorkers {

  function check() {
    while (1) {
      //$this->_check('search', SboardsCore::users());
      $this->_check('join', SboardsCore::groupsReport());
      sleep(5);
    }
  }

  function stop() {
    $this->_stop('search', SboardsCore::users());
    $this->_stop('join', SboardsCore::groupsReport());
  }

  protected function _stop($action, $users) {
    foreach ($users as $userId => $user) {
      $daemon = (new DaemonInstaller(PROJECT_KEY, "$action-$userId", [
        'bin'  => '/usr/bin/casperjs',
        'opts' => SITE_PATH."/casper/$action.js $userId"
      ]));
      print `sudo /etc/init.d/$daemon->name stop`;
    }
  }

  protected function _check($action, $users) {
    $s = '';
    foreach ($users as $userId => $user) {
      $daemon = (new DaemonInstaller(PROJECT_KEY, "$action-$userId", [
        'bin'  => '/usr/bin/casperjs',
        'opts' => SITE_PATH."/casper/$action.js $userId"
      ]));
      if ($action == 'search' or $user['requested'] < $user['all']) {
        $s .= "check $daemon->name                ";
        $checkResult = `sudo /etc/init.d/$daemon->name check`;
        if (strstr($checkResult, 'Starting')) print date('d.m.Y H:i:s').': '.$daemon->name." restarted\n";
      }
    }
  }

}