#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$intervalo = $_GET["v"];
$banco = $_GET["banco"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

$nmapinfo = array();
exec("nmap -p $intervalo $ip | grep 'open' | cut -d/ -f1", $nmapinfo);
$num = count($nmapinfo);

function insert( $data, $data1, $idSensor, $num, $portas, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|
	exec("echo '|$data|$data1|$idSensor|$num|$portas||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

$aux = "";
$portas = "";

if($num > 1) {
	for($i = 0; $i<$num; $i++) {
		$aux .= $nmapinfo[$i].", ";
	}
	$portas = substr_replace($aux, '', -2);
}else if($num == 1) {
	$portas = $nmapinfo[0];
}

if(isset($banco) && $banco != '') {
	if($num > $banco) {
		if($StErro >= $falhas) {
			if($alertar == 1) {
				$statusAlert = 11;
			}else {
				$statusAlert = 12;
			}
		}else {
			$StErro = $StErro + 1;
			$statusAlert = 11;
		}
	}else {
		$statusAlert = 6;
		$StErro = 1;
	}
}else {
	include("/var/www/html/conexao.php");
	mysqli_query($db, "UPDATE Sensores SET banco = '".$num."' WHERE id = '$id';");
	mysqli_close($db);
	$statusAlert = 6;
	$StErro = 1;
}

insert($data, $data1, $id, $num, $portas, $statusAlert, $StErro);

$valor1 = $num;
$valor2 = $portas;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>