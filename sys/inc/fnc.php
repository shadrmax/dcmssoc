<?
// псевдонимы функций
function my_esc($str)
{
  return mysql_real_escape_string($str);
}


function __($str)
{
  return $str;
}

// для php 4 (альтернатива file_put_contents)
if (!function_exists('file_put_contents')) {
  function file_put_contents($file, $data)
  {
    $f = @fopen($file, 'w');
    return @fwrite($f, $data);
    @fclose($f);
  }
}

if ($set['antidos']) {// Защита от частых запросов с одного IP
  $antidos[] = array('time' => $time);
  $k_loads = 0;
  if (test_file(H . 'sys/tmp/antidos_' . $iplong . '.dat')) {
    $antidos_dat = unserialize(file_get_contents(H . 'sys/tmp/antidos_' . $iplong . '.dat'));
    for ($i = 0; $i < 150 && $i < sizeof($antidos_dat); $i++) {
      if ($antidos_dat[$i]['time'] > $time - 5) {
        $k_loads++;
        $antidos[] = $antidos_dat[$i];
      }
    }
  }
  if ($k_loads > 100) {
    if (dbresult(dbquery("SELECT COUNT(*) FROM `ban_ip` WHERE `min` <= '$iplong' AND `max` >= '$iplong'"), 0) == 0)
      dbquery("INSERT INTO `ban_ip` (`min`, `max`, `prich`) values('$iplong', '$iplong', 'AntiDos')", $db);
  }
  @file_put_contents(H . 'sys/tmp/antidos_' . $iplong . '.dat', serialize($antidos));
  @chmod(H . 'sys/tmp/antidos_' . $iplong . '.dat', 0777);
}

// антимат сделает автоматическое предупреждение, а затем бан
function antimat($str)
{
  global $user, $time, $set;
  if ($set['antimat']) {
    $antimat =& $_SESSION['antimat'];
    include_once H . 'sys/inc/censure.php';
    $censure = censure($str);
    if ($censure) {
      $antimat[$censure] = $time;
      if (count($antimat) > 3 && isset($user) && $user['level']) // если сделано больше 3-х предупреждений
      {
        $prich = "Обнаружен мат: $censure";
        $timeban = $time + 60 * 60; // бан на час
        dbquery("INSERT INTO `ban` (`id_user`, `id_ban`, `prich`, `time`) VALUES ('$user[id]', '0', '$prich', '$timeban')");
        admin_log('Пользователи', 'Бан', "Бан пользователя '[url=/amd_panel/ban.php?id=$user[id]]$user[nick][/url]' (id#$user[id]) до " . vremja($timeban) . " по причине '$prich'");
        header('Location: /ban.php?' . SID);
        exit;
      }
      return $censure;
    } else return FALSE;
  } else return FALSE;
}

// рекурсивное удаление папки
function delete_dir($dir)
{
  if (is_dir($dir)) {
    $od = opendir($dir);
    while ($rd = readdir($od)) {
      if ($rd == '.' || $rd == '..') continue;
      if (is_dir("$dir/$rd")) {
        @chmod("$dir/$rd", 0777);
        delete_dir("$dir/$rd");
      } else {
        @chmod("$dir/$rd", 0777);
        @unlink("$dir/$rd");
      }
    }
    closedir($od);
    @chmod("$dir", 0777);
    return @rmdir("$dir");
  } else {
    @chmod("$dir", 0777);
    @unlink("$dir");
  }
}


