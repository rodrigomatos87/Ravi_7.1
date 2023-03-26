#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$mib = $_GET["v"];
$multiplicar = $_GET["m"];
$dividir = $_GET["d"];
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
$maxPer = $_GET["maxPer"];
$minPer = $_GET["minPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }
if(!$multiplicar) { $multiplicar = 1; }
if(!$dividir) { $dividir = 1; }

function insert( $data, $data1, $idSensor, $snmpcustom, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$snmpcustom|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeSNMP($string) {
    $what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Gauge64: ', 'Counter32: ', 'Counter64: ', 'IpAddress: ', '"' );
    $by   = array( '', '', '', '', '', '', '', '' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$snmpcustom = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "{$mib}", 1000000, 30));
}else if($vsnmp == 2) {
	$snmpcustom = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "{$mib}", 1000000, 30));
}else if($vsnmp == 3) {
	$snmpcustom = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "{$mib}", 1000000, 30));
}

if(!isset($snmpcustom)) {
	if($vsnmp == 1) {
		$TestCustom = sanitizeSNMP(snmpwalk("{$ip}:{$porta}", $snmp, "{$mib}", 1000000, 30));
	}else if($vsnmp == 2) {
		$TestCustom = sanitizeSNMP(snmp2_walk("{$ip}:{$porta}", $snmp, "{$mib}", 1000000, 30));
	}else if($vsnmp == 3) {
		$TestCustom = sanitizeSNMP(snmp3_walk("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "{$mib}", 1000000, 30));
	}
	$snmpcustom = $TestCustom[0];
}

if(isset($snmpcustom) && $snmpcustom != "") {
	$snmpcustom = $snmpcustom * $multiplicar / $dividir;
	if(isset($media1) && $media1 != "" && isset($maxPer) && isset($minPer)) {
        $maxima = $media1 + ($media1 / 100 * $maxPer);
        $media = $maxima - ($maxima / 100 * 10);
		$minima = $media1 - ($media1 / 100 * $minPer);

		if ($snmpcustom >= $maxima ) {
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
        }else if ($snmpcustom < $maxima && $snmpcustom >= $media) {
            $statusAlert = 3;
        }else if ($snmpcustom < $media && $snmpcustom >= $minima) {
            $statusAlert = 6;
            $StErro = 1;
        }else if ($snmpcustom < $minima) {
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
        }
	}else {
		$statusAlert = 6;
		$StErro = 1;
	}
}else {
	$statusAlert = 7;
}

insert($data, $data1, $id, $snmpcustom, $statusAlert, $StErro);

$valor1 = $snmpcustom;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>