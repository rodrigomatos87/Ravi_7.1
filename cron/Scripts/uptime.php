#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$host = $_GET["ip"];
$community = $_GET["snmp"];
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

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $uptime, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$uptime|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeSNMP($string) {
    $what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ', 'days', 'day', 'year', 'years', 'month', 'months' );
    $by   = array( '', '', '', '', 'dias', 'dia', 'ano', 'anos', 'mês', 'meses' );
    return str_replace($what, $by, $string);
}



if($vsnmp == 1) {
	$uptime = sanitizeSNMP(snmpget("{$host}:{$porta}", $community, "1.3.6.1.2.1.1.3", 1000000, 5));
}else if($vsnmp == 2) {
	$uptime = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.2.1.1.3", 1000000, 5));
}else if($vsnmp == 3) {
	$uptime = sanitizeSNMP(snmp3_get("{$host}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.2.1.1.3", 1000000, 5));
}

if(!$uptime) {
	if($vsnmp == 1) {
		$uptime = sanitizeSNMP(snmpget("{$host}:{$porta}", $community, "1.3.6.1.2.1.1.3.0", 1000000, 5));
	}else if($vsnmp == 2) {
		$uptime = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.2.1.1.3.0", 1000000, 5));
	}else if($vsnmp == 3) {
		$uptime = sanitizeSNMP(snmp3_get("{$host}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.2.1.1.3.0", 1000000, 5));
	}
}

if(!$uptime) {
	if($vsnmp == 1) {
		$teste = snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.2.1.1.3", 1000000, 5);
	}else if($vsnmp == 2) {
		$teste = snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.2.1.1.3", 1000000, 5);
	}else if($vsnmp == 3) {
		$teste = snmp3_walk("{$host}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.2.1.1.3", 1000000, 5);
	}
	$uptime = sanitizeSNMP($teste[0]);
}

$aux = explode(')', $uptime);
$aux2 = explode('.', $aux[1]);
$uptime = $aux2[0];

if($uptime) {
	$statusAlert = 6;
}else {
	$statusAlert = 7;
}

insert($data, $data1, $id, $uptime, $statusAlert, $StErro);

$valor1 = $uptime;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>