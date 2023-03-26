#!/usr/bin/php
<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$idolt = $_GET["id"];
$host = $_GET["ip"];
$community = $_GET["snmp"];
$vsnmp = $_GET["vsnmp"];
$porta = $_GET["porta"];
$marca = $_GET["marca"];
$cron = $_GET["cron"];
$hora = $_GET["hora"];
$data = $_GET["data"];

$nsnum = '';
$data = ''.$data.' '.$hora.'';

$retries = 5;
$timeout = 30;

function insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn ) {
	$timearq = date("H-i");
	$arq = $idolt . "_" . $idslot . "_" . $idpon . "_" . $idonu . "_" . $idinterface . "_". $timearq;
	// |$data|$idolt|$idonu|$idslot|$idinterface|$provisionamento|$desc|$macnum|$rxpower|$txpower|$oltrx|$voltagem|$temperatura|$distancia|$BiasCurrent|$status|$dataconn|
	exec("echo '|$data|$idolt|$idonu|$idpon|$idslot|$idinterface|$provisionamento|$desc|$macnum|$nsnum|$rxpower|$txpower|$oltrx|$voltagem|$temperatura|$distancia|$BiasCurrent|$status|$dataconn|' > /var/www/html/ram/coletas/olt/$arq");
}

$t1 = strtotime(date("Y-m-d H:i:s"));

/*
Status:
	0) Offline por rompimento
	1) Online
	2) Offline
	3) Online sem dados
	4) Offline por desligamento de energia
*/

