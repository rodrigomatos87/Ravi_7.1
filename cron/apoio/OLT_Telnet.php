<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$idolt = $_GET["id"];
$ip = $_GET["ip"];
$port = $_GET["port"];
$user = $_GET["user"];
$senha = $_GET["senha"];
$marca = $_GET["marca"];
$cron = $_GET["cron"];
$hora = $_GET["hora"];
$data = $_GET["data"];

$nsnum = '';
$data = ''.$data.' '.$hora.'';

function insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn ) {
	$timearq = date("H-i");
	$arq = $idolt . "_" . $idslot . "_" . $idpon . "_" . $idonu . "_" . $idinterface . "_". $timearq;
	// |$data|$idolt|$idonu|$idslot|$idinterface|$provisionamento|$desc|$macnum|$rxpower|$txpower|$oltrx|$voltagem|$temperatura|$distancia|$BiasCurrent|$status|$dataconn|
	exec("echo '|$data|$idolt|$idonu|$idpon|$idslot|$idinterface|$provisionamento|$desc|$macnum|$nsnum|$rxpower|$txpower|$oltrx|$voltagem|$temperatura|$distancia|$BiasCurrent|$status|$dataconn|' > /var/www/html/ram/coletas/olt/$arq");
}

$t1 = strtotime(date("Y-m-d H:i:s"));

