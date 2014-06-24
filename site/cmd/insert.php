<?php

$items = json_decode(file_get_contents(DATA_PATH.'/parsed/'.$_SERVER['argv'][3]), true);
foreach ($items as $v) {
  $v['dateCreate'] = dbCurTime();
  $v['title'] = strip_tags($v['title']);
    db()->insert($_SERVER['argv'][2], $v, Db::modeInsertIgnore);
}