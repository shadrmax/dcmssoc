<?
include_once '../sys/inc/start.php';
include_once COMPRESS;
include_once SESS;
include_once '../sys/inc/home.php';
include_once SETTINGS;
$temp_set=$set;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once ADM_CHECK;
include_once USER;
adm_check();

$set['title']='Настройки системы';



if (isset($_POST['save'])) {

// ShaMan
  $temp_set['title'] = esc(stripcslashes(htmlspecialchars($_POST['title'])), 1);
// Тут конец моих дум
  $temp_set['mail_backup'] = esc($_POST['mail_backup']);
  $temp_set['p_str'] = intval($_POST['p_str']);
  dbquery("ALTER TABLE `user` CHANGE `set_p_str` `set_p_str` INT( 11 ) DEFAULT '$temp_set[p_str]'");


  if (!preg_match('#\.\.#', $_POST['set_them']) && is_dir(H . 'style/themes/' . $_POST['set_them'])) {
    $temp_set['set_them'] = $_POST['set_them'];
    dbquery("ALTER TABLE `user` CHANGE `set_them` `set_them` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '$temp_set[set_them]'");
  }

  if (!preg_match('#\.\.#', $_POST['set_them2']) && is_dir(H . 'style/themes/' . $_POST['set_them2'])) {
    $temp_set['set_them2'] = $_POST['set_them2'];
    dbquery("ALTER TABLE `user` CHANGE `set_them2` `set_them2` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '$temp_set[set_them2]'");
  }

  if ($_POST['show_err_php'] == 1 || $_POST['show_err_php'] == 0) {
    $temp_set['show_err_php'] = intval($_POST['show_err_php']);
  }

  if (isset($_POST['antidos']) && $_POST['antidos'] == 1)
    $temp_set['antidos'] = 1; else $temp_set['antidos'] = 0;

  if (isset($_POST['antimat']) && $_POST['antimat'] == 1)
    $temp_set['antimat'] = 1; else $temp_set['antimat'] = 0;

  $temp_set['meta_keywords'] = esc(stripcslashes(htmlspecialchars($_POST['meta_keywords'])), 1);
  $temp_set['background'] = esc(stripcslashes(htmlspecialchars($_POST['background'])), 1);


  $temp_set['meta_description'] = esc(stripcslashes(htmlspecialchars($_POST['meta_description'])), 1);
  $temp_set['toolbar'] = intval($_POST['toolbar']);
  $temp_set['exit'] = intval($_POST['exit']);
  $temp_set['timeadmin'] = intval($_POST['timeadmin']);
  $temp_set['job'] = intval($_POST['job']);
  $temp_set['replace'] = intval($_POST['replace']);
  if ($_POST['replace'] != 1) {

  }


  $temp_set['main'] = esc(stripcslashes(htmlspecialchars(($_POST['main']))));
  $temp_set['header'] = esc(stripcslashes(htmlspecialchars(($_POST['header']))));
  if (save_settings($temp_set)) {
    admin_log('Настройки', 'Система', 'Изменение системных настроек');
    msg2('Настройки успешно приняты');
  } else
    $err = 'Нет прав для изменения файла настроек';

  header( "Location: " . $_SERVER [ "REQUEST_URI" ]);
exit();




}
include_once THEAD;
title();
err();
aut();

echo "<form method=\"post\" action=\"?\">\n";


echo "Название сайта:<br />\n<input name=\"title\" value=\"$temp_set[title]\" type=\"text\" /><br />\n";
echo "Пунктов на страницу:<br />\n<input name=\"p_str\" value=\"$temp_set[p_str]\" type=\"text\" /><br />\n";

echo "Главная страница:<br />\n<input name=\"main\" value=\"".setget('main',"")."\" type=\"text\" /><br />\n";

echo "Admin Toolbar:<br />\n

<select name='toolbar'>
  <option ".(setget('toolbar',1)==1? " selected ":null)." value='1'>Да</option>
  <option ".(setget('toolbar',1)==0? " selected ":null)." value='0'>Нет</option>
</select>
<br />\n";



echo "Время жизни админ сессии:<br />\n<input name=\"timeadmin\" value='".setget('timeadmin',1000)."' type=\"text\" /><br />\n";


