#!/usr/bin/php
<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

include("/var/www/html/cron/apoio/conexao.php");

$salvarLogs = "n"; // 'sim' para salvar!

$resClientes = mysqli_query($db, "SELECT registroPlano, ativaLinkDown_pppoe, historicotrafegocon FROM system");
$fetClientes = mysqli_fetch_array($resClientes);

if($fetClientes['registroPlano'] != '0') {
	$path = "/var/www/html/ram/coletas/ppoe/procesando/";
	$diretorio = dir($path);
	$num = 1;
	//|$idConc|$idinterface|$mac|$ip|$vlan|$ppoe|$datasinc|$dataconect|$uptimeconect|$down|$up|
	while($arquivo = $diretorio -> read()){
		if($arquivo != "." && $arquivo != "..") {
			$info = exec("cat $path$arquivo");
			$aux = explode('|', $info);
			$idConc = $aux['1'];
			$idinterface = $aux['2'];
			$mac = $aux['3'];
			$ip = $aux['4'];
			$vlan = $aux['5'];
			$ppoe = $aux['6'];
			$datasinc = $aux['7'];
			$dataconect = $aux['8'];
			$uptimeconect = $aux['9'];
			$down = $aux['10'];
			$up = $aux['11'];
            $down_rt = $aux['12'];
            $up_rt = $aux['13'];
            
			if($salvarLogs == "sim") {
				exec("echo 'registro ".$num."' >> /var/www/html/log_pppoe.txt");
				$num = $num + 1;
			}
            
			$resUsersPPPoE = mysqli_query($db, "SELECT id, interface, datadesconect, dataconect, ip, down, up, ping, jitter FROM usersPPPoE WHERE idC = '".$idConc."' AND ppoe = '".$ppoe."';");
			if(mysqli_num_rows($resUsersPPPoE)) {
				$resConPPPoE = mysqli_query($db, "SELECT marca FROM concentradoras WHERE id = '".$idConc."';");
				$ConPPPoE = mysqli_fetch_array($resConPPPoE);
				$usersPPPoE = mysqli_fetch_array($resUsersPPPoE);
				$idUser = $usersPPPoE['id'];
				$ping = $usersPPPoE['ping'];
				$jitter = $usersPPPoE['jitter'];
				if($ConPPPoE['marca'] == 1) {
					$exp1 = explode(".", $idinterface);
					$exp2 = explode(".", $usersPPPoE['interface']);
					$interf_1 = $exp1[0];
					$interf_2 = $exp2[0];
				}else if($ConPPPoE['marca'] == 2 || $ConPPPoE['marca'] == 3) {
					$interf_1 = $usersPPPoE['interface'];
					$interf_2 = $idinterface;
				}
				if($interf_1 == $interf_2) {
					mysqli_query($db, "UPDATE usersPPPoE SET datasinc = '".$datasinc."', uptimeconect = '".$uptimeconect."', ip = '".$ip."', vlan = '".$vlan."', down = '".$down."', up = '".$up."', datadesconect = '', apoio = '0' WHERE id = '".$idUser."';");
					if($salvarLogs == "sim") {
						exec("echo 'UPDATE usersPPPoE SET datasinc = ".$datasinc.", uptimeconect = ".$uptimeconect.", ip = ".$ip.", vlan = ".$vlan.", down = ".$down.", up = ".$up.", datadesconect = , apoio = 0 WHERE id = ".$idUser.";' >> /var/www/html/log_pppoe.txt");
						exec("echo '' >> /var/www/html/log_pppoe.txt");
					}
				}else if($usersPPPoE['datadesconect']) {
					if($salvarLogs == "sim") {
						exec("echo 'interface mudou de ".$usersPPPoE['interface']." para ".$idinterface." e datadesconect não é vazio!' >> /var/www/html/log_pppoe.txt");
						exec("echo 'INSERT INTO LogPPPoE (dataconect, datadesconect, idC, idPPPoE, ip, vlan, down, up) VALUES (".$usersPPPoE['dataconect'].", ".$usersPPPoE['datadesconect'].", ".$idConc.", ".$usersPPPoE['id'].", ".$usersPPPoE['ip'].", ".$usersPPPoE['vlan'].", ".$usersPPPoE['down'].", ".$usersPPPoE['up'].")' >> /var/www/html/log_pppoe.txt");
						exec("echo 'UPDATE usersPPPoE SET interface = ".$idinterface.", mac = ".$mac.", datasinc = ".$datasinc.", dataconect = ".$dataconect.", uptimeconect = ".$uptimeconect.", ip = ".$ip.", vlan = ".$vlan.", down = ".$down.", up = ".$up.", datadesconect = , apoio = 0 WHERE id = ".$idUser.";' >> /var/www/html/log_pppoe.txt");
						exec("echo '' >> /var/www/html/log_pppoe.txt");
					}
					mysqli_query($db, "INSERT INTO LogPPPoE (dataconect, datadesconect, idC, idPPPoE, ip, vlan, down, up) VALUES ('".$usersPPPoE['dataconect']."', '".$usersPPPoE['datadesconect']."', '".$idConc."', '".$usersPPPoE['id']."', '".$usersPPPoE['ip']."', '".$usersPPPoE['vlan']."', '".$usersPPPoE['down']."', '".$usersPPPoE['up']."')");
					mysqli_query($db, "UPDATE usersPPPoE SET interface = '".$idinterface."', mac = '".$mac."', datasinc = '".$datasinc."', dataconect = '".$dataconect."', uptimeconect = '".$uptimeconect."', ip = '".$ip."', vlan = '".$vlan."', down = '".$down."', up = '".$up."', datadesconect = '', apoio = '0' WHERE id = '".$idUser."';");
				}else {
					if($salvarLogs == "sim") { exec("echo 'interface mudou de ".$usersPPPoE['interface']." para ".$idinterface." e datadesconect está vazia! Vamos tentar encontrar uma data aproximada...' >> /var/www/html/log_pppoe.txt"); }
					$resLogPPPoE = mysqli_query($db, "SELECT id, datadesconect FROM LogPPPoE WHERE idPPPoE = '".$idUser."' ORDER BY id DESC LIMIT 1;");
					// Se já existe algum dado histórico vamos analizar a última conexão
					if(mysqli_num_rows($resLogPPPoE) == 1) {
						$oPPPoE = mysqli_fetch_array($resLogPPPoE);
						$ultimo_desconect = strtotime($oPPPoE['datadesconect']);
						$nova_conexao = strtotime($dataconect);
						$media_delay = ($nova_conexao - $ultimo_desconect) / 2;
						$seg_desconect = $nova_conexao - $media_delay;
						$data_desconect = date("Y-m-d H:i:s", $seg_desconect);
						if(strtotime($usersPPPoE['dataconect']) > strtotime($data_desconect)) { $data_desconect = $usersPPPoE['dataconect']; }
						if($salvarLogs == "sim") {
							exec("echo 'imaginamos que o desconect aconteceu em: ".$data_desconect."' >> /var/www/html/log_pppoe.txt");
							exec("echo 'INSERT INTO LogPPPoE (dataconect, datadesconect, idC, idPPPoE, ip, vlan, down, up) VALUES (".$usersPPPoE['dataconect'].", ".$data_desconect.", ".$idConc.", ".$usersPPPoE['id'].", ".$usersPPPoE['ip'].", ".$usersPPPoE['vlan'].", ".$usersPPPoE['down'].", ".$usersPPPoE['up'].")' >> /var/www/html/log_pppoe.txt");
						}
						mysqli_query($db, "INSERT INTO LogPPPoE (dataconect, datadesconect, idC, idPPPoE, ip, vlan, down, up) VALUES ('".$usersPPPoE['dataconect']."', '".$data_desconect."', '".$idConc."', '".$usersPPPoE['id']."', '".$usersPPPoE['ip']."', '".$usersPPPoE['vlan']."', '".$usersPPPoE['down']."', '".$usersPPPoE['up']."')");
					}else {
						if(strtotime($usersPPPoE['dataconect']) > strtotime($dataconect)) { 
							$data_desconect = $usersPPPoE['dataconect']; 
						}else {
							$data_desconect = $dataconect;
						}
						if($salvarLogs == "sim") {
							exec("echo 'como ainda não há histórico de conexões vamos presumir que a desconexão aconteceu no momento da nova conexão' >> /var/www/html/log_pppoe.txt");
							exec("echo 'INSERT INTO LogPPPoE (dataconect, datadesconect, idC, idPPPoE, ip, vlan, down, up) VALUES (".$usersPPPoE['dataconect'].", ".$data_desconect.", ".$idConc.", ".$usersPPPoE['id'].", ".$usersPPPoE['ip'].", ".$usersPPPoE['vlan'].", ".$usersPPPoE['down'].", ".$usersPPPoE['up'].")' >> /var/www/html/log_pppoe.txt");
						}
						mysqli_query($db, "INSERT INTO LogPPPoE (dataconect, datadesconect, idC, idPPPoE, ip, vlan, down, up) VALUES ('".$usersPPPoE['dataconect']."', '".$data_desconect."', '".$idConc."', '".$usersPPPoE['id']."', '".$usersPPPoE['ip']."', '".$usersPPPoE['vlan']."', '".$usersPPPoE['down']."', '".$usersPPPoE['up']."')");
					}
					if($salvarLogs == "sim") {
						exec("echo 'UPDATE usersPPPoE SET interface = ".$idinterface.", mac = ".$mac.", datasinc = ".$datasinc.", dataconect = ".$dataconect.", uptimeconect = ".$uptimeconect.", ip = ".$ip.", vlan = ".$vlan.", down = ".$down.", up = ".$up.", datadesconect = , apoio = 0 WHERE id = ".$idUser.";' >> /var/www/html/log_pppoe.txt");
						exec("echo '' >> /var/www/html/log_pppoe.txt");
					}
					// Já que a interface mudou vamos atualizar agora!
					mysqli_query($db, "UPDATE usersPPPoE SET interface = '".$idinterface."', mac = '".$mac."', datasinc = '".$datasinc."', dataconect = '".$dataconect."', uptimeconect = '".$uptimeconect."', ip = '".$ip."', vlan = '".$vlan."', down = '".$down."', up = '".$up."', datadesconect = '', apoio = '0' WHERE id = '".$idUser."';");
				}
				if($ppoe) {
					if($fetClientes['historicotrafegocon'] == '2' && isset($down_rt) && isset($up_rt)) { 
						mysqli_query($db, "INSERT INTO trafegoPPPoE (idC, idPPPoE, data, down, up) VALUES ('".$idConc."', '".$idUser."', '".$datasinc."', '".$down_rt."', '".$up_rt."')"); 
					}
					exec("echo '|$mac|$datasinc|$dataconect||$uptimeconect|$ppoe|$ip|$vlan|$down|$up|$ping|$jitter|' > /var/www/html/ram/coletas/ppoe/users/$idUser"); 
				}
			}else if($ppoe) {
				mysqli_query($db, "INSERT INTO usersPPPoE (idC, interface, mac, datasinc, dataconect, uptimeconect, ppoe, ip, vlan, down, up) VALUES ('".$idConc."', '".$idinterface."', '".$mac."', '".$datasinc."', '".$dataconect."', '".$uptimeconect."', '".$ppoe."', '".$ip."', '".$vlan."', '".$down."', '".$up."');");
				$idNovoUser = mysqli_insert_id($db);
				if($fetClientes['historicotrafegocon'] == '2' && isset($down_rt) && isset($up_rt)) { 
					mysqli_query($db, "INSERT INTO trafegoPPPoE (idC, idPPPoE, data, down, up) VALUES ('".$idConc."', '".$idNovoUser."', '".$datasinc."', '".$down_rt."', '".$up_rt."')");
				}
				if($idNovoUser) { exec("echo '|$mac|$datasinc|$dataconect||$uptimeconect|$ppoe|$ip|$vlan|$down|$up|||' > /var/www/html/ram/coletas/ppoe/users/$idNovoUser"); }
			}
			exec("rm -fr $path$arquivo");
		}
	}
	$diretorio -> close();
    
	$path_2 = "/var/www/html/ram/coletas/ppoe/ping/";
	$diretorio_2 = dir($path_2);
	//|$idC|$interface|$datasinc|$ip|$ping|$jitter|
	while($arquivo = $diretorio_2 -> read()) {
		if($arquivo != "." && $arquivo != "..") {
			$info = exec("cat $path_2$arquivo");
			$aux = explode('|', $info);
			$idConc = $aux['1'];
			$interface = $aux['2'];
			$datasinc = $aux['3'];
			$ip = $aux['4'];
			$ping = $aux['5'];
			$jitter = $aux['6'];
			$resUsersPPPoE_2 = mysqli_query($db, "SELECT id, mac, dataconect, uptimeconect, ppoe, vlan, down, up FROM usersPPPoE WHERE idC = '".$idConc."' AND interface = '".$interface."' AND ip = '".$ip."';");
			if(mysqli_num_rows($resUsersPPPoE_2)) {
				$usersPPPoE = mysqli_fetch_array($resUsersPPPoE_2);
				$idUser = $usersPPPoE['id'];
				$mac = $usersPPPoE['mac'];
				$dataconect = $usersPPPoE['dataconect'];
				$uptimeconect = $usersPPPoE['uptimeconect'];
				$ppoe = $usersPPPoE['ppoe'];
				$vlan = $usersPPPoE['vlan'];
				$down = $usersPPPoE['down'];
				$up = $usersPPPoE['up'];
				mysqli_query($db, "UPDATE usersPPPoE SET ping = '".$ping."', jitter = '".$jitter."' WHERE id = '".$idUser."';");
				mysqli_query($db, "INSERT INTO PingPPPoE (idC, idPPPoE, ip, data, ping, jitter) VALUES ('".$idConc."', '".$idUser."', '".$ip."', '".$datasinc."', '".$ping."', '".$jitter."');");
				exec("echo '|$mac|$datasinc|$dataconect||$uptimeconect|$ppoe|$ip|$vlan|$down|$up|$ping|$jitter|' > /var/www/html/ram/coletas/ppoe/users/$idUser");
			}
			exec("rm -fr $path_2$arquivo");
		}
	}
	$diretorio_2 -> close();
    
	if($fetClientes['ativaLinkDown_pppoe'] == '2') {
		$resUsersPPPoE_3 = mysqli_query($db, "SELECT id FROM usersPPPoE;");
		while($linkDown = mysqli_fetch_array($resUsersPPPoE_3)) {
			$resCONNECT = mysqli_query($db, "SELECT id FROM LogPPPoE WHERE idPPPoE = '".$linkDown['id']."';");
			$totalReconect = mysqli_num_rows($resCONNECT);
			mysqli_query($db, "UPDATE usersPPPoE SET linkdown = '".$totalReconect."' WHERE id = '".$linkDown['id']."';");
		}
	}
}
mysqli_close($db);
?>