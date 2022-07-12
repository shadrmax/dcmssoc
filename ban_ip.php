<?


include_once 'sys/inc/start.php';


include_once COMPRESS;


include_once SESS;


include_once 'sys/inc/home.php';


include_once SETTINGS;


include_once DB_CONNECT;


include_once IPUA;


$ban_ip_page = TRUE; // чтобы небыло зацикливания


include_once FNC;


//include_once USER;


$set['title'] = 'Бан по IP';


include_once THEAD;


title();


$err = "<h1>Доступ с Вашего IP ($_SERVER[REMOTE_ADDR]) заблокирован</h1>";


err();


//aut();


?>


  <h2>Возможные причины:</h2>


  1) Частые обращения к серверу с одного IP адреса<br/>


  2) Ваш IP адрес совпадает с адресом нарушителя<br/>


  <h2>Способы решения:</h2>


  1) Перезапустить подключение к интернету<br/>


  2) В случае статического IP адреса можно воспользоваться прокси-сервером


  <br/>


<? include_once TFOOT; ?>