/*

echo 'Фон сайта:<br />

<input type="color"  name="background"
           value="'.setget('background').'">

<br />';

*/


echo "Работа сайта:<br />\n

<select name='job'>
  <option ".(setget('job',1)==1? " selected ":null)." value='1'>Включено</option>
  <option ".(setget('job',1)==0? " selected ":null)." value='0'>Выключено</option>
</select>
<br />\n";


echo "Выход с подтверждением:<br />\n

<select name='exit'>
  <option ".(setget('exit',1)==1? " selected ":null)." value='1'>Да</option>
  <option ".(setget('exit',1)==0? " selected ":null)." value='0'>Нет</option>
</select>

<br />\n";

echo "Шапка сайта:<br />\n

<select name='header'>
  <option ".(setget('header',"index")=="index"? " selected ":null)." value='index'>Только на главной</option>
  <option ".(setget('header',"all")=="all"? " selected ":null)." value='all'>На всех страницах</option>
</select>

<br />\n";

/*
echo "  Установка плагинов через папку /Replace/:<br />\n

<select name='replace'>
  <option ".(setget('replace',1)==1? " selected ":null)." value='1'>Включено</option>
  <option ".(setget('replace',1)==0? " selected ":null)." value='0'>Отключено</option>
</select>

<br />\n";

*/



echo "Тема (WAP):<br />\n<select name='set_them'>\n";
$opendirthem=opendir(H.'style/themes');
while ($themes=readdir($opendirthem)){
// пропускаем корневые папки и файлы
if ($themes=='.' || $themes=='..' || !is_dir(H."style/themes/$themes"))continue;
// пропускаем темы для web браузеров
if (test_file2(H."style/themes/$themes/.only_for_web"))continue;
echo "<option value='$themes'".($temp_set['set_them']==$themes?" selected='selected'":null).">".trim(file_get_contents(H.'style/themes/'.$themes.'/them.name'))."</option>\n";
}
closedir($opendirthem);
echo "</select><br />\n";

echo "Тема (WEB):<br />\n<select name='set_them2'>\n";
$opendirthem=opendir(H.'style/themes');

while ($themes=readdir($opendirthem)){
// пропускаем корневые папки и файлы
if ($themes=='.' || $themes=='..' || !is_dir(H."style/themes/$themes"))continue;
// пропускаем темы для wap браузеров
if (file_exists(H."style/themes/$themes/.only_for_wap"))continue;
echo "<option value='$themes'".($temp_set['set_them2']==$themes?" selected='selected'":null).">".trim(file_get_contents(H.'style/themes/'.$themes.'/them.name'))."</option>\n";
}
closedir($opendirthem);
echo "</select><br />\n";
echo "Ключевые слова (META):<br />\n";
echo "<textarea name='meta_keywords'>$temp_set[meta_keywords]</textarea><br />\n";
echo "Описание (META):<br />\n";
echo "<textarea name='meta_description'>$temp_set[meta_description]</textarea><br />\n";


echo "<label><input type='checkbox'".($temp_set['antidos']?" checked='checked'":null)." name='antidos' value='1' /> Анти-Dos*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['antimat']?" checked='checked'":null)." name='antimat' value='1' /> Анти-Мат</label><br />\n";

echo "Ошибки интерпретатора:<br />\n<select name=\"show_err_php\">\n";
echo "<option value='0'".($temp_set['show_err_php']==0?" selected='selected'":null).">Скрывать</option>\n";
echo "<option value='1'".($temp_set['show_err_php']==1?" selected='selected'":null).">Показывать администрации</option>\n";
echo "</select><br />\n";




echo "E-mail для BackUp:<br />\n<input type='text' name='mail_backup' value='$temp_set[mail_backup]'  /><br />\n";

echo "<br />\n";
echo "* Анти-Dos - защита от частых запросов с одного IP-адреса<br />\n";
echo "<input value=\"Изменить\" name='save' type=\"submit\" />\n";
echo "</form>\n";

if (user_access('adm_panel_show')){
echo "<div class='foot'>\n";
echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
echo "</div>\n";
}
include_once TFOOT;
