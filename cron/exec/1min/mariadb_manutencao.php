#!/usr/bin/php
<?php

$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

include("/var/www/html/cron/apoio/conexao.php");

// Excluindo Logs de sensores que foram deletados
$consultadb = mysqli_query($db, "SELECT idSensor FROM Logalertas GROUP BY idSensor");
while($Sensores = mysqli_fetch_array($consultadb)) {
	$Pesquisa = mysqli_query($db, "SELECT count(*) AS deletar FROM Sensores WHERE id = '$Sensores[idSensor]';");
	$dat = mysqli_fetch_array($Pesquisa);
	if($dat['deletar'] == 0) {
		mysqli_query($db, "DELETE FROM Logalertas WHERE idSensor = '$Sensores[idSensor]';");
		mysqli_query($db, "DELETE FROM Log2h WHERE idSensor = '$Sensores[idSensor]';");
		mysqli_query($db, "DELETE FROM Log24h WHERE idSensor = '$Sensores[idSensor]';");
		mysqli_query($db, "DELETE FROM Log30d WHERE idSensor = '$Sensores[idSensor]';");
		mysqli_query($db, "DELETE FROM Log1a WHERE idSensor = '$Sensores[idSensor]';");
	}
}

// Excluindo o excedente por data
mysqli_query($db, "DELETE FROM Log2h WHERE data < DATE_SUB(NOW(), INTERVAL 120 MINUTE)");
mysqli_query($db, "DELETE FROM Log24h WHERE data < DATE_SUB(NOW(), INTERVAL 1440 MINUTE)");
mysqli_query($db, "DELETE FROM Log30d WHERE data < DATE_SUB(NOW(), INTERVAL 720 HOUR)");
mysqli_query($db, "DELETE FROM Log1a WHERE data < DATE_SUB(NOW(), INTERVAL 1 YEAR)");
mysqli_query($db, "DELETE FROM Logalertas WHERE data < DATE_SUB(NOW(), INTERVAL 10 DAY)");
mysqli_query($db, "DELETE FROM ResumoSensores WHERE data < DATE_SUB(NOW(), INTERVAL 6 HOUR)");
mysqli_query($db, "DELETE FROM LogDNS WHERE data < DATE_SUB(NOW(), INTERVAL 60 MINUTE)");
mysqli_query($db, "DELETE FROM rxonus WHERE datt < DATE_SUB(NOW(), INTERVAL 7 DAY)");
mysqli_query($db, "DELETE FROM onus WHERE datasinc < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
mysqli_query($db, "DELETE FROM onus WHERE mac = '';");
$consultaPPPoE = mysqli_query($db, "SELECT dadoshistoricos_pppoe FROM system;");
$PPPoE = mysqli_fetch_array($consultaPPPoE);
if($PPPoE['dadoshistoricos_pppoe'] == '24' || $PPPoE['dadoshistoricos_pppoe'] == '48') {
	mysqli_query($db, "DELETE FROM PingPPPoE WHERE data < DATE_SUB(NOW(), INTERVAL ".$PPPoE['dadoshistoricos_pppoe']." HOUR)");
	mysqli_query($db, "DELETE FROM trafegoPPPoE WHERE data < DATE_SUB(NOW(), INTERVAL ".$PPPoE['dadoshistoricos_pppoe']." HOUR)");
	mysqli_query($db, "DELETE FROM LogPPPoE WHERE datadesconect < DATE_SUB(NOW(), INTERVAL ".$PPPoE['dadoshistoricos_pppoe']." HOUR)");
	mysqli_query($db, "DELETE FROM usersPPPoE WHERE datasinc < DATE_SUB(NOW(), INTERVAL ".$PPPoE['dadoshistoricos_pppoe']." HOUR)");
}else if($PPPoE['dadoshistoricos_pppoe'] == '7d') {
	mysqli_query($db, "DELETE FROM PingPPPoE WHERE data < DATE_SUB(NOW(), INTERVAL 7 DAY)");
	mysqli_query($db, "DELETE FROM trafegoPPPoE WHERE data < DATE_SUB(NOW(), INTERVAL 7 DAY)");
	mysqli_query($db, "DELETE FROM LogPPPoE WHERE datadesconect < DATE_SUB(NOW(), INTERVAL 7 DAY)");
	mysqli_query($db, "DELETE FROM usersPPPoE WHERE datasinc < DATE_SUB(NOW(), INTERVAL 7 DAY)");
}else if($PPPoE['dadoshistoricos_pppoe'] == '30d') {
	mysqli_query($db, "DELETE FROM PingPPPoE WHERE data < DATE_SUB(NOW(), INTERVAL 30 DAY)");
	mysqli_query($db, "DELETE FROM trafegoPPPoE WHERE data < DATE_SUB(NOW(), INTERVAL 30 DAY)");
	mysqli_query($db, "DELETE FROM LogPPPoE WHERE datadesconect < DATE_SUB(NOW(), INTERVAL 30 DAY)");
	mysqli_query($db, "DELETE FROM usersPPPoE WHERE datasinc < DATE_SUB(NOW(), INTERVAL 30 DAY)");
}
mysqli_close($db);
?>