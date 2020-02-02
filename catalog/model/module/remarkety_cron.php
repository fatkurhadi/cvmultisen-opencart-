<?php
if (file_exists('config.php')) {
    require_once('config.php');
}

$ch = curl_init(HTTP_SERVER."index.php?route=extension/module/remarkety/cronjob");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_exec($ch);
curl_close($ch);

