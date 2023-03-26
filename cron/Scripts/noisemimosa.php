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
$media1 = $_GET["media1"];
$media2 = $_GET["media2"];
$maxPer = $_GET["maxPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert ( $data, $data1, $idSensor, $noise1, $noise2, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$noise1|$noise2||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeSNMP($string) {
    $what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ' );
    $by   = array( '', '', '', '' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$noise1 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.1", 1000000, 30));
	$noise2 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.3", 1000000, 30));
}else if($vsnmp == 2) {
	$noise1 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.1", 1000000, 30));
	$noise2 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.3", 1000000, 30));
}else if($vsnmp == 3) {
	$noise1 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.1", 1000000, 30));
	$noise2 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.3", 1000000, 30));
}

if($noise1 && $noise2) {
	$noise1 = $noise1 / 10;
	$noise2 = $noise2 / 10;
	if(isset($media1) && isset($media2) && $media1 != "" && $media2 != "" && $maxPer) {
		$maxima1 = $media1 + ($media1 / 100 * $maxPer);
		$media1 = $maxima1 - ($maxima1 / 100 * 5);
		$maxima2 = $media2 + ($media2 / 100 * $maxPer);
		$media2 = $maxima2 - ($maxima2 / 100 * 5);
		if($noise1 <= $maxima1 || $noise2 <= $maxima2) {
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
		}else if($noise1 > $maxima1 && $noise1 <= $media1 || $noise2 > $maxima2 && $noise2 <= $media2) {
			$statusAlert = 3;
		}else {
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

insert($data, $data1, $id, $noise1, $noise2, $statusAlert, $StErro);

$valor1 = $noise1;
$valor2 = $noise2;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>