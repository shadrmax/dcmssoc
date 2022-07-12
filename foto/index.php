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










if (isset($_GET['acth']) && $_GET['acth']=='show_foto' && isset($_GET['id_gallery']) && isset($_GET['id_foto']))



{



include_once 'inc/user_show_foto.php';







}



if (isset($_GET['acth']) && $_GET['acth']=='user_gallery' && isset($_GET['id_gallery']))



{



include_once 'inc/user_gallery_show.php';







}



elseif(isset($_GET['acth']) && $_GET['acth']=='user_gallery')



{



include_once 'inc/user_gallery.php';







}



else



{



include_once 'inc/all_gallery.php';



}







































include_once TFOOT;



?>



