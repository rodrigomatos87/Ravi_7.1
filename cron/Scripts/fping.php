#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$nome_arquivo = $_GET['arquivo'];

$array = file_get_contents("/var/www/html/ram/coletas/fping/" . $nome_arquivo);
$ar = json_decode($array);

include("/var/www/html/cron/apoio/icmp.php");
include("/var/www/html/cron/apoio/conexao.php");

$aux = explode('-', $ar[0]->adicionais);
$tamanho = $aux[0];
$quantidade = $aux[1];

$list = array();
for($i=0;$i<count($ar);$i++) {
    array_push($list, $ar[$i]->address);
}

foreach($list as $k=>$ip){
    $ip = trim($ip);
    if($p=strpos($ip, '/')){ $ip=substr($ip, 0, $p); }
    if($ip==''){ unset($list[$k]); continue; }
    $list[$k] = $ip;
}

$list = array_unique($list);
$list = array_values($list);

$resposta = ping_list($list, $tamanho, $quantidade);

for($i=0;$i<count($ar);$i++) {
    $ip = $ar[$i]->address;
    $cronograma = $ar[$i]->cron;
    $erro = $ar[$i]->erro;
    
    $maxPer = $ar[$i]->maxPer;
    $alertar = $ar[$i]->alertar;
    $hora = $ar[$i]->hora;
    $data = $ar[$i]->data;
    $data1 = $ar[$i]->data1;
    $loss = $resposta[$ip]["loss"];
    $data = ''.$data.' '.$hora.'';

    if($ar[$i]->media1 == "-") {
        $media1 = "";
    }else {
        $media1 = $ar[$i]->media1;
    }

    $aux2 = explode('-', $ar[$i]->adicionais);
    $falhas = $aux2['4'];

    if($loss < 100) {
        $ping = $resposta[$ip]['ping'];
        if(isset($media1) && $media1 != "" && $ping > 10 && isset($maxPer)) {
            $maxima = $media1 + ($media1 / 100 * $maxPer);
            $media = $maxima - ($maxima / 100 * 10);
            if($ping > $maxima) {
                if($erro >= $falhas) {
                    if($alertar == 1) {
                        $statusAlert = 3;
                    }else if($alertar == 2) {
                        $statusAlert = 4;
                    }else {
                        $statusAlert = 3;
                    }
                }else {
                    $erro = $erro + 1;
                    $statusAlert = 3;
                }
            }else if ($ping <= $maxima && $ping > $media) {
                $statusAlert = 3;
                $erro = 1;
            }else {
                $statusAlert = 6;
                $erro = 1;
            }
        }else {
            $statusAlert = 6;
            $erro = 1;
        }
    }else {
        $ping = '';
        $loss = '100%';
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

    $buscaSensor = mysqli_query($db, "SELECT id FROM Sensores WHERE tag = 'ping' AND valor = '".$ip."' AND cronograma = '".$cronograma."';");
    while($sensor = mysqli_fetch_array($buscaSensor)) {
        $idSensor = $sensor['id'];
        $resultSensores = mysqli_query($db, "SELECT idDispositivo, statusAlert, valor, nome, banco, unidade, display FROM Sensores WHERE id = '$idSensor';");
        $detalhes = mysqli_fetch_array($resultSensores);
        $idDispositivo = $detalhes['idDispositivo'];
        $valor = $detalhes['valor'];
        $nome = $detalhes['nome'];
        $banco = $detalhes['banco'];
        $unidade = $detalhes['unidade'];
        $display = $detalhes['display'];
        $statusAlertdb = $detalhes['statusAlert'];
        if($statusAlertdb == 2) { $statusAlert = 2; }
        exec("echo '|$statusAlert|$ping|$loss||ping|$nome|$banco|$unidade|$display|' > /var/www/html/ram/dispositivos/sensores/$idSensor");

        mysqli_query($db, "UPDATE Sensores SET valor1 = '".$ping."', valor2 = '".$loss."', statusAlert = '".$statusAlert."', erro = '".$erro."' WHERE id = '$idSensor';");
        mysqli_query($db, "INSERT INTO Log2h (data, idSensor, valor1, valor2, statusAlert) VALUES ('".$data."', '".$idSensor."', '".$ping."', '".$loss."', '".$statusAlert."')");
        mysqli_query($db, "INSERT INTO Log24h (data, idSensor, valor1, valor2, statusAlert) VALUES ('".$data."', '".$idSensor."', '".$ping."', '".$loss."', '".$statusAlert."')");
        if($data1 == 00 || $data1 == 05 || $data1 == 10 || $data1 == 15 || $data1 == 20 || $data1 == 25 || $data1 == 30 || $data1 == 35 || $data1 == 40 || $data1 == 45 || $data1 == 50 || $data1 == 55) {
            $aux1 = explode(':', $data);
            if($aux1[2] == 00) { mysqli_query($db, "INSERT INTO Log30d (data, idSensor, valor1, valor2, statusAlert) VALUES ('".$data."', '".$idSensor."', '".$ping."', '".$loss."', '".$statusAlert."')"); }
        }
        if($data1 == 00 || $data1 == 30) { 
            $aux1 = explode(':', $data);
            if($aux1[2] == 00) { mysqli_query($db, "INSERT INTO Log1a (data, idSensor, valor1, valor2, statusAlert) VALUES ('".$data."', '".$idSensor."', '".$ping."', '".$loss."', '".$statusAlert."')"); }
        }

        //($id, $data, $valor1, $valor2, $statusAlert)
        if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
            $aux = explode(' ', $data);
            $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $idSensor . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $ping . " valor2=" . $loss . " statusAlert=" . $statusAlert . " &";
            //exec("echo 'php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $idSensor . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $ping . " valor2=" . $loss . " statusAlert=" . $statusAlert . "' >> /root/temppp");
            exec($cmd);
        }
    }
}
mysqli_close($db);

unlink('/var/www/html/ram/coletas/fping/' . $nome_arquivo);
exit(0);
?>