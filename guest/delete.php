<?
include_once '../sys/inc/start.php';
include_once COMPRESS;
include_once SESS;
include_once '../sys/inc/home.php';
include_once SETTINGS;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once USER;
if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `guest` WHERE `id` = '".intval($_GET['id'])."'"),0) == 1)
{
	$post = dbassoc(dbquery("SELECT * FROM `guest` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));

	if ($post['id_user'] == 0)
	{
		$ank['id'] = 0;
		$ank['pol'] = 'guest';
		$ank['level'] = 0;
		$ank['nick'] = 'Гость';
	}
	else
	$ank = get_user($post['id_user']);
	
	if (user_access('guest_delete'))
	{
		admin_log('Гостевая', 'Удаление сообщения', 'Удаление сообщения от ' . $ank['nick']);
		dbquery("DELETE FROM `guest` WHERE `id` = '$post[id]'");
	}
}

if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL)
header('Location: ' . my_esc($_SERVER['HTTP_REFERER']));
else
header('Location: index.php?' . SID);
?>