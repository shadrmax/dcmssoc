<?




/*




=======================================




Дневники для Dcms-Social




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









if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id` = '".intval($_GET['id'])."'"),0)==1)




{




$post=dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));




$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));









if (isset($user) && (user_access('notes_delete') || $user['id']==$ank['id'])){




dbquery("DELETE FROM `notes` WHERE `id` = '$post[id]'");




dbquery("DELETE FROM `notes_count` WHERE `id_notes` = '$post[id]'");




dbquery("DELETE FROM `notes_komm` WHERE `id_notes` = '$post[id]'");




dbquery("DELETE FROM `mark_notes` WHERE `id_list` = '$post[id]'");









$_SESSION['message']='Дневник успешно удален';




header("Location: index.php?".SID);




exit;









}




}else{




echo output_text('А как ты сюда попал? .дум.');




}









if (isset($_GET['komm']) && dbresult(dbquery("SELECT COUNT(*) FROM `notes_komm` WHERE `id` = '".intval($_GET['komm'])."'"),0)==1)




{




$post=dbassoc(dbquery("SELECT * FROM `notes_komm` WHERE `id` = '".intval($_GET['komm'])."' LIMIT 1"));




$notes=dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '$post[id_notes]' LIMIT 1"));




$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $notes[id_user] LIMIT 1"));









if (isset($user) && (user_access('notes_delete') || $user['id']==$ank['id'])){









dbquery("DELETE FROM `notes_komm` WHERE `id` = '$post[id]'");









$_SESSION['message']='Комментарий успешно удален';




header("Location: " . htmlspecialchars($_SERVER['HTTP_REFERER']));




exit;




}else{




echo output_text('А как ты сюда попал? .дум.');




}




}else{




echo output_text('А как ты сюда попал? .дум.');




}



















if (isset($_GET['dir']) && dbresult(dbquery("SELECT COUNT(*) FROM `notes_dir` WHERE `id` = '".intval($_GET['dir'])."'"),0)==1)




{









if (isset($user) && user_access('notes_delete')){









$q = dbquery("SELECT * FROM `notes_dir` WHERE `id` = '".intval($_GET['dir'])."' LIMIT 1");









while ($post = dbassoc($q))




{




$notes=dbassoc(dbquery("SELECT * FROM `notes` WHERE `id_dir` = '$post[id]'"));




dbquery("DELETE FROM `notes_count` WHERE `id_notes` = '$notes[id]'");




dbquery("DELETE FROM `notes_komm` WHERE `id_notes` = '$notes[id]'");




dbquery("DELETE FROM `mark_notes` WHERE `id_list` = '$notes[id]'");




}









$post = dbassoc(dbquery("SELECT * FROM `notes_dir` WHERE `id` = '".intval($_GET['dir'])."' LIMIT 1"));




dbquery("DELETE FROM `notes_count` WHERE `id_notes` = '$notes[id]'");




dbquery("DELETE FROM `notes_komm` WHERE `id_notes` = '$notes[id]'");




dbquery("DELETE FROM `mark_notes` WHERE `id_list` = '$notes[id]'");




dbquery("DELETE FROM `notes` WHERE `id_dir` = '$post[id]'");




dbquery("DELETE FROM `notes_dir` WHERE `id` = '$post[id]'");









$_SESSION['message']='Категория успешно удалена';




header("Location: " . htmlspecialchars($_SERVER['HTTP_REFERER']));




exit;









}else{




echo output_text('А как ты сюда попал? .дум.');




}









}else{




echo output_text('А как ты сюда попал? .дум.');




}





























?>