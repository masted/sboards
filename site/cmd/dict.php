<?php

foreach (file(__DIR__.'/dict.txt') as $keyword) {
  db()->insert('vkKeywords', ['keyword' => trim($keyword)]);
  print '.';
}
