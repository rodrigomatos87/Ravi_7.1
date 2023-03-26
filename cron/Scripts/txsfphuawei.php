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
$media1 = ajeita($_GET["media1"]);
$minPer = $_GET["minPer"];
$alertar = $_GET["alertar"];
$data = ''.$data.' '.$hora.'';

if(!$falhas) { $falhas = 1; }
if(!$porta) { $porta = 161; }
if(!$retries) { $retries = 3; }
if(!$timeout) { $timeout = 15; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $power, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$power|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function casas($string) {
	$what = array( 'STRING: ', 'INTEGER: ', '"' );
    $by   = array( '', '', '' );
    $valor = str_replace($what, $by, $string) / 100;
	$s = explode('.', $valor);
	if($s[1]) {
		$valor = $s[0] . '.' . substr($s[1], 0, 2);
	}else {
		$valor = $s[0];
	}
	return($valor);
}

function ajeita($string) {
	$s = explode('.', $string);
	if($s[1]) {
		$valor = $s[0] . '.' . substr($s[1], 0, 2);
	}else {
		$valor = $s[0];
	}
	return($valor);
}

if($vsnmp == 1) {
	$power = casas(snmpget("{$ip}:{$porta}", $snmp, ".1.3.6.1.4.1.2011.5.25.31.1.1.3.1.8." . $valor, 1000000, 30));
}else if($vsnmp == 2) {
	$power = casas(snmp2_get("{$ip}:{$porta}", $snmp, ".1.3.6.1.4.1.2011.5.25.31.1.1.3.1.8." . $valor, 1000000, 30));
}else if($vsnmp == 3) {
	$power = casas(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, ".1.3.6.1.4.1.2011.5.25.31.1.1.3.1.8." . $valor, 1000000, 30));
}

if(isset($power) && $power != "" && $power != 0) {
	if(isset($media1) && $media1 != "" && $power != $media1) {
        if($media1 < 0) {
            $minima = ajeita($media1 + ($media1 / 100 * $minPer));
            $media = ajeita($minima - ($minima / 100 * 10));
        }else {
            $minima = ajeita($media1 - ($media1 / 100 * $minPer));
            $media = ajeita($minima + ($minima / 100 * 10));
        }

		if($power <= $minima) {
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
		}else if($power < $media) {
			$statusAlert = 3;
		}else {
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

insert($data, $data1, $id, $power, $statusAlert, $StErro);

$valor1 = $power;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>