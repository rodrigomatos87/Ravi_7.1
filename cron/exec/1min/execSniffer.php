#!/usr/bin/php
<?php
$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

$hora = date("H");
$minuto = date("i");
$dia = date("d");

$diretorio1 = scandir("/var/www/html/ram/dispositivos/sensores");
$arquivos1 = count($diretorio1) - 2;
if($arquivos1 <= 0) { exec("php -f /var/www/html/apoio/dispositivos_ram_init.php > /dev/null &"); }

include("/var/www/html/cron/apoio/conexao.php");

$PesquisaGrupo = mysqli_query($db, "SELECT id, repetir, status FROM GrupoMonitor WHERE autoscan = '1';");
while ($Grupo = mysqli_fetch_array($PesquisaGrupo)) {
    if($Grupo['status'] == 0) {
        // Executar apenas uma vez
        if($Grupo['repetir'] == 1) {
            $tamesmo = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/sniffer.php idG=$Grupo[id]' | grep -v grep | awk '{print $2}'");
            if(!$tamesmo) { exec("php -f /var/www/html/cron/apoio/sniffer.php idG=$Grupo[id] > /dev/null &"); }
        // A cada hora
        }else if($Grupo['repetir'] == 2 && $minuto == "00") {
            $tamesmo = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/sniffer.php idG=$Grupo[id]' | grep -v grep | awk '{print $2}'");
            if(!$tamesmo) { exec("php -f /var/www/html/cron/apoio/sniffer.php idG=$Grupo[id] > /dev/null &"); }
        // A cada semana
        }else if($Grupo['repetir'] == 3 && date('N') == 1 && $hora == "00" && $minuto == "00") {
            $tamesmo = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/sniffer.php idG=$Grupo[id]' | grep -v grep | awk '{print $2}'");
            if(!$tamesmo) { exec("php -f /var/www/html/cron/apoio/sniffer.php idG=$Grupo[id] > /dev/null &"); }
        // A cada mês
        }else if($Grupo['repetir'] == 4 && $dia == "01" && $hora == "00" && $minuto == "00") {
            $tamesmo = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/sniffer.php idG=$Grupo[id]' | grep -v grep | awk '{print $2}'");
            if(!$tamesmo) { exec("php -f /var/www/html/cron/apoio/sniffer.php idG=$Grupo[id] > /dev/null &"); }
        }
    }else {
        $tamesmo = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/sniffer.php idG=$Grupo[id]' | grep -v grep | awk '{print $2}'");
        if(!$tamesmo) { mysqli_query($db, "UPDATE GrupoMonitor SET status = '0' WHERE id = '".$Grupo['id']."';"); }
    }
}

mysqli_close($db);
exit(0);
?>