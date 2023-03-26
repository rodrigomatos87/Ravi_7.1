#!/usr/bin/php
<?php
$partes = explode(':', date("H:i"));
$now = $partes[0] * 60 + $partes[1];

$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

function removehtml($string) {
	$what = array( '<strong>', '</strong>' );
	$by   = array( '', '' );
	return str_replace($what, $by, $string);
}

include("/var/www/html/cron/apoio/conexao.php");

$path = "/var/www/html/ram/coletas/valores/";
$diretorio = dir($path);

while($arquivo = $diretorio -> read()){
	if($arquivo != "." && $arquivo != "..") {
		$info = file_get_contents("/var/www/html/ram/coletas/valores/" . $arquivo);  
		$aux = explode('|', $info);
		$data = $aux['1'];
		$data1 = $aux['2'];
		$idSensor = $aux['3'];
		$valor1 = $aux['4'];
		$valor2 = $aux['5'];
		$valor3 = $aux['6'];
		$statusAlert = $aux['7'];
		$erro = $aux['8'];
		$ifSpeedAlert = $aux['9'];
		$ifDescr = $aux['10'];
		$ifAlias = $aux['11'];
		$mac = $aux['12'];
		if(!$erro) { $erro = 1; }

		$resultSensores = mysqli_query($db, "SELECT idDispositivo, statusAlert, valor, tag, nome, banco, media1, unidade, display, ifSpeed FROM Sensores WHERE id = '$idSensor';");
		$detalhes = mysqli_fetch_array($resultSensores);
		$idDispositivo = $detalhes['idDispositivo'];
		$media1 = $detalhes['media1'];
		$valor = $detalhes['valor'];
		$tag = $detalhes['tag'];
		$nome = $detalhes['nome'];
		$banco = $detalhes['banco'];
		$unidade = $detalhes['unidade'];
		$display = $detalhes['display'];
		$statusAlertdb = $detalhes['statusAlert'];
		$ifSpeed = $detalhes['ifSpeed'];
		if($statusAlertdb == 2) { $statusAlert = 2; }

		exec("echo '|$statusAlert|$valor1|$valor2|$valor3|$tag|$nome|$banco|$unidade|$display|' > /var/www/html/ram/dispositivos/sensores/$idSensor");

		mysqli_query($db, "UPDATE Sensores SET valor1 = '".$valor1."', valor2 = '".$valor2."', valor3 = '".$valor3."', statusAlert = '".$statusAlert."', erro = '".$erro."' WHERE id = '$idSensor';");

		if($tag == "trafegosnmp") {
			if($ifSpeed) {
				mysqli_query($db, "UPDATE Sensores SET descr = '".$ifDescr."', alias = '".$ifAlias."', ifSpeedAlert = '".$ifSpeedAlert."', mac = '".$mac."' WHERE id = '$idSensor';");
			}else {
				mysqli_query($db, "UPDATE Sensores SET descr = '".$ifDescr."', alias = '".$ifAlias."', ifSpeedAlert = '".$ifSpeedAlert."', ifSpeed = '".$ifSpeedAlert."', mac = '".$mac."' WHERE id = '$idSensor';");
			}
		}

		mysqli_query($db, "INSERT INTO Log2h (data, idSensor, valor1, valor2, valor3, statusAlert) VALUES ('".$data."', '".$idSensor."', '".$valor1."', '".$valor2."', '".$valor3."', '".$statusAlert."')");
		mysqli_query($db, "INSERT INTO Log24h (data, idSensor, valor1, valor2, valor3, statusAlert) VALUES ('".$data."', '".$idSensor."', '".$valor1."', '".$valor2."', '".$valor3."', '".$statusAlert."')");
		if($data1 == 00 || $data1 == 05 || $data1 == 10 || $data1 == 15 || $data1 == 20 || $data1 == 25 || $data1 == 30 || $data1 == 35 || $data1 == 40 || $data1 == 45 || $data1 == 50 || $data1 == 55) {
			$aux1 = explode(':', $data);
			if($aux1[2] == 00) { mysqli_query($db, "INSERT INTO Log30d (data, idSensor, valor1, valor2, valor3, statusAlert) VALUES ('".$data."', '".$idSensor."', '".$valor1."', '".$valor2."', '".$valor3."', '".$statusAlert."')"); }
		}
		if($data1 == 00 || $data1 == 30) { 
			$aux1 = explode(':', $data);
			if($aux1[2] == 00) { mysqli_query($db, "INSERT INTO Log1a (data, idSensor, valor1, valor2, valor3, statusAlert) VALUES ('".$data."', '".$idSensor."', '".$valor1."', '".$valor2."', '".$valor3."', '".$statusAlert."')"); }
		}
		
		unlink("/var/www/html/ram/coletas/valores/" . $arquivo);
	}
}

$diretorio -> close();

mysqli_close($db);

$pidexec = exec("ps aux | grep 'envio_alertas_resolvido.php' | grep -v grep | awk '{print $2}'");
if(!$pidexec) { exec("php -f /var/www/html/cron/apoio/envio_alertas_resolvido.php &"); }

exec("php -f /var/www/html/cron/apoio/AtualizaResumoGrupos.php &");
exec("php -f /var/www/html/cron/apoio/sincArquivosBkp.php &");
sleep(5);
exec("php -f /var/www/html/cron/apoio/dispositivos_ram.php &");
?>
