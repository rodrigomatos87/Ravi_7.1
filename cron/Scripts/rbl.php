#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';
$aux = explode('.', $ip);
$iprev = $aux['3'] . "." . $aux['2'] . "." . $aux['1'] . "." . $aux['0'];

include("/var/www/html/cron/apoio/conexao.php");
$resultServRBL = mysqli_query($db, "SELECT * FROM rbl WHERE ativo = '1';");

$num = 0;
while($ServRBL = mysqli_fetch_array($resultServRBL)) {
	$testeRBL = exec("dig " . $iprev . "." . $ServRBL['link'] . " | grep \";; ANSWER\"");
	if($testeRBL) {
		if($nome) {
			$nome = "$nome, " . $ServRBL['nome'];
		}else {
			$nome = $ServRBL['nome'];
		}
		$num++;		
	}
}

mysqli_close($db);

function insert( $data, $data1, $idSensor, $num, $nome, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$num|$nome||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

if($num == 0) {
	$statusAlert = 6;
	$StErro = 1;
}else {
	if($StErro >= $falhas) {
		if($alertar == 1) {
			$statusAlert = 3;
		}else {
			$statusAlert = 4;
		}
	}else {
		$StErro = $StErro + 1;
		$statusAlert = 3;
	}
}

insert($data, $data1, $id, $num, $nome, $statusAlert, $StErro);

$valor1 = $num;
$valor2 = $nome;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>