// очистка временной папки
if (!isset($hard_process)) {
  $q = dbquery("SELECT * FROM `cron` WHERE `id` = 'clear_tmp_dir'");
  if (dbrows($q) == 0) dbquery("INSERT INTO `cron` (`id`, `time`) VALUES ('clear_tmp_dir', '$time')");
  $clear_dir = dbassoc($q);
  if (!isset($clear_dir['time']) || isset($clear_dir['time']) && $clear_dir['time'] < $time - 60 * 60 * 24) {
    $hard_process = TRUE;
    dbquery("UPDATE `cron` SET `time` = '$time' WHERE `id` = 'clear_tmp_dir'");
    if (function_exists('curl_init')) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://dcms-social.ru/curl.php?site=' . $_SERVER['HTTP_HOST'] . '&version=' . $set['dcms_version'] . '&title=' . $set['title']);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $data = curl_exec($ch);
      curl_close($ch);
    }
    $od = opendir(H . 'sys/tmp/');
    while ($rd = readdir($od)) {
      if (!preg_match('#^\.#', $rd) && filectime(H . 'sys/tmp/' . $rd) < $time - 60 * 60 * 24) {
        @delete_dir(H . 'sys/tmp/' . $rd);
      }
    }
    closedir($od);
  }
}// Подведение итогов статистики
if (!isset($hard_process)) {
  $q = dbquery("SELECT * FROM `cron` WHERE `id` = 'visit' LIMIT 1");
  if (dbrows($q) == 0) dbquery("INSERT INTO `cron` (`id`, `time`) VALUES ('visit', '$time')");
  $visit = dbassoc($q);
  if (!isset($visit['time']) || isset($visit['time']) && $visit['time'] < time() - 60 * 60 * 24) {
    if (function_exists('set_time_limit')) @set_time_limit(600); // Ставим ограничение на 10 минут
    $last_day = mktime(0, 0, 0, date('m'), date('d') - 1); // начало вчерашних суток
    $today_time = mktime(0, 0, 0); // начало сегодняшних суток
    if (dbresult(dbquery("SELECT COUNT(*) FROM `visit_everyday` WHERE `time` = '$last_day'"), 0) == 0) {
      $hard_process = TRUE;
// записываем общие данные за вчерашние сутки в отдельную таблицу
      dbquery("INSERT INTO `visit_everyday` (`host` , `host_ip_ua`, `hit`, `time`) VALUES ((SELECT COUNT(DISTINCT `ip`) FROM `visit_today` WHERE `time` < '$today_time'),(SELECT COUNT(DISTINCT `ip`, `ua`) FROM `visit_today` WHERE `time` < '$today_time'),(SELECT COUNT(*) FROM `visit_today` WHERE `time` < '$today_time'),'$last_day')");
      dbquery('DELETE FROM `visit_today` WHERE `time` < ' . $today_time);
    }
  }
}

// запись о переходах на сайт
if (isset($_SERVER['HTTP_REFERER']) && !preg_match('#' . preg_quote($_SERVER['HTTP_HOST']) . '#', $_SERVER['HTTP_REFERER']) && $ref = @parse_url($_SERVER['HTTP_REFERER'])) {
  if (isset($ref['host'])) $_SESSION['http_referer'] = $ref['host'];
}

function br($msg, $br = '<br />')
{
  return preg_replace("#((<br( ?/?)>)|\n|\r)+#i", $br, $msg);
} // переносы строк

function esc($text, $br = NULL)
{ // Вырезает все нечитаемые символы
  if ($br != NULL)
    for ($i = 0; $i <= 31; $i++) $text = str_replace(chr($i), NULL, $text);
  else {
    for ($i = 0; $i < 10; $i++) $text = str_replace(chr($i), NULL, $text);
    for ($i = 11; $i < 20; $i++) $text = str_replace(chr($i), NULL, $text);
    for ($i = 21; $i <= 31; $i++) $text = str_replace(chr($i), NULL, $text);
  }
  return $text;
}

// получаем данные пользователя и уровень прав (+ кеширование)
function get_user($user_id = 0)
{
  static $users; // переменная не удаляется после вызова функции
  if ($user_id == 0) {
// бот
    $ank2['id'] = 0;
    $ank2['nick'] = 'Система';
    $ank2['level'] = 999;
    $ank2['pol'] = 1;
    $ank2['group_name'] = 'Системный робот';
    $ank2['ank_o_sebe'] = 'Создан для уведомлений';
    return $ank2;
  } else {

    $user_id = intval($user_id);
    $users[0] = FALSE;
    if (!isset($users[$user_id])) {
      $users[$user_id] = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$user_id' LIMIT 1"));

      if ($users[$user_id]['id'] != 0) {


        $tmp_us = dbassoc(dbquery("SELECT `level`,`name` AS `group_name` FROM `user_group` WHERE `id` = '" . $users[$user_id]['group_access'] . "' LIMIT 1"));

        if (!isset($tmp_us) or empty($tmp_us['group_name'])) {
          $users[$user_id]['level'] = 0;
          $users[$user_id]['group_name'] = 'Пользователь';
        } else {
          $users[$user_id]['level'] = $tmp_us['level'];
          $users[$user_id]['group_name'] = $tmp_us['group_name'];
        }
      } else $users[$user_id] = FALSE;
    }
    return $users[$user_id];
  }
}

