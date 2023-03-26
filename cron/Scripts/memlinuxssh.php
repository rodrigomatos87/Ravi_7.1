#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$sshport = $_GET["p"];
$sshuser = $_GET["u"];
$sshsenha = $_GET["s"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$maxPer = $_GET["maxPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

function insert( $data, $data1, $idSensor, $PercentRU, $valor2, $statusAlert, $StErro  ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$PercentRU|$valor2||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

$command = "free | grep ^Mem | awk '{print $2\"|\"$3}'";
$connection = "/usr/bin/sshpass -p $sshsenha /usr/bin/ssh -p $sshport -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null $sshuser@$ip";
$mem = exec($connection." ".$command);

if($mem) {
	$StErro = 1;
	
	$aux = explode('|',$mem);
	$totaR = $aux['0'];
	$usoR = $aux['1'];
	$livreR = $totaR - $usoR;

	$PercentRU = ( $usoR * 100 ) / $totaR;

	if($totaR >= "1048576") {
		$totaR = ($totaR / 1024) / 1024;
		$tipoR = "Gb";
	}else if($totaR >= "1024") {
		$totaR = $totaR / 1024;
		$tipoR = "Mb";
	}else {
		$tipoR = "Kb";
	}

	if($usoR >= "1048576") {
		$usoR = ($usoR / 1024) / 1024;
		$tipoUR = "Gb";
	}else if($usoR >= "1024") {
		$usoR = $usoR / 1024;
		$tipoUR = "Mb";
	}else {
		$tipoUR = "Kb";
	}

	if($livreR >= "1048576") {
		$livreR = ($livreR / 1024) / 1024;
		$tipoLR = "Gb";
	}else if($livreR >= "1024") {
		$livreR = $livreR / 1024;
		$tipoLR = "Mb";
	}else {
		$tipoLR = "Kb";
	}

	$tR = explode('.', $totaR);
	$totaR = $tR['0'] . '.' . substr($tR['1'], 0, 2);
	$uR = explode('.', $usoR);
	$usoR = $uR['0'] . '.' . substr($uR['1'], 0, 2);
	$lR = explode('.', $livreR);
	$livreR = $lR['0'] . '.' . substr($lR['1'], 0, 2);

	$PRU = explode('.', $PercentRU);
	$PercentRU = $PRU['0'];

	$valor2 = ''.$totaR.' '.$tipoR.'/'.$usoR.' '.$tipoUR.'/'.$livreR.' '.$tipoLR.'';

	$media = $maxPer - ($maxPer / 100 * 10);

	if ($PercentRU > $maxPer) {
		if($StErro >= $falhas) {
			if($alertar == 1) {
				$statusAlert = 11;
			}else if($alertar == 2) {
				$statusAlert = 12;
			}else {
				$statusAlert = 11;
			}
		}else {
			$StErro = $StErro + 1;
			$statusAlert = 11;
		}
	}else if($PercentRU >= $media && $PercentRU <= $maxPer) {
		$statusAlert = 11;
	}else {
		$statusAlert = 6;
		$StErro = 1;
	}
}else {
	$statusAlert = 7;
	$StErro = 1;
}

insert($data, $data1, $id, $PercentRU, $valor2, $statusAlert, $StErro );

$valor1 = $PercentRU;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>