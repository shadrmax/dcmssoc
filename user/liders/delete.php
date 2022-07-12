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







if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `id_user` = '".intval($_GET['id'])."'"),0)==1)



{



	if (isset($user) && $user['level'] > 2)



	{



		dbquery("DELETE FROM `liders` WHERE `id_user` = '" . intval($_GET['id']) . "'");



		$_SESSION['message'] = '������������ ������ �� ������ �������';



	}







}







if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL)



header("Location: " . $_SERVER['HTTP_REFERER']);



else



header("Location: index.php?".SID);



exit;







?>