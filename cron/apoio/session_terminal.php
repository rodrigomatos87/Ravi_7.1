<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);
$id = $_GET["id"];
sleep(1);
$pidexec = exec("ps aux | grep '/bin/bash /var/www/html/cron/shell/cmd_web.sh' | grep -v grep | awk {'print $2'} | tail -1");
exec("echo '$pidexec' > /var/www/html/ram/terminal_$id");
?>