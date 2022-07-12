<?php
include_once 'sys/inc/start.php';
include_once COMPRESS;
include_once SESS;
include_once 'sys/inc/home.php';
include_once SETTINGS;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once USER;

only_reg();

if (setget('exit',1)==1) {

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_yes'])) {
      setcookie('id_user');
      setcookie('pass');
      session_destroy();
      header('Location: /?' . SID);
      exit();

    } else {
      header('Location: ' . $_POST['return']);
      exit();
    }

  }
}
else
{
  setcookie('id_user');
  setcookie('pass');
  session_destroy();
  header('Location: /?' . SID);
  exit();
}

$set['title']='Выход';

include_once THEAD;

title();
aut();
?>

<form class="foot" method="post">
<center>
  Вы действительно хотите выйти?
  <br/>
  <input type="hidden" name="return" value="<?=$_SERVER['HTTP_REFERER']?>">
  <input type="submit" name="confirm_yes" value="Да">
  <input type="submit" name="confirm_no" value="Нет">
</center>
</form>

<?php
include_once TFOOT;


