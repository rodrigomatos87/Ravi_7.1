#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$valor = $_GET["v"];
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
$retries = 5;
$timeout = 30;

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $valor1, $OperStatus, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro
	exec("echo '|$data|$data1|$idSensor|$valor1|$OperStatus||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeSNMP($string) {
    $what = array( 'INTEGER: ', 'STRING: ', 'Gauge32: ', 'Gauge64: ', 'Hex-', 'Hex-STRING: ', '"' );
    $by   = array( '', '', '', '', '', '', '' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -Ir -v1 -c " . addslashes($snmp) . " " . $ip . ":" . $porta . " 1.3.6.1.2.1.2.2.1.8." . $valor . " 2>/dev/null";
}else if($vsnmp == 2) {
	$cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -Ir -v2c -c " . addslashes($snmp) . " " . $ip . ":" . $porta . " 1.3.6.1.2.1.2.2.1.8." . $valor . " 2>/dev/null";
}else if($vsnmp == 3) {
	$cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -Ir -v3 -l " . $nivelsegsnmp . " -u " . addslashes($snmp) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $ip . ":" . $porta . " 1.3.6.1.2.1.2.2.1.8." . $valor . " 2>/dev/null";
}

//snmpget -Ost -r 5 -t 30  -Ir -v1 -c RaviMonitor 192.168.40.1 1.3.6.1.2.1.2.2.1.8.3

$stdno = 0;
$analysis = array();
exec ($cmd, $analysis, $stdno);
$stdno = (int)$stdno;

// Verifica se a conexão SNMP funcionou
if(!$stdno) {
    $a = explode('= ', $analysis[0]);
    $OperStatus = sanitizeSNMP($a[1]);
	if($OperStatus == 1) {
		$OperStatus = "Link";
		$valor1 = 1;
		$statusAlert = 6;
		$StErro = 1;
	}else {
		$OperStatus = "Down";
		$valor1 = 0;
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
	// Falha na coleta SNMP!
	$statusAlert = 7;
	$StErro = 1;
}

insert($data, $data1, $id, $valor1, $OperStatus, $statusAlert, $StErro);

$valor2 = $OperStatus;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>