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


/* Бан пользователя */ 

if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'forum' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)

{

header('Location: /ban.php?'.SID);exit;

}







if (isset($_GET['id_forum']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE".((!isset($user) || $user['level']==0)?" `adm` = '0' AND":null)." `id` = '".intval($_GET['id_forum'])."'"),0)==1

&& isset($_GET['id_razdel']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."'"),0)==1

&& isset($_GET['id_them']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id` = '".intval($_GET['id_them'])."' AND `id_razdel` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."'"),0)==1

&& isset($_GET['id_post']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id` = '".intval($_GET['id_post'])."' AND `id_them` = '".intval($_GET['id_them'])."' AND `id_razdel` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."'"),0)==1

)

{

$forum=dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '".intval($_GET['id_forum'])."' LIMIT 1"));

$razdel=dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1"));

$them=dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id` = '".intval($_GET['id_them'])."' AND `id_razdel` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1"));

$post=dbassoc(dbquery("SELECT * FROM `forum_p` WHERE `id` = '".intval($_GET['id_post'])."' AND `id_them` = '".intval($_GET['id_them'])."' AND `id_razdel` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1"));

$post2=dbassoc(dbquery("SELECT * FROM `forum_p` WHERE `id_them` = '".intval($_GET['id_them'])."' AND `id_razdel` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' ORDER BY `id` DESC LIMIT 1"));

if (isset($user)){

$ank=get_user($post['id_user']);



if (isset($_GET['act']) && $_GET['act']=='edit' && isset($_POST['msg']) && isset($_POST['post']) &&

 // редактирование поста

(

(user_access('forum_post_ed')) 

// права группы на редактирование

||

(isset($user) && $user['id']==$post['id_user'] && $post['time']>time()-600 && $post['id_user']==$post2['id_user']) 

// право на редактирование своего поста, если он поседний в теме

)

)

{





$msg=$_POST['msg'];

if (isset($_POST['translit']) && $_POST['translit']==1)$msg=translit($msg);

if (strlen2($msg)<2)$err[]='Короткое сообщение';

if (strlen2($msg)>1024)$err[]='Длина сообщения превышает предел в 1024 символа';



$mat=antimat($msg);

if ($mat)$err[]='В тексте сообщения обнаружен мат: '.$mat;



if (!isset($err))dbquery("UPDATE `forum_p` SET `msg` = '".my_esc($msg)."' WHERE `id` = '$post[id]' LIMIT 1");

}

elseif (isset($_GET['act']) && $_GET['act']=='edit' && (user_access('forum_post_ed') && ($ank['level']<$user['level'] || $ank['level']==$user['level'] && $ank['id']==$user['id']) || isset($user) && $post['id']==$post2['id'] && $post['id_user']==$user['id'] && $post['time']>time()-600)){



$set['title']='Форум - редактирование поста'; // заголовок страницы

include_once THEAD;

title();





echo "<form class='foot' method='post' name='message' action='/forum/$forum[id]/$razdel[id]/$them[id]/$post[id]/edit'>\n";

$msg2=output_text($post['msg'],false,true,false,false,false);

if ($set['web'] && test_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))

include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';

else

echo "Сообщение:<br />\n<textarea name=\"msg\">".$msg2."</textarea><br />\n";

echo "<input name='post' value='Изменить' type='submit' /><br />\n";

echo "</form>\n";

echo "<div class=\"foot\">\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?page=end\" title='Вернуться в тему'>В тему</a><br />\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/\" title='В раздел'>" . text($razdel['name']) . "</a><br />\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/\" title='В подфорум'>" . text($forum['name']) . "</a><br />\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">Форум</a><br />\n";

echo "</div>\n";

include_once TFOOT;

}

elseif (isset($_GET['act']) && $_GET['act']=='delete' && isset($user) && $them['close']==0 && ((user_access('forum_post_ed') && ($ank['level']<=$user['level'] || $ank['level']==$user['level'] && $ank['id']==$user['id'])) || $post['id']==$post2['id'] && $post['id_user']==$user['id'] && $post['time']>time()-600)){

dbquery("DELETE FROM `forum_p` WHERE `id` = '".intval($_GET['id_post'])."' AND `id_them` = '".intval($_GET['id_them'])."' AND `id_razdel` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1");

}

elseif (isset($_GET['act']) && $_GET['act']=='msg' && $them['close']==0 && isset($user)){

$ank=get_user($post['id_user']);

$set['title']='Форум - '.text($them['name']); // заголовок страницы

include_once THEAD;

title();

aut();





echo "<form class='foot' method='post' name='message' action='/forum/$forum[id]/$razdel[id]/$them[id]/new'>\n";

echo "<a href='/info.php?id=$ank[id]'>Посмотреть анкету</a><br />\n";

$msg2=$ank['nick'].', ';

if ($set['web'] && test_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))

include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';

else

echo "Сообщение:<br />\n<textarea name=\"msg\">$ank[nick], </textarea><br />\n";

echo "<input name='post' value='Отправить сообщение' type='submit' /><br />\n";

echo "</form>\n";

echo "<div class=\"foot\">\n";

echo "<img src='/style/icons/str.gif' alt='*'> <a href=\"/smiles.php\">Смайлы</a><br />\n";

echo "<img src='/style/icons/str.gif' alt='*'> <a href=\"/rules.php\">Правила</a><br />\n";

echo "</div>\n";



echo "<div class=\"foot\">\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?page=end\" title='Вернуться в тему'>В тему</a><br />\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/\" title='В раздел'>" . text($razdel['name']) . "</a><br />\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/\" title='В подфорум'>" . text($forum['name']) . "</a><br />\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">Форум</a><br />\n";



echo "</div>\n";

include_once TFOOT;

}

elseif (isset($_GET['act']) && $_GET['act']=='cit' && $them['close']==0 && isset($user)){

//$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));

$ank=get_user($post['id_user']);

$set['title']='Форум - '.text($them['name']); // заголовок страницы

include_once THEAD;

title();

aut();

echo "<div class='nav2'>Будет процитировано сообщение:<br/>\n";



echo "<div class='cit'>\n";

echo output_text($post['msg'])."<br />\n";

echo "</div>\n";

echo "<form method='post' name='message' action='/forum/$forum[id]/$razdel[id]/$them[id]/new'>\n";

echo "<input name='cit' value='$post[id]' type='hidden' />";

$msg2=$ank['nick'].', ';

if ($set['web'] && test_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))

include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';

else

echo "Сообщение:<br />\n<textarea name=\"msg\">$ank[nick], </textarea><br />\n";

echo "<input name='post' value='Отправить сообщение' type='submit' /><br />\n";

echo "</form></div>\n";





echo "<div class=\"foot\">\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?page=end\" title='Вернуться в тему'>В тему</a><br />\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/\" title='В раздел'>" . text($razdel['name']) . "</a><br />\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/\" title='В подфорум'>" . text($forum['name']) . "</a><br />\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">Форум</a><br />\n";

echo "</div>\n";

include_once TFOOT;

}



}





}




if (isset($_GET['id_forum']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE".((!isset($user) || $user['level']==0)?" `adm` = '0' AND":null)." `id` = '".intval($_GET['id_forum'])."'"),0)==1

&& isset($_GET['id_razdel']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."'"),0)==1

&& isset($_GET['id_them']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id` = '".intval($_GET['id_them'])."' AND `id_razdel` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."'"),0)==1 )

