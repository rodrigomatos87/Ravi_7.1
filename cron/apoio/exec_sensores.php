<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

$pidexec = exec("ps aux | grep 'GeraBackupRavi.sh' | grep -v grep");
if(!$pidexec) { echo 'entrou'; }else { echo 'n'; }

$log_file = '/var/log/ravi.log';

$dir = '/opt/Ravi';
if (!is_dir($dir)) {
    if (!mkdir($dir, 0777, true)) {
		$message = date('Y-m-d H:i:s') . ' - Não foi possível criar o diretório /opt/Ravi';
        file_put_contents($log_file, $message . "\n", FILE_APPEND);
    }
}

$cronograma = $_GET["valor"];
//$data = date("Y-m-d");
//$hora = date("H:i") . ":" . $_GET["time"];

$data = date("Y-m-d H:i") . ":" . $_GET["time"];

// Esperar caso seja necessário para cumprir com o cronograma utilizado
if($_GET["sleep"]) { sleep($_GET["sleep"]); }

include("/var/www/html/cron/apoio/conexao.php");

$PesquisaSensor = mysqli_query($db, "SELECT id, tag FROM Sensores WHERE cronograma = '".$cronograma."' AND idDispositivo = '" . $_GET['id'] . "';");
if(mysqli_num_rows($PesquisaSensor)) {
	$sondas_snmp = array();
	while($sensor = mysqli_fetch_array($PesquisaSensor)) {
		$sensorSys = mysqli_query($db, "SELECT protocolo FROM sensores_disp WHERE tag = '".$sensor['tag']."';");
		$sys = mysqli_fetch_array($sensorSys);
		if($sys['protocolo'] == 1) {
			// SNMP
			$sondas_snmp[] = $sensor['id'];
		}else if($sys['protocolo'] == 2) {
			// ICMP
		}else if($sys['protocolo'] == 3) {
			// Tracert
		}else if($sys['protocolo'] == 4) {
			// Nmap
		}else if($sys['protocolo'] == 5) {
			// Lookup
		}else if($sys['protocolo'] == 6) {
			// Telnet
		}
	}
}

if(count($sondas_snmp)) {
	$cmd = "php -f /var/www/html/cron/Scripts/coletor_snmp_disp.php i=" . $_GET['id'] . " s=" . implode(',', $sondas_snmp) . " d=\"".$data."\" &";
	//echo $cmd . "\n\n";
	exec($cmd);
}






/*
function insert( $data, $data1, $idSensor ) {
	mysqli_query($GLOBALS["db"], "INSERT INTO Log2h (data, idSensor, statusAlert) VALUES ('$data', '$idSensor', '2')");
	mysqli_query($GLOBALS["db"], "INSERT INTO Log24h (data, idSensor, statusAlert) VALUES ('$data', '$idSensor', '2')");
	if($data1 == 00 || $data1 == 30) {
		mysqli_query($GLOBALS["db"], "INSERT INTO Log30d (data, idSensor, statusAlert) VALUES ('$data', '$idSensor', '2')");
	}
	if($data1 == 00) {
		mysqli_query($GLOBALS["db"], "INSERT INTO Log1a (data, idSensor, statusAlert) VALUES ('$data', '$idSensor', '2')");
	}
}

function AjeitaComando($string) {
    $what = array( ' ','(',')' );
    $by   = array( '_AjeitaaRavii_','\(','\)' );
    return str_replace($what, $by, $string);
}

// Executar apenas se não estiver com licença gratuita vencida
if($registroPlano != 0) {
	$PesquisaSys = mysqli_query($db, "SELECT snmppadrao, portasnmppadrao, versaosnmppadrao, nivelsegsnmppadrao, protocoloauthsnmppadrao, protocolocripsnmppadrao, authsnmppadrao, criptosnmppadrao FROM system");
	$resSys = mysqli_fetch_array($PesquisaSys);

	$community = $resSys['snmppadrao'];
	$porta = $resSys['portasnmppadrao'];
	$vsnmp = $resSys['versaosnmppadrao'];
	$nivelsegsnmp = $resSys['nivelsegsnmppadrao'];
	$protocoloauthsnmp = $resSys['protocoloauthsnmppadrao'];
	$protocolocripsnmp = $resSys['protocolocripsnmppadrao'];
	$authsnmp = $resSys['authsnmppadrao'];
	$criptosnmp = $resSys['criptosnmppadrao'];

	$PesquisaDisp = mysqli_query($db, "SELECT id, ip, HerdarPai, idGrupoPai, snmpcomunit, versaosnmp_d, nivelsegsnmp_d, protocoloauthsnmp_d, protocolocripsnmp_d, authsnmp_d, criptosnmp_d, portasnmp_d FROM Dispositivos;");
	if(mysqli_num_rows($PesquisaDisp)) {
		while($resDisp = mysqli_fetch_array($PesquisaDisp)) {
			if($resDisp['HerdarPai'] == 2) {
				$community = $resDisp['snmpcomunit'];
				$porta = $resDisp['portasnmp_d'];
				$vsnmp = $resDisp['versaosnmp_d'];
				$nivelsegsnmp = $resDisp['nivelsegsnmp_d'];
				$protocoloauthsnmp = $resDisp['protocoloauthsnmp_d'];
				$protocolocripsnmp = $resDisp['protocolocripsnmp_d'];
				$authsnmp = $resDisp['authsnmp_d'];
				$criptosnmp = $resDisp['criptosnmp_d'];
			}else if($resDisp['idGrupoPai'] != 0) {
				$PesquisaGrupoPai = mysqli_query($db, "SELECT comunidadesnmp_g, versaosnmp_g, nivelsegsnmp_g, protocoloauthsnmp_g, protocolocripsnmp_g, authsnmp_g, criptosnmp_g, portasnmp_g FROM GrupoMonitor WHERE id = '".$resDisp['idGrupoPai']."' AND ativasnmp = '2'");
				if(mysqli_num_rows($PesquisaGrupoPai) == 1) {
					$resGpo = mysqli_fetch_array($PesquisaGrupoPai);
					$community = $resGpo['comunidadesnmp_g'];
					$porta = $resGpo['portasnmp_g'];
					$vsnmp = $resGpo['versaosnmp_g'];
					$nivelsegsnmp = $resGpo['nivelsegsnmp_g'];
					$protocoloauthsnmp = $resGpo['protocoloauthsnmp_g'];
					$protocolocripsnmp = $resGpo['protocolocripsnmp_g'];
					$authsnmp = $resGpo['authsnmp_g'];
					$criptosnmp = $resGpo['criptosnmp_g'];
				}
			}

			$args = array();
			$args['id'] = $resDisp['id'];
			$args['ip'] = $resDisp['ip'];
			$args['snmp='] = $community;
			$args['porta='] = $porta;
			$args['vsnmp='] = $vsnmp;
			$args['nivelsegsnmp='] = $nivelsegsnmp;
			$args['protocoloauthsnmp'] = $protocoloauthsnmp;
			$args['protocolocripsnmp'] = $protocolocripsnmp;
			$args['authsnmp'] = $authsnmp;
			$args['criptosnmp'] = $criptosnmp;
			$args['data'] = $data;
            $args['hora'] = $hora;
            $args['cronograma'] = $_GET['valor'];

			$cmd = "/usr/bin/nohup /var/www/html/cron/Scripts/coletor_snmp_disp.php " . implode(' ', $args) . " >/dev/null 2>&1 &";
			echo $cmd . "\n\n";

		}
	}
}
*/


?>