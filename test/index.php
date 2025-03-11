<?php
require "../GenerateSiteMap1e.php";
$GSM1e = new GenerateSiteMap1e("/var/www/html/GenerateSiteMap1e/test/sitemap.xml");
$baseRoute = "http://localhost/GenerateSiteMap1e/test/";
$GSM1e->addUrl($baseRoute . 'index.php');
$GSM1e->addUrl($baseRoute . 'example.html');
/*$GSM1e->updateUrl($baseRoute . 'index.php', $baseRoute . 'index2.php');
$GSM1e->deleteUrl($baseRoute . 'example.html');*/
$GSM1e->save();
echo "File create!";
?>