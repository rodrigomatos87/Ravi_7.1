#!/usr/bin/php
<?PHP
$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

function sanitizeSNMP($string) {
	$what = array( 'STRING: ', 'INTEGER: ', 'IpAddress: ', 'Counter64: ', 'Gauge32: ', '"' );
	$by   = array( '', '', '', '', '', '' );
    return str_replace($what, $by, $string);
}
function encodeBase64($string) {
	$what = array( '+', '/', '=' );
	$by   = array( '-', '_', '' );
	return str_replace($what, $by, base64_encode($string));
}
function encodeComunidade($string) {
	$string = base64_encode($string);
	$what = array( '+', '/', '=' );
	$by   = array( '-', '_', '' );
	return str_replace('=', '', $string);
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

$datasinc = date("Y-m-d H:i:s");
$data = date("Y-m-d");
$hor = date("H:i"); 
$hora = $hor . ":00";
$data1 = date("i");
$data2 = date("H");
$dataatual = $data . " " . $hora;

include("/var/www/html/cron/apoio/conexao.php");

$cores = exec("cat /proc/cpuinfo | grep processor | grep -v 'model name' | wc -l");
$update = exec("uptime");
$exp = explode(',', $update);
$um = floatval(str_replace('load average: ', '', $exp[2]));
$umCalc = (int)$um;

if($umCalc > $cores) {
	$filaTotal = $umCalc - $cores;
	if($filaTotal >= 10) {
		if($cores > 1) {
			$fila = $filaTotal / $cores;
			if($fila >= 10) {
				$executa = 1;
			}else {
				$executa = 2;
			}
		}else if($filaTotal >= 10) {
			$executa = 1;
		}
	}else {
		$executa = 2;
	}
}else {
	$executa = 2;
}

$buscaSystem = mysqli_query($db, "SELECT snmppadrao_pppoe, portasnmppadrao_pppoe, versaosnmppadrao_pppoe, qtdpiores_pppoe, userpadrao_pppoe, senhapadrao_pppoe, portapadrao_pppoe, ativaPing_pppoe, tamanhopacotes_pppoe, quantidadepacotes_pppoe, historicotrafegocon FROM system");
$System = mysqli_fetch_array($buscaSystem);

if($registroPlano == '7' || $registroPlano == '8' || $registroPlano == '9' || $registroPlano == '100' && $executa == 2) {
	if($_GET['id']) {
		$resConcentradora = mysqli_query($db, "SELECT * FROM concentradoras WHERE status = 2 AND ativo = 1 AND id = '".$_GET['id']."';");
		$Concentradora = mysqli_fetch_array($resConcentradora);
		$pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/concentradora.php id=$Concentradora[id]' | grep -v grep | awk '{print $2}'");
		if(!$pidexec) {
			if($Concentradora['novo'] == 1) { mysqli_query($db, "UPDATE concentradoras SET novo = '2' WHERE id = '$Concentradora[id]'"); }
			//Executar via SNMP nas marcas selecionadas
			if($Concentradora['marca'] == 1 || $Concentradora['marca'] == 2 || $Concentradora['marca'] == 3) {
				if($Concentradora['HerdarPai'] == 1) {
					$comunidade = $System['snmppadrao_pppoe'];
					$vsnmp = $System['versaosnmppadrao_pppoe'];
					$porta = $System['portasnmppadrao_pppoe'];
				}else if($Concentradora['HerdarPai'] == 2) {
					$comunidade = $Concentradora['snmp'];
					$vsnmp = $Concentradora['versaosnmp'];
					$porta = $Concentradora['portasnmp'];
				}
				//$comunidade = encodeComunidade($comunidade);
				mysqli_query($db, "UPDATE usersPPPoE SET apoio = '1' WHERE idC = '".$Concentradora['id']."';");
				exec("php -f /var/www/html/cron/apoio/concentradora.php id=$Concentradora[id] ip=$Concentradora[ip] snmp=$comunidade vsnmp=$vsnmp porta=$porta marca=$Concentradora[marca] ativaPing=$System[ativaPing_pppoe] tamanho=$System[tamanhopacotes_pppoe] quantidade=$System[quantidadepacotes_pppoe] historico=$System[historicotrafegocon] cron=$Concentradora[cron] hora=$hora data=$data data1=$data1 > /dev/null &");
				//echo "php -f /var/www/html/cron/apoio/concentradora.php id=$Concentradora[id] ip=$Concentradora[ip] snmp=$comunidade vsnmp=$vsnmp porta=$porta marca=$Concentradora[marca] ativaPing=$System[ativaPing_pppoe] tamanho=$System[tamanhopacotes_pppoe] quantidade=$System[quantidadepacotes_pppoe] historico=$System[historicotrafegocon] cron=$Concentradora[cron] hora=$hora data=$data data1=$data1<br>";
			}
		}
	}else {
		$limite_plano = 0;
		if($registroPlano == '7') {
			$limite_plano = 1;
		}else if($registroPlano == '8') {
			$limite_plano = 4;
		}else if($registroPlano == '9' || $registroPlano == '100') {
			$limite_plano = 9999;
		}
		$qtd_exec = 1;
		$resConcentradora = mysqli_query($db, "SELECT * FROM concentradoras WHERE status = 2 AND ativo = 1;");
		while($Concentradora = mysqli_fetch_array($resConcentradora)) {
			// Executar apenas o que está no plano 
			if($qtd_exec <= $limite_plano) {
				$pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/concentradora.php id=$Concentradora[id]' | grep -v grep | awk '{print $2}'");
				$pidexec_db = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/mariadb_ppoe.php idConc=$Concentradora[id] hora=$hora data=$data' | grep -v grep | awk '{print $2}'");
				// Verificando se tem usuário deslogado para revalidar via SNMP
				if(!$pidexec && !$pidexec_db && ($Concentradora['marca'] == 1 || $Concentradora['marca'] == 2)) {
					$resUsersPPPoE = mysqli_query($db, "SELECT * FROM usersPPPoE WHERE idC = '".$Concentradora['id']."' AND (datadesconect = '' OR datadesconect IS NULL) AND datasinc < '".$Concentradora['datasinc']."' AND apoio = '1';");
					if(mysqli_num_rows($resUsersPPPoE)) {
						if($Concentradora['HerdarPai'] == 1) {
							$comunidade = $System['snmppadrao_pppoe'];
							$vsnmp = $System['versaosnmppadrao_pppoe'];
							$porta = $System['portasnmppadrao_pppoe'];
						}else if($Concentradora['HerdarPai'] == 2) {
							$comunidade = $Concentradora['snmp'];
							$vsnmp = $Concentradora['versaosnmp'];
							$porta = $Concentradora['portasnmp'];
						}
						//$comunidade = encodeComunidade($comunidade);
						$host = $Concentradora['ip'];
						while($UsersPPPoE = mysqli_fetch_array($resUsersPPPoE)) {
							$idinterface = $UsersPPPoE['interface'];
							$idUser = $UsersPPPoE['id'];
							$mac = $UsersPPPoE['id'];
							$datasinc_bd = $UsersPPPoE['datasinc'];
							$dataconect = $UsersPPPoE['dataconect'];
							$uptimeconect = $UsersPPPoE['uptimeconect'];
							$ppoe = $UsersPPPoE['ppoe'];
							$ip = $UsersPPPoE['ip'];
							$vlan = $UsersPPPoE['vlan'];
							$down = $UsersPPPoE['down'];
							$up = $UsersPPPoE['up'];
							$ping = $UsersPPPoE['ping'];
							$jitter = $UsersPPPoE['jitter'];
							$datasinc_now = date("Y-m-d H:i:s");
							// Mikrotik
							if($Concentradora['marca'] = 1) {
								$exp = explode(".", $idinterface);
								$interfacee = $exp[0];
								if($vsnmp == 1) {
									$ppoe_novo = sanitizeSNMP(snmpget("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.9.9.150.1.1.3.1.2.{$interfacee}", 1000000, 30));
								}else if($vsnmp == 2) {
									$ppoe_novo = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.9.9.150.1.1.3.1.2.{$interfacee}", 1000000, 30));
								}
							// Huawei
							}else if($Concentradora['marca'] = 2) {
								if($vsnmp == 1) {
									$ppoe_novo = sanitizeSNMP(snmpget("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.2011.5.2.1.15.1.3.{$idinterface}", 1000000, 30));
								}else if($vsnmp == 2) {
									$ppoe_novo = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.2011.5.2.1.15.1.3.{$idinterface}", 1000000, 30));
								}
							// Cisco
							}else if($Concentradora['marca'] == 3) {
								$exp = explode("|", $idinterface);
								if($vsnmp == 1) {
									$ppoe_novo = sanitizeSNMP(snmpget("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.9.9.150.1.1.3.1.2.{$exp[1]}", 1000000, 30));
								}else if($vsnmp == 2) {
									$ppoe_novo = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.9.9.150.1.1.3.1.2.{$exp[1]}", 1000000, 30));
								}
							}
							if($ppoe_novo && $ppoe_novo == $ppoe) {
								mysqli_query($db, "UPDATE usersPPPoE SET apoio = '0', datasinc = '".$datasinc."' WHERE id = '".$idUser."'");
								exec("echo '|$mac|$datasinc|$dataconect||$uptimeconect|$ppoe|$ip|$vlan|$down|$up|$ping|$jitter|' > /var/www/html/ram/coletas/ppoe/users/$idUser");
							}else if((strtotime($dataconect) + 150) < strtotime($datasinc_now)) {
								mysqli_query($db, "UPDATE usersPPPoE SET apoio = '0', datadesconect = '".$datasinc_now."' WHERE id = '".$idUser."'");
								exec("echo '|$mac|$datasinc_bd|$dataconect|$datasinc|$uptimeconect|$ppoe|$ip|$vlan|$down|$up|$ping|$jitter|' > /var/www/html/ram/coletas/ppoe/users/$idUser");
							}
						}
					}
				}
				$monitorar = 0;
				if(!$pidexec && !$pidexec_db) {
					if($Concentradora['novo'] == 1) {
						mysqli_query($db, "UPDATE concentradoras SET novo = '2' WHERE id = '$Concentradora[id]'");
						$monitorar = 1;
					}else if($Concentradora['cron'] == 1) {
						if($data1 == '00' || $data1 == '05' || $data1 == '10' || $data1 == '15' || $data1 == '20' || $data1 == '25' || $data1 == '30' || $data1 == '35' || $data1 == '40' || $data1 == '45' || $data1 == '50' || $data1 == '55') {
							$monitorar = 1;
						}
					}else if($Concentradora['cron'] == 2) {
						if($data1 == '00' || $data1 == '10' || $data1 == '20' || $data1 == '30' || $data1 == '40' || $data1 == '50') {
							$monitorar = 1;
						}
					}else if($Concentradora['cron'] == 3) {
						if($data1 == '00' || $data1 == '15' || $data1 == '30' || $data1 == '45') {
							$monitorar = 1;
						}
					}else if($Concentradora['cron'] == 4) {
						if($data1 == '00' || $data1 == '30') {
							$monitorar = 1;
						}
					}else if($Concentradora['cron'] == 5 && $data1 == '00') {
						if($data2 == '00' || $data2 == '01' || $data2 == '02' || $data2 == '03' || $data2 == '04' || $data2 == '05' || $data2 == '06' || $data2 == '07' || $data2 == '08' || $data2 == '09' || $data2 == '10' || $data2 == '11' || $data2 == '12' || $data2 == '13' || $data2 == '14' || $data2 == '15' || $data2 == '16' || $data2 == '17' || $data2 == '18' || $data2 == '19' || $data2 == '20' || $data2 == '21' || $data2 == '22' || $data2 == '23') {
							$monitorar = 1;
						}
					}else if($Concentradora['cron'] == 6 && $data1 == '00') {
						if($data2 == '00' || $data2 == '02' || $data2 == '04' || $data2 == '06' || $data2 == '08' || $data2 == '10' || $data2 == '12' || $data2 == '14' || $data2 == '16' || $data2 == '18' || $data2 == '20' || $data2 == '22') {
							$monitorar = 1;
						}
					}else if($Concentradora['cron'] == 7) {
						if($data1 == '00' && $data2 == '00' || $data2 == '03' || $data2 == '06' || $data2 == '09' || $data2 == '12' || $data2 == '15' || $data2 == '18' || $data2 == '21') {
							$monitorar = 1;
						}
					}else if($Concentradora['cron'] == 8) {
						if($data1 == '00' && $data2 == '00' || $data2 == '06' || $data2 == '12' || $data2 == '18') {
							$monitorar = 1;
						}
					}
				}
				
				if($monitorar == 1) {
					if($Concentradora['marca'] == 1 || $Concentradora['marca'] == 2 || $Concentradora['marca'] == 3) {
						if($Concentradora['HerdarPai'] == 1) {
							$comunidade = $System['snmppadrao_pppoe'];
							$vsnmp = $System['versaosnmppadrao_pppoe'];
							$porta = $System['portasnmppadrao_pppoe'];
						}else if($Concentradora['HerdarPai'] == 2) {
							$comunidade = $Concentradora['snmp'];
							$vsnmp = $Concentradora['versaosnmp'];
							$porta = $Concentradora['portasnmp'];
						}
						mysqli_query($db, "UPDATE usersPPPoE SET apoio = '1' WHERE idC = '".$Concentradora['id']."';");
						exec("php -f /var/www/html/cron/apoio/concentradora.php id=$Concentradora[id] ip=$Concentradora[ip] snmp=$comunidade vsnmp=$vsnmp porta=$porta marca=$Concentradora[marca] ativaPing=$System[ativaPing_pppoe] tamanho=$System[tamanhopacotes_pppoe] quantidade=$System[quantidadepacotes_pppoe] historico=$System[historicotrafegocon] cron=$Concentradora[cron] hora=$hora data=$data data1=$data1 > /dev/null &");
					}
				}
				$qtd_exec = $qtd_exec + 1;
			}else {
				// Desativar concentradora além do limitado no plano!
				// mysqli_query($db, "UPDATE concentradoras SET ativo = '2' WHERE id = '".$Concentradora['id']."';");
			}
		}
	}
}else {
	// Desativar todas as concentradoras ativas!
	//mysqli_query($db, "UPDATE concentradoras SET ativo = '2';");
}

mysqli_close($db);
?>