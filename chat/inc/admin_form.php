<?





if (user_access('chat_room') && isset($_GET['set']) && is_numeric($_GET['set']) && dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '".intval($_GET['set'])."'"),0)==1)





{











$room=dbassoc(dbquery("SELECT * FROM `chat_rooms` WHERE `id` = '".intval($_GET['set'])."' LIMIT 1"));











echo "<form class='foot' action='?set=$room[id]&amp;ok' method='post'>";





echo "Название комнаты:<br />\n<input type='text' name='name' value='$room[name]' /><br />\n";





echo "Позиция:<br />\n<input type='text' name='pos' value='$room[pos]' /><br />\n";





echo "Описание:<br />\n<input type='text' name='opis' value='$room[opis]' /><br />\n";











echo "Боты:<br />\n<select name=\"bots\">\n";





echo "<option value='0'".(($room['umnik']==0 && $room['shutnik']==0)?' selected="selected"':null).">Нет</option>\n";





echo "<option value='1'".(($room['umnik']==1 && $room['shutnik']==0)?' selected="selected"':null).">$set[chat_umnik]</option>\n";





echo "<option value='2'".(($room['umnik']==0 && $room['shutnik']==1)?' selected="selected"':null).">$set[chat_shutnik]</option>\n";





echo "<option value='3'".(($room['umnik']==1 && $room['shutnik']==1)?' selected="selected"':null).">$set[chat_umnik] и $set[chat_shutnik]</option>\n";





echo "</select><br />\n";











echo "<input class='submit' type='submit' value='Применить' />";
echo "<img src='/style/icons/delete.gif'> <a href='?cancel=$passgen'>Отмена</a><br />\n";
echo "</form>";

echo "<div class='foot'><img src='/style/icons/trash.gif'> <a href='?delete=$room[id]'>Удалить комнату</a></div>\n";



}











if (user_access('chat_clear') && isset($_GET['act']) && $_GET['act']=='clear')





{











echo "<div class=\"err\">";











echo "Очистить чат?<br />\n";





echo "<a href=\"?act=clear2\">Да</a> / <a href=\"?\">Нет</a><br />\n";





echo "</div>";





}











if (user_access('chat_room') && (isset($_GET['act']) && $_GET['act']=='add_room' || dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms`"),0)==0))





{





echo "<form class=\"foot\" action=\"?act=add_room&amp;ok\" method=\"post\">";





echo "Название комнаты:<br />\n";





echo "<input type='text' name='name' value='' /><br />\n";











$pos=dbresult(dbquery("SELECT MAX(`pos`) FROM `chat_rooms`"), 0)+1;





echo "Позиция:<br />\n";





echo "<input type='text' name='pos' value='$pos' /><br />\n";

















echo "Описание:<br />\n";





echo "<input type='text' name='opis' value='' /><br />\n";











echo "Боты:<br />\n<select name=\"bots\">\n";











echo "<option value='0'>Нет</option>\n";





echo "<option value='1'>$set[chat_umnik]</option>\n";





echo "<option value='2'>$set[chat_shutnik]</option>\n";





echo "<option value='3'>$set[chat_umnik] и $set[chat_shutnik]</option>\n";





echo "</select><br />\n";











echo "<input class=\"submit\" type=\"submit\" value=\"Создать комнату\" />";





echo "<img src='/style/icons/delete.gif' alt='*'> <a href=\"?\">Отмена</a><br />\n";





echo "</form>";





}











echo "<div class=\"foot\">\n";





if (user_access('chat_clear'))





echo "<img src='/style/icons/trash.gif' alt='*'> <a href=\"?act=clear\">Очистить чат от сообщений</a><br />\n";

if (user_access('chat_room') && dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms`"),0)>0)

echo "<img src='/style/icons/lj.gif' alt='*'> <a href=\"?act=add_room\">Создать комнату</a><br />\n";





echo "</div>\n";





?>