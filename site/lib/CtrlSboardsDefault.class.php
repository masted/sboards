<?php

class CtrlSboardsDefault extends CtrlCommon {

  protected function getParamActionN() {
    return 0;
  }

  protected function init() {
    $this->d['lastTopics'] = db()->query("SELECT * FROM vkTopics ORDER BY dateUpdate DESC LIMIT 3");
    if (isset($this->req->params[1])) {
      $this->d['group'] = db()->selectRow("SELECT * FROM vkGroups WHERE id=?d ", $this->req->param(1));
    }
  }

  function action_default() {
    $this->d['tpl'] = 'board/groups';
    //$this->d['topics'] = db()->query("SELECT * FROM vkTopics WHERE 1 ORDER BY dateCreate DESC");
    $this->d['groups'] = db()->query("SELECT * FROM vkGroups WHERE dateExport > 0 ORDER BY dateExport DESC");
    $this->setPageTitle('Общение в группах');
  }

  function action_group() {
    $this->d['tpl'] = 'board/group';
    $this->d['topics'] = db()->query("SELECT * FROM vkTopics WHERE id1=?d ORDER BY dateCreate DESC", $this->req->param(1));
    $this->setPageTitle($this->d['group']['title']);
  }

  function action_topic() {
    $this->d['tpl'] = 'board/topic';
    $this->d['topic'] = db()->selectRow("SELECT * FROM vkTopics WHERE id1=?d AND id2=?d", $this->req->param(1), $this->req->param(2));
    $this->d['comments'] = db()->query("SELECT * FROM vkTopicComments WHERE id1=?d AND id2=?d ORDER BY dateCreate DESC", $this->req->param(1), $this->req->param(2));
    $this->setPageTitle($this->d['topic']['title']);
  }

}