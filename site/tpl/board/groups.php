<div class="items groups">
  <ul>
    <? foreach ($d['groups'] as $v) { ?>
      <li>
        <a href="<?= '/group/'.$v['id'] ?>" class="title"><?= $v['title'] ?></a>
        <small>Обновлено: <?= Date::datetimeStrSql($v['dateExport']) ?></small>
      </li>
    <? } ?>
  </ul>
</div>