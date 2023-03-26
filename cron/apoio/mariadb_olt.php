#!/usr/bin/php
<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/

$idd = $_GET["id"];

include("/var/www/html/cron/apoio/conexao.php");

$resClientes = mysqli_query($db, "SELECT registroPlano FROM system");
$fetClientes = mysqli_fetch_array($resClientes);

if($fetClientes['registroPlano'] != '0') {

$path = "/var/www/html/ram/coletas/olt/";
$diretorio = dir($path);

$datasinc = date("Y-m-d H:i:s");

while($arquivo = $diretorio -> read()){
	if($arquivo != "." && $arquivo != "..") {
		$info = exec("cat $path$arquivo");
		$aux = explode('|', $info);
		// |$data|$idolt|$idonu|$idpon|$idslot|$idinterface|$provisionamento|$desc|$macnum|$rxpower|$txpower|$oltrx|$voltagem|$temperatura|$distancia|$BiasCurrent|$status|
		$data = $aux['1'];
		$idolt = $aux['2'];
		$idonu = $aux['3'];
		$idpon = $aux['4'];
		$idslot = $aux['5'];
		$idinterface = $aux['6'];
		$provisionamento = $aux['7'];
		$desc = $aux['8'];
		$macnum = $aux['9'];
		$nsnum = $aux['10'];
		$rxpower = $aux['11'];
		$txpower = $aux['12'];
		$oltrx = $aux['13'];
		$voltagem = $aux['14'];
		$temperatura = $aux['15'];
		$distancia = $aux['16'];
		$BiasCurrent = $aux['17'];
		$status = $aux['18'];
		$dataconnect = $aux['19'];
		$result = mysqli_query($db, "SELECT id FROM onus WHERE mac = '".$macnum."'");
		$datt = mysqli_fetch_array($result);
		if(!$datt['id']) {
			mysqli_query($db, "INSERT INTO onus (datt, idOLT, idonu, idpon, idslot, idinterface, provisionamento, descr, mac, ns, rxonu, txonu, oltrx, voltagem, temperatura, distancia, biascurrent, stats, dataconnect) VALUES ('".$data."', '".$idolt."', '".$idonu."', '".$idpon."',  '".$idslot."', '".$idinterface."', '".$provisionamento."', '".$desc."', '".$macnum."', '".$nsnum."', '".$rxpower."', '".$txpower."', '".$oltrx."', '".$voltagem."', '".$temperatura."', '".$distancia."', '".$BiasCurrent."', '".$status."', '".$dataconnect."')");
			//echo "INSERT INTO onus (datt, idOLT, idonu, idpon, idslot, idinterface, provisionamento, descr, mac, ns, rxonu, txonu, oltrx, voltagem, temperatura, distancia, biascurrent, stats, dataconnect) VALUES ('".$data."', '".$idolt."', '".$idonu."', '".$idpon."',  '".$idslot."', '".$idinterface."', '".$provisionamento."', '".$desc."', '".$macnum."', '".$nsnum."', '".$rxpower."', '".$txpower."', '".$oltrx."', '".$voltagem."', '".$temperatura."', '".$distancia."', '".$BiasCurrent."', '".$status."', '".$dataconnect."')<br>";
		}else {
			echo "entoru no else <br>";
			if($status == 1 || $status == 3) {
				mysqli_query($db, "UPDATE onus SET datt = '".$data."', datasinc = '".$datasinc."', idOLT = '".$idolt."', idonu = '".$idonu."', idpon = '".$idpon."', idslot = '".$idslot."', idinterface = '".$idinterface."', provisionamento = '".$provisionamento."', rxonu = '".$rxpower."', txonu = '".$txpower."', oltrx = '".$oltrx."', voltagem = '".$voltagem."', temperatura = '".$temperatura."', distancia = '".$distancia."', biascurrent = '".$BiasCurrent."', stats = '".$status."', dataconnect = '".$dataconnect."' WHERE mac = '".$macnum."';");
				mysqli_query($db, "INSERT INTO rxonus (datt, mac, rxonu) VALUES ('".$data."', '".$macnum."', '".$rxpower."')");
			}else {
				mysqli_query($db, "UPDATE onus SET datasinc = '".$datasinc."', idOLT = '".$idolt."', idonu = '".$idonu."', idpon = '".$idpon."', idslot = '".$idslot."', idinterface = '".$idinterface."', provisionamento = '".$provisionamento."', rxonu = '".$rxpower."', txonu = '".$txpower."', oltrx = '".$oltrx."', voltagem = '".$voltagem."', temperatura = '".$temperatura."', distancia = '".$distancia."', biascurrent = '".$BiasCurrent."', stats = '".$status."', dataconnect = '".$dataconnect."' WHERE mac = '".$macnum."';");
			}
			if($desc && $desc != "") { mysqli_query($db, "UPDATE onus SET descr = '".$desc."' WHERE mac = '".$macnum."';"); }
		}
		exec("rm -fr $path$arquivo");
	}
}
$diretorio -> close();
}

mysqli_query($db, "UPDATE olts SET datasinc = '".$datasinc."' WHERE id = '$idd';");
mysqli_query($db, "DELETE FROM rxonus WHERE datt < DATE_SUB(NOW(), INTERVAL 7 DAY)");
mysqli_query($db, "DELETE FROM onus WHERE idOLT = '$idd' AND datasinc < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
mysqli_query($db, "DELETE FROM onus WHERE mac = '';");
mysqli_close($db);

exec("php -f /var/www/html/cron/apoio/telegram_onu.php 2>&1 &");
?>