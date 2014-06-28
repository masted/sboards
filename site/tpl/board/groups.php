<div class="items">
  <ul>
    <? foreach ($d['groups'] as $v) { ?>
      <li>
        <a href="<?= '/group/'.$v['id'] ?>"><?= $v['title'] ?></a>
        <small>Обновлено: <?= Date::datetimeStrSql($v['dateExport']) ?></small>
      </li>
    <? } ?>
  </ul>
</div>