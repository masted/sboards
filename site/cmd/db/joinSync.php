<?php

$ids = json_decode($_SERVER['argv'][2], true);
$userId = $_SERVER['argv'][3];
db()->query("UPDATE vkGroups SET joined=1 WHERE id IN (?a) AND userId=?d", $ids, $userId);