// V-Solution
if($marca == 1) {
	function sanitizeString($string1) {
		$what = array( 'INTEGER: ', 'C', 'mA', 'V', 'STRING: ', '"', 'dBm', 'dbm', 'NULL', 'Gauge32: ', ' ' );
		$by   = array( '','','','','','','','','','','' );
		return str_replace($what, $by, $string1);
	}
	
	if($vsnmp == 1) {
		$o_tipo = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.2.1.1.1.0", 1000000, 30));
	}else if($vsnmp == 2) {
		$o_tipo = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.2.1.1.1.0", 1000000, 30));
	}

	// epon
	if(!$o_tipo || $o_tipo == "1600D") {
		if($vsnmp == 1) {
			$identerprise = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.1", 1000000, 30));
		}else if($vsnmp == 2) {
			$identerprise = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.1", 1000000, 30));
		}
		if($identerprise) {
			if($vsnmp == 1) {
				$idponenterprise = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.2", 1000000, 30));
				$idonuenterprise = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.3", 1000000, 30));
				$statusop = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.5", 1000000, 30));
				$nummac = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.6", 1000000, 30));
				$descricao = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.10", 1000000, 30));
				$valdist = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.25.1.17", 1000000, 30));
			}else if($vsnmp == 2) {
				$idponenterprise = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.2", 1000000, 30));
				$idonuenterprise = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.3", 1000000, 30));
				$statusop = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.5", 1000000, 30));
				$nummac = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.6", 1000000, 30));
				$descricao = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.10", 1000000, 30));
				$valdist = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.25.1.17", 1000000, 30));
			}
			for ($e=0; $e<count($identerprise); $e++) {
				$idslot = 0;
				$idpon = $idponenterprise[$e];
				$idonu = $idonuenterprise[$e];
				$status = $statusop[$e];
				$macnum = strtoupper($nummac[$e]);
				$desc = $descricao[$e];
				$distancia = $valdist[$e];
				$provisionamento = "Pon: ". $idpon . " ONU: " . $idonu;
				// No caso da V-Solutions vamos salvar aqui o id de indexação em enterprises SNMP
				$idinterface = $identerprise[$e];
				$oltrx = "";
				$dataconn = "";
				$nsnum = "";

				if($status == 1) {
					if($vsnmp == 1) {
						$temperatura = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.3.{$idpon}.{$idonu}", 1000000, 30));
						$voltagem = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.4.{$idpon}.{$idonu}", 1000000, 30));
						$BiasCurrent = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.5.{$idpon}.{$idonu}", 1000000, 30));
						$txp = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.6.{$idpon}.{$idonu}", 1000000, 30));
						$rxp = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.{$idpon}.{$idonu}", 1000000, 30));
					}else if($vsnmp == 2) {
						$temperatura = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.3.{$idpon}.{$idonu}", 1000000, 30));
						$voltagem = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.4.{$idpon}.{$idonu}", 1000000, 30));
						$BiasCurrent = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.5.{$idpon}.{$idonu}", 1000000, 30));
						$txp = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.6.{$idpon}.{$idonu}", 1000000, 30));
						$rxp = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.{$idpon}.{$idonu}", 1000000, 30));
					}
					if($txp) {
						$ex1 = explode("(", $txp);
						$txpower = str_replace(')', '', $ex1[1]);
					}
					if($rxp) {
						$ex2 = explode("(", $rxp);
						$rxpower = str_replace(')', '', $ex2[1]);
					}
					$txpower = number_format($txpower, 2, '.', '');
					$rxpower = number_format($rxpower, 2, '.', '');
					insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
				}else {
					$rxpower = "";
					$txpower = "";
					$oltrx = "";
					$voltagem = "";
					$temperatura = "";
					$distancia = "";
					$BiasCurrent = "";
					$dataconn = "";
					$status = 2;
					insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
				}
			}
			exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
		}
	// Novos modelos de gpon
	}else if($o_tipo == "1600G1" || $o_tipo == "1600G1B" || $o_tipo == "1600G1B1" || $o_tipo == "1600G0" || $o_tipo == "1600G0B") {
		if($vsnmp == 1) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.4.1.37950.1.1.6.1.1.1.1.5 | sed \"s/iso.3.6.1.4.1.37950.1.1.6.1.1.1.1.5.//g\" | sed \"s/ = INTEGER: /./g\"", $statusop);
			$nummac = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.2.1.5", 1000000, 30));
			$valdist = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.12.1.3", 1000000, 30));
		}else if($vsnmp == 2) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.4.1.37950.1.1.6.1.1.1.1.5 | sed \"s/iso.3.6.1.4.1.37950.1.1.6.1.1.1.1.5.//g\" | sed \"s/ = INTEGER: /./g\"", $statusop);
			$nummac = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.2.1.5", 1000000, 30));
			$valdist = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.12.1.3", 1000000, 30));
		}
		for ($i=0; $i<count($statusop); $i++) {
			$exp = explode(".", $statusop[$i]);
			$idpon = $exp[0];
			$idonu = $exp[1];
			$ostatus = $exp[2];
			$provisionamento = "Pon: ". $idpon . " ONU: " . $idonu;
			$idinterface = $idpon . "." . $idonu;
			$macnum = strtoupper($nummac[$i]);
			$distancia = $valdist[$i];
			$oltrx = "";
			$dataconn = "";
			$desc = "";
			$idslot = 0;
			if($ostatus == 3) {
				if($vsnmp == 1) {
					$temperatura = str_replace('()', '', sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.3.{$idpon}.{$idonu}", 1000000, 30)));
					$voltagem = str_replace('()', '', sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.4.{$idpon}.{$idonu}", 1000000, 30)));
					$BiasCurrent = str_replace('()', '', sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.5.{$idpon}.{$idonu}", 1000000, 30)));
					$txpower = str_replace('()', '', sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.6.{$idpon}.{$idonu}", 1000000, 30)));
					$rxpower = str_replace('()', '', sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.7.{$idpon}.{$idonu}", 1000000, 30)));
				}else if($vsnmp == 2) {
					$temperatura = str_replace('()', '', sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.3.{$idpon}.{$idonu}", 1000000, 30)));
					$voltagem = str_replace('()', '', sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.4.{$idpon}.{$idonu}", 1000000, 30)));
					$BiasCurrent = str_replace('()', '', sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.5.{$idpon}.{$idonu}", 1000000, 30)));
					$txpower = str_replace('()', '', sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.6.{$idpon}.{$idonu}", 1000000, 30)));
					$rxpower = str_replace('()', '', sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.7.{$idpon}.{$idonu}", 1000000, 30)));
				}
				$txpower = number_format($txpower, 2, '.', '');
				$rxpower = number_format($rxpower, 2, '.', '');
				$status = 1;
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}else {
				//$status = $ostatus;
				// 6 off e 4 off
				$rxpower = "";
				$txpower = "";
				$oltrx = "";
				$voltagem = "";
				$temperatura = "";
				$distancia = "";
				$BiasCurrent = "";
				$dataconn = "";
				$status = 2;
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}
		}
		exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
		
	// outros modelos gpon mais antigos
	}else {
		if($vsnmp == 1) {
			$identerprise = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.2.7", 1000000, 30));
		}else if($vsnmp == 2) {
			$identerprise = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.2.7", 1000000, 30));
		}
		if($identerprise) {
			if($vsnmp == 1) {
				$idponenterprise = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.1.7", 1000000, 30));
				$statusop = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.1.1.5", 1000000, 30));
				$nummac = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.2.1.5", 1000000, 30));
				$valdist = sanitizeString(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.12.1.3", 1000000, 30));
			}else if($vsnmp == 2) {
				$idponenterprise = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.1.7", 1000000, 30));
				$statusop = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.1.1.5", 1000000, 30));
				$nummac = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.2.1.5", 1000000, 30));
				$valdist = sanitizeString(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.12.1.3", 1000000, 30));
			}
			for ($e=0; $e<count($identerprise); $e++) {
				$idpon = $idponenterprise[$e];
				$idonu = $identerprise[$e];
				$macnum = strtoupper($nummac[$e]);
				$distancia = $valdist[$e];
				$provisionamento = "Pon: ". $idpon . " ONU: " . $idonu;
				$idinterface = $idpon . "." . $idonu;
				$oltrx = "";
				$dataconn = "";
				$desc = "";
				if($statusop[$e] == 3) {
					if($vsnmp == 1) {
						$temperatura = str_replace('()', '', sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.3.{$idpon}.{$idonu}", 1000000, 30)));
						$voltagem = str_replace('()', '', sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.4.{$idpon}.{$idonu}", 1000000, 30)));
						$BiasCurrent = str_replace('()', '', sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.5.{$idpon}.{$idonu}", 1000000, 30)));
						$txpower = str_replace('()', '', sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.6.{$idpon}.{$idonu}", 1000000, 30)));
						$rxpower = str_replace('()', '', sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.7.{$idpon}.{$idonu}", 1000000, 30)));
					}else if($vsnmp == 2) {
						$temperatura = str_replace('()', '', sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.3.{$idpon}.{$idonu}", 1000000, 30)));
						$voltagem = str_replace('()', '', sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.4.{$idpon}.{$idonu}", 1000000, 30)));
						$BiasCurrent = str_replace('()', '', sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.5.{$idpon}.{$idonu}", 1000000, 30)));
						$txpower = str_replace('()', '', sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.6.{$idpon}.{$idonu}", 1000000, 30)));
						$rxpower = str_replace('()', '', sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.6.1.1.3.1.7.{$idpon}.{$idonu}", 1000000, 30)));
					}
					$txpower = number_format($txpower, 2, '.', '');
					$rxpower = number_format($rxpower, 2, '.', '');
					$status = 1;
					insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
				}else {
					//$status = $statusop[$e];
					// 6 off e 4 off
					$rxpower = "";
					$txpower = "";
					$oltrx = "";
					$voltagem = "";
					$temperatura = "";
					$distancia = "";
					$BiasCurrent = "";
					$dataconn = "";
					$status = 2;
					insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
				}
			}
			exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
		}
	}

// Huawei	
}else if($marca == 2) {
	function sanitizeSNMP($string) {
		$what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ', '"' );
		$by   = array( '', '', '', '', '' );
		return str_replace($what, $by, $string);
	}
	
	function ajeitamac($string3) {
		$what = array( 'Hex-STRING: ', 'STRING: ', '"', ' ' );
		$by   = array( '','','','' );
		return str_replace($what, $by, $string3);
	}
	
	function hexToStr($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
	
	function macTo($string){
		$mac = str_replace(' ', '', $string);
		$a = hexToStr($mac[0] . $mac[1]);
		$b = hexToStr($mac[2] . $mac[3]);
		$c = hexToStr($mac[4] . $mac[5]);
		$d = hexToStr($mac[6] . $mac[7]);
		$ns = $a . $b . $c . $d . str_replace($mac[0].$mac[1].$mac[2].$mac[3].$mac[4].$mac[5].$mac[6].$mac[7], '', $mac);
		return $ns;
	}
	
	$stdno = 0;
	if($vsnmp == 1) {
		exec("/bin/snmpbulkwalk -v1 -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4", $busca_rxpower, $stdno);
	}else if($vsnmp == 2) {
		exec("/bin/snmpbulkwalk -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4", $busca_rxpower, $stdno);
	}
	$stdno = (int)$stdno;

	if($stdno) {
		$stdno = 0;
		if($vsnmp == 1) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4", $busca_rxpower, $stdno);
		}else if($vsnmp == 2) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4", $busca_rxpower, $stdno);
		}
		$stdno = (int)$stdno;
	}

	if(!$stdno) {
		for ($a=0; $a<count($busca_rxpower); $a++) {
			$exp1 = explode(" = ", $busca_rxpower[$a]);
			$exp2 = explode(".", str_replace('iso.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.', '', $exp1[0]));
			$idinterface = $exp2[0];
			$idonu = $exp2[1];
			$rxpower = sanitizeSNMP($exp1[1]);

			$oids1 = array();
			$oids1[] = ".1.3.6.1.2.1.31.1.1.1.1." . $idinterface;                                     // ifName
			$oids1[] = ".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9." . $idinterface . "." . $idonu;         // desc
			$oids1[] = ".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3." . $idinterface . "." . $idonu;         // macnum
			if($rxpower == 2147483647) {
				$oids1[] = ".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24." . $idinterface . "." . $idonu;    // status
			}else {
				$oids1[] = ".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.20." . $idinterface . "." . $idonu;    // distancia
				$oids1[] = ".1.3.6.1.4.1.2011.6.128.1.1.2.51.1.5." . $idinterface . "." . $idonu;     // voltagem
				$oids1[] = ".1.3.6.1.4.1.2011.6.128.1.1.2.51.1.3." . $idinterface . "." . $idonu;     // txpower
				$oids1[] = ".1.3.6.1.4.1.2011.6.128.1.1.2.51.1.2." . $idinterface . "." . $idonu;     // BiasCurrent
				$oids1[] = ".1.3.6.1.4.1.2011.6.128.1.1.2.51.1.1." . $idinterface . "." . $idonu;     // temperatura
				$oids1[] = ".1.3.6.1.4.1.2011.6.128.1.1.2.51.1.6." . $idinterface . "." . $idonu;     // oltrxpower
			}
			$oidspart1 = implode(' ', $oids1);

			if($vsnmp == 1) {
				$cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
			}else if($vsnmp == 2) {
				$cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
			}

			$stdno = 0;
			$analysis = array();
			exec ($cmd1, $analysis, $stdno);
			$stdno = (int)$stdno;

			if(!$stdno) {
				$dataconn = "";
				if(count($analysis) == 4) {
					$b = explode('= ', $analysis[0]);
					$ifname = sanitizeSNMP($b[1]);
					$c = explode('= ', $analysis[1]);
					$desc = sanitizeSNMP($c[1]);
					$d = explode('= ', $analysis[2]);
					$macnum = ajeitamac($d[1]);
					$e = explode('= ', $analysis[3]);
					$status = sanitizeSNMP($e[1]);
					if($status == 13) {
						$status = 4;
					}else if($status == 2) {
						$status = 0;
					}else {
						$status = 2; // $status == -1 (SEM INFORMAÇÕES)
					}
					$rxpower = "";
					$txpower = "";
					$oltrx = "";
					$voltagem = "";
					$temperatura = "";
					$distancia = "";
					$BiasCurrent = "";
				}else {
					$b = explode('= ', $analysis[0]);
					$ifname = sanitizeSNMP($b[1]);
					$c = explode('= ', $analysis[1]);
					$desc = sanitizeSNMP($c[1]);
					$d = explode('= ', $analysis[2]);
					$macnum = ajeitamac($d[1]);
					$e = explode('= ', $analysis[3]);
					$distancia = sanitizeSNMP($e[1]);
					$f = explode('= ', $analysis[4]);
					$voltagem = sanitizeSNMP($f[1]);
					$g = explode('= ', $analysis[5]);
					$txpower = sanitizeSNMP($g[1]);
					$h = explode('= ', $analysis[6]);
					$BiasCurrent = sanitizeSNMP($h[1]);
					$i = explode('= ', $analysis[7]);
					$temperatura = sanitizeSNMP($i[1]);
					$j = explode('= ', $analysis[8]);
					$oltrx = sanitizeSNMP($j[1]);
					$status = 1;
					
					if($txpower) { $txpower = $txpower / 100; }else { $txpower = ''; }
					if($rxpower) { $rxpower = $rxpower / 100; }else { $rxpower = ''; }
					if($voltagem) { $voltagem = $voltagem / 100; }else { $voltagem = ''; }
					if($oltrx) { $oltrx = ($oltrx - 10000) / 100; }else { $oltrx = ''; }
				}

				$nsnum = macTo($macnum);
				$exp3 = explode("/", $ifname);
				$idslot = $exp3[1];
				$idpon = $exp3[2];
				$provisionamento = "Slot/Placa: " . $idslot . " Pon: " . $idpon . " ONU: " . $idonu;

				/*
				if($status == 1) {
					echo $provisionamento . "\n";
					echo " | Desc: " . $desc . "\n";
					echo " | MAC: " . $macnum . "\n";
					echo " | NS: " . $nsnum . "\n";
					echo " | RX POWER: " . $rxpower . " dBm\n";
					echo " | TX POWER: " . $oltrx . " dBm\n";
					echo " | OLT RX POWER: " . $txpower . " dBm\n";
					echo " | Bias Current: " . $BiasCurrent . " mA\n";
					echo " | Temperatura: " . $temperatura . "° C\n";
					echo " | Voltagem: " . $voltagem . " V\n";
					echo " | Distancia até a OLT: " . $distancia . " metros\n";
					echo " | Status: Online (".$status.")\n\n";
				}else {
					echo $provisionamento . "\n";
					echo " | Desc: " . $desc . "\n";
					echo " | MAC: " . $macnum . "\n";
					echo " | NS: " . $nsnum . "\n";
					echo " | Status: Offline (".$status.")\n\n";
				}
				*/
				
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}
		}
		exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
	}else {
		sleep(5);
		exec("php -f /var/www/html/testeConnectOLT.php id=$idolt &");
	}

// Fiberhome
}else if($marca == 3) {
	function sanitizeSNMP($string) {
		$what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ', '"' );
		$by   = array( '', '', '', '', '' );
		return str_replace($what, $by, $string);
	}

	$stdno = 0;

	if($vsnmp == 1) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -Cc -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.5875.800.3.10.1.1.11", $o_status, $stdno);
	}else if($vsnmp == 2) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.5875.800.3.10.1.1.11", $o_status, $stdno);
	}

	$stdno = (int)$stdno;

	// Verifica se a conexão SNMP funcionou
	if(!$stdno) {
		if($vsnmp == 1) {
			$o_idslot = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.10.1.1.2", 1000000, 30));
			$o_idpon = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.10.1.1.3", 1000000, 30));
			$o_idonu = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.10.1.1.4", 1000000, 30));
			$o_macnum = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.10.1.1.10", 1000000, 30));
			$o_rxpower = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.3.1.6", 1000000, 30));
			$o_txpower = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.3.1.7", 1000000, 30));
			$o_voltagem = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.3.1.8", 1000000, 30));
			$o_BiasCurrent = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.3.1.9", 1000000, 30));
			$o_temperatura = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.3.1.10", 1000000, 30));
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -Cc -c " . $community . " " . $host . ":" . $porta . " 1.3.6.1.4.1.5875.800.3.9.6.1.1.1 | cut -d \"=\" -f2 | sed \"s/ INTEGER: //g\"", $o_distancia);
		}else if($vsnmp == 2) {
			$o_idslot = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.10.1.1.2", 1000000, 30));
			$o_idpon = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.10.1.1.3", 1000000, 30));
			$o_idonu = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.10.1.1.4", 1000000, 30));
			$o_macnum = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.10.1.1.10", 1000000, 30));
			$o_rxpower = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.3.1.6", 1000000, 30));
			$o_txpower = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.3.1.7", 1000000, 30));
			$o_voltagem = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.3.1.8", 1000000, 30));
			$o_BiasCurrent = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.3.1.9", 1000000, 30));
			$o_temperatura = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.3.1.10", 1000000, 30));
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " 1.3.6.1.4.1.5875.800.3.9.6.1.1.1 | cut -d \"=\" -f2 | sed \"s/ INTEGER: //g\"", $o_distancia);
		}
	
		$n = 0;
		for ($i=0; $i<count($o_status); $i++) {
			$exp = explode(" = ", $o_status[$i]);
			$idinterface = str_replace('iso.3.6.1.4.1.5875.800.3.10.1.1.11.', '', $exp[0]);
			$status = sanitizeSNMP($exp[1]);
			$idslot = $o_idslot[$i];
			$idpon = $o_idpon[$i];
			$idonu = $o_idonu[$i];
			$provisionamento = "Slot/Placa: " . $idslot . " Pon: ". $idpon . " ONU: " . $idonu;
			$macnum = $o_macnum[$i];
			$desc = "";
			$oltrx = "";
			$dataconn = "";
	
			if($status == 1) {
				$rxpower = $o_rxpower[$n] / 100;
				$txpower = $o_txpower[$n] / 100;
				$txpower = number_format($txpower, 2, '.', '');
				$rxpower = number_format($rxpower, 2, '.', '');
				$voltagem = $o_voltagem[$n] / 100;
				$BiasCurrent = $o_BiasCurrent[$n] / 100;
				$temperatura = $o_temperatura[$n] / 100;
				$distancia = $o_distancia[$n];
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
				$n = $n + 1; 
			}else {
				if($status == 0){
					$status = 0;
				}else if($status == 2){
					$status = 4;
				}else if($status == 3){
					$status = 2;
				}
				$rxpower = "";
				$txpower = "";
				$voltagem = "";
				$temperatura = "";
				$distancia = "";
				$BiasCurrent = "";
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}
		}

		exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
	}else {
		sleep(5);
		exec("php -f /var/www/html/testeConnectOLT.php id=$idolt &");
		//include("/var/www/html/cron/apoio/conexao.php");
		//mysqli_query($db, "UPDATE olts SET status = '1' WHERE id = ".$idolt."");
		//mysqli_close($db);
	}

// ZTE
}else if($marca == 4) {
	$timeout = 40;
	
	function sanitizeSNMP($string) {
		$what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ', '"' );
		$by   = array( '', '', '', '', '' );
		return str_replace($what, $by, $string);
	}
	
	function ajeitamac($string3) {
		$what = array( 'Hex-STRING: ', 'STRING: ', '"', ' ' );
		$by   = array( '','','','' );
		return str_replace($what, $by, $string3);
	}
	
	function hexToStr($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
	
	function macTo($string){
		$mac = str_replace(' ', '', $string);
		$a = hexToStr($mac[0] . $mac[1]);
		$b = hexToStr($mac[2] . $mac[3]);
		$c = hexToStr($mac[4] . $mac[5]);
		$d = hexToStr($mac[6] . $mac[7]);
		$ns = $a . $b . $c . $d . str_replace($mac[0].$mac[1].$mac[2].$mac[3].$mac[4].$mac[5].$mac[6].$mac[7], '', $mac);
		return $ns;
	}
	
	$stdno = 0;
	if($vsnmp == 1) {
		exec("/bin/snmpbulkwalk -v1 -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.2.1.31.1.1.1.1", $buscaPON, $stdno);
		exec("/bin/snmpbulkwalk -v1 -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.3902.1012.3.13.1.1.2", $o_nomeinterface, $stdno);
	}else if($vsnmp == 2) {
		exec("/bin/snmpbulkwalk -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.2.1.31.1.1.1.1", $buscaPON, $stdno);
		exec("/bin/snmpbulkwalk -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.3902.1012.3.13.1.1.2", $o_nomeinterface, $stdno);
	}
	$stdno = (int)$stdno;
	
	if($stdno) {
		$stdno = 0;
		if($vsnmp == 1) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.2.1.31.1.1.1.1", $buscaPON, $stdno);
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.3902.1012.3.13.1.1.2", $o_nomeinterface, $stdno);
		}else if($vsnmp == 2) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.2.1.31.1.1.1.1", $buscaPON, $stdno);
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.3902.1012.3.13.1.1.2", $o_nomeinterface, $stdno);
		}
		$stdno = (int)$stdno;
	}
	
	if(!$stdno) {
		$slotPon = array();
		for ($u=0; $u<count($buscaPON); $u++) {
			$exp = explode(" = ", $buscaPON[$u]);
			$exp1 = explode("_", sanitizeSNMP($exp[1]));
			if(sanitizeSNMP($exp1[0]) == "gpon") {
				$exppp = explode(" = ", $o_nomeinterface[$u]);
				$idinterface = str_replace('iso.3.6.1.4.1.3902.1012.3.13.1.1.2.', '', $exppp[0]);
				$exp2 = explode("/", $exp1[1]);
				$o_slot = sanitizeSNMP($exp2[1]);
				$o_pon = sanitizeSNMP($exp2[2]);
				$slotPon[$idinterface] = $o_slot . "|" . $o_pon;
				//echo $idinterface . "|" . $o_slot . "|" . $o_pon . "<br><br>";
			}
		}
	
		if($vsnmp == 1) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . $community . " " . $host . ":" . $porta . " 1.3.6.1.4.1.3902.1012.3.28.2.1.4", $o_status);
		}else if($vsnmp == 2) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " 1.3.6.1.4.1.3902.1012.3.28.2.1.4", $o_status);
		}
	
		for ($i=0; $i<count($o_status); $i++) {
			$exp2 = explode(" = ", $o_status[$i]);
			$exp3 = explode(".", $exp2[0]);
			$idinterface = $exp3[13];
			$idonu = $exp3[14];
			$status = sanitizeSNMP($exp2[1]);
			$oids1 = array();
			if($status == 3) {
				$oids1[] = ".1.3.6.1.4.1.3902.1012.3.28.1.1.5." . $idinterface . "." . $idonu;                  // o_macnum
				$oids1[] = ".1.3.6.1.4.1.3902.1012.3.28.1.1.2." . $idinterface . "." . $idonu;                  // o_desc
				$oids1[] = ".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10." . $idinterface . "." . $idonu . ".1";       // o_rxpower
				$oids1[] = ".1.3.6.1.4.1.3902.1012.3.50.12.1.1.14." . $idinterface . "." . $idonu . ".1";       // o_txpower
				$oids1[] = ".1.3.6.1.4.1.3902.1012.3.11.4.1.2." . $idinterface . "." . $idonu;                  // o_distancia
				$status = 1;
			}else {
				$txpower = "";
				$rxpower = "";
				$distancia = "";
				$oids1[] = ".1.3.6.1.4.1.3902.1012.3.28.1.1.5." . $idinterface . "." . $idonu;                  // o_macnum
				$oids1[] = ".1.3.6.1.4.1.3902.1012.3.28.1.1.2." . $idinterface . "." . $idonu;                  // o_desc
				$rxpower = "";
				$txpower = "";
				if($status == 4) {
					$status = 4;
				}else if($status == 6) {
					$status = 2;
				}else if($status == 1) {
					$status = 0;
				}
			}
	
			$oidspart1 = implode(' ', $oids1);
			
			if($vsnmp == 1) {
				$cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
			}else if($vsnmp == 2) {
				$cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
			}
	
			$stdno = 0;
			$analysis1 = array();
			exec ($cmd1, $analysis1, $stdno);
			$stdno = (int)$stdno;
	
			// Verifica se a conexão SNMP funcionou
			if(!$stdno) {
				$exx3 = explode("|", $slotPon[$idinterface]);
				$idslot = $exx3[0];
				$idpon = $exx3[1];
				$provisionamento = "Slot/Placa: " . $idslot . " Pon: " . $idpon . " ONU: " . $idonu;
				$dataconn = "";
				if(count($analysis1) == 2) {
					$a = explode('= ', $analysis1[0]);
					$macnum = ajeitamac($a[1]);
					$b = explode('= ', $analysis1[1]);
					$desc = sanitizeSNMP($b[1]);
					$nsnum = macTo($macnum);
				}else if(count($analysis1) == 5) {
					$a = explode('= ', $analysis1[0]);
					$macnum = ajeitamac($a[1]);
					$b = explode('= ', $analysis1[1]);
					$desc = sanitizeSNMP($b[1]);
					$nsnum = macTo($macnum);
					$c = explode('= ', $analysis1[2]);
					$rxpower = sanitizeSNMP($c[1]);
					$d = explode('= ', $analysis1[3]);
					$txpower = sanitizeSNMP($d[1]);
					$e = explode('= ', $analysis1[4]);
					$distancia = sanitizeSNMP($e[1]);
	
					if($txpower == "No Such Instance currently exists at this OID")  { $txpower = ""; }
					if($rxpower == "No Such Instance currently exists at this OID")  { $rxpower = ""; }
					if($txpower) { $txpower = $txpower * 0.002 - 30; }
					if($rxpower) { $rxpower = $rxpower * 0.002 - 30; }
	
					$txpower = number_format($txpower, 2, '.', '');
					$rxpower = number_format($rxpower, 2, '.', '');
				
					if($rxpower > 60) { $rxpower = ""; }
					if($txpower > 60) { $txpower = ""; }
				}
				/*echo $provisionamento . "<br>";
				echo "Interface: " . $idinterface . "<br>";
				echo "MAC: " . $macnum . "<br>";
				echo "NS: " . $nsnum . "<br>";
				echo "desc: " . $desc . "<br>";
				echo "status: " . $status . "<br>";
				echo "distancia: " . $distancia . "<br>";
				echo "rxpower: " . $rxpower . "<br>";
				echo "txpower: " . $txpower . "<br><br>";*/
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}
		}
		exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
	}else {
		sleep(5);
		exec("php -f /var/www/html/testeConnectOLT.php id=$idolt &");
		//include("/var/www/html/cron/apoio/conexao.php");
		//mysqli_query($db, "UPDATE olts SET status = '1' WHERE id = ".$idolt."");
		//mysqli_close($db);
	}

// Ubiquiti
}else if($marca == 5) {
	function sanitizeString($string7) {
		$what = array( 'INTEGER:', ' ', 'STRING:', '"' );
		$by   = array( '','','','' );
		return str_replace($what, $by, $string7);
	}
	function sanitizeDescr($string8) {
		$what = array( 'STRING: ', '"' );
		$by   = array( '','' );
		return str_replace($what, $by, $string8);
	}
	function sanitizeData($string9) {
		$what = array( 'STRING: ', '"', '.00');
		$by   = array( '','','' );
		return str_replace($what, $by, $string9);
	}

	$stdno = 0;

	if($vsnmp == 1) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . $community . " " . $host . ":" . $porta . " 1.3.6.1.4.1.41112.1.5.6.2.1.3 | sed \"s/iso.3.6.1.4.1.41112.1.5.6.2.1.3.//g\" | sed \"s/ = INTEGER: /|/g\"", $array1, $stdno);
	}else if($vsnmp == 2) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " 1.3.6.1.4.1.41112.1.5.6.2.1.3 | sed \"s/iso.3.6.1.4.1.41112.1.5.6.2.1.3.//g\" | sed \"s/ = INTEGER: /|/g\"", $array1, $stdno);
	}

	$stdno = (int)$stdno;

	// Verifica se a conexão SNMP funcionou
	if(!$stdno) {
		for ($i=0; $i<count($array1); $i++) {
			$ex1 = explode("|", $array1[$i]);
		
			$ex2 = explode(".", $ex1[0]);
			$idinterface = $ex2[0] . "." . $ex2[1] . "." . $ex2[2] . "." . $ex2[3] . "." . $ex2[4] . "." . $ex2[5] . "." . $ex2[6] . "." . $ex2[7] . "." . $ex2[8] . "." . $ex2[9];
			$idonu = $ex2[10] . "." . $ex2[11] . "." . $ex2[12];
	
			$status = $ex1[1];
			if($vsnmp == 1) {
				$macnum = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.1.{$idinterface}.{$idonu}", 1000000, 30));
				$desc = sanitizeDescr(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.2.{$idinterface}.{$idonu}", 1000000, 30));
				$idpon = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.5.{$idinterface}.{$idonu}", 1000000, 30));
				$dataconnect = sanitizeData(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.6.{$idinterface}.{$idonu}", 1000000, 30));
			}else if($vsnmp == 2) {
				$macnum = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.1.{$idinterface}.{$idonu}", 1000000, 30));
				$desc = sanitizeDescr(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.2.{$idinterface}.{$idonu}", 1000000, 30));
				$idpon = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.5.{$idinterface}.{$idonu}", 1000000, 30));
				$dataconnect = sanitizeData(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.6.{$idinterface}.{$idonu}", 1000000, 30));
			}
			$provisionamento = "Pon: ". $idpon;
			$idslot = 1;
		
			$ex3 = explode("-", $desc);
			if($ex3[0] == "Hex") {
				$desc = "";
			}
	
			$ex4 = explode(")", $dataconnect);
			$dataconn = $ex4[1];
		
			$oltrx = "";
			$voltagem = "";
			$temperatura = "";
			$distancia = "";
			$BiasCurrent = "";
		
			if($status == 1) {
				if($vsnmp == 1) {
					$txpower = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.8.{$idinterface}.{$idonu}", 1000000, 30)) / 100;
					$rxpower = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.9.{$idinterface}.{$idonu}", 1000000, 30)) / 100;
				}else if($vsnmp == 2) {
					$txpower = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.8.{$idinterface}.{$idonu}", 1000000, 30)) / 100;
					$rxpower = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.5.6.2.1.9.{$idinterface}.{$idonu}", 1000000, 30)) / 100;
				}
				$txpower = number_format($txpower, 2, '.', '');
				$rxpower = number_format($rxpower, 2, '.', '');
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}else if($status == 2) {
				$rxpower = "";
				$txpower = "";
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}else {
				$rxpower = "";
				$txpower = "";
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}
		
		}
		exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
	}else {
		sleep(5);
		exec("php -f /var/www/html/testeConnectOLT.php id=$idolt &");
		//include("/var/www/html/cron/apoio/conexao.php");
		//mysqli_query($db, "UPDATE olts SET status = '1' WHERE id = ".$idolt."");
		//mysqli_close($db);
	}

// Parks
}else if($marca == 6) {
	function sanitizeSNMP($string) {
		$what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ', '"' );
		$by   = array( '', '', '', '', '' );
		return str_replace($what, $by, $string);
	}

	$stdno = 0;

	if($vsnmp == 1) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.6771.10.1.5.1.15 | sed \"s/iso.3.6.1.4.1.6771.10.1.5.1.15.//g\"| sed \"s/ = INTEGER: /./g\"", $array, $stdno);
	}else if($vsnmp == 2) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.6771.10.1.5.1.15 | sed \"s/iso.3.6.1.4.1.6771.10.1.5.1.15.//g\"| sed \"s/ = INTEGER: /./g\"", $array, $stdno);
	}

	$stdno = (int)$stdno;

	// Verifica se a conexão SNMP funcionou
	if(!$stdno) {
		if($vsnmp == 1) {
			$o_desc = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.6771.10.1.5.1.62", 1000000, 30));
			$o_status = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.6771.10.1.5.1.5", 1000000, 30));
			$o_porks = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.6771.10.1.5.1.16", 1000000, 30));
			$o_macnum = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $community, "1.3.6.1.4.1.6771.10.1.5.1.18", 1000000, 30));
		}else if($vsnmp == 2) {
			$o_desc = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.6771.10.1.5.1.62", 1000000, 30));
			$o_status = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.6771.10.1.5.1.5", 1000000, 30));
			$o_porks = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.6771.10.1.5.1.16", 1000000, 30));
			$o_macnum = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $community, "1.3.6.1.4.1.6771.10.1.5.1.18", 1000000, 30));
		}
	
		for ($i=0; $i<count($array); $i++) {
			$ex1 = explode(".", $array[$i]);
	
			$idinterface = $ex1[0];
			$idpon = $ex1[1];
			$idonu = $ex1[2];
			$rxpower = $ex1[3];
			$idslot = $ex1[0];
	
			$provisionamento = "Slot/Placa: " . $idinterface . " Pon: ". $idpon . " ONU: " . $idonu;
			$desc = $o_desc[$i];
			$status = $o_status[$i];
			$parks = $o_porks[$i];
			$macnum = $o_macnum[$i];
			$macnum = str_replace($parks, '', $macnum);
		
			$txpower = "";
			$oltrx = "";
			$voltagem = "";
			$temperatura = "";
			$distancia = "";
			$BiasCurrent = "";
			$dataconn = "";
		
			if($rxpower == 0) {
				$rxpower = "";
				$status = 2;
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}else {
				$status = 1;
				$rxpower = "-" . $rxpower / 100;
				$rxpower = number_format($rxpower, 2, '.', '');
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}
		}
		exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
	}else {
		sleep(5);
		exec("php -f /var/www/html/testeConnectOLT.php id=$idolt &");
		//include("/var/www/html/cron/apoio/conexao.php");
		//mysqli_query($db, "UPDATE olts SET status = '1' WHERE id = ".$idolt."");
		//mysqli_close($db);
	}

// Digistar
}else if($marca == 11) {
	function sanitizeSNMP($string11) {
		$what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ' );
		$by   = array( '', '', '', '' );
		return str_replace($what, $by, $string11);
	}

	$stdno = 0;

	if($vsnmp == 1) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . $community . " " . $host . ":" . $porta . " 1.3.6.1.4.1.29450.2.6.1.2.1.1.1 | sed 's/iso.3.6.1.4.1.29450.2.6.1.2.1.1.1.//g' | sed 's/ = INTEGER: /./g'", $array, $stdno);
	}else if($vsnmp == 2) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " 1.3.6.1.4.1.29450.2.6.1.2.1.1.1 | sed 's/iso.3.6.1.4.1.29450.2.6.1.2.1.1.1.//g' | sed 's/ = INTEGER: /./g'", $array, $stdno);
	}

	$stdno = (int)$stdno;

	// Verifica se a conexão SNMP funcionou
	if(!$stdno) {
		for ($i=0; $i<count($array); $i++) {
			$ex = explode(".", $array[$i]);
			
			$idpon = $ex[0];
			$idonu = $ex[1];
			$status = $ex[2];
			$idinterface = $idpon . "." . $idonu;
			$provisionamento = "Pon: " . $idpon. " ONU: " . $idonu;
			$idslot = 1;
		
			if($vsnmp == 1) {
				$mac1 = sanitizeSNMP(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.29450.2.6.1.1.3.1.3.{$idinterface}", 1000000, 30));
				$mac2 = sanitizeSNMP(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.29450.2.6.1.1.3.1.4.{$idinterface}", 1000000, 30));
			}else if($vsnmp == 2) {
				$mac1 = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.29450.2.6.1.1.3.1.3.{$idinterface}", 1000000, 30));
				$mac2 = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.29450.2.6.1.1.3.1.4.{$idinterface}", 1000000, 30));
			}
			
			$macnum = $mac1 . $mac2;
			$macnum = str_replace('"', '', $macnum);
	
			$desc = "";
			$txpower = "";
			$voltagem = "";
			$temperatura = "";
			$BiasCurrent = "";
			$dataconn = "";
	
			if($status == 0) {
				$status = 1;
				
				if($vsnmp == 1) {
					$oltrx = sanitizeSNMP(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.29450.2.6.1.2.1.1.6.{$idinterface}", 1000000, 30));
					$rxpower = sanitizeSNMP(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.29450.2.6.1.2.1.1.7.{$idinterface}", 1000000, 30));
					$distancia = sanitizeSNMP(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.29450.2.6.1.2.1.1.9.{$idinterface}", 1000000, 30));
				}else if($vsnmp == 2) {
					$oltrx = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.29450.2.6.1.2.1.1.6.{$idinterface}", 1000000, 30));
					$rxpower = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.29450.2.6.1.2.1.1.7.{$idinterface}", 1000000, 30));
					$distancia = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.29450.2.6.1.2.1.1.9.{$idinterface}", 1000000, 30));
				}
				$oltrx = number_format($oltrx, 2, '.', '');
				$rxpower = number_format($rxpower, 2, '.', '');
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}else {
				$status = 2;
				$rxpower = "";
				$oltrx = "";
				$distancia = "";
				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}
		}
		exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
	}else {
		sleep(5);
		exec("php -f /var/www/html/testeConnectOLT.php id=$idolt &");
		//include("/var/www/html/cron/apoio/conexao.php");
		//mysqli_query($db, "UPDATE olts SET status = '1' WHERE id = ".$idolt."");
		//mysqli_close($db);
	}

// DATACOM
}else if($marca == 13) {
	function sanitizeSNMP($string12) {
		$what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ', 'Timeticks: ', '"' );
		$by   = array( '', '', '', '', '', '' );
		return str_replace($what, $by, $string12);
	}
	
	$stdno = 0;

	if($vsnmp == 1) {
		exec("/bin/snmpbulkwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.3709.3.6.2.1.1.22", $busca, $stdno);
	}else if($vsnmp == 2) {
		exec("/bin/snmpbulkwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.3709.3.6.2.1.1.22", $busca, $stdno);
	}

	$stdno = (int)$stdno;
	
	if($stdno) {
		$stdno = 0;
		if($vsnmp == 1) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.3709.3.6.2.1.1.22", $busca, $stdno);
		}else if($vsnmp == 2) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.3709.3.6.2.1.1.22", $busca, $stdno);
		}
		$stdno = (int)$stdno;
	}
	
	if(!$stdno) {
		for ($i=0; $i<count($busca); $i++) {
			$exp = explode(" = ", $busca[$i]);
			$idinterface = str_replace('iso.3.6.1.4.1.3709.3.6.2.1.1.22.', '', $exp[0]);
			$rxpower = sanitizeSNMP($exp[1]);
	
			$oids1 = array();
			$oids1[] = ".1.3.6.1.4.1.3709.3.6.2.1.1.3." . $idinterface;         // o_provisionamento
			$oids1[] = ".1.3.6.1.4.1.3709.3.6.2.1.1.5." . $idinterface;         // o_descr
			$oids1[] = ".1.3.6.1.4.1.3709.3.6.2.1.1.7." . $idinterface;         // o_status
			$oids1[] = ".1.3.6.1.4.1.3709.3.6.2.1.1.21." . $idinterface;        // o_txpower
			//$oids1[] = ".1.3.6.1.4.1.3709.3.6.2.1.1.26." . $idinterface;      // o_dataconn
			
			$oidspart1 = implode(' ', $oids1);
			
			if($vsnmp == 1) {
				$cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
			}else if($vsnmp == 2) {
				$cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
			}
	
			$stdno = 0;
			$analysis1 = array();
			exec ($cmd1, $analysis1, $stdno);
			$stdno = (int)$stdno;
	
			// Verifica se a conexão SNMP funcionou
			if(!$stdno) {
				//echo "<pre>";
				//print_r($analysis1);
				//echo "</pre>";
				$exp1 = explode("-", $analysis1[0]);
				$exp2 = explode("/", $exp1[1]);
				$idslot = $exp2[1];
				$idpon = $exp2[2];
				$idonu = sanitizeSNMP($exp1[3]);
				$provisionamento = "Slot/Placa: " . $idslot . " Pon: " . $idpon . " ONU: " . $idonu;
				$a = explode('= ', $analysis1[1]);
				$desc = sanitizeSNMP($a[1]);
				$b = explode('= ', $analysis1[2]);
				$status = sanitizeSNMP($b[1]);
				$c = explode('= ', $analysis1[3]);
				$txpower = sanitizeSNMP($c[1]);
				//$d = explode('= ', $analysis1[4]);
				//$dataconn = sanitizeSNMP($d[1]);
				$macnum = $idolt . " ". $idinterface;
	
				$oltrx = "";
				$voltagem = "";
				$temperatura = "";
				$distancia = "";
				$BiasCurrent = "";
				$nsnum = "";
				$dataconn = "";
	
				if($idpon && isset($idonu)) {
					if($status == 1) {
						/*echo $provisionamento . "<br>";
						echo "Interface: " . $idinterface . "<br>";
						echo "MAC: " . $macnum . "<br>";
						echo "desc: " . $desc . "<br>";
						echo "status: " . $status . "<br>";
						echo "rxpower: " . $rxpower . "<br>";
						echo "txpower: " . $txpower . "<br><br>";*/
						insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
					}else {
						$txpower = "";
						$rxpower = "";
						$status = 2;
						/*echo $provisionamento . "<br>";
						echo "Interface: " . $idinterface . "<br>";
						echo "MAC: " . $macnum . "<br>";
						echo "desc: " . $desc . "<br>";
						echo "status: " . $status . "<br><br>";*/
						insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
					}
				}
			}
		}
		exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
	}else {
		sleep(5);
		exec("php -f /var/www/html/testeConnectOLT.php id=$idolt &");
	}

// Cianet
}else if($marca == 14) {
	function sanitizeSNMP($string1) {
		$what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ', '"', 'iso.3.6.1.2.1.31.1.1.1.1.' );
		$by   = array( '', '', '', '', '', '' );
		return str_replace($what, $by, $string1);
	}
	
	if($vsnmp == 1) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.4.1.4 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $RxOpticalPower);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.4.1.5 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $TxOpticalPower);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.1.1.2 | cut -d '=' -f2 | sed \"s/ STRING: //g\" | grep -v 'No Such Object available'", $OnuNome);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.4.1.6 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $OnuCurrentBias);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.4.1.7 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $OnuVoltagem);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.4.1.8 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $OnuTemperature);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.1.1.9 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $OnuDistance);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) .  " " . $host . ":" . $porta . " ifName | sed \"s/ifName.//g\" | sed \"s/ = STRING: /\\\//g\" | grep ':' | sed \"s/:/\\\//g\"", $ifName);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.1.1.103 | sed \"s/iso.3.6.1.4.1.17409.2.8.4.1.1.103.//g\" | sed \"s/ = STRING: /|/g\"", $o_status);
	}else if($vsnmp == 2) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.4.1.4 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $RxOpticalPower);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.4.1.5 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $TxOpticalPower);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.1.1.2 | cut -d '=' -f2 | sed \"s/ STRING: //g\" | grep -v 'No Such Object available'", $OnuNome);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.4.1.6 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $OnuCurrentBias);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.4.1.7 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $OnuVoltagem);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.4.1.8 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $OnuTemperature);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) .  " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.1.1.9 | cut -d '=' -f2 | sed \"s/ INTEGER: //g\"", $OnuDistance);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.2.1.31.1.1.1.1 | sed \"s/ = STRING: /\\\//g\" | grep ':' | sed \"s/:/\\\//g\"", $ifName);
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.17409.2.8.4.1.1.103 | sed \"s/iso.3.6.1.4.1.17409.2.8.4.1.1.103.//g\" | sed \"s/ = STRING: /|/g\"", $o_status);
	}
	for ($i=0; $i<count($TxOpticalPower); $i++) {
		$rxpower = $RxOpticalPower[$i] / 100;
		$txpower = $TxOpticalPower[$i] / 100;
		$exp1 = explode("/", $ifName[$i]);
		$exp2 = explode(" ", $exp1[0]);
		$exp3 = explode("|", $o_status[$i]);
		$idslot = sanitizeSNMP($exp1[2]);
		$idpon = sanitizeSNMP($exp1[3]);
		$idonu = sanitizeSNMP($exp1[4]);
		$index = sanitizeSNMP($exp2[0]);
		$idinterface = sanitizeSNMP($exp3[0]);
		$motivo = sanitizeSNMP($exp3[1]);
		$desc = sanitizeSNMP($OnuNome[$i]);
		if($desc == " ") { $desc = ""; }
		$BiasCurrent = $OnuCurrentBias[$i] / 100;
		$voltagem = $OnuVoltagem[$i] / 100000;
		$temperatura = $OnuTemperature[$i] / 100;
		$distancia = $OnuDistance[$i];
		$provisionamento = "Pon: " . $idpon . " ONU: " . $idonu;
		$macnum = $idolt . " ". $idinterface;
	
		$oltrx = "";
		$nsnum = "";
		$dataconn = "";
	
		if($vsnmp == 1) {
			$status = sanitizeSNMP(snmpget("{$host}:{$porta}", addslashes($community), "1.3.6.1.2.1.2.2.1.8.{$index}", 100000, 5));
		}else if($vsnmp == 2) {
			$status = sanitizeSNMP(snmp2_get("{$host}:{$porta}", addslashes($community), "1.3.6.1.2.1.2.2.1.8.{$index}", 100000, 5));
		}
	/*
		echo "<strong>" . $provisionamento . "</strong><br>";
		echo "Descrição: " . $desc . "<br>";
		echo "interface: " . $idinterface . "<br>";
		echo " | TX Power: " . $txpower . " dBm<br>";
		echo " | RX Power: " . $rxpower . " dBm<br>";
		echo " | Temperatura: " . $temperatura . "° C<br>";
		echo " | Voltagem: " . $voltagem . " V<br>";
		echo " | Bias: " . $BiasCurrent . " mA<br>";
		echo " | Distância até OLT: " . $distancia . " metros<br>";
	*/
		if($status == 1) {
			$status = 1;
			//echo " | Status: Online (" . $status . ")<br><br>";
		}else {
			if($motivo == "dying-gasp") {
				$status = 4;
				//echo " | Offline (" . $status . ") -> Sem energia elétrica<br><br>";
			}else if($motivo == "LOS") {
				$status = 0;
				//echo " | Offline (" . $status . ") -> Perca de sinal Óptico<br><br>";
			}else {
				$status = 2;
				//echo " | Offline (" . $status . ") -> Sem informações<br><br>";
			}
		}
	
		insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
	}
	exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");

