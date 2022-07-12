<?php
$set['meta_keywords']=(isset($set['meta_keywords']))?$set['meta_keywords']:null;
$set['meta_description']=(isset($set['meta_description']))?$set['meta_description']:null;

// Ключевые слова
if ($set['meta_keywords']!=NULL)
{ 
	function meta_keywords($str)
	{
		global $set;
		return str_replace('</head>', '<meta name="keywords" content="'.$set['meta_keywords'].'" />'."\n</head>", $str);
	}
	ob_start('meta_keywords');
}

// Описание мета
if ($set['meta_description']!=NULL)
{
	function meta_description($str)
	{
		global $set;
		return str_replace('</head>', '<meta name="description" content="'.$set['meta_description'].'" />'."\n</head>", $str);
	}
	ob_start('meta_description');
}


//set_token ();



if (file_exists(H."style/themes/$set[set_them]/head.php"))
include_once H."style/themes/$set[set_them]/head.php";
else
{
	$set['web'] = false;
	//header("Content-type: application/vnd.wap.xhtml+xml");
	//header("Content-type: application/xhtml+xml");
	header("Content-type: text/html");
	// echo '<?xml version="1.0" encoding="utf-8"?>';
	?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

	<title><?=$set['title']?></title>
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="stylesheet" href="/style/themes/<?=$set['set_them']?>/style.css" type="text/css" />
	<link rel="alternate" title="Новости RSS" href="/news/rss.php" type="application/rss+xml" />
	</head>
	<body>
	<div class="body">
	<?
}

  if ($user['level']>4)
  {
    if (setget('toolbar',1)==1)
    {
      t_toolbar_html();

    }
  }
  if ($user['level']>4)
  {
    if (setget('toolbar',1)==1)
    {
      t_toolbar_css();

    }
  }

  if (empty(setget('job',1)))
  {

      if (isset($user) and $user['level']>=5)
        echo "<div style='color:red' class='err'>ВНИМАНИЕ! Сайт выключен в  <a href='/adm_panel/settings_sys.php?'>админке</a>. Пользователи видят сообщение о том, что ведуться технические работы</div>";

  }





  // Уведомления
if (isset($_SESSION['message']))
{
	echo '<div class="msg">' . $_SESSION['message'] . '</div>';
	$_SESSION['message'] = NULL;
}

// Вывод ошибок
if (isset($_SESSION['err']))
{
	echo '<div class="err">' . $_SESSION['err'] . '</div>';
	$_SESSION['err'] = NULL;
}



header_html();

?>


<link rel="stylesheet" href="/style/system.css" type="text/css" />

    <div id="load"></div>
    <div id="content">