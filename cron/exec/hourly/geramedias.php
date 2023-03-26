#!/usr/bin/php
<?php
$horaAtual = date("H");
include("/var/www/html/cron/apoio/conexao.php");

function casas($valor) {
	$s = explode('.', $valor);
	$valor = $s['0'];
	if(isset($s['1'])) { $valor = $s['0'] . '.' . substr($s['1'], 0, 2); }
	return $valor;
}

$query = mysqli_query($db, "SELECT id, valor1, valor2, statusAlert FROM Sensores WHERE valor1 != '' AND tag != 'uptime' AND tag != 'snmpcustom' AND tag != 'dnstop' AND tag != 'uptimelinkmimosa' AND tag != 'lastrebootimosa' AND tag != 'disksnmp' AND tag != 'ramsnmp' AND tag != 'memlinuxssh' AND tag != 'rbl' AND tag != 'contagemftp' AND tag != 'portscan' AND tag != 'httpcomp' AND tag != 'traceroute' AND tag != 'associedubnt' AND tag != 'associedmk' AND tag != 'wanmimosa' AND tag != 'velocidadeporta' AND tag != 'statusporta' AND tag != 'coolersnexus' AND tag != 'bgpoper' AND tag != 'bgpadm'");

while($sensor = mysqli_fetch_array($query)) {
	if($sensor['statusAlert'] != 1 && $sensor['statusAlert'] != 2 && $sensor['statusAlert'] != 7 && $sensor['statusAlert'] != 8) {
		mysqli_query($db, "INSERT INTO geramedias (idSensor, valor1, valor2, hora) VALUES ('$sensor[id]', '$sensor[valor1]', '$sensor[valor2]', '$horaAtual');");
	}
}

$remove = mysqli_query($db, "DELETE FROM geramedias WHERE data < DATE_SUB(NOW(), INTERVAL 7 DAY)");

if($horaAtual == 00) {
	$consultas = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23');
	$query1 = mysqli_query($db, "SELECT id FROM Sensores WHERE statusAlert = '3' OR statusAlert = '4' OR statusAlert = '6' OR statusAlert = '9' OR statusAlert = '10' AND valor1 != ''");
	while($isensor = mysqli_fetch_array($query1)) {
		for($j=0;$j<count($consultas);$j++) {
			$query2 = mysqli_query($db, "SELECT valor1, valor2, hora, data FROM geramedias WHERE hora = $consultas[$j] AND idSensor = $isensor[id]");
			$total = mysqli_num_rows($query2);
			if($total >= 3) {
				$v1 = ''; $v2 = '';
				while($sensor = mysqli_fetch_array($query2)) {
					if(isset($sensor['valor1'])) { $v1 = casas($v1 + $sensor['valor1']); }
					if(isset($sensor['valor2'])) { $v2 = casas($v2 + $sensor['valor2']); }
				}
				if(isset($v1)) { $v1 = casas($v1 / $total); }
				if(isset($v2)) { $v2 = casas($v2 / $total); }
				if(isset($v1)) { 
					mysqli_query($db, "INSERT INTO medias (idSensor, hora, valor1, valor2) VALUES ('$isensor[id]', '$consultas[$j]', '$v1', '$v2');");
					if($isensor['id'] == 610) { echo "INSERT INTO medias (idSensor, hora, valor1, valor2) VALUES ('$isensor[id]', '$consultas[$j]', '$v1', '$v2');<br>"; }
				}
			}
		}
	}
}

$upmedia = mysqli_query($db, "SELECT idSensor, valor1, valor2 FROM medias WHERE hora = $horaAtual");
mysqli_query($db, "UPDATE Sensores SET media1 = '', media2 = '';");
while($NewMedia = mysqli_fetch_array($upmedia)) {
	mysqli_query($db, "UPDATE Sensores SET media1 = '".$NewMedia['valor1']."', media2 = '".$NewMedia['valor2']."' WHERE id = $NewMedia[idSensor];");
}

mysqli_close($db);
?>