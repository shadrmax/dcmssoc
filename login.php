<?
include_once 'sys/inc/start.php';
include_once COMPRESS;
include_once SESS;
include_once 'sys/inc/home.php';
include_once SETTINGS;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once SHIF;
$show_all = true; // показ для всех
$input_page = true;
include_once USER;
only_unreg();
reset_token();


if (isset($_GET['id']) && isset($_GET['pass'])) {

    if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '" . intval($_GET['id']) . "' AND `pass` = '" . shif($_GET['pass']) . "' LIMIT 1"), 0) == 1) {
        $user = get_user($_GET['id']);
        $_SESSION['id_user'] = $user['id'];

        dbquery("UPDATE `user` SET `date_aut` = " . time() . " WHERE `id` = '$user[id]' LIMIT 1");
        dbquery("UPDATE `user` SET `date_last` = " . time() . " WHERE `id` = '$user[id]' LIMIT 1");
        dbquery("INSERT INTO `user_log` (`id_user`, `time`, `ua`, `ip`, `method`) values('$user[id]', '$time', '$user[ua]' , '$user[ip]', '0')");
    } else $_SESSION['err'] = 'Неправильный логин или пароль';
} elseif (isset($_POST['nick']) && isset($_POST['pass'])) {
    if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "' AND `pass` = '" . shif($_POST['pass']) . "' LIMIT 1"), 0)) {
        $user = dbassoc(dbquery("SELECT `id` FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "' AND `pass` = '" . shif($_POST['pass']) . "' LIMIT 1"));
        $_SESSION['id_user'] = $user['id'];
        $user = get_user($user['id']);

        // сохранение данных в COOKIE
        if (isset($_POST['aut_save']) && $_POST['aut_save']) {
            setcookie('id_user', $user['id'], time() + 60 * 60 * 24 * 365);
            setcookie('pass', cookie_encrypt($_POST['pass'], $user['id']), time() + 60 * 60 * 24 * 365);
        }

        dbquery("UPDATE `user` SET `date_aut` = '$time', `date_last` = '$time' WHERE `id` = '$user[id]' LIMIT 1");
        dbquery("INSERT INTO `user_log` (`id_user`, `time`, `ua`, `ip`, `method`) values('$user[id]', '$time', '$user[ua]' , '$user[ip]', '1')");
    } else $_SESSION['err'] = 'Неправильный логин или пароль';
} elseif (isset($_COOKIE['id_user']) && isset($_COOKIE['pass']) && $_COOKIE['id_user'] && $_COOKIE['pass']) {
    if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = " . intval($_COOKIE['id_user']) . " AND `pass` = '" . shif(cookie_decrypt($_COOKIE['pass'], intval($_COOKIE['id_user']))) . "' LIMIT 1"), 0) == 1) {
        $user = get_user($_COOKIE['id_user']);
        $_SESSION['id_user'] = $user['id'];
        dbquery("UPDATE `user` SET `date_aut` = '$time', `date_last` = '$time' WHERE `id` = '$user[id]' LIMIT 1");
        $user['type_input'] = 'cookie';
    } else {
        $_SESSION['err'] = 'Ошибка авторизации по COOKIE';
        setcookie('id_user');
        setcookie('pass');
    }
} else $_SESSION['err'] = 'Ошибка авторизации';


if (!isset($user)) {
    header('Location: /aut.php');
    exit;
}


// Пишем ip пользователя

if (isset($ip2['add'])) dbquery("UPDATE `user` SET `ip` = " . ip2long($ip2['add']) . " WHERE `id` = '$user[id]' LIMIT 1");
else dbquery("UPDATE `user` SET `ip` = null WHERE `id` = '$user[id]' LIMIT 1");
if (isset($ip2['cl'])) dbquery("UPDATE `user` SET `ip_cl` = " . ip2long($ip2['cl']) . " WHERE `id` = '$user[id]' LIMIT 1");
else dbquery("UPDATE `user` SET `ip_cl` = null WHERE `id` = '$user[id]' LIMIT 1");
if (isset($ip2['xff'])) dbquery("UPDATE `user` SET `ip_xff` = " . ip2long($ip2['xff']) . " WHERE `id` = '$user[id]' LIMIT 1");
else dbquery("UPDATE `user` SET `ip_xff` = null WHERE `id` = '$user[id]' LIMIT 1");
if ($ua) dbquery("UPDATE `user` SET `ua` = '" . my_esc($ua) . "' WHERE `id` = '$user[id]' LIMIT 1");

// Непонятная сессия
dbquery("UPDATE `user` SET `sess` = '$sess' WHERE `id` = '$user[id]' LIMIT 1");

// Тип браузера
dbquery("UPDATE `user` SET `browser` = '" . ($webbrowser == true ? "wap" : "web") . "' WHERE `id` = '$user[id]' LIMIT 1");

// Проверяем на схожие ники
$collision_q = dbquery("SELECT * FROM `user` WHERE `ip` = '$iplong' AND `ua` = '" . my_esc($ua) . "' AND `date_last` > '" . (time() - 600) . "' AND `id` <> '$user[id]'");

while ($collision = dbassoc($collision_q)) {
    if (dbresult(dbquery("SELECT COUNT(*) FROM `user_collision` WHERE `id_user` = '$user[id]' AND `id_user2` = '$collision[id]' OR `id_user2` = '$user[id]' AND `id_user` = '$collision[id]'"), 0) == 0)
        dbquery("INSERT INTO `user_collision` (`id_user`, `id_user2`, `type`) values('$user[id]', '$collision[id]', 'ip_ua_time')");
}

/*
========================================
Рейтинг
========================================
*/
if (isset($user) && $user['rating_tmp'] > 1000) {
    // Счетчик активности
    $col = $user['rating_tmp'];

    // Делим на 100 что бы получить процент
    $col = $col / 1000;

    // Округляем
    $col = intval($col);

    // Прибавляем % рейтинга
    dbquery("update `user` set `rating` = '" . ($user['rating'] + $col) . "' where `id` = '$user[id]' limit 1");

    // Оповещаем
    $_SESSION['message'] = "Поздравляем! Вам за вашу активность начислено $col% рейтинга!";

    // Вычисляем остаток счетчика активности
    $col = $user['rating_tmp'] - ($col * 1000);

    // Сбрасываем
    dbquery("update `user` set `rating_tmp` = '$col' where `id` = '$user[id]' limit 1");
}

if (isset($_GET['return']))
    header('Location: ' . urldecode($_GET['return']));
else header("Location: /my_aut.php?" . SID);

exit;
?>