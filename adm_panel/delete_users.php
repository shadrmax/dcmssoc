<?



include_once '../sys/inc/start.php';



include_once COMPRESS;



include_once SESS;



include_once '../sys/inc/home.php';



include_once SETTINGS;



include_once DB_CONNECT;



include_once IPUA;



include_once FNC;



include_once ADM_CHECK;



include_once USER;


user_access('user_mass_delete',null,'index.php?'.SID);



adm_check();



$set['title']='Удаление пользователей';



include_once THEAD;



title();



if (isset($_POST['write']) && isset($_POST['write2']))



{



$timeclear1=0;



if ($_POST['write2']=='sut')$timeclear1=$time-intval($_POST['write'])*60*60*24;



elseif ($_POST['write2']=='mes')$timeclear1=$time-intval($_POST['write'])*60*60*24*30;



else $err[]='Не выбран период';



$q = dbquery("SELECT * FROM `user` WHERE `date_last` < '$timeclear1'",$db);



$del_th=0;



while ($post = dbassoc($q))



{







$ank['id']=&$post['id'];







dbquery("DELETE FROM `user` WHERE `id` = '$ank[id]' LIMIT 1");



dbquery("DELETE FROM `chat_post` WHERE `id_user` = '$ank[id]'");



	dbquery("DELETE FROM `frends` WHERE `user` = '$ank[id]' OR `frend` = '$ank[id]'");		dbquery("DELETE FROM `frends_new` WHERE `user` = '$ank[id]' OR `to` = '$ank[id]'");		dbquery("DELETE FROM `blog_list` WHERE `id_user` = '$ank[id]'");		dbquery("DELETE FROM `blog_komm` WHERE `id_user` = '$ank[id]'");		dbquery("DELETE FROM `stena` WHERE `id_user` = '$ank[id]'");		dbquery("DELETE FROM `stena_like` WHERE `id_user` = '$ank[id]'");		dbquery("DELETE FROM `status_like` WHERE `id_user` = '$ank[id]'");		dbquery("DELETE FROM `status` WHERE `id_user` = '$ank[id]'");	



dbquery("DELETE FROM `gifts_user` WHERE `id_user` = '$ank[id]' OR `id_ank` = '$ank[id]'");



$q5=dbquery("SELECT * FROM `forum_t` WHERE `id_user` = '$ank[id]'");



while ($post5 = dbassoc($q5))



{



dbquery("DELETE FROM `forum_p` WHERE `id_them` = '$post5[id]'");



}



dbquery("DELETE FROM `forum_t` WHERE `id_user` = '$ank[id]'");











dbquery("DELETE FROM `forum_p` WHERE `id_user` = '$ank[id]'");



dbquery("DELETE FROM `forum_zakl` WHERE `id_user` = '$ank[id]'");



dbquery("DELETE FROM `guest` WHERE `id_user` = '$ank[id]'");



dbquery("DELETE FROM `loads_komm` WHERE `id_user` = '$ank[id]'");



dbquery("DELETE FROM `news_komm` WHERE `id_user` = '$ank[id]'");



dbquery("DELETE FROM `user_files` WHERE `id_user` = '$ank[id]'");dbquery("DELETE FROM `user_music` WHERE `id_user` = '$ank[id]'");dbquery("DELETE FROM `like_object` WHERE `id_user` = '$ank[id]'");







$opdirbase=@opendir(H.'sys/add/delete_user_act');



while ($filebase=@readdir($opdirbase))



if (preg_match('#\.php$#',$filebase))



include_once(H.'sys/add/delete_user_act/'.$filebase);







$q5=dbquery("SELECT * FROM `obmennik_files` WHERE `id_user` = '$ank[id]'");



while ($post5 = dbassoc($q5))



{



unlink(H.'sys/obmen/files/'.$post5['id'].'.dat');



}











dbquery("DELETE FROM `obmennik_files` WHERE `id_user` = '$ank[id]'");







dbquery("DELETE FROM `users_konts` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'");



dbquery("DELETE FROM `mail` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'");



dbquery("DELETE FROM `user_voice` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'");



dbquery("DELETE FROM `user_collision` WHERE `id_user` = '$ank[id]' OR `id_user2` = '$ank[id]'");



dbquery("DELETE FROM `votes_user` WHERE `u_id` = '$ank[id]'");























$del_th++;



}



dbquery("OPTIMIZE TABLE `user`",$db);



msg ("Удалено $del_th пользователей");



}



err();



aut();



















echo "<form method=\"post\" class='foot' action=\"?\">\n";



echo "Будут удалены пользователи, не посещавшие сайт<br />\n";



echo "<input name=\"write\" value=\"6\" type=\"text\" size='3' />\n";



echo "<select name=\"write2\">\n";



echo "<option value=\"\">       </option>\n";



echo "<option value=\"mes\">Месяцев</option>\n";



echo "<option value=\"sut\">Суток</option>\n";



echo "</select><br />\n";



echo "<input value=\"Удалить\" type=\"submit\" /><br />\n";



echo "<a href=\"?\">Отмена</a><br />\n";



echo "</form>\n";



if (user_access('adm_panel_show')){



echo "<div class='foot'>\n";



echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";



echo "</div>\n";



}



include_once TFOOT;



?>