// NOKIA
if($marca == 7) {
	$cmd1 = "show equipment ont status pon xml";
	$cmd2 = "show equipment ont optics";
	$cmd3 = "show equipment ont operational-data";

	exec("(echo open ".$ip." ".$port."; sleep 3; echo ".$user."; sleep 1; echo ".$senha."; sleep 1; echo ".$cmd1."; sleep 360;) | telnet | egrep '<info name=\"desc1\"|<info name=\"sernum\"' | cut -d '>' -f2 | cut -d '<' -f1 | sed 'N;s/\\n/|/' | sed 's/$/|/' | sed 's/^/|/'", $array1);
	exec("(echo open ".$ip." ".$port."; sleep 3; echo ".$user."; sleep 1; echo ".$senha."; sleep 1; echo ".$cmd2."; sleep 360;) | telnet | grep '^1/1/' | awk '{print \"|\"$1\"|\"$2\"|\"$3\"|\"$4\"|\"$5\"|\"$6\"|\"$7\"|\"}'", $array2);
	exec("(echo open ".$ip." ".$port."; sleep 3; echo ".$user."; sleep 1; echo ".$senha."; sleep 1; echo ".$cmd3."; sleep 360;) | telnet | grep '^1/1/' | awk '{print \"|\"$2\"|\"$6\"|\"$7\"|\"$8\"|\"}'", $array3);

	for ($i=0; $i<count($array1); $i++) {
		$valores1 = explode("|", $array1[$i]);
		$valores2 = explode("|", $array2[$i]);
		$valores3 = explode("|", $array3[$i]);
	
		$exp = explode("/", $valores2[1]);
	
		$idslot = $exp[2];
		$idpon = $exp[3];
		$idonu = $exp[4];
		$provisionamento = "Slot/Placa: " . $idslot . " Pon: " . $idpon . " ONU: " . $idonu;
		$idinterface = $idolt . "." . $idslot . "." . $idpon . "." . $idonu;

		$macnum = $valores1[1];
		$desc = str_replace('&quot;', '', $valores1[2]);
	
		$rxpower = $valores2[2];
		$txpower = $valores2[3];
		$txpower = number_format($txpower, 2, '.', '');
		$rxpower = number_format($rxpower, 2, '.', '');
		$temperatura = $valores2[4];
		$voltagem = $valores2[5];
		$BiasCurrent = $valores2[6];
		$oltrx = $valores2[7];
	
		$distancia = $valores3[4] * 1000;
		$dataconn = "";
	
		if($idpon && $idonu) {
			if($valores3[2] == "no") {
				$status = 1;
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}else {
				$rxpower = "";
				$txpower = "";
				$oltrx = "";
				$voltagem = "";
				$temperatura = "";
				$distancia = "";
				$BiasCurrent = "";

				if($valores3[1] == "yes") {
					$status = 0;
				}else if($valores3[3] == "yes") {
					$status = 4;
				}else {
					$status = 2;
				}
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}
		}
	}

// C-DATA
}else if($marca == 9) {

	$qtdportas = exec("(sleep 1; printf \"$user\\r\\n\"; echo; sleep 2; printf \"$senha\\r\\n\"; sleep 1; printf \"show olt ?\\r\\n\"; sleep 1;) | telnet ".$ip." ".$port." | grep '<oltId>' | cut -d '<' -f3 | sed 's/>//' | sed 's/1-//'");
	
	for ($i = 1; $i <= $qtdportas; $i++) {
		$cmd1 = "show olt " . $i . " all-onu-info";
		$cmd2 = "show olt " . $i . " optical-online-onu";
		$cmd3 = "show olt " . $i . " online-onu";
		exec("(sleep 1; printf \"$user\\r\\n\"; echo; sleep 2; printf \"$senha\\r\\n\"; sleep 1; printf \"$cmd1\\r\\n\"; sleep 5; printf \"a\\r\\n\"; sleep 10; ) | telnet ".$ip." ".$port." | grep '^  " . $i . "' | awk '{print \"|\"$1\"|\"$2\"|\"$3\"|\"$4\"|\"}'", $array1);
		exec("(sleep 1; printf \"$user\\r\\n\"; echo; sleep 2; printf \"$senha\\r\\n\"; sleep 1; printf \"$cmd2\\r\\n\"; sleep 5; printf \"a\\r\\n\"; sleep 10; ) | telnet ".$ip." ".$port." | grep '^  " . $i . "' | awk '{print \"|\"$1\"|\"$2\"|\"$3\"|\"$4\"|\"$5\"|\"$6\"|\"$7\"|\"$8\"|\"}'", $array2);
		exec("(sleep 1; printf \"$user\\r\\n\"; echo; sleep 2; printf \"$senha\\r\\n\"; sleep 1; printf \"$cmd3\\r\\n\"; sleep 5; printf \"a\\r\\n\"; sleep 10; ) | telnet ".$ip." ".$port." | grep '^  " . $i . "' | awk '{print \"|\"$1\"|\"$2\"|\"$3\"|\"$4\"|\"$6\"|\"}'", $array3);
	}

	$r = 0;
	for ($i=0; $i<count($array1); $i++) {
		$valores1 = explode("|", $array1[$i]);
		$idpon = $valores1[1];
		$idonu = $valores1[2];
		$macnum = $valores1[3];
		$status = $valores1[4];
		$provisionamento = "Pon: " . $idpon . " ONU: " . $idonu;
		$idinterface = $idolt . "." . $idpon . "." . $idonu;
		$idslot = 1;

		$desc = "";
		$dataconn = "";
		$txpower = "";

		if($status == "online") {
			$valores2 = explode("|", $array2[$r]);

			if($valores2[4] == "-") {
				$status = 3;
				$rxpower = "";
				$oltrx = "";
				$voltagem = "";
				$temperatura = "";
				$distancia = "";
				$BiasCurrent = "";
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}else {
				$status = 1;
				//$idpon2 = $valores2[1];
				//$idonu2 = $valores2[2];
				//$macnum2 = $valores2[3];
				$voltagem = $valores2[4];
				$oltrx = $valores2[5];
				$rxpower = $valores2[6];
				$oltrx = number_format($oltrx, 2, '.', '');
				$rxpower = number_format($rxpower, 2, '.', '');
				$BiasCurrent = $valores2[7];
				$temperatura = $valores2[8];
				$valores3 = explode("|", $array3[$r]);
				//$idpon3 = $valores3[1];
				//$idonu3 = $valores3[2];
				//$macnum3 = $valores3[3];
				//$desc = $valores3[4];
				$distancia = $valores3[5];
				$status = 1;
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}
			
			$r = $r + 1;
		}else {
			if($status == "offline") { $status = 0; }else if($status == "powerdown") { $status = 4; }else { $status = 2; }
			$rxpower = "";
			$oltrx = "";
			$voltagem = "";
			$temperatura = "";
			$distancia = "";
			$BiasCurrent = "";
			insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
		}
	}

// Intelbras
}else if($marca == 10) {

	$nome = exec("(echo open ".$ip." ".$port."; sleep 3; echo ".$user."; sleep 1; echo ".$senha."; sleep 3; echo 'logout'; sleep 1;) | telnet | grep 'login: ' | awk '{print $1}'");
	// GPON
	if($nome) {
		exec("(echo open ".$ip." ".$port."; sleep 3; echo ".$user."; sleep 1; echo ".$senha."; sleep 1; echo 'onu status'; sleep 30; echo 'logout'; sleep 1;) | telnet | egrep \"^[0-9]|GPON\" | awk '{print \"|\"$1\"|\"$2\"|\"$3\"|\"$5\"|\"$7\"|\"$8\"|\"$9\"|\"}'", $array);
	
		for ($i=0; $i<count($array); $i++) {
			$valores = explode("|", $array[$i]);
	
			if(!isset($idpon) || $valores[1] && $valores[2] && !$valores[3] && !$valores[4] && !$valores[5] && !$valores[6] && !$valores[7] && !$valores[8]) {
				$idpon = $valores[2];
				$os_desc = array();
				$o_desc = array();
				sleep(10);
				exec("(sleep 1; printf \"$user\\r\\n\"; echo; sleep 2; printf \"$senha\\r\\n\"; sleep 1; printf \"onu description show gpon ".$idpon."\\r\\n\"; sleep 15; printf \"logout\\r\\n\"; ) | telnet ".$ip." ".$port." | grep \"^gpon \" | awk '{print \"|\"$4\"|\"$5\"|\"}'", $os_desc);
				for ($a=0; $a<count($os_desc); $a++) {
					$valores1 = explode("|", $os_desc[$a]);
					$o_desc[$valores1[1]] = $valores1[2];
				}
			}else if($valores[1] != "Serial" && $valores[2] != "OMCI" && $valores[3] != "Config" && $valores[4] != "ONU") {
				$idonu = $valores[1];
				$provisionamento = "Pon: " . $idpon . " ONU: " . $idonu;
				$idinterface = $idolt . "." . $idpon . "." . $idonu;
				$idslot = 1;
			
				$macnum = $valores[2];
				$status = $valores[3];
				$oltrx = $valores[4];
				$rxpower = $valores[5];
				$oltrx = number_format($oltrx, 2, '.', '');
				$rxpower = number_format($rxpower, 2, '.', '');
				$erro = explode("+", $valores[6]);
				$distancia = $valores[7] * 1000;
				
				$desc = $o_desc[$idonu];           
				
				$txpower = "";
				$voltagem = "";
				$temperatura = "";
				$BiasCurrent = "";
				$dataconn = "";
				
				if($status == "Active") {
					$status = 1;
					$rxpower = number_format($rxpower, 2, '.', '');
					$oltrx = number_format($oltrx, 2, '.', '');
					insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
	
				}else {
					$oltrx = "";
					$rxpower = "";
					if($erro[1] == "DGI") {
						$status = 4;
					}else if($erro[1] == "LOAMI" || $erro[1] == "LOFI" || $erro[1] == "LOSI") {
						$status = 0;
					}else {
						$status = 2;
					}
					insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
				}
			}
		}

	}else {

		$nome = exec("(echo open ".$ip." ".$port."; sleep 3; echo ".$user."; sleep 1; echo ".$senha."; sleep 3;) | telnet | sed 's/>//'");
		// EPON
		if($nome) {
			$cmd1 = "show onu-status";
			$cmd2 = "show onu-optical-info";
			$cmd3 = "show pon";
			exec("(echo open ".$ip." ".$port."; sleep 3; echo ".$user."; sleep 1; echo ".$senha."; sleep 1; echo ".$cmd1."; sleep 10;) | telnet | grep \"^0/\" | awk '{print \"|\"$1\"|\"$2\"|\"$3\"|\"$8\"|\"}'", $array1);
			exec("(echo open ".$ip." ".$port."; sleep 3; echo ".$user."; sleep 1; echo ".$senha."; sleep 1; echo 'enable'; sleep 1; echo 'configure terminal'; sleep 1; echo ".$cmd2."; sleep 40;) | telnet | grep \"^0/\" | awk '{print \"|\"$1\"|\"$2\"|\"$3\"|\"$4\"|\"$5\"|\"$6\"|\"}'", $array2);
			exec("(echo open ".$ip." ".$port."; sleep 3; echo ".$user."; sleep 1; echo ".$senha."; sleep 1; echo 'enable'; sleep 1; echo 'configure terminal'; sleep 1; echo ".$cmd3."; sleep 40;) | telnet | grep \"^ 0/\" | awk '{print \"|\"$1\"|\"$6\"|\"}'", $array3);
	
			$num = 0;
			for ($i=0; $i<count($array1); $i++) {
				$valores1 = explode("|", $array1[$i]);
				$valores2 = explode("/", $valores1[1]);
				$valores3 = explode("|", $array2[$num]);
				$valores4 = explode("|", $array3[$num]);
	
				$idpon = $valores2[1];
				$idonu = $valores2[2];
				$macnum = $valores1[2];
				$distancia = $valores1[3];
				$status = preg_replace("/[^A-Za-z]/", "", $valores1[4]);
				$desc = substr($valores4[2],0,-1);
	
				$valores5 = explode("/", $valores3[5]);
				$valores6 = explode("/", $valores3[6]);
	
				$temperatura = $valores3[2];
				$voltagem = $valores3[3];
				$BiasCurrent = $valores3[4];
				$txpower = $valores5[1];
				$rxpower = substr($valores6[1],0,-1);
				$txpower = number_format($txpower, 2, '.', '');
				$rxpower = number_format($rxpower, 2, '.', '');

				$oltrx = "";
				$dataconn = "";
				
				$provisionamento = "Pon: " . $idpon . " ONU: " . $idonu;
				$idinterface = $idolt . "." . $idpon . "." . $idonu;
				$idslot = 1;
	
				if($status == "Up") {
					$status = 1;
					insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
					$num = $num + 1;
				}else {
					$txpower = "";
					$rxpower = "";
					$voltagem = "";
					$temperatura = "";
					$BiasCurrent = "";
					$distancia = "";
					$status = 2;
					insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
				}
			}
		}
	}

// V-Solution
}else if($marca == 12) {
	$qtdportas = exec("(sleep 1; printf \"$user\\r\\n\"; echo; sleep 2; printf \"$senha\\r\\n\"; sleep 1; printf \"show olt ?\\r\\n\"; sleep 1;) | telnet ".$ip." ".$port." | grep '<oltId>' | cut -d '<' -f3 | sed 's/>//' | sed 's/1-//'");
	for ($i = 1; $i <= $qtdportas; $i++) {
		$cmd1 = "show olt " . $i . " all-onu-info";
		$cmd2 = "show olt " . $i . " optical-online-onu";
		$cmd3 = "show olt " . $i . " online-onu";
		exec("(sleep 1; printf \"$user\\r\\n\"; echo; sleep 2; printf \"$senha\\r\\n\"; sleep 1; printf \"$cmd1\\r\\n\"; sleep 5; printf \"a\\r\\n\"; sleep 10; ) | telnet ".$ip." ".$port." | grep '^  " . $i . "' | awk '{print \"|\"$1\"|\"$2\"|\"$3\"|\"$4\"|\"}'", $array1);
		exec("(sleep 1; printf \"$user\\r\\n\"; echo; sleep 2; printf \"$senha\\r\\n\"; sleep 1; printf \"$cmd2\\r\\n\"; sleep 5; printf \"a\\r\\n\"; sleep 10; ) | telnet ".$ip." ".$port." | grep '^  " . $i . "' | awk '{print \"|\"$1\"|\"$2\"|\"$3\"|\"$4\"|\"$5\"|\"$6\"|\"$7\"|\"}'", $array2);
		exec("(sleep 1; printf \"$user\\r\\n\"; echo; sleep 2; printf \"$senha\\r\\n\"; sleep 1; printf \"$cmd3\\r\\n\"; sleep 5; printf \"a\\r\\n\"; sleep 10; ) | telnet ".$ip." ".$port." | grep '^  " . $i . "' | awk '{print $6}'", $array3);
	}

	$r = 0;
	for ($i=0; $i<count($array1); $i++) {
		$valores1 = explode("|", $array1[$i]);
		$idpon = $valores1[1];
		$idonu = $valores1[2];
		$macnum = $valores1[3];
		$status = $valores1[4];
		$provisionamento = "Pon: " . $idpon . " ONU: " . $idonu;
		$idslot = 1;
		$idinterface = $idolt . "." . $idpon . "." . $idonu;
		$desc = "";
		$dataconn = "";
		$oltrx = "";
		if($status == "online") {
			$valores2 = explode("|", $array2[$r]);
			$status = 1;
			$voltagem = $valores2[3];
			$txpower = $valores2[4];
			$rxpower = $valores2[5];
			$txpower = number_format($txpower, 2, '.', '');
			$rxpower = number_format($rxpower, 2, '.', '');
			$BiasCurrent = $valores2[6];
			$temperatura = $valores2[7];
			$distancia = $array3[$r];
			insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			$r = $r + 1;
		}else {
			if($status == "offline") { $status = 0; }else if($status == "powerdown") { $status = 4; }else { $status = 2; }
			$rxpower = "";
			$txpower = "";
			$voltagem = "";
			$temperatura = "";
			$distancia = "";
			$BiasCurrent = "";
			insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
		}
	}
}

