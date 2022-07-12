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









only_reg();




$set['title']='Моя анкета';




include_once THEAD;




title();














if (isset($_POST['save'])){









if (isset($_POST['stena_foto']) && $_POST['stena_foto']==0)




{




$user['stena_foto']=0;




dbquery("UPDATE `user` SET `stena_foto` = '0' WHERE `id` = '$user[id]' LIMIT 1");




}




else




{




$user['stena_foto']=1;




dbquery("UPDATE `user` SET `stena_foto` = '1' WHERE `id` = '$user[id]' LIMIT 1");




}



















if (!isset($err))msg('Изменения успешно приняты');









}




err();




aut();














echo "<div id='comments' class='menu'>";




echo "<div class='webmenu'>";




        




echo "<a href='settings.php'>Общие</a>";









echo "</div>"; 









        




echo "<div class='webmenu last'>";




        




echo "<a href='stena.php' class='activ'>Стена</a>";









echo "</div>"; 




echo "</div>";




echo "<form method='post' action='?$passgen'>";




	




		




echo "









<label><input type='checkbox' name='stena_foto'".($user['stena_foto']==0?' checked="checked"':null)." value='0' /> Фотографии</label><br />














	<input type='submit' name='save' value='Сохранить' />




	</form>




	<div class='foot'>




	&raquo;<a href='anketa.php'>Посмотреть анкету</a><br />\n";














if(isset($_SESSION['refer']) && $_SESSION['refer']!=NULL && otkuda($_SESSION['refer']))




echo "&laquo;<a href='$_SESSION[refer]'>".otkuda($_SESSION['refer'])."</a><br />\n";









echo "&laquo;<a href='/umenu.php'>Мое меню</a><br /></div>\n";









	




include_once TFOOT;




?>