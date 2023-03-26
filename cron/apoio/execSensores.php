<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

if(!is_dir("/var/www/html/ram/coletas")) { mkdir('/var/www/html/ram/coletas/', 0777, true); }
if(!is_dir("/var/www/html/ram/coletas/trafegoSNMP")) { mkdir('/var/www/html/ram/coletas/trafegoSNMP/', 0777, true); }
if(!is_dir("/var/www/html/ram/coletas/valores")) { mkdir('/var/www/html/ram/coletas/valores/', 0777, true); }

function encodeBase64($string) {
	$what = array( '+', '/', '=' );
	$by   = array( '-', '_', '' );
	return str_replace($what, $by, base64_encode($string));
}

function remove_utf8_bom($text) {
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}

$key = 'lZke4%QQ5y6uo%WPtBXDy9gfv';
$captSinc = exec("cat /var/www/html/.sinc");

if($captSinc) {
	$aux_sinc = explode('.', $captSinc);
	$signature_sinc = hash_hmac('sha256', $aux_sinc[1].'.'.$aux_sinc[2], $key);
	$signature_sinc = encodeBase64($signature_sinc);
	if($signature_sinc == $aux_sinc[3]) {
		$payload_ret = json_decode(base64_decode($aux_sinc[2]));
		$status = $payload_ret->status;
		if($status == 1) {
            $registroPlano = $payload_ret->plano;
		}else {
			//echo "o que fazer quando a senha de sincronização não bater";
            $pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/exec/10min/token.php' | grep -v grep | awk '{print $2}'");
            if(!$pidexec) { exec("php -f /var/www/html/cron/exec/10min/token.php &"); }
		}
	}else {
		//echo "o que fazer quando a senha não bater";
        $pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/exec/10min/token.php' | grep -v grep | awk '{print $2}'");
        if(!$pidexec) { exec("php -f /var/www/html/cron/exec/10min/token.php &"); }
	}
}else {
	//echo "o que fazer quando não existe arquivo";
    $pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/exec/10min/token.php' | grep -v grep | awk '{print $2}'");
    if(!$pidexec) { exec("php -f /var/www/html/cron/exec/10min/token.php &"); }
}

if(!$registroPlano) { $registroPlano = 0; }

$cronograma = $_GET["valor"];
$data = date("Y-m-d");
$hora = date("H:i");
$hora = $hora . ":" . $_GET["time"];
$data1 = date("i");

if($_GET["sleep"]) { sleep($_GET["sleep"]); }

include("/var/www/html/cron/apoio/conexao.php");

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

