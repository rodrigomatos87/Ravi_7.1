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
$valor = $_GET["v"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$media1 = $_GET["media1"];
$maxPer = $_GET["maxPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $ram, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$ram|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeString($string) {
    $what = array( 'INTEGER:', ' ', '.C', 'STRING:', '"' );
    $by   = array( '','','','','' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$ram = sanitizeString(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.2011.2.6.7.1.1.2.1.6.0.{$valor}", 1000000, 30));
}else if($vsnmp == 2) {
	$ram = sanitizeString(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.2011.2.6.7.1.1.2.1.6.0.{$valor}", 1000000, 30));
}else if($vsnmp == 3) {
	$ram = sanitizeString(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.2011.2.6.7.1.1.2.1.6.0.{$valor}", 1000000, 30));
}

if(isset($ram) && $ram != "") {
	$media = $maxPer - ($maxPer / 100 * 10);
	if($ram > $maxPer) {
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
	}else if ($ram >= $media && $ram <= $maxPer) {
		$statusAlert = 11;
	}else {
		$statusAlert = 6;
		$StErro = 1;
	}
}else {
	$statusAlert = 7;
}

insert($data, $data1, $id, $ram, $statusAlert, $StErro);

$valor1 = $ram;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>