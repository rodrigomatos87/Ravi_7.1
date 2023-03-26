#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$snmp = $_GET["snmp"];
$porta = $_GET["porta"];
$vsnmp = $_GET["vsnmp"];
$nivelsegsnmp = $_GET["nivelsegsnmp"];
$protocoloauthsnmp = $_GET["protocoloauthsnmp"];
$protocolocripsnmp = $_GET["protocolocripsnmp"];
$authsnmp = $_GET["authsnmp"];
$criptosnmp = $_GET["criptosnmp"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$maxPer = $_GET["maxPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $PercentRU, $valor2, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$PercentRU|$valor2||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeSNMP($string) {
    $what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ', '"' );
    $by   = array( '', '', '', '', '' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$totaR = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.3893.4.4.2.1.0", 1000000, 30));
}else if($vsnmp == 2) {
	$totaR = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.3893.4.4.2.1.0", 1000000, 30));
}else if($vsnmp == 3) {
	$totaR = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.3893.4.4.2.1.0", 1000000, 30));
}

if($totaR) {
	if($vsnmp == 1) {
		$usoR = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.3893.4.4.2.3.0", 1000000, 30));
	}else if($vsnmp == 2) {
		$usoR = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.3893.4.4.2.3.0", 1000000, 30));
	}else if($vsnmp == 3) {
		$usoR = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.3893.4.4.2.3.0", 1000000, 30));
	}
	$livreR = $totaR - $usoR;
	$PercentRU = ( $usoR * 100 ) / $totaR;

	if($totaR >= "1048576") {
		$totaR = ($totaR / 1024) / 1024;
		$tipoR = "Gb";
	}else if($totaR >= "1024") {
		$totaR = $totaR / 1024;
		$tipoR = "Mb";
	}else {
		$tipoR = "Kb";
	}

	if($usoR >= "1048576") {
		$usoR = ($usoR / 1024) / 1024;
		$tipoUR = "Gb";
	}else if($usoR >= "1024") {
		$usoR = $usoR / 1024;
		$tipoUR = "Mb";
	}else {
		$tipoUR = "Kb";
	}

	if($livreR >= "1048576") {
		$livreR = ($livreR / 1024) / 1024;
		$tipoLR = "Gb";
	}else if($livreR >= "1024") {
		$livreR = $livreR / 1024;
		$tipoLR = "Mb";
	}else {
		$tipoLR = "Kb";
	}

	$tR = explode('.', $totaR);
	$totaR = $tR['0'] . '.' . substr($tR['1'], 0, 2);
	$uR = explode('.', $usoR);
	$usoR = $uR['0'] . '.' . substr($uR['1'], 0, 2);
	$lR = explode('.', $livreR);
	$livreR = $lR['0'] . '.' . substr($lR['1'], 0, 2);

	$PRU = explode('.', $PercentRU);
	$PercentRU = $PRU['0'];
	$valor2 = ''.$totaR.' '.$tipoR.'/'.$usoR.' '.$tipoUR.'/'.$livreR.' '.$tipoLR.'';
	
	$media = $maxPer - ($maxPer / 100 * 10);

	if ($PercentRU > $maxPer) {
		if($StErro >= $falhas) {
			if($alertar == 1) {
				$statusAlert = 11;
			}else if($alertar == 2) {
				$statusAlert = 12;
			}else {
				$statusAlert = 11;
			}
		}else {
			$StErro = $StErro + 1;
			$statusAlert = 11;
		}
	}else if($PercentRU >= $media && $PercentRU <= $maxPer) {
		$statusAlert = 11;
	}else {
		$statusAlert = 6;
		$StErro = 1;
	}
}else {
	$statusAlert = 7;
	$StErro = 1;
}

insert($data, $data1, $id, $PercentRU, $valor2, $statusAlert, $StErro);

$valor1 = $PercentRU;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>