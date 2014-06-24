<?php

$d = json_decode(file_get_contents(DATA_PATH.'/parsed/topics/'.$_SERVER['argv'][2]), true);
db()->insert('vkTopics', [
  'id1' => $d['id1'],
  'id2' => $d['id2'],
  'title' => $d['title']
]);
foreach ($d['comments'] as $n => $text) {
  db()->insert('vkTopicComments', [
    'id1' => $d['id1'],
    'id2' => $d['id2'],
    'comment' => $text,
    'author' => $d['authors'][$n][1],
  ]);
}
print 'topic & comments created';