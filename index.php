<?
include_once 'sys/inc/home.php';
check_file(__FILE__);
include_once INC;


include_once check_replace('sys/inc/icons.php'); // Иконки главного меню
include_once check_replace('sys/inc/thead.php'); // Иконки главного меню

title();
err();

global $set;
if (!$set['web']) {
  ?>
  <div class="title">
    <center>
      <a href="/online.php" title="онлайн" style="color:#cdcecf; text-decoration: none">
        <font color="#fee300" size="2">Онлайн </font>
        <font
            color="#ffffff"><?= dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > " . (time() - 600) . ""), 0) ?></font>
      </a>
      <font color="#fee300" size="2"> (</font>
      <font
          color="#ffffff">+<?= dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > " . (time() - 600) . " AND `pereh` > '0'"), 0) ?></font>
      <font color="#fee300" size="2"> гостей )</font>
    </center>
  </div>

  <div class='main_menu'>
  <?
  if (isset($user)) {
    ?>
    <div align="right">
      <img src="/style/icons/icon_stranica.gif" alt="DS"/>
      <?= user::nick($user['id']) ?> | <a href="/exit.php"><font color="#ff0000">Выход</font></a>
    </div>
    <?
  } else {
    ?>
    <div align="right">
      <a href="/aut.php">Вход</a> | <a href="/reg.php">Регистрация</a>
    </div>
    <?
  }
  ?></div><?

  // новости
  include_once NEWS_MAIN;

  // главное меню

  include_once H . 'sys/inc/main_menu.php';
  include_once H . 'sys/inc/main_notes.php';

} else {
  // главная web темы
  include_once 'style/themes/' . $set['set_them'] . '/index.php';
}
include_once TFOOT;
