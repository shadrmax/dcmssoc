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






user_access('adm_set_chat',null,'index.php?'.SID);







adm_check();















$set['title']='Чат - шутки';







include_once THEAD;







title();







if (isset($_GET['act']) && isset($_FILES['file']['tmp_name']))







{















if (isset($_POST['replace']))







dbquery('TRUNCATE `chat_shutnik`');







$k_add=0;







$list=@file($_FILES['file']['tmp_name']);







for($i=0;$i<count($list);$i++)







{







$shut=trim($list[$i]);







if (strlen2($shut)<10)continue;







dbquery("INSERT INTO `chat_shutnik` (`anek`) VALUES ('".my_esc($shut)."')");







$k_add++;







}







admin_log('Чат','Добавление',"Добавлено $k_add шуток");







msg("Успешно добавлено $k_add из $i шуток");















}







err();







aut();























echo "Всего шуток в базе: ".dbresult(dbquery("SELECT COUNT(*) FROM `chat_shutnik`"),0)."<br />\n";







echo "<form method='post' action='?act=$passgen' enctype='multipart/form-data'>\n";















echo "<input type='file' name='file' /><br />\n";







echo "Поддерживаются только текстовые файлы в кодировке UTF-8.<br />\nКаждая шутка должна быть в отдельной строке.\nШутки короче 10 символов игнорируются.<br />\n";







echo "<input value='Заменить' name='replace' type='submit' /><br />\n";







echo "<input value='Добавить' name='add' type='submit' /><br />\n";







echo "</form>\n";























echo "<div class='foot'>\n";







echo "&raquo;<a href='/adm_panel/settings_chat.php'>Настройки чата</a><br />\n";







echo "&raquo;<a href='/adm_panel/chat_vopr.php'>Вопросы викторины</a><br />\n";







if (user_access('adm_panel_show'))







echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";







echo "</div>\n";















include_once TFOOT;







?>