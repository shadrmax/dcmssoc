<?PHP




/*




=======================================




Личные файлы юзеров для Dcms-Social




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



/*-----------------------Удаление папки----------------------*/


if (!defined("USER")) die('No access');



if (isset($_GET['delete_folder']) && !isset($_GET['ok']))




{


//---Удаление папки
  $id=abs(intval(@$_GET['delete_folder'])  ? "$_GET[delete_folder]":'0');

  $folder = dbassoc(dbquery("SELECT id_user FROM `user_files`  WHERE `id` = '".$id."' AND `osn` != '1' LIMIT 1"));
  if (empty($folder['id_user']) or empty($user['id']) or (!user_access('obmen_file_delete') and $folder['id_user']!=$user['id']))
  {
    header("Location: /?".SID);
   exit;
  }



  $folder = dbassoc(dbquery("SELECT * FROM `user_files`  WHERE `id` = '".intval($_GET['delete_folder'])."' LIMIT 1"));









echo "<div class='mess'><center>";




echo "Вы действительно желаете удалить <b>".htmlspecialchars($folder['name'])."</b><br />";




echo "[<a href='?delete_folder=$folder[id]&amp;ok'><img src='/style/icons/ok.gif' alt='*'> Да</a>] [<a href='?'><img src='/style/icons/delete.gif' alt='*'> Нет</a>] \n";




echo "</center></div>";




include_once TFOOT;




}









$a = 0;




$b = 0;




if (isset($_GET['delete_folder']) && isset($_GET['ok']) )




{




$folder = dbassoc(dbquery("SELECT * FROM `user_files`  WHERE `id` = '".intval($_GET['delete_folder'])."' LIMIT 1"));









$q=dbquery("SELECT * FROM `user_files` WHERE `id_dires` like '%/$dir[id]/".intval($_GET['delete_folder'])."/%'");




while ($post = dbassoc($q))




{




$a++;




$q2=dbquery("SELECT * FROM `obmennik_files` WHERE `my_dir` = '$post[id]'");




while ($post2 = dbassoc($q2))




{




echo $post2['name'].'<br />';




unlink(H.'sys/obmen/files/'.$post2['id'].'.dat');




unlink(H.'sys/obmen/screens/128/'.$post2['id'].'.gif');




unlink(H.'sys/obmen/screens/128/'.$post2['id'].'.png');




unlink(H.'sys/obmen/screens/128/'.$post2['id'].'.jpg');




unlink(H.'sys/obmen/screens/128/'.$post2['id'].'.jpeg');




unlink(H.'sys/obmen/screens/48/'.$post2['id'].'.gif');




unlink(H.'sys/obmen/screens/48/'.$post2['id'].'.png');




unlink(H.'sys/obmen/screens/48/'.$post2['id'].'.jpg');




unlink(H.'sys/obmen/screens/48/'.$post2['id'].'.jpeg');









dbquery("DELETE FROM `user_music` WHERE `id_file` = '$post2[id]' AND `dir` = 'obmen'");




dbquery("DELETE FROM `obmennik_files` WHERE `id` = '$post2[id]'");




$b++;




}




echo $post['name'].'<br />';




dbquery("DELETE FROM `user_files` WHERE `id` = '$post[id]' LIMIT 1");




}



















$q2=dbquery("SELECT * FROM `user_files` WHERE `id` = '".intval($_GET['delete_folder'])."'");




while ($post = dbassoc($q2))




{




$a++;




$q3=dbquery("SELECT * FROM `obmennik_files` WHERE `my_dir` = '$post[id]'");




while ($post2 = dbassoc($q3))




{




echo $post2['name'].'<br />';




unlink(H.'sys/obmen/files/'.$post2['id'].'.dat');	




unlink(H.'sys/obmen/screens/128/'.$post2['id'].'.gif');




unlink(H.'sys/obmen/screens/128/'.$post2['id'].'.png');




unlink(H.'sys/obmen/screens/128/'.$post2['id'].'.jpg');




unlink(H.'sys/obmen/screens/128/'.$post2['id'].'.jpeg');




unlink(H.'sys/obmen/screens/48/'.$post2['id'].'.gif');




unlink(H.'sys/obmen/screens/48/'.$post2['id'].'.png');




unlink(H.'sys/obmen/screens/48/'.$post2['id'].'.jpg');




unlink(H.'sys/obmen/screens/48/'.$post2['id'].'.jpeg');




dbquery("DELETE FROM `user_music` WHERE `id_file` = '$post2[id]' AND `dir` = 'obmen'");




dbquery("DELETE FROM `obmennik_files` WHERE `id` = '$post2[id]'");




$b++;




}




dbquery("DELETE FROM `user_files` WHERE `id` = '$post[id]' LIMIT 1");




echo $post['name'].'<br />';




}




$_SESSION['message']="Удалено \"папок $a \" и \"файлов $b\"";




header("Location: ?".SID);




exit;









}



/*------------------------------------------------------------*/




