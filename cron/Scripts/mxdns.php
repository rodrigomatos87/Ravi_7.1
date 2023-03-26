#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$dominio = $_GET["v"];
$dns = $_GET["banco"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$alertar = $_GET["alertar"];
$text = $_GET["text"];
$explode = explode(",", $text);
$data = ''.$data.' '.$hora.'';

function gravidade($alertar, $StErro, $falhas, $alerta, $critico) {
    if($StErro >= $falhas) {
        if($alertar == 1) {
            $statusAlert = $alerta;
        }else {
            $statusAlert = $critico;
        }
    }else {
        $StErro = $StErro + 1;
        $statusAlert = $alerta;
    }
    return array($statusAlert, $StErro);
}

$array = array();
$naotem = array();
$buscaAr = array();

$cmd = "dig @" . $dns . " +nocmd +noall +answer +ttlid MX " . $dominio;
exec($cmd . " | awk '{print \"|\"$2\"|\"$5\"|\"$6\"|\"}'", $array);

for ($a = 0; $a < count($array); $a++) {
    $valores = explode("|", $array[$a]);
    $mx = $valores[3];
    array_push($buscaAr, $mx);
}

for ($b = 0; $b < count($explode); $b++) {
    if (!in_array($explode[$b], $buscaAr)) {
        array_push($naotem, $explode[$b]);
    }
}

$valor2 = "";
for($c=0;$c<count($naotem);$c++) {
    $valor2 .= $naotem[$c];
    if(count($naotem) != $c+1) {
        $valor2 .= ",";
    }
}

if($valor2 != "") {
    $valor1 = 0;
    $busca = gravidade($alertar, $StErro, $falhas, 11, 12);
    $statusAlert = $busca[0];
    $StErro = $busca[1];
}else {
    $valor1 = 1;
    $statusAlert = 6;
}

$timearq = date("H-i-s");
$arq = $id . "_" . $timearq;
// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|erro|banco|
exec("echo '|$data|$data1|$id|$valor1|$valor2||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");

if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>