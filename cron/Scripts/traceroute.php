#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$banco = $_GET["banco"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

function insert( $data, $data1, $idSensor, $traceroute, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$traceroute|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeString($string) {
    $what = array( 'rtt min/avg/max/mdev =', 'ms', ' ' );
    $by   = array( '', '', '' );
    return str_replace($what, $by, $string);
}

function testping( $ip ) {
	$pingexec = sanitizeString(exec("/bin/ping -c 10 $ip | tail -1 | grep -v pipe"));
	$aux = explode('/', $pingexec);
	$ping = $aux[0];
	return($ping);
}

$ping = testping($ip);

if(isset($ping)) {
	$traceroute = exec("/bin/traceroute $ip | tail -1 | awk '{print $1}'");
	if($banco) {
		if($traceroute != $banco) {
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
		mysqli_query($db, "UPDATE Sensores SET banco = '".$traceroute."' WHERE id = '$id';");
		mysqli_close($db);
		$statusAlert = 6;
		$StErro = 1;
	}
}else {
	$statusAlert = 7;
	$StErro = 1;
}

insert($data, $data1, $id, $traceroute, $statusAlert, $StErro);

$valor1 = $traceroute;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>