#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$host = $_GET["ip"];
$valor = $_GET["v"];
$banco = $_GET["banco"];
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
$alertar = $_GET["alertar"];
$retries = 5;
$timeout = 30;

$data = ''.$data.' '.$hora.'';

if(!$porta) { $porta = 161; }
if(!$vsnmp) { $vsnmp = 2; }

function insert( $data, $data1, $idSensor, $velocidade, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$velocidade|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeSNMP($string) {
    $what = array( 'INTEGER: ', 'STRING: ', 'Gauge32: ', 'Gauge64: ', 'Hex-', 'Hex-STRING: ', '"' );
    $by   = array( '', '', '', '', '', '', '' );
    return str_replace($what, $by, $string);
}

function gravidade($alertar, $StErro, $falhas, $alerta, $critico) {
    if($StErro >= $falhas) {
        if($alertar == 1) {
            $statusAlert = $alerta;
        }else {
            $statusAlert = $critico;
        }
    }else {
        $StErro = $StErro + 1;
        $statusAlert = $alerta;
    }
    return array($statusAlert, $StErro);
}

$oids = array();
$oids[] = ".1.3.6.1.2.1.2.2.1.8." . $valor;                   // ifOperStatus
$oids[] = ".1.3.6.1.2.1.2.2.1.5." . $valor;                   // ifSpeed
if($vsnmp == 2) { 
	$oids[] = ".1.3.6.1.2.1.31.1.1.1.15" . $valor;            // ifHightSpeed
}
$oidspart = implode(' ', $oids);

if($vsnmp == 1) {
	$cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
}else if($vsnmp == 2) {
	$cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
}else if($vsnmp == 3) {
	$cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
}

$stdno = 0;
$analysis = array();
exec ($cmd, $analysis, $stdno);
$stdno = (int)$stdno;

// Verifica se a conexão SNMP funcionou
if(!$stdno) {
	$ifSpeed = 0;
	$ifHightSpeed = 0;
	if(count($oids) == 2 && count($analysis) == 2) {
		$a = explode('= ', $analysis[0]);
		$ifOperStatus = sanitizeSNMP($a[1]);
		$b = explode('= ', $analysis[1]);
		$ifSpeed = sanitizeSNMP($b[1]);
	}else if(count($analysis) == 3) {
		$a = explode('= ', $analysis[0]);
		$ifOperStatus = sanitizeSNMP($a[1]);
		$b = explode('= ', $analysis[1]);
		$ifSpeed = sanitizeSNMP($b[1]);
		$c = explode('= ', $analysis[2]);
		$ifHightSpeed = sanitizeSNMP($c[1]);
	}else {
		$a = explode('= ', $analysis[0]);
		$b = explode('= ', $analysis[1]);
		$c = explode('= ', $analysis[2]);
		if(preg_match('/ifOperStatus./', $a[0])) { $ifOperStatus = sanitizeSNMP($a[1]); }
		if(preg_match('/ifOperStatus./', $b[0])) { $ifOperStatus = sanitizeSNMP($b[1]); }
		if(preg_match('/ifOperStatus./', $c[0])) { $ifOperStatus = sanitizeSNMP($c[1]); }
		if(preg_match('/ifSpeed./', $a[0])) { $ifSpeed = sanitizeSNMP($a[1]); }
		if(preg_match('/ifSpeed./', $b[0])) { $ifSpeed = sanitizeSNMP($b[1]); }
		if(preg_match('/ifSpeed./', $c[0])) { $ifSpeed = sanitizeSNMP($c[1]); }
		if(preg_match('/ifHightSpeed./', $a[0])) { $ifHightSpeed = sanitizeSNMP($a[1]); }
		if(preg_match('/ifHightSpeed./', $b[0])) { $ifHightSpeed = sanitizeSNMP($b[1]); }
		if(preg_match('/ifHightSpeed./', $c[0])) { $ifHightSpeed = sanitizeSNMP($c[1]); }
	}

	// Verifica se a interface está operante
	if($ifOperStatus == 1 || $ifOperStatus == 6) {
		if($ifHightSpeed) {
			$velocidade = $ifHightSpeed / 1000000;
		}else if($ifSpeed) {
			$velocidade = $ifSpeed / 1000000;
		}
		$ex = explode(".", $velocidade);
		$velocidade = $ex[0];
		$aux2 = explode('.', $banco);
		
		if($velocidade >= $aux2[0]) {
			$statusAlert = 6;
			$StErro = 1;
		}else {
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
		// Interface inoperante!
		$busca = gravidade(1, $StErro, $falhas, 3, 4);
		$statusAlert = $busca[0];
		$StErro = $busca[1];
	}
}else {
	// Falha na coleta SNMP!
	$statusAlert = 7;
	$StErro = 1;
}

insert($data, $data1, $id, $velocidade, $statusAlert, $StErro);

$valor1 = $velocidade;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>