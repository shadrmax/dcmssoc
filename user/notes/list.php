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



header('Location: /ban.php?'.SID);exit;



}







$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '". intval($_GET['id']) ."' LIMIT 1"));



$avtor=get_user($notes['id_user']);



if (isset($user))



$count=dbresult(dbquery("SELECT COUNT(*) FROM `notes_count` WHERE `id_user` = '".$user['id']."' AND `id_notes` = '".$notes['id']."' LIMIT 1"),0);



 // Закладки



$markinfo=dbresult(dbquery("SELECT COUNT(*) FROM `mark_notes` WHERE `id_list` = '".$notes['id']."'"),0);







if (isset($user))



dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'notes_komm' AND `id_user` = '$user[id]' AND `id_object` = '$notes[id]'");











/*



================================



Модуль жалобы на пользователя



и его сообщение либо контент



в зависимости от раздела



================================



*/



if (isset($_GET['spam'])  &&  isset($user))



{



$mess = dbassoc(dbquery("SELECT * FROM `notes_komm` WHERE `id` = '".intval($_GET['spam'])."' limit 1"));



$spamer = get_user($mess['id_user']);



if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'notes_komm' AND `spam` = '".$mess['msg']."'"),0)==0)



{



if (isset($_POST['msg']))



{



if ($mess['id_user']!=$user['id'])



{



$msg=mysql_real_escape_string($_POST['msg']);







if (strlen2($msg)<3)$err='Укажите подробнее причину жалобы';



if (strlen2($msg)>1512)$err='Длина текста превышает предел в 512 символов';







if(isset($_POST['types'])) $types=intval($_POST['types']);



else $types='0'; 



if (!isset($err))



{



dbquery("INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$notes[id]', '$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'notes_komm', '".my_esc($mess['msg'])."')");



$_SESSION['message'] = 'Заявка на рассмотрение отправлена'; 



header("Location: ?id=$notes[id]&page=".intval($_GET['page'])."&spam=$mess[id]");



exit;



}



}



}



}



$set['title']='Дневник ' . htmlspecialchars($notes['name']) . '';







include_once THEAD;



title();



aut();



err();







if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'notes_komm'"),0)==0)



{



echo "<div class='mess'>Ложная информация может привести к блокировке ника. 



Если вас постоянно достает один человек - пишет всякие гадости, вы можете добавить его в черный список.</div>";



echo "<form class='nav1' method='post' action='?id=$notes[id]&amp;page=".intval($_GET['page'])."&amp;spam=$mess[id]'>\n";



echo "<b>Пользователь:</b> ";



echo " ".status($spamer['id'])."  ".group($spamer['id'])." <a href=\"/info.php?id=$spamer[id]\">$spamer[nick]</a>\n";



echo "".medal($spamer['id'])." ".online($spamer['id'])." (".vremja($mess['time']).")<br />";



echo "<b>Нарушение:</b> <font color='green'>".output_text($mess['msg'])."</font><br />";



echo "Причина:<br />\n<select name='types'>\n";



echo "<option value='1' selected='selected'>Спам/Реклама</option>\n";



echo "<option value='2' selected='selected'>Мошенничество</option>\n";



echo "<option value='3' selected='selected'>Оскорбление</option>\n";



echo "<option value='0' selected='selected'>Другое</option>\n";



echo "</select><br />\n";



echo "Комментарий:$tPanel";



echo "<textarea name=\"msg\"></textarea><br />";



echo "<input value=\"Отправить\" type=\"submit\" />\n";



echo "</form>\n";



}else{



echo "<div class='mess'>Жалоба на <font color='green'>$spamer[nick]</font> будет рассмотрена в ближайшее время.</div>";



}







echo "<div class='foot'>\n";



echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?id=$notes[id]&amp;page=".intval($_GET['page'])."'>Назад</a><br />\n";



echo "</div>\n";



include_once TFOOT;



exit;



}



/*



==================================



The End



==================================



*/











 // Запись просмотра



if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `notes_count` WHERE `id_user` = '".$user['id']."' AND `id_notes` = '".$notes['id']."' LIMIT 1"),0)==0){



dbquery("INSERT INTO `notes_count` (`id_notes`, `id_user`) VALUES ('$notes[id]', '$user[id]')");