{

$forum=dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '".intval($_GET['id_forum'])."' LIMIT 1"));

$razdel=dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1"));

$them=dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id` = '".intval($_GET['id_them'])."' AND `id_razdel` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1"));

/*
===============================
Помечаем уведомление прочитанным
===============================
*/

	dbquery("UPDATE `notification` SET `read` = '1' WHERE `id_object` = '$them[id]' AND `type` = 'them_komm' AND `id_user` = '$user[id]'");

	/*------------очищаем счетчик этого обсуждения-------------*/

	if (isset($user)){

		dbquery("UPDATE `discussions` SET `count` = '0' WHERE `id_user` = '$user[id]' AND `type` = 'them' AND `id_sim` = '$them[id]' LIMIT 1");
	}

	/*---------------------------------------------------------*/

$set['title']='Форум - '.text($them['name']); // заголовок страницы

include_once THEAD;

title();

$ank2=get_user($them['id_user']);





include 'inc/set_them_act.php';



include 'inc/them.php';





include 'inc/set_them_form.php';



echo "<div class=\"foot\">\n";





echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">Форум</a> | <a href=\"/forum/$forum[id]/\" title='В подфорум'>" . text($forum['name']) . "</a> | <a href=\"/forum/$forum[id]/$razdel[id]/\" title='В раздел'>" . text($razdel['name']) . "</a><br />\n";

echo "</div>\n";

include_once TFOOT;

}



if (isset($_GET['id_forum']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE".((!isset($user) || $user['level']==0)?" `adm` = '0' AND":null)." `id` = '".intval($_GET['id_forum'])."'"),0)==1

&& isset($_GET['id_razdel']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."'"),0)==1)

{

$forum=dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '".intval($_GET['id_forum'])."' LIMIT 1"));

$razdel=dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1"));



if (isset($user) && isset($_GET['act']) && $_GET['act']=='new' && (!isset($_SESSION['time_c_t_forum']) || $_SESSION['time_c_t_forum']<$time-600 || $user['level']>0))

include 'inc/new_t.php'; // создание новой темы

else

{

$set['title']='Форум - '.text($razdel['name']); // заголовок страницы

include_once THEAD;

title();



if (user_access('forum_razd_edit'))include 'inc/set_razdel_act.php';



include 'inc/razdel.php';



if (user_access('forum_razd_edit'))include 'inc/set_razdel_form.php';



echo "<div class=\"foot\">\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">Форум</a> | <a href=\"/forum/$forum[id]/\">" . text($forum['name']) . "</a>";



echo "</div>\n";

}





include_once TFOOT;

}



if (isset($_GET['id_forum']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE".((!isset($user) || $user['level']==0)?" `adm` = '0' AND":null)." `id` = '".intval($_GET['id_forum'])."'"),0)==1)

{

$forum=dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '".intval($_GET['id_forum'])."' LIMIT 1"));



$set['title']='Форум - '.text($forum['name']); // заголовок страницы

include_once THEAD;

title();



include 'inc/set_forum_act.php'; // действия над подфорумом



include 'inc/forum.php'; // содержимое



include 'inc/set_forum_form.php'; // формы действий над подфорумом





echo "<div class=\"foot\">\n";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">Форум</a>\n";

echo "</div>\n";

include_once TFOOT;

}





$set['title']='Форум'; // заголовок страницы



include_once THEAD;

title();



if (user_access('forum_for_create') && isset($_GET['act']) && isset($_GET['ok']) && $_GET['act']=='new' && isset($_POST['name']) && isset($_POST['opis']) && isset($_POST['pos']))

{



$name=my_esc($_POST['name']);


if (strlen2($name)<3)$err='Слишком короткое название';

if (strlen2($name)>32)$err='Слишком днинное название';



$opis=$_POST['opis'];


if (strlen2($opis)>512)$err='Слишком длинное описание';

$opis=my_esc($opis);

if (!isset($_POST['icon']) || $_POST['icon']==null)

$icons='default';

else

$icons=preg_replace('#[^a-z0-9 _\-\.]#i', null, $_POST['icon']);

$pos=intval($_POST['pos']);

if (!isset($err)){

admin_log('Форум','Подфорумы',"Создание подфорума '$name'");

dbquery("INSERT INTO `forum_f` (`opis`, `name`, `pos`, `icon`) values('$opis', '$name', '$pos', '$icons')");

msg('Подфорум успешно создан');

}

}







err();

aut(); // форма авторизации

echo "<div class=\"err\">\n";

echo "<a href='/rules.php'>Правила</a><br />\n";

echo "</div>\n";

echo "<div class=\"main\">\n";

echo "<img src='/style/icons/New.gif'> Новые: <a href='/forum/new_t.php'>&bull; темы</a> | \n";

echo "<a href='/forum/new_p.php'>&bull; коммы</a><br />\n";

if (isset($user)){

echo "<img src='/style/icons/top.gif'> Мои: <a href='/user/info/them_p.php?id=".$user['id']."'>&bull; темы</a> | \n";
echo "<a href='/user/bookmark/forum.php?id=".$user['id']."'> &bull; закладки</a> | <a href='/user/info/them_p.php?id=".$user['id']."&komm'> &bull; посты</a><br/>";
}
echo "<img src='/style/icons/searcher.png'> <a href='/forum/search.php'>Поиск по форуму<br /></a>\n";

echo "</div>\n";





echo "<table class='post'>\n";







$q=dbquery("SELECT * FROM `forum_f`".((!isset($user) || $user['level']==0)?" WHERE `adm` = '0'":null)." ORDER BY `pos` ASC");

if (dbrows($q)==0) {

echo "  <div class='mess'>\n";

echo "Нет подфорумов\n";

echo "  </div>\n";

}

while ($forum = dbassoc($q))

{
/*-----------зебра-----------*/
  if ($num==0){
  echo "  <div class='nav1'>\n";
  $num=1;
  }
  elseif ($num==1)
  {
  echo "  <div class='nav2'>\n";
  $num=0;
  }
/*---------------------------*/

echo "<img src='/style/forum/$forum[icon]' alt='*'/> ";

echo "<a href='/forum/$forum[id]/'><b>" . text($forum['name']) . "</b></a> <span style='color:#666;'>(".dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_forum` = '$forum[id]'"),0).'/'.dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id_forum` = '$forum[id]'"),0).")\n";



