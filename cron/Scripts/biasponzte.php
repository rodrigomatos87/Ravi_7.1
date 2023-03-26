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

function insert( $data, $data1, $idSensor, $bias, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$bias|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeString($string) {
    $what = array( 'INTEGER:', ' ', 'dbm', 'STRING:', '"' );
    $by   = array( '','','','','' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$bias = sanitizeString(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.3902.1015.3.1.13.1.9.{$valor}", 1000000, 30));
}else if($vsnmp == 2) {
	$bias = sanitizeString(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.3902.1015.3.1.13.1.9.{$valor}", 1000000, 30));
}else if($vsnmp == 3) {
	$bias = sanitizeString(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.3902.1015.3.1.13.1.9.{$valor}", 1000000, 30));
}

if(isset($bias) && $bias != "") {
    $bias = $bias / 1000;
    $bias = number_format($bias, 1, '.', '');
	if(isset($media1)&& $media1 != "" && isset($maxPer)) {
		$maxima = $media1 + ($media1 / 100 * $maxPer);
		$media = $maxima - ($maxima / 100 * 10);
		if ($bias >= $maxima) {
			if($StErro >= $falhas) {
				if($alertar == 1) {
					$statusAlert = 3;
				}else {
					$statusAlert = 4;
				}
			}else {
				$StErro = $StErro + 1;
				$statusAlert = 3;
			}
		} else if ($bias < $maxima && $bias >= $media) {
			$statusAlert = 3;
		} else if ($bias < $media) {
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

insert($data, $data1, $id, $bias, $statusAlert, $StErro);

$valor1 = $bias;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>