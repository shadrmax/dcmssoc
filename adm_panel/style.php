<?


include_once '../sys/inc/start.php';


include_once COMPRESS;


include_once SESS;


include_once '../sys/inc/home.php';


include_once SETTINGS;


include_once DB_CONNECT;


include_once IPUA;


include_once FNC;


include_once ADM_CHECK;


include_once USER;


adm_check();

user_access('adm_set_sys', NULL, 'index.php?' . SID);


$set['title'] = 'style.css';


include_once THEAD;


title();


err();


aut();



if (isset($_POST['robots'])) $robots = $_POST['robots']; else  $robots = '' ;
if (isset($_POST['save'])) {
  $fs = fopen(H."style/themes/$set[set_them]/style.css", "w");
  $text = fputs($fs, $robots);
  fclose($fs);
}
$text = '';
$f = file(H."style/themes/$set[set_them]/style.css");
for ($i = 0; $i < count($f); $i++) {
  $text = "$text$f[$i]";
}
?>

<form method="POST">
  <textarea rows="20" cols="50" name="robots"><? echo $text; ?></textarea><br>
  <input type=submit name="save" value="Сохранить">
</form>



<?php

if (user_access('adm_panel_show')) {
  echo "<div class='foot'>\n";
  echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
  echo "</div>\n";
}


include_once TFOOT;

