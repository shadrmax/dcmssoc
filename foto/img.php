<?
include_once '../sys/inc/start.php';
//include_once COMPRESS;
include_once SESS;
include_once '../sys/inc/home.php';
include_once SETTINGS;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once '../sys/inc/downloadfile.php';
//include_once USER;//header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime($time))." GMT");
//header("Expires: ".gmdate("D, d M Y H:i:s", time() + 3600)." GMT");

if (!isset($_GET['id']) || !isset($_GET['size']))exit;
$size = intval($_GET['size']);
$if_foto = intval($_GET['id']);
$foto = dbassoc(dbquery("SELECT * FROM `gallery_foto` WHERE `id` = '$if_foto'  LIMIT 1"));
$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '$foto[id_gallery]'  LIMIT 1"));

$ank = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '$gallery[id_user]' LIMIT 1"));

if (isset($_SESSION['id_user']))
$user = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '$_SESSION[id_user]' LIMIT 1"));
else 
$user = array('id' => '0', 'level' => '0', 'group_access' => '0');

if ($ank['id'] != $user['id'] && isset($user['group_access']) && ($user['group_access'] == 0 || $user['group_access'] <= $ank['group_access']) && isset($foto['avatar']) && $foto['avatar'] == 0)
{
	// Настройки юзера
	$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));

	// Статус друг ли вы
	$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE 
	 (`user` = '$user[id]' AND `frend` = '$ank[id]') OR 
	 (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"),0);

	// Проверка завки в друзья
	$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE 
	 (`user` = '$user[id]' AND `to` = '$ank[id]') OR 
	 (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"),0);
	 
	// Начинаем вывод если стр имеет приват настройки
	if ($uSet['privat_str'] == 2 && $frend != 2)
	$if_foto = 0; // Если только для друзей

	// Если только для меня
	if ($uSet['privat_str'] == 0)
	$if_foto = 0;	
	
	/*
	* Если установлена приватность альбома
	*/	
	if ($gallery['privat'] == 1 && ($frend != 2 || !isset($user)) && $user['level'] <= $ank['level'] && $user['id'] != $ank['id'])
	{
		$if_foto = 0;	
	}
	elseif ($gallery['privat'] == 2 && $user['id'] != $ank['id'] && $user['level'] <= $ank['level'])
	{
		$if_foto = 0;	
	}
	
	/*--------------------Альбом под паролем-------------------*/
	if ($user['id'] != $ank['id'] && $gallery['pass'] != NULL)
	{
		if (!isset($_SESSION['pass']) || $_SESSION['pass'] != $gallery['pass'])
		{
			$if_foto = 0;	
		}
	}
	/*---------------------------------------------------------*/
}


if ($size == '48')
{
	if (test_file(H.'sys/gallery/48/'.$if_foto.'.png'))
	{
		DownloadFile(H.'sys/gallery/48/'.$if_foto.'.png', 'Фото.png', ras_to_mime('png'));
		exit;
	}
	
	if (test_file(H.'sys/gallery/48/'.$if_foto.'.gif'))
	{
		DownloadFile(H.'sys/gallery/48/'.$if_foto.'.gif', 'Фото.gif', ras_to_mime('gif'));
		exit;
	}
	
	if (test_file(H.'sys/gallery/48/'.$if_foto.'.jpg'))
	{
		DownloadFile(H.'sys/gallery/48/'.$if_foto.'.jpg', 'Фото.jpg', ras_to_mime('jpg'));
		exit;
	}
}


if ($size == '128')
{
	if (test_file(H.'sys/gallery/128/'.$if_foto.'.jpg'))
	{
		DownloadFile(H.'sys/gallery/128/'.$if_foto.'.jpg', 'Фото.jpg', ras_to_mime('jpg'));
		exit;
	}
}

if ($size == '50')
{
	if (test_file(H.'sys/gallery/50/'.$if_foto.'.jpg'))
	{
		DownloadFile(H.'sys/gallery/50/'.$if_foto.'.jpg', 'Фото.jpg', ras_to_mime('jpg'));
		exit;
	}
}

if ($size == '640')
{
	if (test_file(H.'sys/gallery/640/'.$if_foto.'.jpg'))
	{
		DownloadFile(H.'sys/gallery/640/'.$if_foto.'.jpg', 'Фото.jpg', ras_to_mime('jpg'));
		exit;
	}
}

if ($size == '0')
{
	if (test_file(H.'sys/gallery/foto/'.$if_foto.'.jpg'))
	{
		DownloadFile(H.'sys/gallery/foto/'.$if_foto.'.jpg', 'foto_'.$if_foto.'.jpg', ras_to_mime('jpg'));
		exit;
	}
}
?>