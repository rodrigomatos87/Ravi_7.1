#!/usr/bin/php
<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$idC = $_GET["idC"];
$interface = $_GET["int"];
$ip = $_GET["ip"];
$tamanho = $_GET["tamanho"];
$qtd = $_GET["qtd"];
$hora = $_GET["hora"];
$data = $_GET["data"];

$datasinc = $data . " " . $hora;

function sanitizeString($string) {
    $what = array( 'rtt min/avg/max/mdev =', 'ms', ' ' );
    $by   = array( '', '', '/' );
    return str_replace($what, $by, $string);
}

function testping( $ip, $tamanho, $qtd ) {
	$auxping = explode(':', $ip);
	if($auxping[1]) {
		$pingexec = sanitizeString(exec("/bin/ping6 -c $qtd -s $tamanho -w $qtd -i 1 $ip | tail -2 | tr -d '\n' | grep -v exceeded | grep -v errors | grep -v pipe"));
	}else {
		$pingexec = sanitizeString(exec("/bin/ping -c $qtd -s $tamanho -w $qtd -i 1 $ip | tail -2 | tr -d '\n' | grep -v exceeded | grep -v errors | grep -v pipe"));
	}
	$aux = explode('/', $pingexec);
	$packetloss = $aux['5'];
	$ping = $aux['10'];
	$jitter = $aux['13'];
	return array($ping, $packetloss, $jitter);
}

function insert( $datasinc, $idC, $interface, $ip, $ping, $jitter, $packetLoss ) {
	$timearq = date("H-i-s");
	$arq = $idC . "_" . $interface . "_" . $timearq;
	exec("echo '|$idC|$interface|$datasinc|$ip|$ping|$jitter|$packetLoss|' > /var/www/html/ram/coletas/ppoe/ping/$arq");
}

$testPing = testping($ip, $tamanho, $qtd);

$ping = $testPing[0];
$packetLoss = $testPing[1];
$jitter = $testPing[2];

insert($datasinc, $idC, $interface, $ip, $ping, $jitter, $packetLoss);
?>