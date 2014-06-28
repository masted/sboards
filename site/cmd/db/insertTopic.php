<?php

$items = json_decode(file_get_contents(DATA_PATH.'/parsed/'.$_SERVER['argv'][3]), true);
foreach ($items as $v) {
  if (!isset($v['dateCreate'])) $v['dateCreate'] = dbCurTime();
  if (isset($v['title'])) $v['title'] = strip_tags($v['title']);
  db()->insert($_SERVER['argv'][2], $v, Db::modeInsertIgnore);
}
if (!empty($items)) db()->update('vkGroups', $items[0]['id1'], ['dateExport' => dbCurTime()]);
