
<?php
if ($_SESSION['install_step']!=0)
{
?>
    <form method='post' >

        <input value="Шаг назад" style="background-color: red" type="submit" name="back" />
        <?php
        if (isset($_POST['back']))
        {

            $_SESSION['install_step']--;
            header("Refresh: 0");
        }
        ?>

    </form>



    <form method='post' >

 <input value="Начать сначала" style="background-color: red" type="submit" name="reset" />
    <?php
    if (isset($_POST['reset']))
    {

        $_SESSION = array();
       header("Location: /install");
    }
    ?>

</form>






    <?php } ?>

<?php

list($msec, $sec) = explode(chr(32), microtime());
echo "<div class='foot'>\n";
//echo "<a href='/' accesskey='0' title='На главную'>".(isset ($set['copy']) && $set['copy']!=null?$set['copy']:'На главную')."</a><br />\n";

$page_size = ob_get_length(); 
ob_end_flush(); 
if(!isset($_SESSION['traf'])) 
	$_SESSION['traf'] = 0; 
	$_SESSION['traf'] += $page_size; 

echo '<center>
	Вес страницы: '.round($page_size / 1024, 2).' Кб<br />
	Ваш трафик: '.round($_SESSION['traf'] / 1024, 2).' Кб <br />
	Генерация страницы: '.round(($sec + $msec) - $conf['headtime'], 3).'сек
	</center>'; 
echo "</div>\n";
echo "</div>\n</body>\n</html>";
exit;

?>