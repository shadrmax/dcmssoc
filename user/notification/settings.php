<?



/*



=======================================



Уведомления для Dcms-Social



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







only_reg();



$set['title']='Настройка уведомлений';



include_once THEAD;



title();







$notSet = dbarray(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '".$user['id']."' LIMIT 1"));







if (isset($_POST['save'])){







 // Комментарии



if (isset($_POST['komm']) && ($_POST['komm']==0 || $_POST['komm']==1))



{



dbquery("UPDATE `notification_set` SET `komm` = '".intval($_POST['komm'])."' WHERE `id_user` = '$user[id]'");



}







$_SESSION['message'] = 'Изменения успешно приняты';



header('Location: settings.php');



exit;



}



err();



aut();



echo "<div id='comments' class='menus'>";







echo "<div class='webmenu'>";



echo "<a href='/user/info/settings.php'>Общие</a>";



echo "</div>"; 







echo "<div class='webmenu last'>";



echo "<a href='/user/tape/settings.php'>Лента</a>";



echo "</div>"; 







echo "<div class='webmenu last'>";



echo "<a href='/user/discussions/settings.php'>Обсуждения</a>";



echo "</div>"; 







echo "<div class='webmenu last'>";



echo "<a href='/user/notification/settings.php' class='activ'>Уведомления</a>";



echo "</div>"; 















echo "<div class='webmenu last'>";



echo "<a href='/user/info/settings.privacy.php' >Приватность</a>";



echo "</div>"; 







echo "<div class='webmenu last'>";echo "<a href='/user/info/secure.php' >Пароль</a>";echo "</div>"; 







echo "</div>";







echo "<form action='?' method=\"post\">";



 // Лента фото



echo "<div class='mess'>";



echo "Уведомления о ответах в комментариях";



echo "</div>";







echo "<div class='nav1'>";



echo "<input name='komm' type='radio' ".($notSet['komm']==1?' checked="checked"':null)." value='1' /> Да ";



echo "<input name='komm' type='radio' ".($notSet['komm']==0?' checked="checked"':null)." value='0' /> Нет ";



echo "</div>";











echo "<div class='main'>";



echo "<input type='submit' name='save' value='Сохранить' />";



echo "</div>";







echo "</form>";







echo "<div class='foot'>\n";



echo "<img src='/style/icons/str2.gif' alt='*' /> <a href='index.php'>Уведомления</a> | <b>Настройки</b><br />\n";



echo "</div>\n";



	



include_once TFOOT;



?>