<div class="items">
<ul>
<? foreach ($d['comments'] as $v) { ?>
  <li>
    <div class="text"><?= $v['comment'] ?></div>
    <small><?= Date::datetimeStrSql($v['dateCreate']) ?></small>
    <span class="author"><?= $v['author'] ?></span>
  </li>
<? } ?>
</ul>
</div>