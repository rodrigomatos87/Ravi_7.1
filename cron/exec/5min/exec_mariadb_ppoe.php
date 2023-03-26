#!/usr/bin/php
<?php
$pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/mariadb_ppoe.php | grep -v grep | awk '{print $2}'");
if(!$pidexec) { exec("php -f /var/www/html/cron/apoio/mariadb_ppoe.php > /dev/null &"); }
?>