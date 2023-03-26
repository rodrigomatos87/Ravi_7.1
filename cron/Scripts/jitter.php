#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$falhas = $_GET["ad"];
$erro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$media1 = $_GET["media1"];
$maxPer = $_GET["maxPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

function sanitizeString($string) {
    $what = array( 'rtt min/avg/max/mdev =', 'ms', ' ' );
    $by   = array( '', '', '' );
    return str_replace($what, $by, $string);
}

function testjitter( $ip ) {
	$pingexec = sanitizeString(exec("/bin/ping -c 20 $ip | tail -1 | grep -v pipe"));
	$aux = explode('/', $pingexec);
	$jitter = $aux[3];
	return($jitter);
}

function insert( $data, $data1, $idSensor, $jitter, $statusAlert, $erro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$erro|
	exec("echo '|$data|$data1|$idSensor|$jitter|||$statusAlert|$erro|' > /var/www/html/ram/coletas/valores/$arq");
}

$jitter = testjitter($ip);

if(isset($jitter)) {
	$erro = 1;
	$statusAlert = 6;
	if(isset($media1) && $media1 != "" && $jitter > 10) {
		if(isset($maxPer)) {
			$maxima = $media1 + ($media1 / 100 * $maxPer);
			$media = $maxima - ($maxima / 100 * 20);
			if ($jitter >= $maxima) {
				if($alertar == 1) {
					$statusAlert = 3;
				}else if($alertar == 2) {
					$statusAlert = 4;
				}else {
					$statusAlert = 3;
				}
			} else if ($jitter < $maxima && $jitter >= $media) {
				$statusAlert = 3;
			}
		}
	}
}else {
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

insert($data, $data1, $id, $jitter, $statusAlert, $erro);

$valor1 = $jitter;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>