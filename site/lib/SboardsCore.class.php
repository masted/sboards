<?php

class SboardsCore {

  static function users($all = false) {
    $users = Misc::checkEmpty(json_decode(str_replace("\n", "", file_get_contents(DATA_PATH.'/users.json')), true));
    $users = Arr::assoc($users, 'id');
    if (!$all) {
      $users = array_filter($users, function($v) {
        return isset($v['server']) and $v['server'] == gethostname();
      });
    }
    return $users;
  }

  static function groupsReport($all = false) {
    $users = self::users($all);
    $result = [];
    $describe = function(array $data, $name, &$result) use ($users) {
      foreach ($data as $k => $v) {
        if (!isset($users[$v['userId']])) {
          unset($data[$k]);
          continue;
        }
        if (!isset($result[$v['userId']]['title'])) $result[$v['userId']]['title'] = $users[$v['userId']]['title'];
        $result[$v['userId']][$name] = $v['cnt'];
      }
    };
    $select = function($name) {
      return db()->select('SELECT userId, COUNT(*) AS cnt FROM vkGroups WHERE private=0 AND '.$name.'=1 AND userId!=0 GROUP BY userId');
    };
    $selectAll = function() {
      return db()->select('SELECT userId, COUNT(*) AS cnt FROM vkGroups WHERE private=0 AND userId!=0 GROUP BY userId');
    };
    $exported = db()->select('SELECT userId, COUNT(*) AS cnt FROM vkGroups WHERE private=0 AND dateExport>? AND userId!=0 GROUP BY userId', dbCurTime(self::exportTime()));
    $describe($exported, 'exported', $result);
    $describe($select('requested'), 'requested', $result);
    $describe($select('joined'), 'joined', $result);
    $describe($selectAll(), 'all', $result);
    foreach ($result as $userId => &$v) $v['id'] = $userId;
    foreach ($result as &$v) if (!isset($v['exported'])) $v['exported'] = 0;
    foreach ($result as &$v) if (!isset($v['joined'])) $v['joined'] = 0;
    foreach ($result as &$v) if (!isset($v['requested'])) $v['requested'] = 0;
    return $result;
  }

  static function exportTime() {
    return time() - 7 * 60 * 60 * 24;
  }

}