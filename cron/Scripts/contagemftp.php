#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$host = $_GET["host"];
$usuario = $_GET["usuario"];
$senha = $_GET["senha"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

function insert( $data, $data1, $idSensor, $contagemftp, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$contagemftp|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

$conn = ftp_connect($host);
$login = ftp_login($conn, $usuario, $senha);

$mode = ftp_pasv($conn, TRUE);

if((!$conn) || (!$login) || (!$mode)) {
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
}else { 
    $file_list = ftp_nlist($conn, "");
	$contagemftp = count(array_filter($file_list));
	$StErro = 1;
	$statusAlert = 6;
    ftp_close($conn);
}

insert($data, $data1, $id, $contagemftp, $statusAlert, $StErro);

$valor1 = $contagemftp;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>