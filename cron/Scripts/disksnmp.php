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
$maxPer = $_GET["maxPer"];
$minPer = $_GET["minPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $PercentU, $valor2, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$PercentU|$valor2||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeString($string) {
    $what = array( '<','>','STRING:',' ','pppoe-', 'IpAddress:', 'INTEGER:' );
    $by   = array( '','','','','','','' );
    return str_replace($what, $by, $string);
}

function numerico($string) {
    $what = array( ',', ' ' );
    $by   = array( '.', '' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$total = sanitizeString(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.2.1.25.2.3.1.5.{$valor}", 1000000, 30));
	$usado = sanitizeString(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.2.1.25.2.3.1.6.{$valor}", 1000000, 30));
	$unialoc = sanitizeString(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.2.1.25.2.3.1.4.{$valor}", 1000000, 30));	
}else if($vsnmp == 2) {
	$total = sanitizeString(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.2.1.25.2.3.1.5.{$valor}", 1000000, 30));
	$usado = sanitizeString(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.2.1.25.2.3.1.6.{$valor}", 1000000, 30));
	$unialoc = sanitizeString(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.2.1.25.2.3.1.4.{$valor}", 1000000, 30));
}else if($vsnmp == 3) {
	$total = sanitizeString(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.2.1.25.2.3.1.5.{$valor}", 1000000, 30));
	$usado = sanitizeString(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.2.1.25.2.3.1.6.{$valor}", 1000000, 30));
	$unialoc = sanitizeString(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.2.1.25.2.3.1.4.{$valor}", 1000000, 30));
}

$totalOrig = numerico($total);
$usadoOrig = numerico($usado);
$unialoc = numerico($unialoc);

if($totalOrig && $usadoOrig && $unialoc) {
	$sizeT = $totalOrig * $unialoc;
	$total = ($sizeT / 1024) / 1024;

	if($total >= "1024") {
		$total = (($sizeT / 1024) / 1024) / 1024;
		$tipoT = "Gb";
	}else {
		$tipoT = "Mb";
	}

	$sizeU = $usadoOrig * $unialoc;
	$usado = ($sizeU / 1024) / 1024;

	if($usado >= "1024") {
		$usado = (($sizeU / 1024) / 1024) / 1024;
		$tipoU = "Gb";
	}else {
		$tipoU = "Mb";
	}

	$livreOrig = $sizeT - $sizeU;
	$livre = ($livreOrig / 1024) / 1024;

	if($livre >= "1024") {
		$livre = (($livreOrig / 1024) / 1024) / 1024;
		$tipoL = "Gb";
	}else {
		$tipoL = "Mb";
	}

	$t = explode('.', $total);
	if($t['1']) {
		$total = $t['0'] . '.' . substr($t['1'], 0, 2);
	}else {
		$total = $t['0'];
	}

	$u = explode('.', $usado);
	if($u['1']) {
		$usado = $u['0'] . '.' . substr($u['1'], 0, 2);
	}else {
		$usado = $u['0'];
	}

	$l = explode('.', $livre);
	if($l['1']) {
		$livre = $l['0'] . '.' . substr($l['1'], 0, 2);
	}else {
		$livre = $l['0'];
	}

	$PercentU = ( $usadoOrig * 100 ) / $totalOrig;
	$PercentU = number_format($PercentU, 0);

	$total = ''.$total.' '.$tipoT.'';
	$usado = ''.$usado.' '.$tipoU.'';
	$livre = ''.$livre.' '.$tipoL.'';

	$valor2 = ''.$total.'/'.$usado.'/'.$livre.'';

	if ($PercentU >= $maxPer) {
		if($StErro >= $falhas) {
			if($alertar == 1) {
				$statusAlert = 3;
			}else if($alertar == 2) {
				if ($minPer && $PercentU >= $minPer) { $statusAlert = 4; }else { $statusAlert = 3; }
				//$statusAlert = 4;
			}else {
				$statusAlert = 3;
			}
		}else {
			$StErro = $StErro + 1;
			$statusAlert = 3;
		}
	//} else if ($PercentU >= $minPer) {
	//} else if ($PercentU >= $minPer) {
	//	$statusAlert = 3;
	} else {
		$statusAlert = 6;
		$StErro = 1;
	}
}else {
	$statusAlert = 7;
}

insert($data, $data1, $id, $PercentU, $valor2, $statusAlert, $StErro);

$valor1 = $PercentU;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}
?>