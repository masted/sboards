<?php

while (1) {
  $workers = ['search', 'join'];
  $lines = explode("\n", `ps aux | grep sboards`);
  $r = [];
  foreach ($workers as $worker) $r[$worker] = [];
  foreach ($workers as $worker) {
    foreach ($lines as $line) {
      $p = '#'.PROJECT_KEY.'/site/casper/'.$worker.'\\.js (\d+)#';
      if (preg_match($p, $line, $m)) {
        $r[$worker][] = $m[1];
      }
    }
  }
  $s = "-------------                     \n".date('i:H:s')."                     \n";
  $s .= "workers --------------           \nsearch: ".implode(', ', $r['search']).'          '. //
    "\n".'join:   '.implode(', ', $r['join'])."              \n";
  Cli::replaceOut($s);
  sleep(5);
}
