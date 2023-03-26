<?php
$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

// [bd Sensores]     statusAlert: [ off 1 | pausado 2 | alerta 3 | erro 4 | novo 5 | ok 6 | off por 60s 7 / desconect 8 / alerta limitador 9 / erro limitador 10 ]

include("/var/www/html/cron/apoio/conexao.php");

mysqli_query($db, "UPDATE Sensores SET statusAlert = '2' WHERE pausar = '1';");

$BuscaOFF = mysqli_query($db, "SELECT COUNT(*) AS total FROM Sensores WHERE statusAlert = '1'");
$print1 = mysqli_fetch_array($BuscaOFF);
$Off = $print1['total'];

$BuscaPAUSE = mysqli_query($db, "SELECT COUNT(*) AS total FROM Sensores WHERE statusAlert = '2'");
$print2 = mysqli_fetch_array($BuscaPAUSE);
$Pausado = $print2['total'];

$BuscaALERT = mysqli_query($db, "SELECT COUNT(*) AS total FROM Sensores WHERE statusAlert = '3' OR statusAlert = '7' OR statusAlert = '9' OR statusAlert = '11' OR statusAlert = '13'");
$print3 = mysqli_fetch_array($BuscaALERT);
$Alerta = $print3['total'];

$BuscaERRO = mysqli_query($db, "SELECT COUNT(*) AS total FROM Sensores WHERE statusAlert = '4' OR statusAlert = '8' OR statusAlert = '10' OR statusAlert = '12' OR statusAlert = '14'");
$print4 = mysqli_fetch_array($BuscaERRO);
$Erro = $print4['total'];

$BuscaNOVO = mysqli_query($db, "SELECT COUNT(*) AS total FROM Sensores WHERE statusAlert = '5'");
$print5 = mysqli_fetch_array($BuscaNOVO);
$Novo = $print5['total'];

$BuscaOK = mysqli_query($db, "SELECT COUNT(*) AS total FROM Sensores WHERE statusAlert = '6'");
$print6 = mysqli_fetch_array($BuscaOK);
$Ok = $print6['total'];

$totalSensores = $Off + $Pausado + $Alerta + $Erro + $Novo + $Ok;

mysqli_query($db, "insert into ResumoSensores (off, pausado, ok, alerta, erro, total, novos) values ('".$Off."', '".$Pausado."', ".$Ok.", ".$Alerta.", ".$Erro.", ".$totalSensores.", ".$Novo.")");

mysqli_close($db);
?>