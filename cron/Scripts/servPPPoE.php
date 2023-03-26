#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$idC = $_GET["idC"];
$ppoe = $_GET["user"];
$ip = $_GET["ip"];
$tamanho = $_GET["tamanho"];
$qtd = $_GET["qtd"];
$hora = $_GET["hora"];
$data = $_GET["data"];

$datasinc = $data . " " . $hora;

function sanitizeString($string) {
    $what = array( 'rtt min/avg/max/mdev =', 'ms', ' ' );
    $by   = array( '', '', '' );
    return str_replace($what, $by, $string);
}

function testping( $ip ) {
	$pingexec = sanitizeString(exec("/bin/ping -c $qtd -s $tamanho -w $qtd -i 1 $ip | tail -1 | grep -v pipe"));
	$aux = explode('/', $pingexec);
	return($aux);
}

function insert( $datasinc, $idC, $ppoe, $ip, $ping, $jitter ) {
	$timearq = date("H-i-s");
	$arq = $idC . "_" . str_replace(' ', '', $ppoe) . "_" . $timearq;
	exec("echo '|$idC|$ppoe|$datasinc|$ip|$ping|$jitter|' > /var/www/servPPPoE/Ping/$arq");
}

$pingT = testping($ip);
$ping = $pingT[0];
$jitter = $pingT[3];

insert($datasinc, $idC, $ppoe, $ip, $ping, $jitter);

$valor1 = $ppoe;
$valor2 = "";
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>