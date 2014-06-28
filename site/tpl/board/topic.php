<div class="items">
<ul>
<? foreach ($d['comments'] as $v) { ?>
  <li>
    <div class="text"><?= $v['comment'] ?></div>
    <span class="author"><?= $v['author'] ?>:</span>
    <small><?= Date::datetimeStrSql($v['dateCreate']) ?></small>
  </li>
<? } ?>
</ul>
</div>