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











if (isset($user))$ank['id'] = $user['id'];



if (isset($_GET['id']))$ank['id'] = intval($_GET['id']);



$ank = get_user($ank['id']);







if ($ank['id'] == 0)



{



	header("Location: /index.php?" . SID);exit;



	exit;



}







if (isset($user) && isset($_GET['delete']) && $user['id'] == $ank['id'])



{



dbquery("DELETE FROM `bookmarks` WHERE `id` = '" . intval($_GET['delete']) . "' AND `id_user` = '$user[id]' AND `type`='notes' LIMIT 1");



	



	$_SESSION['message'] = 'Закладка удалена';



	header("Location: ?page=" . intval($_GET['page']) . "" . SID);exit;



	exit;



}







if( !$ank ){ header("Location: /index.php?".SID); exit; }



$set['title']='Закладки - Дневники - '. $ank['nick'] .''; // заголовок страницы







include_once THEAD;



title();











err();



aut(); // форма авторизации











echo '<div class="foot">';



echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/user/bookmark/index.php?id=' . $ank['id'] . '">Закладки</a> | <b>Дневники</b>';



echo '</div>';















$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '$ank[id]' AND `type`='notes' "),0);



$k_page=k_page($k_post,$set['p_str']);



$page=page($k_page);



$start=$set['p_str']*$page-$set['p_str'];



echo '<table class="post">';







if ($k_post == 0)



{



	echo '<div class="mess">';



	echo 'Нет дневников в закладках';



	echo '</div>';



}







$q=dbquery("SELECT * FROM `bookmarks`  WHERE `id_user` = '$ank[id]' AND `type`='notes' ORDER BY id DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q))
{
$f = $post['id_object'];
$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id`='" . $f . "'  LIMIT 1"));
$ank_p = dbassoc(dbquery("SELECT nick,id FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
if ($num==0){



	echo '<div class="nav1">';



	$num=1;



}



elseif ($num==1){



	echo '<div class="nav2">';



	$num=0;



}



/*---------------------------*/







echo '<img src="/style/icons/dnev.png" alt="S" /> <a href="/plugins/notes/list.php?id=' . $notes['id'] . '">' . htmlspecialchars($notes['name']) . '</a> ' . vremja($post['time']) . '<br />';











echo group($ank_p['id']) , '<a href="/info.php?id=' . $ank_p['id'] . '">' . $ank_p['nick'] . '</a> ';







echo medal($ank_p['id']) , online($ank_p['id']);







if ($ank['id'] == $user['id'])



echo '<div style="text-align:right;"><a href="?delete=' . $post['id'] . '&amp;page=' . $page . '"><img src="/style/icons/delete.gif" alt="*" /></a></div>';







echo '</div>';



}



echo "</table>\n";



















if ($k_page>1)str('?',$k_page,$page); // Вывод страниц







echo '<div class="foot">';



echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/user/bookmark/index.php?id=' . $ank['id'] . '">Закладки</a> | <b>Дневники</b>';



echo '</div>';







include_once TFOOT;



?>



