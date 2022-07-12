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














$set['title']='Раздел администрации'; // заголовок страницы









include_once THEAD;




title();














aut(); // форма авторизации




if (user_access('adm_panel_show')){








  echo "<div class='mess'>\n";
  echo "<center><span style='font-size:16px;'><strong>DCMS-Social v.$set[dcms_version]</strong></span></center>\n";

  echo "<center><span style='font-size:14px;'> Официальный сайт поддержки <a href='https://dcms-social.ru'>https://dcms-social.ru</a></span></center>\n";echo "\n";





  if (status_version() >= 0)
    echo "<center> <font color='green'>Актуальная версия </font>		</center>	";

  else    echo "<center>	 <font color='red'>Есть новая версия - ".version_stable()."! <a href='/adm_panel/update.php'>Подробнее</a></font>		</center>	";


  echo "</div>";
  echo "<div class='main'>\n";

echo "<img src='/style/icons/spam.gif' alt='S' /> <a href='spam'>Жалобы</a> ";




include_once "spam/count.php";




echo "</div>";














echo "<div class='main'>\n";




echo "<img src='/style/icons/chat.gif' alt='S' /> <a href='chat'>Чат</a> ";




include_once "chat/count.php";




echo "</div>";









if (user_access('adm_panel_show')){




echo "<div class='main_seriy'>\n";




echo "<div class='main'>\n";




echo "<img src='/style/icons/settings.png' alt='S' /> <a href='/adm_panel/'>Админка</a> ";




echo "</div>";




echo "</div>";




}





























}




include_once TFOOT;




?>