// определение оператора
function opsos($ips = NULL)
{
  global $ip;
  if ($ips == NULL) $ips = $ip;
  $ipl = ip2long($ips);
  if (dbresult(dbquery("SELECT COUNT(*) FROM `opsos` WHERE `min` <= '$ipl' AND `max` >= '$ipl'"), 0) != 0) {
    $opsos = dbassoc(dbquery("SELECT opsos FROM `opsos` WHERE `min` <= '$ipl' AND `max` >= '$ipl' LIMIT 1"));
    return stripcslashes(htmlspecialchars($opsos['opsos']));
  } else return FALSE;
}// вывод времени
function vremja($time = NULL)
{
  global $user;
  if ($time == NULL) $time = time();
  if (isset($user)) $time = $time + $user['set_timesdvig'] * 60 * 60;
  $timep = "" . date("j M Y в H:i", $time) . "";
  $time_p[0] = date("j n Y", $time);
  $time_p[1] = date("H:i", $time);
  if ($time_p[0] == date("j n Y")) $timep = date("H:i:s", $time);
  if (isset($user)) {
    if ($time_p[0] == date("j n Y", time() + $user['set_timesdvig'] * 60 * 60)) $timep = date("H:i:s", $time);
    if ($time_p[0] == date("j n Y", time() - 60 * 60 * (24 - $user['set_timesdvig']))) $timep = "Вчера в $time_p[1]";
  } else {
    if ($time_p[0] == date("j n Y")) $timep = date("H:i:s", $time);
    if ($time_p[0] == date("j n Y", time() - 60 * 60 * 24)) $timep = "Вчера в $time_p[1]";
  }
  $timep = str_replace("Jan", "Янв", $timep);
  $timep = str_replace("Feb", "Фев", $timep);
  $timep = str_replace("Mar", "Марта", $timep);
  $timep = str_replace("May", "Мая", $timep);
  $timep = str_replace("Apr", "Апр", $timep);
  $timep = str_replace("Jun", "Июня", $timep);
  $timep = str_replace("Jul", "Июля", $timep);
  $timep = str_replace("Aug", "Авг", $timep);
  $timep = str_replace("Sep", "Сент", $timep);
  $timep = str_replace("Oct", "Окт", $timep);
  $timep = str_replace("Nov", "Ноября", $timep);
  $timep = str_replace("Dec", "Дек", $timep);
  return $timep;
}

// только для зарегистрированых
function only_reg($link = NULL)
{
  global $user;
  if (!isset($user)) {
    if ($link == NULL) $link = '/index.php?' . SID;
    header("Location: $link");
    exit;
  }
}


function only_auth()
{
    global $user;
    if (isset($user)) return true;
    else return false;
}


// только для незарегистрированых
function only_unreg($link = NULL)
{
  global $user;
  if (isset($user)) {
    if ($link == NULL) $link = '/index.php?' . SID;
    header("Location: $link");
    exit;
  }
}


// только для тех, у кого уровень доступа больше или равен $level
function only_level($level = 0, $link = NULL)
{
  global $user;
  if (!isset($user) || $user['level'] < $level) {
    if ($link == NULL) $link = '/index.php?' . SID;
    header("Location: $link");
    exit;
  }
}

