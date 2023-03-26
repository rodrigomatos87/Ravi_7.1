#!/usr/bin/php
<?php
include("/var/www/html/cron/apoio/conexao.php");

$cores_cpu = exec("cat /proc/cpuinfo | grep processor | grep -v 'model name' | wc -l");

$resultCronogramas = mysqli_query($db, "SELECT num_threads FROM configsRaviDNS");
$dns = mysqli_fetch_array($resultCronogramas);

if(($dns['num_threads'] == 1 && $cores_cpu > 1) || !$dns['num_threads'] || ($dns['num_threads'] > $cores_cpu && $cores_cpu >= 1)) {
    mysqli_query($db, "UPDATE configsRaviDNS SET num_threads = '".$cores_cpu."';");
}

$sel = mysqli_query($db, "select ativaOokla, ativaSSL from system");
$reg = mysqli_fetch_array($sel);

if($reg['ativaOokla'] == 1 && ($reg['ativaSSL'] == 1 || $reg['ativaSSL'] == 2)) {
    $pidexec = exec("ps aux | grep 'OoklaServer --daemon --pidfile=/var/www/html/ookla/OoklaServer.pid' | grep -v grep | awk '{print $2}'");
    if(!$pidexec) {
        exec("/bin/touch /var/www/html/ram/startookla");
    }
}

$selolt = mysqli_query($db, "select id from olts where status = 1 and ativo = 1");
if(mysqli_num_rows($selolt)) {
    while ($regolt = mysqli_fetch_array($selolt)) {
        exec("php -f /var/www/html/testeConnectOLT.php id=$regolt[id] 2>/dev/null &");
    }
}

$selconcentradoras = mysqli_query($db, "select id from concentradoras where status = 1 and ativo = 1");
if(mysqli_num_rows($selconcentradoras)) {
    while ($regconcentradoras = mysqli_fetch_array($selconcentradoras)) {
        exec("php -f /var/www/html/testeConnectPPPoE.php id=$regconcentradoras[id] > /dev/null &");
    }
}

exec("chown -R www-data:www-data /var/www/html");

mysqli_close($db);
?>