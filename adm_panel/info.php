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



user_access('adm_info',null,'index.php?'.SID);




adm_check();




$set['title']='Общая информация';




include_once THEAD;




title();




err();




aut();



















include_once H.'sys/inc/testing.php';




echo "<hr />\n";




include_once H.'sys/inc/chmod_test.php';

echo "http://dcms199/adm_panel/rights.php";


























if (isset($err))




{




if (is_array($err))




{




foreach ($err as $key=>$value) {




echo "<div class='err'>$value</div>\n";




}




}




else




echo "<div class='err'>$err</div>\n";




}




if (user_access('adm_panel_show')){




echo "<div class='foot'>\n";




echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";




echo "</div>\n";




}









include_once TFOOT;




?>