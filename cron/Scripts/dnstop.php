#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$dominio = $_GET["v"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];

$data = ''.$data.' '.$hora.'';
$ipservidor = exec("cat /var/www/ifcfg-eth0 | grep 'IPADDR' | cut -d= -f2 | sed 's/\"//g'");

include("/var/www/html/cron/apoio/conexao.php");

$nome = array();
$tempo = array();
$num = 0;
$resultServDNS = mysqli_query($db, "SELECT id, nome, Primario FROM ServDNS;");
while($ServDNS = mysqli_fetch_array($resultServDNS)) {
    if($ServDNS['Primario'] == "127.0.0.1" || $ServDNS['Primario'] == $ipservidor) {
        $tempo[$num] = system("dig +time=5 +tries=3 $dominio | grep 'Query time' | awk '{print $4}'");
    }else {
        $tempo[$num] = exec("dig +time=5 +tries=3 @" . $ServDNS['Primario'] . " " . $dominio . " | grep \"Query time\" | awk '{print $4}'");
    }
    if(!$tempo[$num] && $tempo[$num] != 0) {
        if($ServDNS['Primario'] == "127.0.0.1" || $ServDNS['Primario'] == $ipservidor) {
            $tempo[$num] = system("dig +time=5 +tries=3 $dominio | grep 'Query time' | awk '{print $4}'");
        }else {
            $tempo[$num] = exec("dig +time=5 +tries=3 @" . $ServDNS['Primario'] . " " . $dominio . " | grep \"Query time\" | awk '{print $4}'");
        }
        if(!$tempo[$num]) {
            $tempo[$num] = "Erro";
        }
    }
    $nome[$num] = $ServDNS['nome'];
    $num = $num + 1;
}

mysqli_close($db);

$menorv = min($tempo);

for ($i=0; $i<count($tempo); $i++) {
    if($tempo[$i] == $menorv) {
        if(!isset($nomes)) {
            $nomes = $nome[$i];
        }else {
            $nomes = $nomes . ", " . $nome[$i];
        }
    }
}

function insert( $data, $data1, $idSensor, $tempo, $servidor, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$tempo|$servidor||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

if(isset($menorv) && $menorv != '') {
	$statusAlert = 6;
	$StErro = 1;
}else {
	$statusAlert = 7;
}

insert($data, $data1, $id, $menorv, $nomes, $statusAlert, $StErro);

$valor1 = $menorv;
$valor2 = $nomes;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>