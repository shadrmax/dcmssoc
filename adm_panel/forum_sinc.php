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






user_access('adm_forum_sinc',null,'index.php?'.SID);







adm_check();







$set['title']='Синхронизация таблиц форума';







include_once THEAD;







title();







err();







aut();























if (isset($_GET['ok']) && isset($_POST['accept']))







{







$d_r=0;$d_t=0;$d_p=0;























// удаление разделов







$q=dbquery("SELECT `id`,`id_forum` FROM `forum_r`");







while ($razd=dbassoc($q))







{







if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE `id` = '$razd[id_forum]'"), 0)==0)







{







dbquery("DELETE FROM `forum_r` WHERE `id` = '$razd[id]' LIMIT 1");







$d_r++;







}















}















// удаление тем







$q=dbquery("SELECT `id`, `id_razdel`, `id_user` FROM `forum_t`");







while ($them=dbassoc($q))







{







if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id` = '$them[id_razdel]'"), 0)==0 || dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$them[id_user]'"), 0)==0)







{







dbquery("DELETE FROM `forum_t` WHERE `id` = '$them[id]' LIMIT 1");







$d_t++;







}







}















// удаление постов







$q=dbquery("SELECT `id`, `id_them`, `id_user` FROM `forum_p`");







while ($post=dbassoc($q))







{







if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id` = '$post[id_them]'"), 0)==0 || dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$post[id_user]'"), 0)==0)







{







dbquery("DELETE FROM `forum_p` WHERE `id` = '$post[id]' LIMIT 1");







$d_p++;







}







}







msg("Удалено разделов: $d_r, тем: $d_t, постов: $d_p");







}















echo "<form method=\"post\" action=\"?ok\">\n";







echo "<input value=\"Начать\" name='accept' type=\"submit\" />\n";







echo "</form>\n";















echo "* В зависимости от количества сообщений и тем, данное действие может занять длительное время.<br />\n";







echo "** Рекомендуется использовать только в случах расхождений счетчиков форума с реальными данными<br />\n";















if (user_access('adm_panel_show')){







echo "<div class='foot'>\n";







echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";







echo "</div>\n";







}















include_once TFOOT;







?>