$t2 = strtotime(date("Y-m-d H:i:s"));
$segundos = round(abs($t1 - $t2));
$mudacron = 1;

// 5 minutos
if($cron == 1 && $segundos > 300) {
    $mudacron = 2;
// 10 minutos
}else if(($cron == 2 || $mudacron == 2) && $segundos > 600) {
    $mudacron = 3;
// 15 minutos
}else if(($cron == 3 || $mudacron == 3) && $segundos > 900) {
    $mudacron = 4;
// 30 minutos
}else if(($cron == 4 || $mudacron == 4) && $segundos > 1800) {
    $mudacron = 5;
// 1 hora
}else if(($cron == 5 || $mudacron == 5) && $segundos > 3600) {
    $mudacron = 6;
// 2 horas
}else if(($cron == 6 || $mudacron == 6) && $segundos > 7200) {
    $mudacron = 7;
// 3 horas
}else if(($cron == 7 || $mudacron == 7) && $segundos > 10800) {
    $mudacron = 8;
}

include("/var/www/html/conexao.php");
if($mudacron > 1) {
    mysqli_query($db, "UPDATE olts SET temposinc = '".$segundos."', ocronolt = '".$mudacron."' WHERE id = '".$idolt."';");
}else {
    mysqli_query($db, "UPDATE olts SET temposinc = '".$segundos."' WHERE id = '".$idolt."';");
}
mysqli_close($db);
exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
?>