#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$url = $_GET["v"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$media1 = $_GET["media1"];
$maxPer = $_GET["maxPer"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

// Inicia a sessão do cURL
$ch = curl_init();

// Define as opções para a requisição HTTP
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

// Faz a requisição HTTP
$start_time = microtime(true);
$response = curl_exec($ch);
$end_time = microtime(true);

// Verifica se a requisição foi bem-sucedida
if (curl_errno($ch)) {
    // Se houve um erro na requisição, dispara um alerta
    //trigger_alert('O domínio não está respondendo.');
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
} else {
    // Se a requisição foi bem-sucedida, calcula a latência e a velocidade da conexão
    $info = curl_getinfo($ch);
    $latency = round(($end_time - $start_time) * 1000, 2); // Latência em milissegundos
    $speed = round($info['speed_download'] / 1024, 2); // Velocidade em KB/s

    // Dispara um alerta positivo com os valores de latência e velocidade
    //$message = "O domínio está respondendo. Latência: {$latency} ms, Velocidade: {$speed} KB/s";
    //trigger_alert($message);

    if(isset($media1) && $media1 != "" && $latency > 10 && isset($maxPer)) {
		$maxima = $media1 + ($media1 / 100 * $maxPer);
		$media = $maxima - ($maxima / 100 * 10);

		if ($latency >= $maxima) {
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
		} else if ($latency < $maxima && $latency >= $media) {
			$statusAlert = 3;
		} else if ($latency < $media) {
			$statusAlert = 6;
			$StErro = 1;
		}
	}else {
		$statusAlert = 6;
		$StErro = 1;
	}
}

// Fecha a sessão do cURL
curl_close($ch);
/*
function trigger_alert($message)
{
    // Dispara o alerta para o responsável
    // Aqui pode ser utilizado um serviço de envio de alertas como o PagerDuty, Twilio, ou outro
    // Ou pode ser adicionado ao banco de dados para exibir em uma interface de usuário posteriormente
    // Por exemplo: $db->insert('alerts', ['message' => $message, 'created_at' => date('Y-m-d H:i:s')]);
    echo $message;
}
*/

function insert( $data, $data1, $idSensor, $latency, $speed, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|erro
	exec("echo '|$data|$data1|$idSensor|$latency|$speed||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

insert($data, $data1, $id, $latency, $speed, $statusAlert, $StErro);

$valor1 = $latency;
$valor2 = $speed;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>