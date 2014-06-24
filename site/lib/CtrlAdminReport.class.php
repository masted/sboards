<?php

class CtrlAdminReport extends CtrlAdmin {

  function action_default() {
    die2();
    //$this->d['table'] = [
      //['пользователь', 'всего групп', 'подано заявок', 'приняли']
      //[1, 2, 3]
    //];
    $this->d['tpl'] = 'common/tableTable';
  }

}