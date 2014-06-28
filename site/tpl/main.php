<head>
  <title><?= $d['pageHeadTitle'] ?></title>
  <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed&subset=latin,cyrillic' rel='stylesheet'
        type='text/css'>
  <style>
    body {
      background: url(/m/img/bg.png) no-repeat -200px -50px;
      font-family: 'Roboto Condensed', sans-serif;
      margin: 0px;
    }
    .wrap {
      margin: 10px;
      position: relative;
    }
    .logo {
      display: block;
      position: absolute;
      left: 775px;
    }
    .logoNav {
      position: absolute;
      left: 923px;
      font-size: 12px;
      z-index: 2;
    }
    .logoNav a {
      color: #777;
    }
    a:hover {
      color: #000;
    }
    .page {
      width: 600px;
      padding-top: 152px;
      margin-left: 300px;
    }
    .page .items ul li {
      margin-bottom: 7px;
    }
    .page h1 {
      margin: 0;
      margin-left: 50px;
      font-size: 16px;
      color: #fff;
      font-weight: normal;
    }
    .lastBlock {
      width: 310px;
      height: 200px;
      position: absolute;
      top: 152px;
      left: 790px;
    }
    .lastBlock h2 {
      margin: 0;
      margin-left: 60px;
      font-size: 16px;
      color: #fff;
      font-weight: normal;
    }
    .lastBlock .items {
      margin-top: 20px;
    }
    .items ul, .items li {
      margin: 0;
    }
    .items li {
      list-style: none;
    }
    .lastBlock .items li {
      padding: 5px 10px 5px 15px;
      background: #e5e5e5;
      border-bottom: 7px solid #ccc;
      margin-bottom: 10px;
    }
    .lastBlock .items a {
      color: #555;
    }
    .lastBlock .items a:hover {
      color: #000;
    }
    .items a {
      display: block;
    }
    .items .author {
      margin-top: 2px;
      font-size: 11px;
      display: block;
    }
    .items small {
      float: right;
      display: block;
      font-size: 11px;
      color: #555;
    }
    .groupImage {
      position: absolute;
      top: -95px;
      left: 550px;
      background: url(/m/img/groupImageShadow.png);
      width: 144px;
      height: 205px;
    }
    .groupImage div {
      padding: 105px 25px 0;
    }
    .groupTitle {
      text-align: right;
      position: absolute;
      font-size: 14px;
      top: 10px;
      left: 300px;
      width: 250px;
    }
    .groupTitle a {
      color: #fff;
      text-shadow: 1px 0px 3px #000;
    }
    .content {
      width: 460px;
      padding: 20px 40px 20px 10px;
    }
    .content .items ul li {
      margin-bottom: 10px;
    }
    .content .items small {
      display: block;
      font-size: 11px;
      color: #999;
    }
    .content .items a {
      color: #00738A;
    }
    .content .items a:hover {
      color: #000;
    }
    .text {
      font-size: 14px;
    }
    .author {
      color: #CC3E1D;
    }
    .footer {
      background: url(/m/img/footer.png) center bottom no-repeat;
      height: 110px;
      color: #fff;
      text-shadow: 1px 0px 3px #555;
      font-size: 14px;
    }
    .footer div {
      text-align: center;
      padding-top: 70px;
    }
  </style>
</head>
<body>
<div class="wrap">
  <div class="logoNav"><a href="/">на главную</a></div>
  <a href="/" class="logo"><img src="/m/img/logo.png"/></a>
  <? if ($d['group']) { ?>
    <div class="groupTitle"><a href="/group/<?= $d['group']['id'] ?>"><?= $d['group']['title'] ?></a></div>
    <? if ($d['group']['img']) { ?>
      <div class="groupImage">
        <div><a href="/group/<?= $d['group']['id'] ?>"><img src="<?= $d['group']['img'] ?>"/></a></div>
      </div>
    <? } ?>
  <? } ?>
  <div class="page">
    <h1><?= Misc::cut($d['pageTitle'], 50) ?></h1>
    <div class="lastBlock">
      <h2>ОБСУЖДАЮТ ПРЯМО СЕЙЧАС</h2>
      <? $this->tpl('board/topics', $d['lastTopics']) ?>
    </div>
    <div class="content">
      <? $this->tpl($d['tpl'], $d) ?>
    </div>
  </div>
</div>
<div class="footer"><div>Разработано в Majexa Development</div></div>
</body>