<div class="items">
  <ul>
    <? foreach ($d as $v) { ?>
      <li>
        <a href="<?= '/topic/'.$v['id1'].'/'.$v['id2'] ?>" class="title"><?= $v['title'] ?></a>
        <small><?= Date::datetimeStrSql($v['dateCreate']) ?></small>
        <span class="author"><?= $v['author'] ?></span>
      </li>
    <? } ?>
  </ul>
</div>