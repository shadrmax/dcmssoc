<?

include_once '../../sys/inc/start.php';

include_once COMPRESS;

include_once SESS;

include_once '../../sys/inc/home.php';

include_once SETTINGS;

include_once DB_CONNECT;

include_once IPUA;

include_once FNC;

include_once USER;



$set['title']='Информация';



include_once THEAD;

title();

aut(); // форма авторизации



if (isset($user) && $user['level'] > 2){

	if (isset($_GET['del']) && is_numeric($_GET['del']) && dbresult(dbquery("SELECT COUNT(*) FROM `rules` WHERE `id` = '".intval($_GET['del'])."' LIMIT 1",$db), 0)==1)

	{

		dbquery("DELETE FROM `rules` WHERE `id` = '".intval($_GET['del'])."' LIMIT 1");

		dbquery("OPTIMIZE TABLE `rules`");

		$_SESSION['message'] = 'Пункт успешно удален';

		header("Location: ?");

		exit;

	}

}





if (isset($_GET['id']) && isset($_GET['act']) && dbresult(dbquery("SELECT COUNT(*) FROM `rules` WHERE `id` = '".intval($_GET['id'])."'"),0))

{



	$menu=dbassoc(dbquery("SELECT * FROM `rules` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));

	if ($_GET['act']=='up' && $user['level'] > 2)

	{

		dbquery("UPDATE `rules` SET `pos` = '".($menu['pos'])."' WHERE `pos` = '".($menu['pos']-1)."' LIMIT 1");

		dbquery("UPDATE `rules` SET `pos` = '".($menu['pos']-1)."' WHERE `id` = '".intval($_GET['id'])."' LIMIT 1");



		$_SESSION['message'] = 'Пункт меню сдвинут на позицию вверх';

		header("Location: ?");

		exit;

	}

	

	

	if ($_GET['act']=='down' && $user['level'] > 2)

	{

		dbquery("UPDATE `rules` SET `pos` = '".($menu['pos'])."' WHERE `pos` = '".($menu['pos']+1)."' LIMIT 1");

		dbquery("UPDATE `rules` SET `pos` = '".($menu['pos']+1)."' WHERE `id` = '".intval($_GET['id'])."' LIMIT 1");



		$_SESSION['message'] = 'Пункт меню сдвинут на позицию вниз';

		header("Location: ?");

		exit;

	}





}







$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `rules`"),0);



$q = dbquery("SELECT * FROM `rules` ORDER BY `pos` ASC");



echo '<table class="post">';



if ($k_post==0)

{

	echo '<div class="mess">';

	echo 'Раздел информации не заполнен';

	echo '</div>';

}



while ($post = dbassoc($q))

{

	

	/*-----------зебра-----------*/

	if ($num==0)

	{echo '<div class="nav1">';

	$num=1;

	}elseif ($num==1)

	{echo '<div class="nav2">';

	$num=0;}

	/*---------------------------*/

		

		

	if ($post['title'])echo (($user['level'] > 2) ? $post['pos'] . ") " : "") . ' <a href="post.php?id=' . $post['id'] . '">' . output_text($post['title']) . '</a> ';



	if ($post['url'])echo (($user['level'] > 2) ? $post['pos'] . ") " : "") . ' <a href="' . htmlspecialchars($post['url']) . '">' . output_text($post['name_url']) . '</a> ';



	if ($post['msg'])echo (($user['level'] > 2)? $post['pos'] . ") " : "") . output_text($post['msg']) . ' ';





	if ($user['level'] > 2){

	

		echo '<a href="?id=' . $post['id'] . '&amp;act=up&amp;' . $passgen . '"><img src="/style/icons/up.gif" alt="*" /></a> | ';

		echo '<a href="?id=' . $post['id'] . '&amp;act=down&amp;' . $passgen . '"><img src="/style/icons/down.gif" alt="*" /></a> | ';

		echo '<a href="edit.php?id=' . $post['id'] . '&amp;act=edits&amp;' . $passgen . '"><img src="/style/icons/edit.gif" alt="*" /></a> | ';

		echo '<a href="index.php?del=' . $post['id'] . '"><img src="/style/icons/delete.gif" alt="*" /></a>';

	}

	

	echo '</div>';

}

echo '</table>';



if ($user['level'] > 2){

	echo '<div class="foot"><img src="/style/icons/ok.gif" alt="*" /> <a href="new.php?msg">Добавить текст</a></div>';

	echo '<div class="foot"><img src="/style/icons/ok.gif" alt="*" /> <a href="new.php?post">Добавить пункт</a></div>';

	echo '<div class="foot"><img src="/style/icons/ok.gif" alt="*" /> <a href="new.php?url">Добавить ссылку</a></div>';

}





include_once TFOOT;

?>

