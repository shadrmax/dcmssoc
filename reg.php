<?
//include_once 'sys/inc/mp3.php';
//include_once 'sys/inc/zip.php';
include_once 'sys/inc/start.php';
include_once COMPRESS;
include_once SESS;
include_once 'sys/inc/home.php';
include_once SETTINGS;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once SHIF;
$show_all=true; // показ для всех
include_once USER;
only_unreg();
$set['title']='Регистрация';
include_once THEAD;
title();


if ($set['guest_select']=='1')msg("Доступ к сайту разрешен только авторизованым пользователям");
if ((!isset($_SESSION['refer']) || $_SESSION['refer']==NULL)
&& isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL &&
!preg_match('#mail\.php#',$_SERVER['HTTP_REFERER']))
$_SESSION['refer']=str_replace('&','&amp;',preg_replace('#^http://[^/]*/#','/', $_SERVER['HTTP_REFERER']));if ($set['reg_select']=='close')
{
	$err='Регистрация временно приостановлена';
	err();

	echo "<a href='/aut.php'>Авторизация</a><br />\n";
	include_once TFOOT;
}

if (isset($_SESSION['step']) && $_SESSION['step']==1 && dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '".$_SESSION['reg_nick']."'"),0)==0 && isset($_POST['pass1']) && $_POST['pass1']!=NULL && $_POST['pass2'] && $_POST['pass2']!=NULL)
{
if (!isset($err))
{
    if ($_POST['ank_mail'] == NULL) $err[] = 'Неоходимо ввести Email';
    elseif (!preg_match('#^[A-z0-9-\._]+@[A-z0-9]{2,}\.[A-z]{2,4}$#ui', $_POST['ank_mail'])) $err[] = 'Неверный формат Email';
    elseif (dbresult(dbquery("SELECT COUNT(*) FROM `reg_mail` WHERE `mail` = '" . my_esc($_POST['ank_mail']) . "'"), 0) != 0) {
        $err[] = "Пользователь с этим E-mail уже зарегистрирован";
    }

    dbquery("INSERT INTO `user` (`nick`, `pass`, `date_reg`, `date_last`, `pol`, `ank_mail`) values('" . $_SESSION['reg_nick'] . "', '" . shif($_POST['pass1']) . "', '$time', '$time', '" . intval($_POST['pol']) . "', '".$_POST['ank_mail']."')", $db);
$user = dbassoc(dbquery("SELECT * FROM `user` WHERE `nick` = '".my_esc($_SESSION['reg_nick'])."' AND `pass` = '".shif($_POST['pass1'])."' LIMIT 1"));

/*
========================================
Создание настроек юзера 
========================================
*/

dbquery("INSERT INTO `user_set` (`id_user`) VALUES ('$user[id]')");
dbquery("INSERT INTO `discussions_set` (`id_user`) VALUES ('$user[id]')");
dbquery("INSERT INTO `tape_set` (`id_user`) VALUES ('$user[id]')");
dbquery("INSERT INTO `notification_set` (`id_user`) VALUES ('$user[id]')");


if (isset($_SESSION['http_referer']))
dbquery("INSERT INTO `user_ref` (`time`, `id_user`, `type_input`, `url`) VALUES ('$time', '$user[id]', 'reg', '".my_esc($_SESSION['http_referer'])."')");

$_SESSION['id_user']=$user['id'];
setcookie('id_user', $user['id'], time()+60*60*24*365);
setcookie('pass', cookie_encrypt($_POST['pass1'],$user['id']), time()+60*60*24*365);
	dbquery("update `user` set `wall` = '0' where `id` = '$user[id]' limit 1");
	header('Location: /umenu.php?login=' . htmlspecialchars($_POST['reg_nick']) . '&pass=' . htmlspecialchars($_POST['pass1']));

echo "Если Ваш браузер не поддерживает Cookie, Вы можете создать закладку для автовхода<br />\n";
echo "<input type='text' value='http://$_SERVER[SERVER_NAME]/login.php?id=$user[id]&amp;pass=".htmlspecialchars($_POST['pass1'])."' /><br />\n";
if ($set['reg_select']=='open_mail')unset($user);
echo "<div class='foot'>\n";
echo "&raquo;<a href='settings.php'>Мои настройки</a><br />\n";
echo "&raquo;<a href='umenu.php'>Мое меню</a><br />\n";
echo "</div>\n";
include_once TFOOT;
}
}
elseif (isset($_POST['nick']) && $_POST['nick']!=NULL )
{
if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '".my_esc($_POST['nick'])."'"),0)==0)
{
$nick=my_esc($_POST['nick']);if( !preg_match("#^([A-zА-я0-9\-\_\ ])+$#ui", $_POST['nick']))$err[]='В нике присутствуют запрещенные символы';
if (preg_match("#[a-z]+#ui", $_POST['nick']) && preg_match("#[а-я]+#ui", $_POST['nick']))$err[]='Разрешается использовать символы только русского или только английского алфавита';
if (preg_match("#(^\ )|(\ $)#ui", $_POST['nick']))$err[]='Запрещено использовать пробел в начале и конце ника';
if (strlen2($nick)<3)$err[]='Короткий ник';
if (strlen2($nick)>32)$err[]='Длина ника превышает 32 символа';
}
else $err[]='Ник "'.stripcslashes(htmlspecialchars($_POST['nick'])).'" уже зарегистрирован';if (!isset($err)){
$_SESSION['reg_nick']=$nick;
$_SESSION['step']=1;
msg ("Ник \"$nick\" может быть успешно зарегистрирован");
}
}

err();
if (isset($_SESSION['step']) && $_SESSION['step']==1)
{

	echo "<form method='post' action='/reg.php?$passgen'>\n";
	echo "Ваш ник [A-zА-я0-9 -_]:<br /><input type='text' name='nick' maxlength='32' value='$_SESSION[reg_nick]' /><br />\n";
	echo "<input type='submit' value='Другой' />\n";
	echo "</form><br />\n";echo "<form method='post' action='/reg.php?$passgen'>\n";
	echo "Ваш пол:<br /><select name='pol'><option value='1'>Мужской</option><option value='0'>Женский</option></select><br />\n";
    echo "E-mail:<br /><input type='text' name='ank_mail' /><br />\n";
	echo "Введите пароль (6-32 символов):<br /><input type='password' name='pass1' maxlength='32' /><br />\n";
	echo "Повторите пароль:<br /><input type='password' name='pass2' maxlength='32' /><br />\n";
	echo "<img src='/captcha.php?$passgen&amp;SESS=$sess' width='100' height='30' alt='Проверочное число' /><br />\n<input name='chislo' size='5' maxlength='5' value='' type='text' /><br/>\n";
	echo "Регистрируясь, Вы автоматически соглашаетесь с <a href='/rules.php'>правилами</a> сайта<br />\n";

	echo "<input type='submit' value='Продолжить' />\n";
	echo "</form><br />\n";
}
else{
	echo "<form class='mess' method='post' action='/reg.php?$passgen'>\n";
	echo "Выберите ник [A-zА-я0-9 -_]:<br /><input type='text' name='nick' maxlength='32' /><br />\n";
	echo "Регистрируясь, Вы автоматически соглашаетесь с <a href='/rules.php'>правилами</a> сайта<br />\n";
	echo "<input type='submit' value='Продолжить' />\n";
	echo "</form><br />\n";
}

echo "<div class = 'foot'>Уже зарегистрированы?<br />&raquo;<a href='/aut.php'>Авторизация</a></div>
<div class = 'foot'>Не можете вспомнить пароль?<br />&raquo;<a href='/pass.php'>Восстановить пароль</a></div>\n";
include_once TFOOT;
?>