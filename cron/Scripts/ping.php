#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$adicionais = $_GET["ad"];
$erro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$media1 = $_GET["media1"];
$maxPer = $_GET["maxPer"];
$alertar = $_GET["alertar"];

if($adicionais) {
	$aux = explode('-', $adicionais);
	$tamanho = $aux['0'];
	$tempo = $aux['1'];
	$quantidade = $aux['2'];
	$delay = $aux['3'];
	$falhas = $aux['4'];
}else {
	$tamanho = 32;
	$tempo = 20;
	$quantidade = 20;
	$delay = 1;
	$falhas = 3;
}

$data = ''.$data.' '.$hora.'';

function sanitizeString($string) {
    $what = array( 'rtt min/avg/max/mdev =', 'ms', ' ' );
    $by   = array( '', '', '/' );
    return str_replace($what, $by, $string);
}

function testping( $ip, $tamanho, $tempo, $quantidade, $delay ) {
	$auxping = explode(':', $ip);
	if($auxping[1]) {
		$pingexec = sanitizeString(exec("/bin/ping6 -c $quantidade -s $tamanho -w $tempo -i $delay $ip | tail -2 | tr -d '\n' | grep -v exceeded | grep -v errors | grep -v pipe"));
	}else {
		$pingexec = sanitizeString(exec("/bin/ping -c $quantidade -s $tamanho -w $tempo -i $delay $ip | tail -2 | tr -d '\n' | grep -v exceeded | grep -v errors | grep -v pipe"));
	}
	
	$aux = explode('/', $pingexec);
	$packetloss = $aux['5'];
	$ping = $aux['10'];
	$jitter = $aux['13'];
	return array($ping, $packetloss, $jitter);
}

function insert( $data, $data1, $idSensor, $ping, $packetLoss, $jitter, $statusAlert, $erro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|erro|
	exec("echo '|$data|$data1|$idSensor|$ping|$packetLoss|$jitter|$statusAlert|$erro|' > /var/www/html/ram/coletas/valores/$arq");
}

$testPing = testping($ip, $tamanho, $tempo, $quantidade, $delay);
$ping = $testPing[0];
$packetLoss = $testPing[1];
$jitter = $testPing[2];

if(!isset($ping) || $packetLoss == '100%' || $ping == "") {
	sleep(10);
	$tamanho = 32;
	$tempo = 35;
	$quantidade = 35;
	$delay = 1;
	$testPing = testping($ip, $tamanho, $tempo, $quantidade, $delay);
	$ping = $testPing[0];
	$packetLoss = $testPing[1];
	$jitter = $testPing[2];
}

if($testPing[0] == "time") { 
	$testPing = testping($ip, $tamanho, $tempo, $quantidade, $delay);
	$ping = $testPing[0];
	$packetLoss = $testPing[1];
	$jitter = $testPing[2];
}

if(isset($ping) && $packetLoss != '100%') {
	if(isset($media1) && $media1 != "" && $ping > 10 && isset($maxPer)) {
		$maxima = $media1 + ($media1 / 100 * $maxPer);
		$media = $maxima - ($maxima / 100 * 10);
		if($ping > $maxima) {
			if($erro >= $falhas) {
				if($alertar == 1) {
					$statusAlert = 3;
				}else if($alertar == 2) {
					$statusAlert = 4;
				}else {
					$statusAlert = 3;
				}
			}else {
				$erro = $erro + 1;
				$statusAlert = 3;
			}
		}else if ($ping <= $maxima && $ping > $media) {
			$statusAlert = 3;
			$erro = 1;
		}else {
			$statusAlert = 6;
			$erro = 1;
		}
	}else {
		$statusAlert = 6;
		$erro = 1;
	}
}else {
	$packetLoss = '100%';
	if($erro >= $falhas) {
		if($alertar == 1) {
			$statusAlert = 7;
		}else {
			$statusAlert = 1;
		}
	}else {
		$erro = $erro + 1;
		$statusAlert = 7;
	}
}
if($ping != "time") { 
	insert($data, $data1, $id, $ping, $packetLoss, $jitter, $statusAlert, $erro);
}

$valor1 = $ping;
$valor2 = $packetLoss;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>