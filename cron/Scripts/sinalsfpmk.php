#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$valor = $_GET["v"];
$banco = $_GET["banco"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$snmp = $_GET["snmp"];
$porta = $_GET["porta"];
$vsnmp = $_GET["vsnmp"];
$nivelsegsnmp = $_GET["nivelsegsnmp"];
$protocoloauthsnmp = $_GET["protocoloauthsnmp"];
$protocolocripsnmp = $_GET["protocolocripsnmp"];
$authsnmp = $_GET["authsnmp"];
$criptosnmp = $_GET["criptosnmp"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$media1 = $_GET["media1"];
$media2 = $_GET["media2"];
$maxPer = $_GET["maxPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $tx, $rx, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|erro|
	system("echo '|$data|$data1|$idSensor|$tx|$rx||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeString($string) {
    $what = array( ' ','Counter32:','<','>','STRING:',' ','pppoe-', 'IpAddress:', 'INTEGER:', '(1)', '(2)' );
    $by   = array( '','','','','','','','','','','' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$tx = sanitizeString(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.19.1.1.9.{$valor}", 1000000, 30));
	$rx = sanitizeString(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.19.1.1.10.{$valor}", 1000000, 30));
}else if($vsnmp == 2) {
	$tx = sanitizeString(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.19.1.1.9.{$valor}", 1000000, 30));
	$rx = sanitizeString(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.19.1.1.10.{$valor}", 1000000, 30));
}else if($vsnmp == 3) {
	$tx = sanitizeString(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.19.1.1.9.{$valor}", 1000000, 30));
	$rx = sanitizeString(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.19.1.1.10.{$valor}", 1000000, 30));
}

if(isset($tx) && isset($rx)) {
	$tx = $tx / 1000;
	$rx = $rx / 1000;
	
	if(isset($banco)) {
		$statusAlert = 6;
		$StErro = 1;
	}

	if(isset($media1) && isset($media2) && $media1 != $tx && $media2 != $rx && $media1 != "" && $media2 != "" && isset($maxPer) ) {
		$maxima1 = $media1 + ($media1 / 100 * $maxPer);
		$media1 = $maxima1 - ($maxima1 / 100 * 10);
		$maxima2 = $media2 + ($media2 / 100 * $maxPer);
		$media2 = $maxima2 - ($maxima2 / 100 * 10);
		$maxima1 = abs($maxima1);
		$media1 = abs($media1);
		$valor1 = abs($tx);
		$maxima2 = abs($maxima2);
		$media2 = abs($media2);
		$valor2 = abs($rx);
		if ($valor1 >= $maxima1 || $valor2 >= $maxima2) {
			if($StErro >= $falhas) {
				if($alertar == 1) {
					$statusAlert = 3;
				}else if($alertar == 2) {
					$statusAlert = 4;
				}else {
					$statusAlert = 3;
				}
			}else {
				$StErro = $StErro + 1;
				$statusAlert = 3;
			}
		}else if ($valor1 < $maxima1 && $valor1 >= $media1 || $valor2 < $maxima2 && $valor2 >= $media2) {
			$statusAlert = 3;
		} else if ($valor1 < $media1 && $valor2 < $media2) {
			$statusAlert = 6;
			$StErro = 1;
		}
	}else {
		$statusAlert = 6;
		$StErro = 1;
	}
}else {
	$statusAlert = 7;
}

insert($data, $data1, $id, $tx, $rx, $statusAlert, $StErro);

$valor1 = $tx;
$valor2 = $rx;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>