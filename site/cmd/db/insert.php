<?php

$items = json_decode(file_get_contents(DATA_PATH.'/parsed/'.$_SERVER['argv'][3]), true);
foreach ($items as $v) {
  if (!isset($v['dateCreate'])) $v['dateCreate'] = dbCurTime();
  if (isset($v['title'])) $v['title'] = strip_tags($v['title']);
  if (isset($v['comment'])) {
    $v['comment'] = str_replace('away.php', 'away', $v['comment']);
    $v['comment'] = preg_replace('/<a href="\\/id\d+"[^>]+>(.*)<\\/a>/', '$1', $v['comment']);
   $v['comment'] = str_replace(' onclick="return mentionClick(this, event)" onmouseover="Board.mentionOver(this)"', '', $v['comment']);
  }
  db()->insert($_SERVER['argv'][2], $v, Db::modeInsertIgnore);
}