if ($forum['opis']!=NULL)echo '<br />'.output_text($forum['opis']); 



echo "  </span> </div>\n";

}

echo "</table>\n";

echo "<div class='foot'>";
echo "<img src='/style/icons/online.gif'> <a href='on-forum.php'>Кто в форуме?</a> | <img src='/style/icons/user_gray.png'><a href='/user/admin.user.php?forum'>Модерация</a>";
echo "</div>";

if (user_access('forum_for_create') && (isset($_GET['act']) && $_GET['act']=='new' || dbresult(dbquery("SELECT COUNT(*) FROM `forum_f`"),0)==0))

{

echo "<form class='foot' method=\"post\" action=\"/forum/index.php?act=new&amp;ok\">\n";

echo "Название подфорума:<br />\n";

echo "<input name=\"name\" type=\"text\" maxlength='32' value='' /><br />\n";

echo "Описание:<br />\n";

echo "<textarea name=\"opis\"></textarea><br />\n";

echo "Позиция:<br />\n";

$pos=dbresult(dbquery("SELECT MAX(`pos`) FROM `forum_f`"), 0)+1;

echo "<input name=\"pos\" type=\"text\" maxlength='3' value='$pos' /><br />\n";

$icon=array();

$opendiricon=opendir(H.'style/forum');

while ($icons=readdir($opendiricon))

{

if (preg_match('#^\.|default.png#',$icons))continue;

$icon[]=$icons;

}

closedir($opendiricon);

echo "Иконка:<br />\n";

echo "<select name='icon'>\n";

echo "<option value='default.png'>По умолчанию</option>\n";

for ($i=0;$i<sizeof($icon);$i++)

{

echo "<option value='$icon[$i]'>$icon[$i]</option>\n";

}

echo "</select><br />\n";

echo "<input value=\"Создать\" type=\"submit\" />";

echo "<img src='/style/icons/delete.gif' alt='*'> <a href=\"/forum/\">Отмена</a><br />\n";

echo "</form>\n";

}



if (user_access('forum_for_create') && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f`"),0)>0){

echo "<div class=\"foot\">\n";

echo "<img src='/style/icons/lj.gif' alt='*'> <a href=\"/forum/?act=new\">Новый подфорум</a><br />\n";

echo "</div>\n";

}





include_once TFOOT;

?>