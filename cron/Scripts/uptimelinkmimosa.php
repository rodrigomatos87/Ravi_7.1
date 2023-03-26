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

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $LinkUptime, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$LinkUptime|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function dateString($string) {
    $what = array( 'day','days','month','months','hear', 'hears' );
    $by   = array( 'dia','dias','mês','meses','ano','anos' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$LinkUptime = snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.3.4.0", 1000000, 30);
}else if($vsnmp == 2) {
	$LinkUptime = snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.43356.2.1.2.3.4.0", 1000000, 30);
}else if($vsnmp == 3) {
	$LinkUptime = snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.3.4.0", 1000000, 30);
}

if($LinkUptime) {
	$StErro = 1;
	$aux = explode(')',$LinkUptime);
	$aux1 = explode('.',$aux[1]);
	$LinkUptime = dateString($aux1[0]);
	$statusAlert = 6;
}else {
	$statusAlert = 7;
}

insert($data, $data1, $id, $LinkUptime, $statusAlert, $StErro);

$valor1 = $LinkUptime;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>