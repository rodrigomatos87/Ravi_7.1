#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["v"];
$valor = (int)$_GET["banco"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

$timearq = date("H-i-s");
$arq = $id . "_" . $timearq;

//echo "id: " . $id . "<br>";
//echo "arq: " . $arq . "<br>";

if(!is_dir("/var/www/html/ram/httpcomp")) { mkdir('/var/www/html/ram/httpcomp/', 0777, true); }
chown("/var/www/html/ram/httpcomp", "www-data");

$path = "/var/www/html/ram/httpcomp/" . $arq . "/";
exec("mkdir /var/www/html/ram/httpcomp/" . $arq);

if(!$valor) {
    //echo "entrou 1 " . $valor . "<br>";
    $cmd = "wget -P " . $path . " --output-file=/var/www/html/ram/httpcomp/" . $arq . ".txt -p --no-check-certificate --no-cookies -erobots=off --refer=\"http://google.com\" --retry-connrefused --waitretry=1 --read-timeout=20 --timeout=15 --tries=6 --header=\"Accept: text/html\" --user-agent=\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0\" " . $ip;
	//exec("wget -P " . $path . " --output-file=/var/www/html/ram/httpcomp/" . $arq . ".txt -p --no-check-certificate --no-cookies -erobots=off ‐‐refer=\"http://google.com\" --retry-connrefused --waitretry=1 --read-timeout=20 --timeout=15 --tries=6 --header=\"Accept: text/html\" --user-agent=\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0\" " . $ip);
}else {
    //echo "entrou 2 " . $valor . "<br>";
	//exec("wget -P " . $path . " --output-file=/var/www/html/ram/httpcomp/" . $arq . ".txt -p --no-check-certificate --no-cookies -erobots=off --limit-rate=" . $valor . " ‐‐refer=\"http://google.com\" --retry-connrefused --waitretry=1 --read-timeout=20 --timeout=15 --tries=6 --header=\"Accept: text/html\" --user-agent=\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0\" " . $ip);
    $cmd = "wget -P " . $path . " --output-file=/var/www/html/ram/httpcomp/" . $arq . ".txt -p --no-check-certificate --no-cookies -erobots=off --limit-rate=" . $valor . " ‐‐refer=\"http://google.com\" --retry-connrefused --waitretry=1 --read-timeout=20 --timeout=15 --tries=6 --header=\"Accept: text/html\" --user-agent=\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0\" " . $ip;
}

//echo "cmd: " . $cmd . "<br>";
exec($cmd);

//wget -p --no-check-certificate --no-cookies -erobots=off ‐‐refer="http://google.com" --retry-connrefused --waitretry=1 --read-timeout=20 --timeout=15 --tries=6 --header="Accept: text/html" --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0" https://trainner.tweezer.jobs

$teste = exec("cat /var/www/html/ram/httpcomp/" . $arq . ".txt | grep \"wget: não foi possível resolver\" | wc -l");

//echo $teste . "<br>";

function insert( $data, $data1, $idSensor, $tempo, $tamanho, $velocidade, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|$StErro|
	exec("echo '|$data|$data1|$idSensor|$tempo|$tamanho|$velocidade|$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

if($teste == 0) {
    //echo "entrou <br>";
	$tamanho = exec("cat /var/www/html/ram/httpcomp/" . $arq . ".txt | tail -1 | awk '{print $4}' | sed 's/M/ M/g' | sed 's/K/ K/g' | sed 's/G/ G/g'");
	$tempo = exec("cat /var/www/html/ram/httpcomp/" . $arq . ".txt | tail -1 | awk '{print $6}' | sed 's/s/\/s/g' | sed 's/m/\/m/g' | sed 's/h/\/h/g' | sed 's/,/./g'");
	$velocidade = exec("cat /var/www/html/ram/httpcomp/" . $arq . ".txt | tail -1 | cut -d'(' -f2 | cut -d')' -f1");

    echo $tamanho . " / " . $tempo . " / " . $velocidade . "<br>";

	if($tamanho != '' && $tempo != '' && $velocidade != '') {
		$statusAlert = 6;
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

insert($data, $data1, $id, $tempo, $tamanho, $velocidade, $statusAlert, $StErro);
//echo "$data, $data1, $id, $tempo, $tamanho, $velocidade, $statusAlert, $StErro";

exec("rm -fr /var/www/html/ram/httpcomp/" . $arq . "*");

$valor1 = $tempo;
$valor2 = $tamanho;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>