<?








include_once '../sys/inc/start.php';








include_once COMPRESS;








include_once SESS;








include_once '../sys/inc/home.php';








include_once SETTINGS;








include_once DB_CONNECT;








include_once IPUA;








include_once FNC;








include_once ADM_CHECK;








include_once USER;







user_access('adm_mysql',null,'index.php?'.SID);








adm_check();








$set['title']='Залитие таблиц';








include_once THEAD;








title();








if (isset($_FILES['file'])){








$file=esc(stripcslashes(htmlspecialchars($_FILES['file']['name'])));








$ras=strtolower(preg_replace('#^.*\.#i', NULL, $file));








if($ras!='sql')$err='Не верный формат файла';








if(!isset($err)){








@chmod(H."sys/update/",0777);








copy($_FILES['file']['tmp_name'], H."sys/update/".$_FILES['file']['name']."");








// выполнение одноразовых запросов








$opdirtables=opendir(H.'sys/update/');








while ($rd=readdir($opdirtables))








{








if (preg_match('#^\.#',$rd))continue;








if (isset($set['update'][$rd]))continue;

















if (preg_match('#\.sql$#i',$rd))








{








include_once H.'sys/inc/sql_parser.php';








$sql=SQLParser::getQueriesFromFile(H.'sys/update/'.$rd);








for ($i=0;$i<count($sql);$i++){dbquery($sql[$i]);}








$set['update'][$rd]=true;








$save_settings=true;








}








}








closedir($opdirtables);








@unlink(H."sys/update/".$_FILES['file']['name']."");








msg("Таблицы успешно залиты!");








}








}








if(isset($_GET['update'])){








// выполнение одноразовых запросов








$opdirtables=opendir(H.'sys/update/');








while ($rd=readdir($opdirtables))








{








if (preg_match('#^\.#',$rd))continue;








if (isset($set['update'][$rd]))continue;

















if (preg_match('#\.sql$#i',$rd))








{








include_once H.'sys/inc/sql_parser.php';








$sql=SQLParser::getQueriesFromFile(H.'sys/update/'.$rd);








for ($i=0;$i<count($sql);$i++){dbquery($sql[$i]);}








$set['update'][$rd]=true;








$save_settings=true;








}








}








closedir($opdirtables);








@unlink(H."sys/update/".$_FILES['file']['name']."");








msg("Таблицы успешно залиты!");








}


























err();








aut();








	echo "<form method='post' enctype='multipart/form-data' action='?$passgen'>








	Выгрузить:<br />








	<input name='file' type='file' accept='sql' /><br /><input value='Залить!' type='submit' />








	</form>








	<br /> Внимание! После загрузки файла и выполнения запроса, он будет автоматически удален!";

















	echo "<div class='foot'>








	Если файл с таблицами уже в папке, то переходите по ссылке ниже.<br /> 








	&raquo;<a href='?update'>Залить из папки</a>








	</div>\n";

















echo "<div class='foot'>\n";








echo "&laquo;<a href='mysql.php'>MySQL запросы</a><br />\n";








if (user_access('adm_panel_show'))








echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";








echo "</div>\n";








include_once TFOOT;








?>