if($registroPlano != 0) {
	$resultSensores = mysqli_query($db, "SELECT id, idDispositivo, statusAlert, valor, erro, tag, media1, media2, multiplicar, dividir, banco, text, host, usuario, senha, porta, comandoSQL, valor1, adicionais, ifSpeed FROM Sensores WHERE cronograma = '".$cronograma."';");
	$PesquisaSys = mysqli_query($db, "SELECT snmppadrao, portasnmppadrao, versaosnmppadrao, nivelsegsnmppadrao, protocoloauthsnmppadrao, protocolocripsnmppadrao, authsnmppadrao, criptosnmppadrao FROM system");
	$resSys = mysqli_fetch_array($PesquisaSys);
	$unidade_cron = substr($cronograma, -1);
	$valor_cron = str_replace($unidade_cron, '', $cronograma);

	if($unidade_cron == 's' && $valor_cron < 60) {
		$segundos = $valor_cron;
	}else if($unidade_cron == 'm' && $valor_cron < 60) {
		$segundos = $valor_cron * 60;
	}else if($unidade_cron == 'h' && $valor_cron < 24) {
		$segundos = ($valor_cron * 60) * 60;
	}else if($unidade_cron == 'd' && $valor_cron <= 7) {
		$segundos = (($valor_cron * 60) * 60) * 60;
	}

	if($segundos <= 60) {
		$Sleep = 1;
	}else if($segundos <= 300) {
		$Sleep = 10;
	}else if($segundos <= 900) {
		$Sleep = 15;
	}else {
		$Sleep = 5;
	}

	if(mysqli_num_rows($resultSensores) <= 100) {
		$divideCarga = mysqli_num_rows($resultSensores) + 10;
	}else if(mysqli_num_rows($resultSensores) <= 200) {
		$divideCarga = mysqli_num_rows($resultSensores) / 2;
	}else if(mysqli_num_rows($resultSensores) <= 300) {
		$divideCarga = mysqli_num_rows($resultSensores) / 3;
	}else if(mysqli_num_rows($resultSensores) <= 400) {
		$divideCarga = mysqli_num_rows($resultSensores) / 4;
	}else {
		$divideCarga = mysqli_num_rows($resultSensores) / 5;
	}
	$divideCarga = (int)$divideCarga;

	$test_icmp = array();
	$list = array();

	$nLoop = 1;
	while($Sensores = mysqli_fetch_array($resultSensores)) {
		$community = $resSys['snmppadrao'];
		$porta = $resSys['portasnmppadrao'];
		$vsnmp = $resSys['versaosnmppadrao'];
		$nivelsegsnmp = $resSys['nivelsegsnmppadrao'];
		$protocoloauthsnmp = $resSys['protocoloauthsnmppadrao'];
		$protocolocripsnmp = $resSys['protocolocripsnmppadrao'];
		$authsnmp = $resSys['authsnmppadrao'];
		$criptosnmp = $resSys['criptosnmppadrao'];

		$PesquisaDisp = mysqli_query($db, "SELECT ip, HerdarPai, idGrupoPai, snmpcomunit, versaosnmp_d, nivelsegsnmp_d, protocoloauthsnmp_d, protocolocripsnmp_d, authsnmp_d, criptosnmp_d, portasnmp_d FROM Dispositivos WHERE id = '".$Sensores['idDispositivo']."';");
		$resDisp = mysqli_fetch_array($PesquisaDisp);	
		$IP = $resDisp['ip'];

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
				$nivelsegsnmp = $resDisp['nivelsegsnmp_g'];
				$protocoloauthsnmp = $resDisp['protocoloauthsnmp_g'];
				$protocolocripsnmp = $resDisp['protocolocripsnmp_g'];
				$authsnmp = $resDisp['authsnmp_g'];
				$criptosnmp = $resDisp['criptosnmp_g'];
			}
		}
		$erro = $Sensores['erro'];

		$PesquisaSonda = mysqli_query($db, "SELECT maxPer, minPer, maxSet, minSet, alertar FROM Sondas WHERE tag = '".$Sensores['tag']."';");
		$resSonda = mysqli_fetch_array($PesquisaSonda);	
		$maxSet = $resSonda['maxSet'];
		$minSet = $resSonda['minSet'];
		$maxPer = $resSonda['maxPer'];
		$minPer = $resSonda['minPer'];
		$alertar = $resSonda['alertar'];
		
		if(isset($Sensores['adicionais'])) {
			$adicionais = $Sensores['adicionais'];
			if($adicionais == 10) { 
				$alertar = 1;
				$erro = 1;
			}else if($adicionais != 1 && $adicionais != 2 && $adicionais != 3 && $adicionais != 5 && $adicionais != 10) {
				$aux = explode('-', $adicionais);
				if($aux['4'] == 10) { 
					$alertar = 1;
					$erro = 1;
				}
			}
		}else {
			// Considerar off se falhar duas vezes
			$adicionais = 2;
		}

		// http://IP/cron/apoio/execSensores.php?valor=1m&time=00&sleep=0
		// php -f /var/www/html/cron/apoio/execSensores.php valor=1m time=00 sleep=0

		// [bd Sensores]     statusAlert: [ off 1 | pausado 2 | alerta 3 | erro 4 | novo 5 | ok 6 | off em alerta 7 / desconect 8 / alerta limitador 9 / erro limitador 10 ]
		if($Sensores['statusAlert'] == "2") {
			insert("$data $hora", $data1, $Sensores["id"]);
		}else if($Sensores['tag'] == "ping") {
			if($Sensores['valor']) { $ipping = $Sensores['valor']; }else { $ipping = $IP; }
			if(!isset($test_icmp[$ipping])) { $test_icmp[$ipping] = array(); }
			if(!isset($test_icmp_list[$adicionais])) { $test_icmp_list[$adicionais] = array(); }
			$ajeita_media1 = $Sensores['media1'];
			if($Sensores['media1'] >= 10) {
				$ajeita_media1 = $Sensores['media1'];
			}else {
				$ajeita_media1 = "-";
			}
			$test_icmp[$ipping] = array(
				'id' => $Sensores['id'],
				'address' => $ipping,
				'cron' => $cronograma,
				'adicionais' => $adicionais,
				'erro' => $erro,
				'media1' => $ajeita_media1,
				'maxPer' => $maxPer,
				'alertar' => $alertar,
				'hora' => $hora,
				'data' => $data,
				'data1' => $data1
			);
            array_push($test_icmp_list[$adicionais], $test_icmp[$ipping]);
		}else {
			if($Sensores['tag'] == "traceroute" && $Sensores['valor']) { $IP = $Sensores['valor']; }
			$args = array();
			$args[] = 'id=' . $Sensores['id'];
			$args[] = 'ad=' . $adicionais;
			$args[] = 'erro=' . $erro;
			$args[] = 'ip=' . $IP;
			if($Sensores['tag'] == "mysql" || $Sensores['tag'] == "postgresql" || $Sensores['tag'] == "contagemftp") { $args[] = 'host=' . $Sensores['host']; }
			$args[] = 'snmp=' . $community;
			$args[] = 'porta=' . $porta;
			$args[] = 'vsnmp=' . $vsnmp;
			$args[] = 'nivelsegsnmp=' . $nivelsegsnmp;
			$args[] = 'protocoloauthsnmp=' . $protocoloauthsnmp;
			$args[] = 'protocolocripsnmp=' . $protocolocripsnmp;
			$args[] = 'authsnmp=' . $authsnmp;
			$args[] = 'criptosnmp=' . $criptosnmp;
			$args[] = 'hora=' . $hora;
			$args[] = 'data=' . $data; 
			$args[] = 'data1=' . $data1;
			$args[] = 'media1=' . $Sensores['media1']; 
			$args[] = 'maxPer=' . $maxPer;
			$args[] = 'minPer=' . $minPer;
			$args[] = 'maxSet=' . $maxSet;
			$args[] = 'minSet=' . $minSet;
			$args[] = 'alertar=' . $alertar;
			$args[] = 'banco=' . $Sensores['banco'];
			$args[] = 'v=' . $Sensores['valor'];
			if($Sensores['tag'] == "trafegosnmp") { $args[] = 'v1=' . $Sensores['valor1']; }
			$args[] = 'm=' . $Sensores['multiplicar'];
			$args[] = 'd=' . $Sensores['dividir'];
			$args[] = 'speed=' . $Sensores['ifSpeed']; 
			if($Sensores['tag'] == "mysql") { $args[] = 'comandoSQL=' . AjeitaComando($Sensores['comandoSQL']); }
			if($Sensores['tag'] == "mxdns") { $args[] = 'text=' . $Sensores['text']; }
			$cmd = "/usr/bin/nohup /var/www/html/cron/Scripts/" . $Sensores['tag'] . ".php " . implode(' ', $args) . " >/dev/null 2>&1 &";
			//echo "/usr/bin/nohup /var/www/html/cron/Scripts/" . $Sensores['tag'] . ".php " . implode(' ', $args) . " >/dev/null 2>&1 &<br><br>";
			//if($Sensores['tag'] == "disksnmp") { echo "/cron/Scripts/" . $Sensores['tag'] . ".php?" . implode('&', $args) . "<br>"; }
			exec($cmd);
		}

		if($nLoop == $divideCarga) { 
			sleep($Sleep);
			$nLoop = 1;
		}else {
			$nLoop = $nLoop + 1;
		}
	}

    if(!is_dir("/var/www/html/ram/coletas/")) { mkdir('/var/www/html/ram/coletas/', 0777, true); }
    if(!is_dir("/var/www/html/ram/coletas/fping/")) { mkdir('/var/www/html/ram/coletas/fping/', 0777, true); }

    foreach ($test_icmp_list as $data_list) {
        $nome_arquivo = date("His") . rand(1, 9999);
        $conn = fopen('/var/www/html/ram/coletas/fping/'.$nome_arquivo.'', 'w+');
        fwrite($conn, json_encode($data_list));
        fclose($conn);
		exec("/usr/bin/nohup /var/www/html/cron/Scripts/fping.php arquivo=$nome_arquivo >/dev/null 2>&1 &");
		//echo "/usr/bin/nohup /var/www/html/cron/Scripts/fping.php arquivo=$nome_arquivo >/dev/null 2>&1 &\n";
    }

	$pid = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/mariadb_dispositivos.php' | grep -v grep | awk '{print $2}'");
	if(!$pid) { exec("php -f /var/www/html/cron/apoio/mariadb_dispositivos.php > /dev/null &"); }
}

mysqli_close($db);
exit(0);
?>