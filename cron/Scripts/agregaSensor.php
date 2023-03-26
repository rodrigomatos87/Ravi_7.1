#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$valor = $_GET["v"];
$banco = $_GET["banco"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$media1 = $_GET["media1"];
$data = ''.$data.' '.$hora.'';
$maxPer = $_GET["maxPer"];
$minPer = $_GET["minPer"];
$alertar = $_GET["alertar"];

if(!$falhas) { $falhas = 1; }

include("/var/www/html/cron/apoio/conexao.php");

function insert( $data, $data1, $idSensor, $valor1, $valor2, $valor3, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|erro|
	system("echo '|$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

$valor = str_replace(' ', '', $valor);
$aux = explode(',', $valor);

for ($i=0; $i<count($aux); $i++) {
    if($i == 0) {
        $BuscaTag = mysqli_query($db, "SELECT valor1, valor2, valor3 FROM Sensores WHERE id = '".$aux[$i]."';");
        $AchaTag = mysqli_fetch_array($BuscaTag);
        $valor1 = $AchaTag['valor1'];
        $valor2 = $AchaTag['valor2'];
        $valor3 = $AchaTag['valor3'];
    }else {
        $BuscaTag = mysqli_query($db, "SELECT valor1, valor2, valor3 FROM Sensores WHERE id = '".$aux[$i]."';");
        $AchaTag = mysqli_fetch_array($BuscaTag);
        $valor1 = $valor1 + $AchaTag['valor1'];
        $valor2 = $valor2 + $AchaTag['valor2'];
        $valor3 = $valor3 + $AchaTag['valor3'];
    }
}
if(isset($valor1)) {
    if(isset($media1) && $media1 != "" && isset($maxPer) && isset($minPer)) {
        $maxima = $media1 + ($media1 / 100 * $maxPer);
        $media = $maxima - ($maxima / 100 * 10);
        $minima = $media1 - ($media1 / 100 * $minPer);
        if ($valor1 >= $maxima ) {
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
        }else if ($valor1 < $maxima && $valor1 >= $media) {
            $statusAlert = 3;
        }else if ($valor1 < $media && $valor1 >= $minima) {
            $statusAlert = 6;
            $StErro = 1;
        }else if ($valor1 < $minima) {
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
        }
    }else {
        $statusAlert = 6;
        $StErro = 1;
    }
}else {
    $statusAlert = 7;
}

mysqli_close($db);
insert($data, $data1, $id, $valor1, $valor2, $valor3, $statusAlert, $StErro);

if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>