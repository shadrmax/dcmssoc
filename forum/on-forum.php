<?
include_once '../sys/inc/start.php';
include_once COMPRESS;
include_once SESS;
include_once '../sys/inc/home.php';
include_once SETTINGS;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once USER;/* Бан пользователя */
 if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'forum' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0){header('Location: /ban.php?'.SID);exit;}
 
 
$set['title']='Кто на форуме?'; // заголовок страницы
include_once THEAD;
title();
aut();

$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > '".(time()-600)."' AND `url` like '/forum/%'"), 0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
$q = dbquery("SELECT * FROM `user` WHERE `date_last` > '".(time()-600)."' AND `url` like '/forum/%' ORDER BY `date_last` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>\n";
if ($k_post==0)
{
echo "   <tr>\n";
echo "  <td class='p_t'>\n";
echo "Нет никого\n";
echo "  </td>\n";
echo "   </tr>\n";
}


while ($forum = dbarray($q))
{
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;

	echo avatar($forum['id']) . group($forum['id']);

	echo " <a href='/info.php?id=$forum[id]'>$forum[nick]</a>\n";
	echo " ".medal($forum['id'])."  ".online($forum['id'])."</td>\n";
	echo "</div>\n"; 
}

echo "</table>\n";


if ($k_page>1)str("?",$k_page,$page); // Вывод страниц

echo "<div class='foot'>
	  &laquo;<a href='/forum/'>Назад в форум</a><br />
	  </div>\n";


include_once TFOOT;
?>