// Overtek
}else if($marca == 15) {
	function sanitizeString($string1) {
		$what = array( 'INTEGER: ', 'STRING: ', '"', 'Gauge32: ', 'iso.3.6.1.2.1.2.2.1.2.' );
		$by   = array( '','','','','' );
		return str_replace($what, $by, $string1);
	}
	
	function hexToStr($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
	
	function macTo($string){
		$mac = str_replace(' ', '', $string);
		$a = hexToStr($mac[0] . $mac[1]);
		$b = hexToStr($mac[2] . $mac[3]);
		$c = hexToStr($mac[4] . $mac[5]);
		$d = hexToStr($mac[6] . $mac[7]);
		$ns = $a . $b . $c . $d . str_replace($mac[0].$mac[1].$mac[2].$mac[3].$mac[4].$mac[5].$mac[6].$mac[7], '', $mac);
		return $ns;
	}
	
	$stdno = 0;
	
	if($vsnmp == 1) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . $community . " " . $host . ":" . $porta . " 1.3.6.1.2.1.2.2.1.2", $description, $stdno);
	}else if($vsnmp == 2) {
		exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " 1.3.6.1.2.1.2.2.1.2", $description, $stdno);
	}
	
	$stdno = (int)$stdno;
	
	// Verifica se a conexão SNMP funcionou
	if(!$stdno) {
		for ($i=0; $i<count($description); $i++) {
			$exp = explode(" ", $description[$i]);
			$exp1 = explode("/", sanitizeString($exp[3]));
			$exp2 = explode(":", $exp1[1]);
	
			if(isset($exp2[1])) {
				$idinterface = sanitizeString($exp[0]);
				$idpon = $exp2[0];
				$idonu = $exp2[1];
				$provisionamento = "Pon: ". $idpon . " ONU: " . $idonu;
				$desc = "";
				$oltrx = "";
				$idslot = 0;
				$txpower = "";
				$voltagem = "";
				$temperatura = "";
				$BiasCurrent = "";
	
				if($vsnmp == 1) {
					$status = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.2.1.2.2.1.8.{$idinterface}", 1000000, 5));
					$macnum = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.3320.10.3.3.1.2.{$idinterface}", 1000000, 5));
				}else if($vsnmp == 2) {
					$status = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.2.1.2.2.1.8.{$idinterface}", 1000000, 5));
					$macnum = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.3320.10.3.3.1.2.{$idinterface}", 1000000, 5));
				}
	
				if(isset($idpon) && isset($idonu)) {
					if($status == 1) {
						$status = 1;
						if($vsnmp == 1) {
							//$txpower = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.3320.10.2.2.1.5.{$idinterface}", 1000000, 5)) / 10;
							$rxpower = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.3320.10.2.3.1.3.{$idinterface}", 1000000, 5)) / 10;
							$distancia = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.3320.10.3.1.1.33.{$idinterface}", 1000000, 5));
							$dataconn = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.2.1.2.2.1.9.{$idinterface}", 1000000, 5));
						}else if($vsnmp == 2) {
							//$txpower = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.3320.10.2.2.1.5.{$idinterface}", 1000000, 5)) / 10;
							$rxpower = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.3320.10.2.3.1.3.{$idinterface}", 1000000, 5)) / 10;
							$distancia = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.3320.10.3.1.1.33.{$idinterface}", 1000000, 5));
							$dataconn = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.2.1.2.2.1.9.{$idinterface}", 1000000, 5));
						}
						$exp1 = explode(") ", $dataconn);
						$exp2 = explode(".", $exp1[1]);
						$dataconn = $exp2[0];
						insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
					}else {
						if($vsnmp == 1) {
							$motivo_off = sanitizeString(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.3320.10.3.1.1.35.{$idinterface}", 1000000, 5));
						}else if($vsnmp == 2) {
							$motivo_off = sanitizeString(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.3320.10.3.1.1.35.{$idinterface}", 1000000, 5));
						}
						if($motivo_off == 1){
							$status = 4; // dying-gasp (sem energia)
						}else if($motivo_off == 2){
							$status = 0; // loss (rompimento)
						}else {
							$status = 2;
						}
						insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
					}
				}
			}
		}
		exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
	}else {
		sleep(5);
		exec("php -f /var/www/html/testeConnectOLT.php id=$idolt &");
	}

// Intelbras | Documentação: https://olts-guias-e-manuais.intelbras.com.br/G16/MIBsSNMP.html
}else if($marca == 16) {
	function sanitizeSNMP($string) {
		$what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ', '"' );
		$by   = array( '', '', '', '', '' );
		return str_replace($what, $by, $string);
	}

	$stdno = 0;
	if($vsnmp == 1) {
		exec("/bin/snmpbulkwalk -v1 -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.13464.1.14.2.4.1.1.1.6", $busca_status, $stdno);
	}else if($vsnmp == 2) {
		exec("/bin/snmpbulkwalk -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.13464.1.14.2.4.1.1.1.6", $busca_status, $stdno);
	}
	$stdno = (int)$stdno;

	if($stdno) {
		$stdno = 0;
		if($vsnmp == 1) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v1 -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.13464.1.14.2.4.1.1.1.6", $busca_status, $stdno);
		}else if($vsnmp == 2) {
			exec("snmpwalk -Os -Ir -r " . $retries . " -t " . $timeout . " -v2c -c " . $community . " " . $host . ":" . $porta . " .1.3.6.1.4.1.13464.1.14.2.4.1.1.1.6", $busca_status, $stdno);
		}
		$stdno = (int)$stdno;
	}

	if(!$stdno) {
		for ($a=0; $a<count($busca_status); $a++) {
			$exp = explode(" = ", $busca_status[$a]);
			$idinterface = str_replace('iso.3.6.1.4.1.13464.1.14.2.4.1.1.1.6.', '', $exp[0]);
			$status = sanitizeSNMP($exp[1]);
			if($status == 0) { $status = 2; }
			$exp1 = explode(".", $idinterface);
			$idslot = sanitizeSNMP($exp1[0]);
			$idpon = sanitizeSNMP($exp1[1]);
			$idonu = sanitizeSNMP($exp1[2]);
			$provisionamento = "Slot/Placa: " . $idslot . " Pon: " . $idpon . " ONU: " . $idonu;

			$oids1 = array();
			$oids1[] = ".1.3.6.1.4.1.13464.1.14.2.4.1.1.1.4." . $idinterface;          // o_desc
			$oids1[] = ".1.3.6.1.4.1.13464.1.14.2.4.1.1.1.7." . $idinterface;          // o_distancia
			$oids1[] = ".1.3.6.1.4.1.13464.1.14.2.4.1.1.1.8." . $idinterface;          // o_macnum
			if($status == 1) {
				$oids1[] = ".1.3.6.1.4.1.13464.1.14.2.4.1.9.1.4." . $idinterface;      // o_voltagem
				$oids1[] = ".1.3.6.1.4.1.13464.1.14.2.4.1.9.1.5." . $idinterface;      // o_rxpower
				$oids1[] = ".1.3.6.1.4.1.13464.1.14.2.4.1.9.1.6." . $idinterface;      // o_txpower
				$oids1[] = ".1.3.6.1.4.1.13464.1.14.2.4.1.9.1.7." . $idinterface;      // o_BiasCurrent
				$oids1[] = ".1.3.6.1.4.1.13464.1.14.2.4.1.9.1.8." . $idinterface;      // o_temperatura
				//$oids1[] = ".1.3.6.1.4.1.13464.1.14.2.4.1.9.1.11." . $idinterface;   // o_oltrxpower
				//$oids1[] = ".1.3.6.1.4.1.13464.1.14.2.4.1.9.1.12." . $idinterface;   // o_olttxpower
			}
			$oidspart1 = implode(' ', $oids1);

			if($vsnmp == 1) {
				$cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
			}else if($vsnmp == 2) {
				$cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
			}

			$stdno = 0;
			$analysis = array();
			exec ($cmd1, $analysis, $stdno);
			$stdno = (int)$stdno;

			// Verifica se a conexão SNMP funcionou
			if(!$stdno) {
				$dataconn = "";
				$oltrx = "";
				$nsnum = "";
				
				if(count($analysis) == 3) {
					$a1 = explode('= ', $analysis[0]);
					$desc = sanitizeSNMP($a1[1]);
					$b = explode('= ', $analysis[1]);
					$distancia = sanitizeSNMP($b[1]);
					$c = explode('= ', $analysis[2]);
					$macnum = sanitizeSNMP($c[1]);
					$temperatura = "";
					$BiasCurrent = "";
					$txpower = "";
					$rxpower = "";
					$voltagem = "";
				}else {
					$a1 = explode('= ', $analysis[0]);
					$desc = sanitizeSNMP($a1[1]);
					$b = explode('= ', $analysis[1]);
					$distancia = sanitizeSNMP($b[1]);
					$c = explode('= ', $analysis[2]);
					$macnum = sanitizeSNMP($c[1]);
					$d = explode('= ', $analysis[3]);
					$voltagem = sanitizeSNMP($d[1]);
					$e = explode('= ', $analysis[4]);
					$rxpower = sanitizeSNMP($e[1]);
					$f = explode('= ', $analysis[5]);
					$txpower = sanitizeSNMP($f[1]);
					$g = explode('= ', $analysis[6]);
					$BiasCurrent = sanitizeSNMP($g[1]);
					$h = explode('= ', $analysis[7]);
					$temperatura = sanitizeSNMP($h[1]);
				}
				
				if($desc == "-") { $desc = ''; }

				insert( $data, $idolt, $idonu, $idpon, $idslot, $idinterface, $provisionamento, $desc, $macnum, $nsnum, $rxpower, $txpower, $oltrx, $voltagem, $temperatura, $distancia, $BiasCurrent, $status, $dataconn );
			}
		}
		exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
	}else {
		sleep(5);
		exec("php -f /var/www/html/testeConnectOLT.php id=$idolt &");
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

include("/var/www/html/cron/apoio/conexao.php");
if($mudacron > 1) {
    mysqli_query($db, "UPDATE olts SET temposinc = '".$segundos."', ocronolt = '".$mudacron."' WHERE id = '".$idolt."';");
}else {
    mysqli_query($db, "UPDATE olts SET temposinc = '".$segundos."' WHERE id = '".$idolt."';");
}
mysqli_close($db);
//exec("php -f /var/www/html/cron/apoio/mariadb_olt.php id=$idolt &");
?>