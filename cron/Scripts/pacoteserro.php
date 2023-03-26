#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$host = $_GET["ip"];
$valor = $_GET["v"];
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
$media1 = $_GET["media1"];
$alertar = $_GET["alertar"];
$sleep = 5;
$retries = 5;
$timeout = 30;

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $Total, $errosIn, $errosOut, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$Total|$errosIn|$errosOut|$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeSNMP($string) {
    $what = array( 'INTEGER: ', 'Hex-STRING: ', 'STRING: ', 'Gauge32: ', 'Gauge64: ', 'Counter32: ', 'Counter64: ', 'Hex-', '"' );
    $by   = array( '', '', '', '', '', '', '' );
    return str_replace($what, $by, $string);
}

$oids = array();
$oids[] = ".1.3.6.1.2.1.2.2.1.14." . $valor;                   // ifInErrors
$oids[] = ".1.3.6.1.2.1.2.2.1.20." . $valor;                   // ifOutErrors

$oidspart = implode(' ', $oids);

if($vsnmp == 1) {
	$cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
}else if($vsnmp == 2) {
	$cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
}else if($vsnmp == 3) {
	$cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
}

$stdno = 0;
$analysis1 = array();
exec ($cmd, $analysis1, $stdno);
$stdno = (int)$stdno;

// Verifica se a conexão SNMP funcionou
if(!$stdno) {
	$a = explode('= ', $analysis1[0]);
	$errosIn1 = sanitizeSNMP($a[1]);
	$b = explode('= ', $analysis1[1]);
	$errosOut1 = sanitizeSNMP($b[1]);
	sleep($sleep);
	$stdno = 0;
	$analysis2 = array();
	exec ($cmd, $analysis2, $stdno);
	$stdno = (int)$stdno;
	if(!$stdno) {
		$c = explode('= ', $analysis2[0]);
		$errosIn2 = sanitizeSNMP($c[1]);
		$d = explode('= ', $analysis2[1]);
		$errosOut2 = sanitizeSNMP($d[1]);

		$errosIn = $errosIn2 - $errosIn1; 
		$errosOut = $errosOut2 - $errosOut1;
		$Total = $errosIn + $errosOut;
		
		if($Total == 0) {
			$statusAlert = 6;
			$StErro = 1;
		}else {
			if(isset($media1) && $media1 != "" && isset($maxPer)) {
				$maxima = $media1 + ($media1 / 100 * $maxPer);
				$media = $maxima - ($maxima / 100 * 10);
				if ($total >= $maxima) {
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
				} else if ($total < $maxima && $total >= $media) {
					$statusAlert = 3;
				} else if ($total < $media) {
					$statusAlert = 6;
					$StErro = 1;
				}
			}else {
				$statusAlert = 6;
				$StErro = 1;
			}
		}
	}else {
		// Falha na coleta SNMP!
		$statusAlert = 7;
		$StErro = 1;
	}
}else {
	// Falha na coleta SNMP!
	$statusAlert = 7;
	$StErro = 1;
}

insert($data, $data1, $id, $Total, $errosIn, $errosOut, $statusAlert, $StErro);

$valor1 = $Total;
$valor2 = $errosIn;

if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}
?>