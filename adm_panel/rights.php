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


$set['title'] = 'Получение прав';


include_once THEAD;


title();


err();


aut();


if (isset($_GET['chmod_ok'])) {
  //chmod(H . 'install/', 0777);
 // chmod(H . 'sys/avatar/', 0777);
  chmod(H . 'sys/dat/', 0777);
  chmod(H . 'sys/forum/files', 0777);
  chmod(H . 'sys/gallery/48/', 0777);
  chmod(H . 'sys/gallery/50/', 0777);
  chmod(H . 'sys/gallery/128/', 0777);
  chmod(H . 'sys/gallery/640/', 0777);
  chmod(H . 'sys/gallery/foto/', 0777);
  chmod(H . 'sys/inc/', 0777);
  chmod(H . 'sys/fnc/', 0777);
  chmod(H . 'sys/obmen/files/', 0777);
  chmod(H . 'sys/obmen/screens/14/', 0777);
  chmod(H . 'sys/obmen/screens/48/', 0777);
  chmod(H . 'sys/obmen/screens/128/', 0777);
  chmod(H . 'sys/update/', 0777);
  chmod(H . 'sys/tmp/', 0777);
  chmod(H . 'style/themes/', 0777);
  chmod(H . 'style/smiles/', 0777);
  chmod(H . 'sys/gift/', 0777);
  msg('Права успешно получены!');
}
echo "<form method='post' action='?chmod_ok'>";
echo "<input type='submit' name='refresh' value='Получить права!' />";
echo "</form>";

include_once check_replace(H . 'sys/inc/chmod_test.php');


if (user_access('adm_panel_show')) {
  echo "<div class='foot'>\n";
  echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
  echo "</div>\n";
}


include_once TFOOT;


?>