<?php

$c = '<a href="/id53772310" class="mem_link" mention="bp-19529_152776" mention_id="id53772310" onclick="return mentionClick(this, event)" onmouseover="Board.mentionOver(this)">Diana</a>';
$c = preg_replace('/<a href="\\/id\d+"[^>]+>(.*)<\\/a>/', '$1', $c);
$c = str_replace(' onclick="return mentionClick(this, event)" onmouseover="Board.mentionOver(this)"', '', $c);
print $c;
//foreach (db()->query("SELECT * FROM vkTopicComments") as $v) {
  //db()->query("UPDATE vkTopicComments SET comment=?", $v['comment']);

//}