#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$dominio = $_GET["v"];
$porta = $_GET["porta"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$media1 = $_GET["media1"];
$maxPer = $_GET["maxPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

function insert( $data, $data1, $idSensor, $TempFinal, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$TempFinal|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

$Start = microtime(true);
$smtpconect = exec("nc -w2 " . $dominio . " " . $porta . " | grep -v 'Name or service not known'");
$End = microtime(true);

$Final = $End - $Start;
$TempFinal = substr("$Final", 0, 5);

if($smtpconect) {
	if(isset($media1) && $media1 != "" && $TempFinal > 10 && isset($maxPer)) {
		$maxima = $media1 + ($media1 / 100 * $maxPer);
		$media = $maxima - ($maxima / 100 * 10);
		if ($TempFinal >= $maxima) {
			if($alertar == 1) {
				$statusAlert = 3;
			}else if($alertar == 2) {
				$statusAlert = 4;
			}else {
				$statusAlert = 3;
			}
		} else if ($TempFinal < $maxima && $TempFinal >= $media) {
			$statusAlert = 3;
		} else if ($TempFinal < $media) {
			$statusAlert = 6;
			$StErro = 1;
		}
	}else {
		$statusAlert = 6;
		$StErro = 1;
	}
}else {
	if($StErro >= $falhas) {
		if($alertar == 1) {
			$statusAlert = 7;
		}else {
			$statusAlert = 1;
		}
	}else {
		$StErro = $StErro + 1;
		$statusAlert = 7;
	}
}

insert($data, $data1, $id, $TempFinal, $statusAlert, $StErro);

$valor1 = $TempFinal;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>