#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$host = $_GET["host"];
$usuario = $_GET["usuario"];
$senha = $_GET["senha"];
$banco = $_GET["banco"];
$porta = $_GET["porta"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$media1 = $_GET["media1"];
$maxPer = $_GET["maxPer"];
$minPer = $_GET["minPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

$Start = microtime(true);

$command = "host=" . $host . " port=" . $porta . " user=" . $usuario . " password=" . $senha . " dbname=" . $banco;
$db_handle = pg_connect($command);

$erro = 0;
if($db_handle) {
    $erro = 1;
    $StErro = 1;
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

pg_close($db_handle);

$End = microtime(true);
$Final = $End - $Start;
$TempFinal = substr("$Final", 0, 5);

function insert( $data, $data1, $idSensor, $TempFinal, $statusAlert, $StErro ) {
    $timearq = date("H-i-s");
    $arq = $idSensor . "_" . $timearq;
    // |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
    exec("echo '|$data|$data1|$idSensor|$TempFinal|||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

if($erro == 1) {
    if(isset($media1) && $media1 != "" && isset($maxPer) && isset($minPer)) {
        $maxima = $media1 + ($media1 / 100 * $maxPer);
        $media = $maxima - ($maxima / 100 * 10);
        $minima = $media1 - ($media1 / 100 * $minPer);
        if($TempFinal >= $maxima) {
            if($StErro >= $falhas) {
                if($alertar == 1) {
                    $statusAlert = 3;
                }else if($alertar == 2) {
                    $statusAlert = 4;
                }else {
                    $statusAlert = 3;
                }
            }else {
                $StErro = $StErro + 1;
                $statusAlert = 3;
            }
        }else if($TempFinal < $maxima && $TempFinal >= $media) {
            $statusAlert = 3;
        }else if($TempFinal <= $minima) {
            if($StErro >= $falhas) {
                if($alertar == 1) {
                    $statusAlert = 3;
                }else if($alertar == 2) {
                    $statusAlert = 4;
                }else {
                    $statusAlert = 3;
                }
            }else {
                $StErro = $StErro + 1;
                $statusAlert = 3;
            }
        }else {
            $statusAlert = 6;
            $StErro = 1;
        }
    }else {
        $statusAlert = 6;
        $StErro = 1;
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