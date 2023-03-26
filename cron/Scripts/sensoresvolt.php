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
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $sensor1, $sensor2, $sensor3, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$sensor1|$sensor2|$sensor3|$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

if($vsnmp == 1) {
	$sensor1 = snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.17095.1.3.14.0", 1000000, 30);
	$sensor2 = snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.17095.1.3.15.0", 1000000, 30);
	$sensor3 = snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.17095.1.3.16.0", 1000000, 30);
}else if($vsnmp == 2) {
	$sensor1 = snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.17095.1.3.14.0", 1000000, 30);
	$sensor2 = snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.17095.1.3.15.0", 1000000, 30);
	$sensor3 = snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.17095.1.3.16.0", 1000000, 30);
}else if($vsnmp == 3) {
	$sensor1 = snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.17095.1.3.14.0", 1000000, 30);
	$sensor2 = snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.17095.1.3.15.0", 1000000, 30);
	$sensor3 = snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.17095.1.3.16.0", 1000000, 30);
}

$aux1 = explode(':',$sensor1);
$sensor1 = $aux1[1];

$aux2 = explode(':',$sensor2);
$sensor2 = $aux1[1];

$aux3 = explode(':',$sensor3);
$sensor3 = $aux3[1];

if(isset($sensor1) && isset($sensor2) && isset($sensor3)) {
	if($sensor1 == '0' && $sensor2 == '0' && $sensor3 == '0') {
		$statusAlert = 6;
		$StErro = 1;
	}else {
		if($StErro >= $falhas) {
			if($alertar == 1) {
				$statusAlert = 11;
			}else {
				$statusAlert = 12;
			}
		}else {
			$StErro = $StErro + 1;
			$statusAlert = 11;
		}
	}
}else {
	$statusAlert = 7;
	$StErro = 1;
}

insert($data, $data1, $id, $sensor1, $sensor2, $sensor3, $statusAlert, $StErro);

$valor1 = $sensor1;
$valor2 = $sensor2;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>