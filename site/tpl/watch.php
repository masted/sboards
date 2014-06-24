<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?= SITE_TITLE ?> - Watch</title>
  <script src="/i/js/mootools/mootools-core-1.4.0.js"></script>
</head>
<body>
<style>
  body {
    padding-top: 10px;
    text-align: center;
  }
  .loading {
    background: url(/i/img/loader-horizontal.gif) repeat-x;
  }
  .shadow {
    border: 1px solid #888;
    box-shadow: 0px 0px 5px #555;
  }
</style>
<img src="" id="capture" class="shadow" />
<script>
  var Ngn = {};
  console.log('Init');
  $('capture').set('src', '/i/img/empty.png');
  Ngn.webSocketReconnect = function(host, port, events, attempt) {
    if (!attempt) attempt = 0;
    var ws = new WebSocket('ws://' + host + ':' + port + '/ws/');
    var _events = Object.clone(events);
    _events.onclose = function(e) {
      if (attempt > 5) return;
      (function() {
        console.log('Reconnecting...');
        Ngn.webSocketReconnect(host, port, events, attempt + 1);
      }).delay(5000);
    };
    for (i in _events) ws[i] = _events[i];
  };
  Ngn.webSocketReconnect('<?= SITE_DOMAIN ?>', 9000, {
    onmessage: function(e) {
      if (e.data.test(/domain:.*/)) {
        var domain = e.data.match(/domain:(.*)/)[1];
        document.body.addClass('loading');
        $('capture').set('src', '/sd/watch/' + domain + '.png?' + Math.random());
        $('capture').onload = function() {
          document.body.removeClass('loading');
        };
      }
    },
    onopen: function() {
      console.log("Connected");
    }
  });
</script>
</body>
