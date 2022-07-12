<?php
/*
=======================================
Личные файлы юзеров для Dcms-Social
Автор: Искатель
---------------------------------------
Этот скрипт распостроняется по лицензии
движка Dcms-Social. 
При использовании указывать ссылку на
оф. сайт http://dcms-social.ru
---------------------------------------
Контакты
ICQ: 587863132
http://dcms-social.ru
=======================================
*/
include_once '../../sys/inc/start.php';
include_once COMPRESS;
include_once SESS;
include_once '../../sys/inc/home.php';
include_once SETTINGS;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once USER;
include_once '../../sys/inc/files.php';



/* Бан пользователя */ 
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'files' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
	header('Location: /ban.php?'.SID);
	exit;
}

include_once THEAD;

if (isset($user))$ank['id']=$user['id'];
if (isset($_GET['id']))$ank['id']=intval($_GET['id']);

if ($ank['id']==0)
{
echo "Ошибка! Это папка системы!";
exit;
}
 // Определяем id автора папки
$ank = get_user($ank['id']);
if(!$ank){header("Location: /index.php?".SID);exit;}

 // Если у юзера нет основной папки создаем
if (dbresult(dbquery("SELECT COUNT(*) FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` = '1'"), 0) == 0)
{


	$t = dbquery("INSERT INTO `user_files` (`id_user`, `name`,  `osn`) values('$ank[id]', 'Файлы', '1')");

	$dir = dbassoc(dbquery("SELECT * FROM `user_files`  WHERE `id_user` = '$ank[id]' AND `osn` = '1'"));
	header("Location: /user/personalfiles/$ank[id]/$dir[id]/".SID);
} 

 // Основная папка
$dir_osn = dbassoc(dbquery("SELECT * FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` = '1' LIMIT 1"));

 // Текущая папка
$dir = dbassoc(dbquery("SELECT * FROM `user_files` WHERE `id_user` = '$ank[id]' AND `id` = '".intval($_GET['dir'])."' LIMIT 1"));


 // Блокируем в случае отсутствия папки
if ($dir['id_user']!=$ank['id'])
{
echo "Ошибка! Возможно папка была удалена, проверьте правильность адреса!";
exit;
}

if (isset($_GET['id']) && isset($_GET['dir'])  && !isset($_GET['add']) && !isset($_GET['upload']) && !isset($_GET['id_file']))
{
 // Вывод папок
include_once 'inc/folder.php';
}
else if (isset($_GET['id']) && isset($_GET['dir']) && isset($_GET['add']) && !isset($_GET['upload']) && !isset($_GET['id_file']))
{
 // Создание и редактирование папок
include_once 'inc/folder.create.php';
}
else if (isset($_GET['id']) && isset($_GET['dir']) && isset($_GET['upload']) && !isset($_GET['id_file']) && !isset($_GET['add']))
{ 
 	// Загрузка файла
	
	include_once 'inc/upload.wap.php';
	
}
else if (isset($_GET['id']) && isset($_GET['dir']) && isset($_GET['id_file']) && !isset($_GET['upload']) && !isset($_GET['add']))
{
 // Вывод файла пользователю
include_once 'inc/file.php';
}
 // (c) Искатель
include_once TFOOT;

?>