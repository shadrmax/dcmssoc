<?

ini_set('max_execution_time', 180);
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");


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


//user_access('adm_mysql', NULL, 'index.php?' . SID);


adm_check();

user_access('adm_set_sys', NULL, 'index.php?' . SID);
$temp_set = $set;

$set['title'] = 'Обновление движка (альфа версия)';


include_once THEAD;


title();


err();


aut();


if (isset($_POST['update'])) {





    if (function_exists("disk_free_space")) {
        if (disk_free_space("/") < 1048576) exit("Для системы обновления нужно минимум 20 мб свободного места");
    }

    $temp_set['job'] = 0;
    save_settings($temp_set);


    if (isset($_POST['backup'])) {

        $backup = H . "sys/backup/";
        if (!file_exists($backup)) {
            if (!mkdir($backup, 0777, TRUE) && !is_dir($backup)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $backup));
            }
        }

        if (!file_exists($backup . ".htaccess")) {
            $f = fopen($backup . ".htaccess", "a+");
            fwrite($f, "Options All -Indexes
deny from all");
            fclose($f);
        }

        $version = $temp_set['dcms_version'];
        $backup = $backup . $version . "_" . time() . "/";

        $dir30 = H;
        $files_new = getFileListAsArray($dir30);

        foreach ($files_new as $index => $file) {
            if (!file_exists(dirname($backup . $index))) {
                if (!mkdir($concurrentDirectory = dirname($backup . $index), 0755, TRUE) && !is_dir($concurrentDirectory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }

            copy($dir30 . $index, $backup . $index);
        }

    }


    $content = file_get_contents("https://dcms-social.ru/launcher/social.json");
    $data = json_decode($content, TRUE);

    $temp_set['dcms_version'] = $data['stable']['version'];
    $downloads = H . "sys/update/";
    if (!file_exists($downloads)) {
        if (!mkdir($downloads, 0777, TRUE) && !is_dir($downloads)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $downloads));
        }
    }
    if (!file_exists($downloads . ".htaccess")) {
        $f = fopen($downloads . ".htaccess", "a+");
        fwrite($f, "Options All -Indexes
deny from all");
        fclose($f);
    }

    $url = $data['stable']['url'];;
    $version = $data['stable']['version'];

    // Скачивание
    if ($updated = file_get_contents($url)) {

        $nf = $data['stable']['version'] . ".social-new.zip";
        file_put_contents($downloads . $nf, $updated);
        //  echo "Скачивание</br>";


        $zip = new ZipArchive;
        $res = $zip->open($downloads . $nf);
        if ($res === TRUE) {

            $dir30 = $downloads . $version . "_" . time() . "/";
            if (!file_exists($dir30)) {
                if (!mkdir($dir30, 0777, TRUE) && !is_dir($dir30)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir30));
                }
            }


            $zip->extractTo($dir30);


            $zip->close();


            //  echo "Установка</br>";


            $files_new = getFileListAsArray($dir30);

            $newpatch = H . "s";
            if (!file_exists($newpatch)) {
                if (!mkdir($newpatch, 0755, TRUE) && !is_dir($newpatch)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $newpatch));
                }
            }

            foreach ($files_new as $index => $file) {
                if (!file_exists(dirname($newpatch . $index))) mkdir(dirname($newpatch . $index), 0755, TRUE);

                copy($dir30 . $index, $newpatch . $index);
            }

            $temp_set['job'] = 1;
            save_settings($temp_set);

            if (save_settings($temp_set)) {
                admin_log('Настройки', 'Система', 'Обновление системы');
                msg2('Система обновлена');
            }
            header("Location: /adm_panel/update.php");
        }
    }

}








$content = file_get_contents("https://dcms-social.ru/launcher/social.json");
$data = json_decode($content, TRUE);

echo "<div class='mess'>\n";
echo "<center><span style='font-size:16px;'><strong>DCMS-Social v.$set[dcms_version]</strong></span></center>\n";

echo "<center><span style='font-size:14px;'> Официальный сайт поддержки <a href='https://dcms-social.ru'>https://dcms-social.ru</a></span></center>\n";
echo "\n";




if (status_version() >= 0) {
    echo "<div class='mess'> У вас последняя актуальная версия. Вы можете проверить наличие новой версии вручную на официальном сайте движка  <a target='_blank' href='https://dcms-social.ru'>DCMS-Social.ru</a></div>";
}
else
{
    echo "<div class='mess' style='font-size: 16px; background-color: #9aff9a' >Есть новая версия - " . $data['stable']['version'] . "! Требуется обновление. Вся информация о новом релизе на официальном сайте <a target='_blank' href='https://dcms-social.ru'>DCMS-Social.ru</a> Вы можете обновить движок автоматически на этой странице.</div>";

}

echo "<div class='mess'> <h3 style='color: red'>Внимание! Это альфа версия автоматического обновления. Используйте с умом! Все вручную внесенные изменения в оригинальные файлы движка вне папки  /replace/ будут потеряны. Сделайте резервные копии!</h3>  </div>";

echo "<form method='post' >";

    echo "<label><input type='checkbox' name='backup'> Сделать резервную копию файлов в /sys/backup/</label></br> ";

    if (status_version() < 0)
    echo "<input type='submit' name='update' value='Обновить!' />";
    else
        echo "<input type='submit' name='update' value='Переустановить текущую версию!' />";



    echo "</form>";


if (user_access('adm_panel_show')) {
    echo "<div class='foot'>\n";
    echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
    echo "</div>\n";
}


include_once TFOOT;

function getFileListAsArray(string $dir, bool $recursive = TRUE, string $basedir = ''): array
{
    if ($dir == '') {
        return array();
    } else {
        $results = array();
        $subresults = array();
    }
    if (!is_dir($dir)) {
        $dir = dirname($dir);
    } // so a files path can be sent
    if ($basedir == '') {
        $basedir = realpath($dir) . DIRECTORY_SEPARATOR;
    }

    $files = scandir($dir);
    foreach ($files as $key => $value) {
        if (($value != '.') && ($value != '..')) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (is_dir($path)) { // do not combine with the next line or..
                if ($recursive) { // ..non-recursive list will include subdirs
                    $subdirresults = getFileListAsArray($path, $recursive, $basedir);
                    $results = array_merge($results, $subdirresults);
                }
            } else { // strip basedir and add to subarray to separate file list
                $subresults[str_replace($basedir, '', $path)] = $value;
            }
        }
    }
    // merge the subarray to give the list of files then subdirectory files
    if (count($subresults) > 0) {
        $results = array_merge($subresults, $results);
    }
    return $results;
}