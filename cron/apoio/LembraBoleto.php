<?PHP
include("/var/www/html/cron/apoio/conexao.php");

$teste_db = mysqli_query($db, "SELECT 1 FROM alertasMensalidade;");
if($teste_db == FALSE) {
    print("Doesn't exist<br>");
    mysqli_query($db, "
        CREATE TABLE `alertasMensalidade` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `idBoleto` int(10) DEFAULT NULL,
            `expira` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `texto` varchar(255) DEFAULT NULL,
            `boleto` varchar(255) DEFAULT NULL,
            UNIQUE KEY `id` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    ");
}

function executacurl($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "RaviMonitor");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$contents = curl_exec($ch);
	curl_close($ch);
	return($contents);
}

//$cons1 = mysqli_query($db, "SELECT idBoleto FROM alertasMensalidade WHERE DATE_FORMAT(`expira`,'%Y/%m/%d') < DATE(NOW( ))");
$cons1 = mysqli_query($db, "SELECT idBoleto FROM alertasMensalidade");
$resp1 = mysqli_fetch_array($cons1);

if($resp1['idBoleto']) {
	$url = "http://www.ravimonitor.com.br/LembraBoleto.php?tipo=1&id=".$resp1['idBoleto'];
	$retorno = executacurl($url);
	if($retorno = 'true') {
		mysqli_query($db, "DELETE FROM alertasMensalidade WHERE idBoleto = '".$resp1['idBoleto']."'");
		//echo "DELETE FROM alertasMensalidade WHERE idBoleto = '".$resp1['idBoleto']."'<br>";
	}
}

$consultabd = mysqli_query($db, "SELECT COUNT(*) AS total FROM alertasMensalidade");
$resp = mysqli_fetch_array($consultabd);

if($resp['total'] == 0) {
	$consulta = mysqli_query($db, "SELECT tokenRAVI FROM system");
	
	$resposta = mysqli_fetch_array($consulta);
	$token = $resposta['tokenRAVI'];
	$url = "http://www.ravimonitor.com.br/LembraBoleto.php?tipo=2&token=".$token;
	$retorno = executacurl($url);
	//echo "<br>" . $retorno . "<br>";
	if($retorno) {
		$aux = explode('|',$retorno);
		$idBoleto = $aux[1];
		$data = $aux[2];
		$boleto = $aux[3];
		mysqli_query($db, "INSERT INTO alertasMensalidade (idBoleto, expira, boleto) VALUES ('".$idBoleto."', '".$data."', '".$boleto."')");
		//echo "INSERT INTO alertasMensalidade (idBoleto, expira, boleto) VALUES ('".$idBoleto."', '".$data."', '".$boleto."')<br>";
	}
}

mysqli_close($db);
?>