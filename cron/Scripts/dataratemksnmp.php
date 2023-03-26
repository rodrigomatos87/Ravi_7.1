#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$limite = $_GET["v"];
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

function insert( $data, $data1, $idSensor, $tx, $rx, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$tx|$rx||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeSNMP($string) {
    $what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ' );
    $by   = array( '', '', '', '' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
	$tx = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.1.1.2.6", 1000000, 30));
    $rx = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.1.1.3.6", 1000000, 30));
}else if($vsnmp == 2) {
	$tx = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.1.1.2.6", 1000000, 30));
    $rx = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.1.1.3.6", 1000000, 30));
}else if($vsnmp == 3) {
	$tx = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.1.1.1.2.6", 1000000, 30));
    $rx = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.1.1.1.3.6", 1000000, 30));
}

if(!$tx && !$rx) {
	if($vsnmp == 1) {
		$tx = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.3.1.2.2", 1000000, 30));
		$rx = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.3.1.3.2", 1000000, 30));
	}else if($vsnmp == 2) {
		$tx = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.3.1.2.2", 1000000, 30));
		$rx = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.3.1.3.2", 1000000, 30));
	}else if($vsnmp == 3) {
		$tx = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.1.3.1.2.2", 1000000, 30));
		$rx = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.1.3.1.3.2", 1000000, 30));
	}
}

if(!$tx && !$rx) {
	if($vsnmp == 1) {
		$tx = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.3.1.2.1", 1000000, 30));
		$rx = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.3.1.3.1", 1000000, 30));
	}else if($vsnmp == 2) {
		$tx = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.3.1.2.1", 1000000, 30));
		$rx = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.14988.1.1.1.3.1.3.1", 1000000, 30));
	}else if($vsnmp == 3) {
		$tx = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.1.3.1.2.1", 1000000, 30));
		$rx = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.1.3.1.3.1", 1000000, 30));
	}
}

if($tx && $rx) {
	$tx = $tx / 1000000;
	$rx = $rx / 1000000;

	$t = explode('.', $tx);	
	if($t['1']) {
		$tx = $t['0'] . '.' . substr($t['1'], 0, 2);
	}else {
		$tx = $t['0'];
	}

	$r = explode('.', $rx);
	if($r['1']) {
		$rx = $r['0'] . '.' . substr($r['1'], 0, 2);
	}else {
		$rx = $r['0'];
	}

	if($limite) {
		$alerta = $limite + $limite * 20 / 100;
		if ($tx > $alerta || $rx > $alerta) {
			$statusAlert = 6;
		}else if ($tx > $limite && $tx <= $alerta || $rx > $limite && $rx <= $alerta) {
			$statusAlert = 9;
		}else if ($tx <= $limite || $rx <= $limite) {
			$statusAlert = 10;
		}
	}else {
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