<?



/*



=======================================



Подарки для Dcms-Social



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







only_reg();











$width = ($webbrowser == 'web' ? '100' : '70'); // Размер подарков при выводе в браузер















	if (isset($_GET['id']))$ank['id'] = intval($_GET['id']);



	else $ank['id'] = $user['id']; // Определяем юзера



	



	$ank = get_user($ank['id']);



	if(!$ank || $ank['id'] == 0){ header("Location: /index.php?".SID); exit; }



	$set['title'] = 'Подарки ' . $ank['nick'];



	











	include_once THEAD;



	title();



	aut();



	







/*



==================================



Вывод подарков пользователя



==================================



*/











	echo '<div class="foot">';



	echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/info.php?id=' . $ank['id'] . '">' . $ank['nick'] . '</a> | <b>Подарки</b>';



	echo '</div>';



	



	// Список подарков







	



	$k_post = dbresult(dbquery("SELECT COUNT(id) FROM `gifts_user` WHERE `id_user` = '$ank[id]'" . ($ank['id'] != $user['id'] ? " AND `status` = '1' " : "") . ""),0);







	if ($k_post == 0)



	{



		echo '<div class="mess">';



		echo 'Нет подарков';



		echo '</div>';



	}



	



	$k_page=k_page($k_post,$set['p_str']);



	$page=page($k_page);



	$start=$set['p_str']*$page-$set['p_str'];



	$q = dbquery("SELECT id,status,coment,id_gift,id_ank,time FROM `gifts_user` WHERE `id_user` = '$ank[id]'" . ($ank['id'] != $user['id'] ? " AND `status` = '1' " : "") . " ORDER BY `time` DESC LIMIT $start, $set[p_str]");



	



	while ($post = dbassoc($q))



	{



		$gift = dbassoc(dbquery("SELECT id,name FROM `gift_list` WHERE `id` = '$post[id_gift]' LIMIT 1"));



		$anketa = get_user($post['id_ank']);



	



	/*-----------зебра-----------*/ 



		if ($num==0){



			echo '<div class="nav1">';



			$num=1;



		}	



		elseif ($num==1){



			echo '<div class="nav2">';



			$num=0;



		}



	/*---------------------------*/



	



		echo '<img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="*" /><br />';



		echo '<img src="/style/icons/present.gif" alt="*" /> <a href="gift.php?id=' . $post['id'] . '"><b>' . htmlspecialchars($gift['name']) . '</b></a> :: ';



		echo 'от ' . group($anketa['id']) , '<a href="/info.php?id=' . $anketa['id'] . '">' . $anketa['nick'] . '</a>' ,  medal($anketa['id']) ,  online($anketa['id']) , ' ' . vremja($post['time']);



		if ($post['status'] == 0) echo ' <font color=red>NEW</font> ';



		echo '</div>';



	}







	if ($k_page>1)str('index.php?id=' . intval($_GET['id']) . '&amp;',$k_page,$page); // Вывод страниц



















	echo '<div class="foot">';



	echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/info.php?id=' . $ank['id'] . '">' . $ank['nick'] . '</a> | <b>Подарки</b>';



	echo '</div>';



















include_once TFOOT;



?>