#!/usr/bin/php
<?php
$pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/mariadb_olt.php | grep -v grep | awk '{print $2}'");
if(!$pidexec) { exec("php -f /var/www/html/cron/apoio/mariadb_olt.php 2>&1 &"); }
?>