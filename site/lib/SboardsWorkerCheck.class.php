<?php

class SboardsWorkerCheck {

  function __construct() {
    while (1) {
      $s = "-------------                               \n".date('i:H:s')."                                 \n";
      $s .= $this->run('search', SboardsCore::users());
      $s .= $this->run('join', SboardsCore::groupsReport());
      $s .= "                                            \n";
      $s .= "                                            \n";
      $s .= "                                            \n";
      Cli::replaceOut($s);
      sleep(5);
    }
  }

  protected function run($action, $users) {
    $s = '';
    $mustPresents = [];
    foreach ($users as $userId => $user) {
      $daemon = (new DaemonInstaller(PROJECT_KEY, "$action-$userId", [
        'bin'  => '/usr/bin/casperjs',
        'opts' => SITE_PATH."/casper/$action.js $userId"
      ]));
      if ($action == 'search' or $user['requested'] < $user['all']) {
        $s .= "check $daemon->name                ";
        $s .= "\n".`sudo /etc/init.d/$daemon->name check`;
        $mustPresents[] = $userId;
      }
    }
    $runner = PROJECT_KEY."/site/casper/$action.js";
    $cmd = "ps aux | grep '$runner'";
    $r = `$cmd`;
    /*
    sleep(2);
    foreach ($mustPresents as $userId) {
      if (!strstr($r, "$runner $userId")) Err::log(new Exception("daemon '$runner $userId' is not standing up"));
    }
    */
    return $s;
  }

}