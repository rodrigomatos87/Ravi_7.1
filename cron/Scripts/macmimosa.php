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
$minPer = $_GET["minPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $mimosaTxMacTotal, $mimosaRxMacTotal, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$mimosaTxMacTotal|$mimosaRxMacTotal||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function porcentagem_xn ( $porcentagem, $total ) {
	return ( $porcentagem / 100 ) * $total;
}

function sanitizeSNMP($string) {
    $what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ' );
    $by   = array( '', '', '', '' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$txphy1 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.1", 1000000, 30));
	$txphy2 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.2", 1000000, 30));
	$txphy3 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.3", 1000000, 30));
	$txphy4 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.4", 1000000, 30));
	$rxphy1 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.1", 1000000, 30));
	$rxphy2 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.2", 1000000, 30));
	$rxphy3 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.3", 1000000, 30));
	$rxphy4 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.4", 1000000, 30));
}else if($vsnmp == 2) {
	$txphy1 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.1", 1000000, 30));
	$txphy2 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.2", 1000000, 30));
	$txphy3 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.3", 1000000, 30));
	$txphy4 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.4", 1000000, 30));
	$rxphy1 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.1", 1000000, 30));
	$rxphy2 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.2", 1000000, 30));
	$rxphy3 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.3", 1000000, 30));
	$rxphy4 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.4", 1000000, 30));
}else if($vsnmp == 3) {
	$txphy1 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.1", 1000000, 30));
	$txphy2 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.2", 1000000, 30));
	$txphy3 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.3", 1000000, 30));
	$txphy4 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.4", 1000000, 30));
	$rxphy1 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.1", 1000000, 30));
	$rxphy2 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.2", 1000000, 30));
	$rxphy3 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.3", 1000000, 30));
	$rxphy4 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.4", 1000000, 30));
}

if($txphy1 && $txphy2 && $rxphy1 && $rxphy2) {
	$StErro = 1;
	$mimosaTxPhyTotal = $txphy1 + $txphy2 + $txphy3 + $txphy4;
	$mimosaRxPhyTotal = $rxphy1 + $rxphy2 + $rxphy3 + $rxphy4;
	$tx = porcentagem_xn(60, $mimosaTxPhyTotal);
	$rx = porcentagem_xn(60, $mimosaRxPhyTotal);
	if(isset($media1) && isset($media2) && $media1 != "" && $media2 != "" && isset($minPer)) {
		$maxima1 = $media1 - ($media1 / 100 * $minPer);
		$media1 = $maxima1 + ($maxima1 / 100 * 10);
		$maxima2 = $media2 - ($media2 / 100 * $minPer);
		$media2 = $maxima2 + ($maxima2 / 100 * 10);
		if ($tx <= $maxima1 || $rx <= $maxima2) {
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
		} else if ($tx > $maxima1 && $tx <= $media1 || $rx > $maxima2 && $rx <= $media2) {
			$statusAlert = 3;
		} else if ($tx > $media1 || $rx > $media2) {
			$statusAlert = 6;
			$StErro = 1;
		}
	}else {
		$statusAlert = 6;
		$StErro = 1;
	}
}else {
	$statusAlert = 7;
	$StErro = 1;
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