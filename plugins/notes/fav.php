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

/* Бан пользователя */ 
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0)!=0)
{
	header('Location: /ban.php?'.SID);
	exit;
}
$set['title']='Добавили в закладки';
include_once THEAD;
title();
aut();
if(dbresult(dbquery("SELECT COUNT(*)FROM `notes` WHERE `id`='".intval($_GET['id'])."' LIMIT 1"),0)==0){
echo "<div class='err'>Легче, легче! Такого дневника нет.</div>";
include_once TFOOT;
exit;
}else{
$k_post=dbresult(dbquery("SELECT COUNT(*)FROM `bookmarks` WHERE `id_object`='".intval($_GET['id'])."' AND `type`='notes' "),0);
$k_page=k_page($k_post,$set['p_str']);


	$page=page($k_page);


	$start=$set['p_str']*$page-$set['p_str'];
if($k_post==0){
echo "<div class='mess'>Никто в закладки не добавлял</div>";
}else{
$q=dbquery("SELECT*FROM `bookmarks` WHERE `id_object`='".intval($_GET['id'])."' AND `type`='notes' LIMIT $start,$set[p_str]");
while($post=dbassoc($q)){
echo "<div class='nav2'>";
echo group($post['id_user'])." ";
echo user::nick($post['id_user'],1,1,1)." ";
echo "Добавлено ".vremja($post['time'])."</div>";
}
if ($k_page > 1)str("?id=".intval($_GET['id'])."&amp;",$k_page,$page); // Вывод страниц
}
include_once TFOOT;

}