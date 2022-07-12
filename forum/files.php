<?
include_once '../sys/inc/start.php';
//include_once COMPRESS; // если раскомментировать то файл будет качаться некорректно
include_once SESS;
include_once '../sys/inc/home.php';
include_once SETTINGS;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once USER;include_once '../sys/inc/downloadfile.php';


if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_files` WHERE `id` = '".intval($_GET['id'])."'"),0)==1)
{
$file=dbassoc(dbquery("SELECT * FROM `forum_files` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));if (test_file(H.'sys/forum/files/'.$file['id'].'.frf') && isset($user) && $user['level']>=1 && isset($_GET['del']))
{



if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL)
$link =$_SERVER['HTTP_REFERER'];
else
$link='/index.php';
dbquery("DELETE FROM `forum_files` WHERE `id` = '$file[id]' LIMIT 1");
unlink(H.'sys/forum/files/'.$file['id'].'.frf');


if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL)
header("Location: $_SERVER[HTTP_REFERER]");
else
header("Location: /forum/index.php?".SID);



}
elseif (test_file(H.'sys/forum/files/'.$file['id'].'.frf'))
{
dbquery("UPDATE `forum_files` SET `count` = '".($file['count']+1)."' WHERE `id` = '$file[id]' LIMIT 1");
DownloadFile(H.'sys/forum/files/'.$file['id'].'.frf', $file['name'].'.'.$file['ras'],ras_to_mime($file['ras']));
exit;
}

}
else
{
header("Refresh: 3; url=/index.php");
header("Content-type: text/html",NULL,404);
echo "<html>
<head>
<title>Ошибка 404</title>\n";
echo "<link rel=\"stylesheet\" href=\"/style/themes/default/style.css\" type=\"text/css\" />\n";
echo "</head>\n<body>\n<div class=\"body\"><div class=\"err\">\n";

echo "Нет такой страницы\n";
echo "<br />";
echo "<a href=\"/index.php\">На главную</a>";
echo "</div>\n</div>\n</body>\n</html>";
exit;
}



?>