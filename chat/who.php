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



/* Бан пользователя */ if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'chat' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0){header('Location: /ban.php?'.SID);exit;}




$set['title']='Чат - Кто здесь?'; // заголовок страницы




include_once THEAD;




title();




aut();









$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > '".(time()-100)."' AND `url` like '/chat/%'"), 0);




$k_page=k_page($k_post,$set['p_str']);




$page=page($k_page);




$start=$set['p_str']*$page-$set['p_str'];




$q = dbquery("SELECT * FROM `user` WHERE `date_last` > '".(time()-100)."' AND `url` like '/chat/%' ORDER BY `date_last` DESC LIMIT $start, $set[p_str]");




echo "<table class='post'>\n";




if ($k_post==0)




{




echo "   <tr>\n";




echo "  <td class='p_t'>\n";




echo "Нет никого\n";




echo "  </td>\n";




echo "   </tr>\n";




}




while ($chat = dbarray($q))




{




echo "   <tr>\n";









if ($set['set_show_icon']==2){




echo "  <td class='icon48' rowspan='2'>\n";




avatar($chat['id']);




echo "  </td>\n";




}




elseif ($set['set_show_icon']==1)




{




echo "  <td class='icon14'>\n";




echo "".status($chat['id'])."";




echo "  </td>\n";




}



















echo "  <td class='p_t'>\n";




echo "<a href='/info.php?id=$chat[id]'>$chat[nick]</a>\n";




echo "  ".medal($chat['id'])." ".online($chat['id'])."\n";




echo "  </td>\n";




echo "   </tr>\n";




}









echo "</table>\n";

echo "<div class='foot'><img src='/style/icons/str2.gif'> <a href='/chat/'>В чат</a></div>\n";












if ($k_page>1)str("?",$k_page,$page); // Вывод страниц
























include_once TFOOT;




?>