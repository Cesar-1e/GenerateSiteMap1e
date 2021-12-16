<?php
require "../GenerateSiteMap1e.php";
$GSM1e = new GenerateSiteMap1e();
$baseRoute = "localhost/GenerateSiteMap1e/test/";
$GSM1e->addUrl($baseRoute . 'index.php');
$GSM1e->addUrl($baseRoute . 'example.html');
$GSM1e->save();
echo "File create!";
?>