dbquery("UPDATE `notes` SET `count` = '".($notes['count']+1)."' WHERE `id` = '$notes[id]' LIMIT 1");



}







/*------------очищаем счетчик этого обсуждения-------------*/



if (isset($user))



{



dbquery("UPDATE `discussions` SET `count` = '0' WHERE `id_user` = '$user[id]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1");



}



/*---------------------------------------------------------*/







$set['title']='Дневник - ' . htmlspecialchars($notes['name']) . '';



$set['meta_description'] = htmlspecialchars($notes['msg']);







include_once THEAD;



if (isset($_POST['msg']) && isset($user))



{



$msg=$_POST['msg'];







if (strlen2($msg)>1024){$err='Сообщение слишком длинное';}







elseif (strlen2($msg)<2){$err='Короткое сообщение';}







elseif (dbresult(dbquery("SELECT COUNT(*) FROM `notes_komm` WHERE `id_notes` = '".intval($_GET['id'])."' AND `id_user` = '$user[id]' AND `msg` = '".my_esc($msg)."' LIMIT 1"),0)!=0){$err='Ваше сообщение повторяет предыдущее';}







elseif(!isset($err)){







		/*



		==========================



		Уведомления об ответах



		==========================



		*/



		if (isset($user) && $respons==TRUE){



		$notifiacation=dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '".$ank_otv['id']."' LIMIT 1"));



			



			if ($notifiacation['komm'] == 1 && $ank_otv['id'] != $user['id'])



			dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', '$notes[id]', 'notes_komm', '$time')");



		



		}



















/*



====================================



Обсуждения



====================================



*/



$q = dbquery("SELECT * FROM `frends` WHERE `user` = '".$notes['id_user']."' AND `i` = '1'");



while ($f = dbarray($q))



{



$a=get_user($f['frend']);



$discSet = dbarray(dbquery("SELECT * FROM `discussions_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); // Общая настройка обсуждений







if ($f['disc_notes']==1 && $discSet['disc_notes']==1) /* Фильтр рассылки */



{



//---------друзьям автора--------------//



if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1"),0)==0)



{



if ($notes['id_user'] != $a['id']  || $a['id'] != $user['id'])



dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$a[id]', '$notes[id_user]', 'notes', '$time', '$notes[id]', '1')"); 



}



else



{



$disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1"));



if ($notes['id_user'] != $a['id'] || $a['id'] != $user['id'])



dbquery("UPDATE `discussions` SET `count` = '".($disc['count']+1)."', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1");



}



//-------------------------------------//



}



}







//-------------отправляем автору------------//



if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$notes[id_user]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1"),0)==0)



{



if ($notes['id_user'] != $user['id'])



dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$notes[id_user]', '$notes[id_user]', 'notes', '$time', '$notes[id]', '1')"); 



}



else



{



$disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$notes[id_user]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1"));



if ($notes['id_user'] != $user['id'])



dbquery("UPDATE `discussions` SET `count` = '".($disc['count']+1)."', `time` = '$time' WHERE `id_user` = '$notes[id_user]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1");



}















dbquery("INSERT INTO `notes_komm` (`id_user`, `time`, `msg`, `id_notes`) values('$user[id]', '$time', '".my_esc($msg)."', '".intval($_GET['id'])."')");



dbquery("UPDATE `user` SET `balls` = '".($user['balls']+1)."' WHERE `id` = '$user[id]' LIMIT 1");



$_SESSION['message'] = 'Сообщение успешно отправлено';



header("Location: list.php?id=$notes[id]&page=".intval($_GET['page'])."");



exit;



}



}







if (isset($user))



$frend=dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$avtor[id]') OR (`user` = '$avtor[id]' AND `frend` = '$user[id]') LIMIT 1"),0);











title();



aut(); // форма авторизации



err();



if ($notes['private']==1 && $user['id']!=$avtor['id'] && $frend!=2  && !user_access('notes_delete')){



msg('Дневник доступен только для друзей');



echo "  <div class='foot'>\n";



echo "<a href='index.php'>Назад</a><br />\n";



echo "   </div>\n";



include_once TFOOT;



exit;



}



if ($notes['private']==2 && $user['id']!=$avtor['id']  && !user_access('notes_delete')){



msg('Пользователь запретил просмотр дневника');



echo "  <div class='foot'>\n";



echo "<a href='index.php'>Назад</a><br />\n";



echo "   </div>\n";



include_once TFOOT;



exit;



}















if (isset($_GET['delete']) && ($user['id']==$avtor['id'] || user_access('notes_delete'))){



echo "<center>";



echo "Вы действительно хотите удалить дневник " . output_text($notes['name']) . "?<br />";



echo "[<a href='delete.php?id=$notes[id]'><img src='/style/icons/ok.gif'> удалить</a>] [<a href='list.php?id=$notes[id]'><img src='/style/icons/delete.gif'> отмена</a>] \n";



echo "</center>";



include_once TFOOT;



}















if (isset($user))



{



//------------------------like-----------------//



if (isset($_GET['like']) && $_GET['like'] == 1){



if (dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `id_user` = '".$user['id']."' AND `id_notes` = '".$notes['id']."' LIMIT 1"),0)==0){



dbquery("INSERT INTO `notes_like` (`id_notes`, `id_user`, `like`) VALUES ('$notes[id]', '$user[id]', '1')");



dbquery("UPDATE `notes` SET `count` = '".($notes['count']+1)."' WHERE `id` = '$notes[id]' LIMIT 1");



$_SESSION['message'] = 'Ваш голос засчитан';



header("Location: list.php?id=$notes[id]&page=".intval($_GET['page'])."");



exit;



}



}



//-----------------------------------------------//











//------------------------dlike-----------------//



if (isset($_GET['like']) && $_GET['like'] == 0){



if (dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `id_user` = '".$user['id']."' AND `id_notes` = '".$notes['id']."' LIMIT 1"),0)==0){



dbquery("INSERT INTO `notes_like` (`id_notes`, `id_user`, `like`) VALUES ('$notes[id]', '$user[id]', '0')");



dbquery("UPDATE `notes` SET `count` = '".($notes['count']-1)."' WHERE `id` = '$notes[id]' LIMIT 1");



$_SESSION['message'] = 'Ваш голос засчитан';



header("Location: list.php?id=$notes[id]&page=".intval($_GET['page'])."");



exit;



}



}



//-----------------------------------------------//















//-----------------------добавляем в закладки------------//



if (isset($_GET['fav']) && $_GET['fav']==1){



if (dbresult(dbquery("SELECT COUNT(*) FROM `mark_notes` WHERE `id_user` = '".$user['id']."' AND `id_list` = '".$notes['id']."' LIMIT 1"),0)==0){



dbquery("INSERT INTO `mark_notes` (`id_list`, `id_user`, `time`) VALUES ('$notes[id]', '$user[id]', '$time')");



$_SESSION['message'] = 'Дневник добавлен в закладки';



header("Location: list.php?id=$notes[id]&page=".intval($_GET['page'])."");



exit;



}



}



//-------------------------------------------------------//







//-----------------------удаляем из закладок------------//



if (isset($_GET['fav']) && $_GET['fav']==0){



if (dbresult(dbquery("SELECT COUNT(*) FROM `mark_notes` WHERE `id_user` = '".$user['id']."' AND `id_list` = '".$notes['id']."' LIMIT 1"),0)==1){



dbquery("DELETE FROM `mark_notes` WHERE `id_user` = '$user[id]' AND  `id_list` = '$notes[id]' ");



$_SESSION['message'] = 'Дневник удален из закладок';



header("Location: list.php?id=$notes[id]&page=".intval($_GET['page'])."");



exit;



}



}



//-------------------------------------------------------//



}







echo "<div class=\"foot\">\n";



echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>Дневники</a> | <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>\n";



echo ' | <b>' . output_text($notes['name']) . '</b>';



echo "</div>\n";







echo "<div class=\"main\">\n";



echo "Cоздан: (".vremja($notes['time']).")\n";



echo "</div>\n";







$stat1 = $notes['msg'];







if (!$set['web'])$mn=10;else $mn=30; // количество слов выводится в зависимости от браузера







$stat=explode(' ', $stat1); // деление статьи на отдельные слова







$k_page=k_page(count($stat),$set['p_str']*$mn);



$page=page($k_page);



$start=$set['p_str']*$mn*($page-1);



$stat_1=NULL;







for ($i=$start;$i<$set['p_str']*$mn*$page && $i<count($stat);$i++){







$stat_1.=$stat[$i].' ';







}







echo output_text($stat_1) , '<br />'; // вывод статьи со всем форматированием







if ($k_page>1)str("?id=$notes[id]&amp;",$k_page,$page); // Вывод страниц











/*----------------------листинг-------------------*/



$listr = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` < '$notes[id]' ORDER BY `id` DESC LIMIT 1"));



$list = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` > '$notes[id]' ORDER BY `id`  ASC LIMIT 1"));



echo '<div class="c2" style="text-align: center;">';



echo '<span class="page">'.($list['id']?'<a href="list.php?id='.$list['id'].'">&laquo; Пред.</a> ':'&laquo; Пред. ').'</span>';







$k_1=dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id` > '$notes[id]'"),0)+1;



$k_2=dbresult(dbquery("SELECT COUNT(*) FROM `notes`"),0);



echo ' ('.$k_1.' из '.$k_2.') ';







echo '<span class="page">' . ($listr['id'] ? '<a href="list.php?id=' . $listr['id'] . '">След. &raquo;</a>' : ' След. &raquo;') . '</span>';



echo '</div>';



/*----------------------alex-borisi---------------*/















if (isset($user) && (user_access('notes_delete') || $user['id']==$avtor['id'])){



echo "<div class='main2'>";



echo "Инструменты: ";



echo " [<a href='edit.php?id=$notes[id]'><img src='/style/icons/edit.gif'> ред</a>] [<a href='?id=$notes[id]&amp;delete'><img src='/style/icons/delete.gif'> удл</a>]\n";



echo "</div>";



}















echo "<div class='main'>";



echo "<b>Рейтинг: $notes[count]</b><br />";



echo "</div>";











if (isset($user) && $user['id']!=$avtor['id']){



echo "<div class='main'>";



if (dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `id_user` = '".$user['id']."' AND `id_notes` = '".$notes['id']."' LIMIT 1"),0)==0)



echo "[<img src='/style/icons/like.gif' alt='*' /> <a href='list.php?id=$notes[id]&amp;like=1'>Нравится</a>] [<a href='list.php?id=$notes[id]&amp;like=0'><img src='/style/icons/dlike.gif' alt='*' /></a>]<br />\n";



else



echo "[<img src='/style/icons/like.gif' alt='*' /> ".dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '1' AND `id_notes` = '".$notes['id']."' LIMIT 1"),0)."] [<img src='/style/icons/dlike.gif' alt='*' /> ".dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '0' AND `id_notes` = '".$notes['id']."' LIMIT 1"),0)."]\n";



echo "</div>";



}else{



echo "<div class='main'>";



echo "[<img src='/style/icons/like.gif' alt='*' /> ".dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '1' AND `id_notes` = '".$notes['id']."' LIMIT 1"),0)."] [<img src='/style/icons/dlike.gif' alt='*' /> ".dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '0' AND `id_notes` = '".$notes['id']."' LIMIT 1"),0)."]\n";



echo "</div>";



}











//--------------------------В закладки-----------------------------//



if (isset($user)){



echo "<div class='main_seriy'>";



echo "<div class='main'>";



echo "<img src='/style/icons/fav.gif' alt='*' /> ";



if (dbresult(dbquery("SELECT COUNT(*) FROM `mark_notes` WHERE `id_user` = '".$user['id']."' AND `id_list` = '".$notes['id']."' LIMIT 1"),0)==0)



echo "<a href='list.php?id=$notes[id]&amp;fav=1'>Добавить в закладки</a><br />\n";



else



echo "<a href='list.php?id=$notes[id]&amp;fav=0'>Удалить из закладок</a><br />\n";



echo "В закладках у <a href='list.php?id=$notes[id]&amp;markinfo'>$markinfo</a> чел.";



echo "</div>";



echo "</div>";



}



//-------------------------------------------------------------//







echo "<div class='main'>";



echo 'Поделится:<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>



<span class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="vkontakte,twitter,odnoklassniki,moimir"></span>';



echo "</div>";











/*



===================================



Комментарии дневников



===================================



*/



$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `notes_komm` WHERE `id_notes` = '".intval($_GET['id'])."'"),0);



$k_page=k_page($k_post,$set['p_str']);



$page=page($k_page);



$start=$set['p_str']*$page-$set['p_str'];







echo '<div class="foot">';



echo "Комментарии\n";



echo '</div>';







if ($k_post==0)



{



echo '<div class="mess">';



echo "Нет сообщений\n";



echo '</div>';



}else if (isset($user)){



/*------------сортировка по времени--------------*/



if (isset($user)){



echo "<div id='comments' class='menus'>";



echo "<div class='webmenu'>";



echo "<a href='list.php?id=$notes[id]&amp;page=$page&amp;sort=1' class='".($user['sort']==1?'activ':'')."'>Внизу</a>";



echo "</div>"; 



echo "<div class='webmenu'>";



echo "<a href='list.php?id=$notes[id]&amp;page=$page&amp;sort=0' class='".($user['sort']==0?'activ':'')."'>Вверху</a>";



echo "</div>"; 



echo "</div>";



}



/*---------------alex-borisi---------------------*/



}











$q=dbquery("SELECT * FROM `notes_komm` WHERE `id_notes` = '".intval($_GET['id'])."' ORDER BY `time` $sort LIMIT $start, $set[p_str]");



echo "<table class='post'>\n";















while ($post = dbassoc($q))



{



$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));







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











echo group($ank['id'])." <a href='/info.php?id=$ank[id]'>$ank[nick]</a> ";



if (isset($user) && $ank['id'] != $user['id'])echo "<a href='?id=$notes[id]&amp;response=$ank[id]'>[*]</a> \n";







echo "".medal($ank['id'])." ".online($ank['id'])." (".vremja($post['time']).")<br />";











$postBan = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE (`razdel` = 'all' OR `razdel` = 'notes') AND `post` = '1' AND `id_user` = '$ank[id]' AND (`time` > '$time' OR `navsegda` = '1')"), 0);



if ($postBan == 0) // Блок сообщения



{	



echo output_text($post['msg'])."<br />\n";



}else{



	echo output_text($banMess).'<br />';



}







if (isset($user))



{



echo '<div style="text-align:right;">';







if ($ank['id']!=$user['id'])



echo "<a href=\"?id=$notes[id]&amp;page=$page&amp;spam=$post[id]\"><img src='/style/icons/blicon.gif' alt='*' title='Это спам'></a> "; 







	if (isset($user) && (user_access('notes_delete') || $user['id']==$notes['id_user']))



		echo '<a href="delete.php?komm='.$post['id'].'"><img src="/style/icons/delete.gif" alt="*"></a>';



	







echo "</div>\n";



}



echo "</div>\n";



}



echo "</table>\n";











if ($k_page>1)str("list.php?id=".intval($_GET['id']).'&amp;',$k_page,$page); // Вывод страниц







if ($notes['private_komm']==1 && $user['id']!=$avtor['id'] && $frend!=2  && !user_access('notes_delete')){



msg('Комментировать могут только друзья');



echo "  <div class='foot'>\n";



echo "<a href='index.php'>Назад</a><br />\n";



echo "   </div>\n";



include_once TFOOT;



exit;



}







if ($notes['private_komm']==2 && $user['id']!=$avtor['id'] && !user_access('notes_delete')){



msg('Пользователь запретил комментирование дневника');



echo "  <div class='foot'>\n";



echo "<a href='index.php'>Назад</a><br />\n";



echo "   </div>\n";



include_once TFOOT;



exit;



}







if (isset($user))



{



echo "<form method=\"post\" name='message' action=\"?id=".intval($_GET['id'])."&amp;page=$page".$go_otv."\">\n";



if ($set['web'] && test_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))



include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';



else



echo "$tPanel<textarea name=\"msg\">$otvet</textarea><br />\n";



echo "<input value=\"Отправить\" type=\"submit\" />\n";



echo "</form>\n";



}







echo "<div class=\"foot\">\n";



echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>Дневники</a> | <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>\n";



echo ' | <b>' . output_text($notes['name']) . '</b>';



echo "</div>\n";







include_once TFOOT;



?>



