<?



/*



=======================================



Статусы юзеров для Dcms-Social



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






if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `status` WHERE `id` = '".intval($_GET['id'])."'"),0)==1)



{



$post=dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));




$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));



if (isset($user) && ($user['level']>$ank['level']) || $post['id_user']==$user['id']) {


  dbquery("DELETE FROM `status` WHERE `id` = '$post[id]'");


  dbquery("DELETE FROM `status_komm` WHERE `id_status` = '$post[id]'");


  dbquery("DELETE FROM `status_like` WHERE `id_status` = '$post[id]'");


  $_SESSION['message'] = 'Статус упешно удален';
}

  header("Location: index.php?id=$ank[id]");


  exit;



}





