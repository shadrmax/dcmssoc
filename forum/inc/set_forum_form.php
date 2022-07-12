<?


if (user_access('forum_razd_create') && (isset($_GET['act']) && $_GET['act']=='new' || !isset($_GET['act']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id_forum` = '$forum[id]'"),0)==0))
{
	echo "<form class='foot' method=\"post\" action=\"/forum/$forum[id]/?act=new&amp;ok\">\n";
	echo "Название раздела:<br />\n";
	echo "<input name=\"name\" type=\"text\" maxlength='32' value='' /><br />\n";
	echo "Описание<br/>\n";
	echo "<textarea name='opis' placeholder='Описание раздела'></textarea><br/>";
	echo "<input value=\"Создать\" type=\"submit\" />";
	echo "<img src='/style/icons/delete.gif' alt='*'> <a href=\"/forum/$forum[id]/\">Отмена</a><br />\n";
	echo "</form>\n";
}


if (user_access('forum_for_edit') && (isset($_GET['act']) && $_GET['act']=='set')) 
{ 
    echo "<form class='foot' method='post' action='/forum/$forum[id]/?act=set&ok'>\n"; 
    echo "Название форума:<br/>\n"; 
    echo '<input name="name" type="text" maxlength="32" value="' . text($forum['name']) . '" /><br/>';   
    echo "Описание:<br/>\n"; 
    echo "<textarea name='opis'>".esc(trim(stripcslashes(htmlspecialchars($forum['opis']))))."</textarea><br/>\n"; 
    $icon=array();

    $opendiricon=opendir(H.'style/forum');

    while ($icons=readdir($opendiricon))

    {

        if (preg_match('#^\.|default.png#',$icons))continue;

        $icon[]=$icons;

    }

    closedir($opendiricon);


    echo "Иконка:<br/>\n"; 
    echo "<select name='icon'>\n"; 
    echo "<option value='default.png'>По умолчанию</option>\n";
    for ($i=0;$i<sizeof($icon);$i++)

    {

        echo "<option value='$icon[$i]'>$icon[$i]</option>\n";

    }
    echo "</select><br/>\n";
    echo "Позиция:<br/>\n"; 
    echo "<input name='pos' type='text' maxlength='3' value='$forum[pos]' /><br/>\n"; 
     
    if ($user['level'] >= 3) { 
        if ($forum['adm']==1)$check=' checked="checked"'; 
        else  
        $check=NULL; 
         
        echo "<label><input type='checkbox".$check."' name='adm' value='1' /> Только для администрации</label><br/>\n"; 
    } 
     
    echo "<input value='Изменить' type='submit' />\n"; 
    echo "<img src='/style/icons/delete.gif' alt='*'> <a href='/forum/$forum[id]/'>Отмена</a>\n"; 
    echo "</form>\n"; 
} 

if (isset($_GET['act']) && $_GET['act']=='del' && user_access('forum_for_delete')) 
{ 
    echo "<div class='err'>\n"; 
    echo "Подтвердите удаление форума \n"; 
    echo '<a href="/forum/'.$forum['id'].'/?act=delete&ok">Да</a> / <a href="/forum/'.$forum['id'].'/">Нет</a>'; 
    echo "</div>\n"; 
} 

if (user_access('forum_razd_create') || user_access('forum_for_edit') || user_access('forum_for_delete'))
{ 
    echo "<div class='foot'>\n"; 

    if(user_access('forum_razd_create')) 
    echo "<img src='/style/icons/lj.gif' alt='*'> <a href='/forum/$forum[id]/?act=new'>Новый раздел</a>\n"; 

    if(user_access('forum_for_edit')) 
    echo "<br/><img src='/style/icons/cog.png' alt='*'> <a href='/forum/$forum[id]/?act=set'>Параметры форума</a>\n"; 

    if(user_access('forum_for_delete')) 
    echo "<br/><img src='/style/icons/trash.gif' alt='*'> <a href='/forum/$forum[id]/?act=del'>Удалить форум</a>\n"; 
    echo "</div>\n"; 
}
?>