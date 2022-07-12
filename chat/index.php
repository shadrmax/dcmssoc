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








/* Бан пользователя */ 




if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'chat' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)




{




header('Location: /ban.php?'.SID);exit;




}














if (isset($user))dbquery("DELETE FROM `chat_who` WHERE `id_user` = '$user[id]'");




dbquery("DELETE FROM `chat_who` WHERE `time` < '".($time-120)."'");




if (isset($user) && isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."'"),0)==1




&& isset($_GET['msg']) && dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '".intval($_GET['msg'])."'"),0)==1)




{




$room=dbassoc(dbquery("SELECT * FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));




$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '".intval($_GET['msg'])."' LIMIT 1"));




if (isset($user))dbquery("INSERT INTO `chat_who` (`id_user`, `time`,  `room`) values('$user[id]', '$time', '$room[id]')");




if ($set['time_chat']!=0)header("Refresh: $set[time_chat]; url=/chat/room/$room[id]/".rand(1000,9999).'/'); // автообновление




$set['title']='Чат - '.$room['name'].' ('.dbresult(dbquery("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"),0).')'; // заголовок страницы




include_once THEAD;




title();




echo "<div class='foot'><a href='/info.php?id=$ank[id]'>Посмотреть анкету</a></div>\n";




echo "<form class='foot' method=\"post\" action=\"/chat/room/$room[id]/".rand(1000,9999)."/\">\n";




echo "Сообщение:<br />\n<textarea name=\"msg\">$ank[nick], </textarea><br />\n";




echo "<label><input type=\"checkbox\" name=\"privat\" value=\"$ank[id]\" /> Приватно</label><br />\n";




if ($user['set_translit']==1)echo "<label><input type=\"checkbox\" name=\"translit\" value=\"1\" /> Транслит</label><br />\n";




echo "<input value=\"Отправить\" type=\"submit\" />\n";




echo "</form>\n";




echo "<div class=\"foot\">\n";




echo " <img src='/style/icons/str2.gif' alt='*'><a href=\"/chat/room/$room[id]/".rand(1000,9999)."/\">В комнату</a><br />\n";




echo " <img src='/style/icons/str2.gif' alt='*'><a href=\"/chat/\">Список чатов</a><br />\n";




echo "</div>\n";




include_once TFOOT;




}




if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."'"),0)==1)




{




$room=dbassoc(dbquery("SELECT * FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));




if (isset($user))dbquery("INSERT INTO `chat_who` (`id_user`, `time`,  `room`) values('$user[id]', '$time', '$room[id]')");




if ($set['time_chat']!=0)header("Refresh: $set[time_chat]; url=/chat/room/$room[id]/".rand(1000,9999).'/'); // автообновление




$set['title']='Чат - '.$room['name'].' ('.dbresult(dbquery("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"),0).')'; // заголовок страницы




include_once THEAD;




title();




include 'inc/room.php';




echo "<div class=\"foot\">\n";




echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/chat/\">Список чатов</a><br />\n";




echo "</div>\n";




include_once TFOOT;




}




$set['title']='Чат - прихожая'; // заголовок страницы




include_once THEAD;




title();




include 'inc/admin_act.php';




err();




aut(); // форма авторизации




echo "<table class='post'>\n";




$q=dbquery("SELECT * FROM `chat_rooms` ORDER BY `pos` ASC");




if ( dbrows($q) == 0 ) {




echo "  <div class='mess'>\n";




echo "Нет комнат\n";




echo "  </div>\n";




}




while ($room = dbassoc($q))




{




/*-----------зебра-----------*/if ($num==0){echo '<div class="nav1">';$num=1;}elseif ($num==1){echo '<div class="nav2">';$num=0;}/*---------------------------*/




echo "<img src='/style/themes/$set[set_them]/chat/14/room.png' alt='' /> ";














echo "<a href='/chat/room/$room[id]/".rand(1000,9999)."/'>$room[name] (".dbresult(dbquery("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"),0).")</a> \n";




if (user_access('chat_room'))echo "<a href='?set=$room[id]'><img src='/style/icons/edit.gif' alt='*' /></a> \n"; 




if ($room['opis']!=NULL)echo '<br />'.esc(trim(br(bbcode(smiles(links(stripcslashes(htmlspecialchars($room['opis']))))))))."<br />\n";





























echo "   </div>\n";




}




echo "</table>\n";


include 'inc/admin_form.php';


echo "<div class=\"foot\">\n";
echo "<img src='/style/icons/online.gif' alt='*'> <a href='who.php'>Кто в чате?</a><br />\n"; 
echo "</div>\n";


include_once TFOOT;




?>