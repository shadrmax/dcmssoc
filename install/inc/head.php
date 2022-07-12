<!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title><?echo $set['title'];?></title>
        <link rel="stylesheet" href="/style/themes/<? echo $set['set_them']; ?>/style.css" type="text/css" />
    </head>
    <body>
    <div class="body">
        <div class="logo"><img src="/style/themes/new/logo.png"  alt="Logotype" /><br />
            DCMS Social - Движок социальной сети</div>
        <div class="title">
            <?
            echo $set['title']."\n";
            ob_start();
            ?>
        </div>
<? if (status_version() <0) {
    $content = file_get_contents("https://dcms-social.ru/launcher/social.json");
    $data = json_decode($content, TRUE);

    echo "<div class='mess' style='font-size: 16px; background-color: #9aff9a' >ВНИМАНИЕ! Есть новая версия - <strong>" . $data['stable']['version'] . "!</strong> Рекомендуется устанавливать актуальную версию. Вся информация о новом релизе доступна на официальном сайте - <a target='_blank' href='https://dcms-social.ru'>DCMS-Social.ru</a></div>";

}
?>