if (!isset($hard_process)) {
  $q = dbquery("SELECT * FROM `cron` WHERE `id` = 'everyday'");
  if (dbrows($q) == 0) dbquery("INSERT INTO `cron` (`id`, `time`) VALUES ('everyday', '" . time() . "')");
  $everyday = dbassoc($q);
  if (!isset($everyday['time']) || isset($everyday['time']) && $everyday['time'] < time() - 60 * 60 * 24) {
    $hard_process = TRUE;
    if (function_exists('set_time_limit')) @set_time_limit(600); // Ставим ограничение на 10 минут
    dbquery("UPDATE `cron` SET `time` = '" . time() . "' WHERE `id` = 'everyday'");
    dbquery("DELETE FROM `guests` WHERE `date_last` < '" . (time() - 600) . "'");
    dbquery("DELETE FROM `chat_post` WHERE `time` < '" . (time() - 60 * 60 * 24) . "'"); // удаление старых постов в чате
    dbquery("DELETE FROM `user` WHERE `activation` != null AND `time_reg` < '" . (time() - 60 * 60 * 24) . "'"); // удаление неактивированных аккаунтов

// удаляем все контакты, помеченные на удаление более месяца назад
    $qd = dbquery("SELECT * FROM `users_konts` WHERE `type` = 'deleted' AND `time` < " . ($time - 60 * 60 * 24 * 30));
    while ($deleted = dbarray($qd)) {
      dbquery("DELETE FROM `users_konts` WHERE `id_user` = '$deleted[id_user]' AND `id_kont` = '$deleted[id_kont]'");

      if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_kont` = '$deleted[id_user]' AND `id_user` = '$deleted[id_kont]'"), 0) == 0) {
// если юзер не находится в контакте у другого, то удаляем и все сообщения
        dbquery("DELETE FROM `mail` WHERE `id_user` = '$deleted[id_user]' AND `id_kont` = '$deleted[id_kont]' OR `id_kont` = '$deleted[id_user]' AND `id_user` = '$deleted[id_kont]'");
      }
    }
    $tab = dbquery('SHOW TABLES FROM ' . $set['mysql_db_name']);
    for ($i = 0; $i < dbrows($tab); $i++) {
      dbquery("OPTIMIZE TABLE `" . mysql_tablename($tab, $i) . "`"); // оптимизация таблиц
    }
  }
}


// вывод ошибок
function err()
{
  global $err;
  if (isset($err)) {
    if (is_array($err)) {
      foreach ($err as $key => $value) {
        echo "<div class='err'>$value</div>\n";
      }
    } else echo "<div class='err'>$err</div>\n";
  }
}

function msg($msg)
{
  echo "<div class='msg'>$msg</div>\n";

} // вывод сообщений
function msg2($msg)
{
  $_SESSION['message'] = $msg;

} // вывод сообщений



// отправка запланированных писем
$q = dbquery("SELECT * FROM `mail_to_send` LIMIT 1");
if (dbrows($q) != 0) {
  $mail = dbassoc($q);
  $adds = "From: \"admin@$_SERVER[HTTP_HOST]\" <admin@$_SERVER[HTTP_HOST]>\n";
  $adds .= "Content-Type: text/html; charset=utf-8\n";
  mail($mail['mail'], '=?utf-8?B?' . base64_encode($mail['them']) . '?=', $mail['msg'], $adds);
  dbquery("DELETE FROM `mail_to_send` WHERE `id` = '$mail[id]'");
}

// сохранение настроек системы
function save_settings($set)
{
  unset($set['web']);
  
  $file = H.'sys/dat/settings.php';
$text = '<?php return '.var_export($set,true).'; ';
$fle = fopen($file, 'w+');
fwrite($fle, $text); 
fclose($fle);
    @chmod($fle, 0755);

  
  if ($fopen = @fopen(H . 'sys/dat/settings_6.2.dat', 'w')) {
    @fputs($fopen, serialize($set));
    @fclose($fopen);
    @chmod(H . 'sys/dat/settings_6.2.dat', 0755);
    return TRUE;
  } else
    return FALSE;






}

// запись действий администрации
function admin_log($mod, $act, $opis)
{
  global $user;

  $q = dbquery("SELECT * FROM `admin_log_mod` WHERE `name` = '" . my_esc($mod) . "' LIMIT 1");
  if (dbrows($q) == 0) {
    dbquery("INSERT INTO `admin_log_mod` (`name`) VALUES ('" . my_esc($mod) . "')");
    $id_mod = mysql_insert_id();
  } else $id_mod = dbresult($q, 0);

  $q2 = dbquery("SELECT * FROM `admin_log_act` WHERE `name` = '" . my_esc($act) . "' AND `id_mod` = '$id_mod' LIMIT 1");
  if (dbrows($q2) == 0) {
    dbquery("INSERT INTO `admin_log_act` (`name`, `id_mod`) VALUES ('" . my_esc($act) . "', '$id_mod')");
    $id_act = mysql_insert_id();
  } else $id_act = dbresult($q2, 0);
  dbquery("INSERT INTO `admin_log` (`time`, `id_user`, `mod`, `act`, `opis`) VALUES
('" . time() . "','$user[id]', '$id_mod', '$id_act', '" . my_esc($opis) . "')");
}


// LoginAPI
if (isset($_POST['token'])) {
  $s = file_get_contents('https://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
  $_POST['loginAPI'] = TRUE;
}

// Загрузка остальных функций из папки "sys/fnc"
$opdirbase = opendir(H . 'sys/fnc');

while ($filebase = readdir($opdirbase)) {
  if (preg_match('#\.php$#i', $filebase)) {
    include_once(H . 'sys/fnc/' . $filebase);
  }
}

// запись о посещении
dbquery("INSERT INTO `visit_today` (`ip` , `ua`, `time`) VALUES ('$iplong', '" . @my_esc($_SERVER['HTTP_USER_AGENT']) . "', '$time')");

function csrf_token_new()
{
  setcookie('token', random_bytes(), time() + 60 * 10);


}

function ages($age)
{
  $str = '';
  $num = $age > 100 ? substr($age, -2) : $age;
  if ($num >= 5 && $num <= 14) $str = "лет";
  else {
    $num = substr($age, -1);
    if ($num == 0 || ($num >= 5 && $num <= 9)) $str = 'лет';
    if ($num == 1) $str = 'год';
    if ($num >= 2 && $num <= 4) $str = 'года';
  }
  return $age . ' ' . $str;
}


function t_toolbar_css ()
{
  ?>
  <style>


      .toolbar {
          position: fixed;
          text-align: center;
          vertical-align: middle;

          color: #e07dc0;
          top: 0;
          left: 0;
          right: 0;
          margin-bottom: 50px;

          z-index: 9999;
          border-bottom: 1px solid #656969;
          width: 100%;

          background: rgb(15, 15, 15);
          height: 40px;

      }
      .toolbar_inner
      {
          display: inline-block;

          vertical-align: middle;

          text-align: center;
      }

      html {

          padding-top: 40px;
      }
  </style>
  <?php
}

function version_stable ()
{
    $content = file_get_contents("https://dcms-social.ru/launcher/social.json");
    $data = json_decode($content, TRUE);
    return $data['stable']['version'];

}
function t_toolbar_html()
{
    global $set;

  ?>
  <div class="toolbar">
    <div class="toolbar_inner">
      <span style="color: white">Admin Tool</span> ::
      <a href="/">Главная</a>  |
      <a href="/plugins/admin/">Админ разделы</a> |
      <a href="/adm_panel/">Админ панель</a> |
      <a target="_blank" href="https://dcms-social.ru">DCMS-Social.ru</a>
       v. <?=$set['dcms_version']?>
        <?
        if (status_version() < 0)   echo "<center>	 <font color='red'>Есть новая версия - ".version_stable()."! <a href='/adm_panel/update.php'>Подробнее</a></font>		</center>	";
?>

    </div>
  </div>
  <?php
}


function new_token ()
{
    $token = rand(10000,100000);
    return bin2hex($token); // ffa7a910ca2dfce501b0d548605aaf

}

function token_p($token)
{
    if ($token===$_SESSION['token']) return true;
    else
    {
        header("/");
        exit("error token");

    }
}


function set_token ()
{

    if (empty($_SESSION['token']))  $_SESSION['token'] = new_token();



}
function reset_token ()
{

    $_SESSION['token'] = new_token();


}


function check_token()
{

    add_header(token_js());

    if (isset($_POST)&&!empty($_POST))
    {
        if (isset($_POST['token'])) token_p ($_POST['token']);
        else
        {
            header("/");
            exit("error token");
        }
    }

}

function add_header ($value)
{
    static $add;
    return $add[]=$value;
    header_html($add);

}
function header_html($add=null)
{
    static $header;
    if ($add==null)
    {
     //   var_dump($header);
        echo "".$header;
    }
    else $header = $add;
}

function token()
{
    return $_SESSION['token'];
}
function token_js()
{
    ob_start()
?>

  <script>


      window.onload = function() {
          form = document.querySelector('form');
          var x = document.createElement("input");
          x.setAttribute("type", "text");
          x.setAttribute("value", "<?=token()?>");
          x.setAttribute("name", "token");
          form.appendChild(x);
      }

    </script>



    <?php
    $page = ob_get_contents();
    ob_end_clean();


return $page;

}
function token_form()
{
    echo '<input type="text" name="token" value="'.$_SESSION['token'].'">';

}

function status_version ()
{
    /*global $set;
    $content = file_get_contents("https://dcms-social.ru/launcher/social.json");
    $data = json_decode($content, TRUE);


    return version_compare($set['dcms_version'], $data['stable']['version']);*/
}


