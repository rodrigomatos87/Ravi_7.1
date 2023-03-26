<?php
/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/

$partes = explode(':', date("H:i"));
$now = $partes[0] * 60 + $partes[1];

$minuto = date("i");
$data = date("Y-m-d H:i:s"); 

$dataresolvido = '';

class WhatsappClient
{
    private const BASE_URL = "http://localhost:9050";

    private function doRequest($uri = "/", $method = "GET", $data = [], $headers = [])
    {
        $url = self::BASE_URL . $uri;
        $defaultHeaders = ["Content-type: application/json"];

        $curl = curl_init();

        if ($method === "GET") {
            $url = sprintf("%s?%s", $url, http_build_query($data));
        } elseif (!empty($data)) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $defaultHeaders + $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result);
    }

    public function checkUpdate()
    {
        return $this->doRequest("/check-update");
    }

    public function getContacts()
    {
        return $this->doRequest("/get-contacts");
    }

    public function send($contactId, $message)
    {
        //$ex = explode("@", $contactId);
        $data = [
            "contact_id" => $contactId,
            "message" => $message,
        ];
        return $this->doRequest("/send", "POST", $data);
    }
}

include("/var/www/html/cron/apoio/conexao.php");

$config_linguagem = mysqli_query($db, "SELECT linguagem FROM system");
if(mysqli_num_rows($config_linguagem) != 0) {
	$resLing = mysqli_fetch_array($config_linguagem);
	if($resLing['linguagem'] == 1) {
		include("/var/www/html/languages/portugues.php");
	}else if($resLing['linguagem'] == 2) {
		include("/var/www/html/languages/ingles.php");
	}else if($resLing['linguagem'] == 3) {
		include("/var/www/html/languages/espanhol.php");
	}
}else {
	include("/var/www/html/languages/portugues.php");
}

function GeraData($date1) {
	$date2 = date("Y-m-d H:i:s"); 
	$dateS1 = new \DateTime($date1);
	$dateS2 = new \DateTime($date2);
	$dateDiff = $dateS1->diff($dateS2);
	$anos = $dateDiff->y;
	$meses = $dateDiff->m;
	$dias = $dateDiff->d;
	$hora = $dateDiff->h;
	$minuto = $dateDiff->i;
	$segundo = $dateDiff->s;
	if($anos == 1) { $nomeA = " " . $GLOBALS['lang'][373] . ", "; }else if($anos > 1) { $nomeA = " " . $GLOBALS['lang'][374] . ", "; }else { $anos = ""; $nomeA = ""; }
	if($meses == 1) { $nomeM = " " . $GLOBALS['lang'][375] . ", "; }else if($meses > 1) { $nomeM = " " . $GLOBALS['lang'][376] . ", "; }else { $meses = ""; $nomeM = ""; }
	if($dias == 1) { $nomeD = " " . $GLOBALS['lang'][377] . ", "; }else if($dias > 1) { $nomeD = " " . $GLOBALS['lang'][61] . ", "; }else { $dias = ""; $nomeD = ""; }
	if($hora < 10) { $hora = "0" . $hora; }
	if($minuto < 10) { $minuto = "0" . $minuto; }
	if($segundo < 10) { $segundo = "0" . $segundo; }
	$tempo = $hora . ":" . $minuto . ":" . $segundo;
	return($anos.$nomeA.$meses.$nomeM.$dias.$nomeD.$tempo);
}

function trafegoUnidade($valor) {
	if($valor >= "1048576"){
		$valor = ($valor / 1024) / 1024;
		$s = explode('.', $valor);
		if($s[1]) {
			$valor = $s[0] . '.' . substr($s[1], 0, 2);
		}else {
			$valor = $s[0];
		}
		$unidade = " Gbps";
	}else if($valor >= "1024"){
		$valor = $valor / 1024;
		$s = explode('.', $valor);
		if($s[1]) {
			$valor = $s[0] . '.' . substr($s[1], 0, 2);
		}else {
			$valor = $s[0];
		}
		$unidade = " Mbps";
	}else {
		$unidade = " Kbps";
	}
	$resultado = $valor . " " . $unidade;
	return($resultado);
}

function removehtml($string) {
    $what = array( '<strong>', '</strong>' );
    $by   = array( '', '' );
    return str_replace($what, $by, $string);
}

$resultSystem = mysqli_query($db, "SELECT registroPlano FROM system;");
$dataSystem = mysqli_fetch_array($resultSystem);

if($dataSystem['registroPlano'] != 0) {
	// Salvando status resolvido -> Logs
	$consultAlertas1 = mysqli_query($db, "SELECT id, idSensor, data FROM Logalertas WHERE resolvido = '0'");
	while($Alert1 = mysqli_fetch_array($consultAlertas1)) {
		$resultSensores = mysqli_query($db, "SELECT statusAlert, pausar FROM Sensores WHERE id = $Alert1[idSensor]");
		$Sensores = mysqli_fetch_array($resultSensores);
		// Tempo levado para resolver o problema
		if($Sensores['statusAlert'] == 6 || $Sensores['statusAlert'] == 2 || $Sensores['statusAlert'] == 3 || $Sensores['statusAlert'] == 9 || $Sensores['statusAlert'] == 11 || $Sensores['statusAlert'] == 13) {
			$dataresolvido = GeraData($Alert1['data']);
			mysqli_query($db, "UPDATE Logalertas SET resolvido = '1', dataresolvido = '".$dataresolvido."' WHERE id = $Alert1[id];");
			//echo "UPDATE Logalertas SET resolvido = '1', dataresolvido = '".$dataresolvido."' WHERE id = $Alert1[id];<br>";
		}
	}

	$resSystem = mysqli_query($db, "SELECT ativaTELEGRAM, ativaTELEGRAMdisp, ativaSMTP, ativaWHATS, ativaWHATSdisp, prioridadewhats, prioridadewhatsdisp, prioridadeSMTP FROM system;");
	$SysAlertas = mysqli_fetch_array($resSystem);

	// Verificando se a mensagem deve ser enviada pelo Telegram
	if($SysAlertas['ativaTELEGRAMdisp'] == 1 && $SysAlertas['ativaTELEGRAM'] == 1) {
		$resTelegram = mysqli_query($db, "SELECT * FROM telegrampadrao;");
	}else if($SysAlertas['ativaTELEGRAMdisp'] == 2) {
		$resTelegram = mysqli_query($db, "SELECT * FROM telegramdisp;");
	}

	// Verifica se tem algum canal de comunicação para envio de alertas ativo
	if(mysqli_num_rows($resTelegram) >= 1 || $SysAlertas['ativaSMTP'] == 1 || $SysAlertas['ativaWHATS'] == 1) {
		// Verifica se tem algum problema solicionado
		$consultAlertas3 = mysqli_query($db, "SELECT * FROM Logalertas WHERE (enviado = '1' OR enviadoSMTP = '1' OR enviadoWHATS = '1') AND resolvido = '1' AND tipo != '2' AND tipo != '3'");
		if(mysqli_num_rows($consultAlertas3)) {
			$resModels_alerta1 = mysqli_query($db, "SELECT * FROM models_alerta WHERE tipo = 'up'");
			$fetAlerta1 = mysqli_fetch_array($resModels_alerta1);

			while($Alert3 = mysqli_fetch_array($consultAlertas3)) {
				$consultDisp = mysqli_query($db, "SELECT Nome, ip, idGrupoPai FROM Dispositivos WHERE id = $Alert3[idDispositivo]");
				$Disp = mysqli_fetch_array($consultDisp);
				$NomeDispositivo = $Disp['Nome'];
				$customizar2 = 1;
				$execwhats = 1;
				$statusAlert = $Alert3['statusAlert'];

				$resSensor = mysqli_query($db, "SELECT id, nome, idDispositivo, tag, valor, valor1, banco, media1 FROM Sensores WHERE id = ".$Alert3['idSensor'].";");
				$Sensores = mysqli_fetch_array($resSensor);

				if(isset($Sensores['tag']) && ($Sensores['tag'] == "ping" || $Sensores['tag'] == "mysql" || $Sensores['tag'] == "dns" || $Sensores['tag'] == "jitter" || $Sensores['tag'] == "http" || $Sensores['tag'] == "httpresp" || $Sensores['tag'] == "dnstop")) {
					$unidade = " ms";
				}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "cpusnmp" || $Sensores['tag'] == "cpuoltparks" || $Sensores['tag'] == "cpucoremk" || $Sensores['tag'] == "cpujuniper" || $Sensores['tag'] == "cpurouterhuawei" || $Sensores['tag'] == "ramjuniper" || $Sensores['tag'] == "cpunexus" || $Sensores['tag'] == "cpuplacahuawei" || $Sensores['tag'] == "cpuplacazte" || $Sensores['tag'] == "cpunehuawei" || $Sensores['tag'] == "cpuvsol" || $Sensores['tag'] == "cpuplacafiberhome" || $Sensores['tag'] == "ccqubnt" || $Sensores['tag'] == "ccqubntsnmp" || $Sensores['tag'] == "airmaxcubntsnmp" || $Sensores['tag'] == "airmaxqubntsnmp")) {
					$unidade = " %";
				}else if(isset($Sensores['tag']) && $Sensores['tag'] == 'smtpconect') {
					$unidade = " Segundos";
				}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "biasinterfacevsol" || $Sensores['tag'] == "biasponhuawei" || $Sensores['tag'] == "biasponfiberhome" || $Sensores['tag'] == "biasponzte" || $Sensores['tag'] == "biassfpmk" || $Sensores['tag'] == "biassfphuawei")) {
					$unidade = " mA";
				}else if(isset($Sensores['tag']) && $Sensores['tag'] == "contagemftp") {
					$unidade = "Arquivos";
				}else if(isset($Sensores['tag']) && $Sensores['tag'] == "traceroute") {
					$unidade = " Saltos";
				}else if(isset($Sensores['tag']) && $Sensores['tag'] == "snmpcustom") {
					$unidade = $Sensores['unidade'];
				}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "voltagem" || $Sensores['tag'] == "tensbatalgcom" || $Sensores['tag'] == "tensbatrondotec" || $Sensores['tag'] == "tensaorederondotec" || $Sensores['tag'] == "tenssaidaalgcom" || $Sensores['tag'] == "voltagemnetprobevolt" || $Sensores['tag'] == "voltagemnetprobeplusvolt" || $Sensores['tag'] == "voltagemceragon" || $Sensores['tag'] == "voltagemnexus" || $Sensores['tag'] == "voltagemxpsuscc" || $Sensores['tag'] == "voltagemsfpmk" || $Sensores['tag'] == "voltsfphuawei" || $Sensores['tag'] == "voltponhuawei" || $Sensores['tag'] == "voltponzte" || $Sensores['tag'] == "voltinterfacevsol" || $Sensores['tag'] == "voltagemvsol" || $Sensores['tag'] == "voltponfiberhome")) {
					$unidade = " V";
				}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "temperatura" || $Sensores['tag'] == "tempoltparks" || $Sensores['tag'] == "tempextalgcom" || $Sensores['tag'] == "tempintalgcom" || $Sensores['tag'] == "tempmodvolt"  || $Sensores['tag'] == "tempambvolt" || $Sensores['tag'] == "tempintmpptvolt" || $Sensores['tag'] == "tempnetprobevolt" || $Sensores['tag'] == "tempnetprobeplusvolt" || $Sensores['tag'] == "tempoduceragon" || $Sensores['tag'] == "tempiduceragon" || $Sensores['tag'] == "temperaturacpu" || $Sensores['tag'] == "temprondotec" || $Sensores['tag'] == "tempchassiszte" || $Sensores['tag'] == "tempxpsuscc" || $Sensores['tag'] == "tempsfpmk" || $Sensores['tag'] == "tempsfphuawei" || $Sensores['tag'] == "temperaturajuniper" || $Sensores['tag'] == "temperaturamimosa" || $Sensores['tag'] == "temperaturaponhuawei" || $Sensores['tag'] == "temperaturaponzte" || $Sensores['tag'] == "temperaturanehuawei" || $Sensores['tag'] == "tempplacahuawei" || $Sensores['tag'] == "tempplacazte" || $Sensores['tag'] == "tempslothuawei" || $Sensores['tag'] == "tempinterfacevsol" || $Sensores['tag'] == "temperaturavsol" || $Sensores['tag'] == "temperaturafiberhome" || $Sensores['tag'] == "temperaturaponfiberhome")) {
					$unidade = "° C";
				}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "sinalubnt" || $Sensores['tag'] == "rxsfphuawei" || $Sensores['tag'] == "rxsfphuaweiS6730" || $Sensores['tag'] == "txsfphuawei" || $Sensores['tag'] == "sinalmk" || $Sensores['tag'] == "sinalubntsnmp" || $Sensores['tag'] == "sinalceragon" || $Sensores['tag'] == "sinalmimosa" || $Sensores['tag'] == "noisefloorubnt" || $Sensores['tag'] == "noisefloormk" || $Sensores['tag'] == "noisefloorubntsnmp" || $Sensores['tag'] == "noisemimosa" || $Sensores['tag'] == "txpowerhuawei" || $Sensores['tag'] == "txponoltparks" || $Sensores['tag'] == "txpowerzte" || $Sensores['tag'] == "powerinterfacevsol" || $Sensores['tag'] == "txpowerfiberhome" || $Sensores['tag'] == "sinalintelbras" || $Sensores['tag'] == "sinalmksnmp" || $Sensores['tag'] == "noisefloormksnmp" || $Sensores['tag'] == "noisefloorintelbras" || $Sensores['tag'] == "potenciacambium" || $Sensores['tag'] == "potenciaceragon")) {
					$unidade = " dBm";
				}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "datarateubnt" || $Sensores['tag'] == "datarateubntsnmp" || $Sensores['tag'] == "phymimosa" || $Sensores['tag'] == "macmimosa")) {
					$unidade = " Mbps";
				}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "disksnmp" || $Sensores['tag'] == "ramsnmp" || $Sensores['tag'] == "ramoltparks" || $Sensores['tag'] == "memlinuxssh" || $Sensores['tag'] == "ramplacahuawei" || $Sensores['tag'] == "ramplacazte" || $Sensores['tag'] == "ramvsol" || $Sensores['tag'] == "ramplacafiberhome")) {
					$unidade = " %";
				}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "correntesaidaalgcom" || $Sensores['tag'] == "correntebatalgcom" || $Sensores['tag'] == "correnterondotec")) {
					$unidade = " A";
				}else {
					$unidade = "";
				}

				$valor1 = $Sensores['valor1'];
				$media1 = $Sensores['media1'];

				if($statusAlert == 1) {
					//$mensagem = "<strong>Online</strong> - Latência em: " . $valor1 . $unidade;
					$mensagem = "<strong>Online</strong> - " . $GLOBALS['lang'][379] . " " . $valor1 . $unidade;
				
				}else if($statusAlert == 4) {
					if(isset($Sensores['tag']) && $Sensores['tag'] == "rbl") {
						//$mensagem = "IP limpo em todas as blacklist consultadas";
						$mensagem = $GLOBALS['lang'][380];
					}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "disksnmp" || $Sensores['tag'] == "ramrouterhuawei" || $Sensores['tag'] == "memlinuxssh")) {
						//$mensagem = "<strong>Capacidade de utilização dentro de limites esperados: " . $valor1 . $unidade;
						$mensagem = "<strong>" . $GLOBALS['lang'][381] . "</strong> " . $valor1 . $unidade;
					}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "pppoe" || $Sensores['tag'] == "pppoehuawei" || $Sensores['tag'] == "pppoejuniper" || $Sensores['tag'] == "convlanhuawei" || $Sensores['tag'] == "pppoecisco" || $Sensores['tag'] == "dhcpmk" || $Sensores['tag'] == "conexubntsnmp" || $Sensores['tag'] == "conexintelbras")) {
						//$mensagem = "<strong>Quantidade normal conexões reestabelecida: " . $valor1 . ".</strong> Média dos últimos dias: " . $media1;
						$mensagem = "<strong>" . $GLOBALS['lang'][382] . " " . $valor1 . ".</strong> " . $GLOBALS['lang'][325] . " " . $media1;
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "trafegosnmp") {
						//$mensagem = "<strong>Tráfeto normal reestabelecido: " . trafegoUnidade($valor1) . ".</strong> Média dos últimos dias: " . trafegoUnidade($media1);
						$mensagem = "<strong>" . $GLOBALS['lang'][383] . " " . trafegoUnidade($valor1) . ".</strong> " . $GLOBALS['lang'][325] . " " . trafegoUnidade($media1);
					}else {
						//$mensagem = "<strong>Valor normal reestabeleido: " . $valor1 . $unidade . ".</strong> Média dos últimos dias: " . $media1 . $unidade;
						$mensagem = "<strong>" . $GLOBALS['lang'][384] . " " . $valor1 . $unidade . ".</strong> " . $GLOBALS['lang'][325] . " " . $media1 . $unidade;
					}
				
				}else if($statusAlert == 8) {
					//$mensagem = "<strong>Porta reconectada!</strong>";
					$mensagem = "<strong>" . $GLOBALS['lang'][385] . "</strong>";
				
				}else if($statusAlert == 10) {
					if(isset($Sensores['tag']) && $Sensores['tag'] == "datarateubntsnmp") {
						//$mensagem = "O limite estipulado em " . $Sensores['banco'] . $unidade . " voltou a ser respeitado: <strong>" . $valor1 . $unidade . "</strong>";
						$mensagem = $GLOBALS['lang'][328] . " " . $Sensores['banco'] . $unidade . " " . $GLOBALS['lang'][386] . " <strong>" . $valor1 . $unidade . "</strong>";
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "trafegosnmp") {
						//$mensagem = "O limite de tráfego mínimo ou máximo estipulado voltou a ser respeitado: <strong>" . trafegoUnidade($valor1) . "</strong>";
						$mensagem = $GLOBALS['lang'][387] . " <strong>" . trafegoUnidade($valor1) . "</strong>";
					}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "voltagem" || $Sensores['tag'] == "voltagemceragon" || $Sensores['tag'] == "voltagemnetprobevolt" || $Sensores['tag'] == "voltagemnetprobeplusvolt")) {
						//$mensagem = "O limite de voltagem estipulado em " . $Sensores['valor'] . " V (min-max) voltou a ser respeitado: <strong>" . $valor1 . $unidade . "</strong>";
						$mensagem = $GLOBALS['lang'][388] . " " . $Sensores['valor'] . " V " . $GLOBALS['lang'][389] . " <strong>" . $valor1 . $unidade . "</strong>";
					}
				
				}else if($statusAlert == 12) {
					if(isset($Sensores['tag']) && $Sensores['tag'] == "statusporta") {
						//$mensagem = "Link Up - Porta reconectada!";
						$mensagem = "Link Up - " . $GLOBALS['lang'][390];
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "bgpoper") {
						//$mensagem = "Peer reestabelecido - " . $valor1;
						$mensagem = $GLOBALS['lang'][391] . " - " . $valor1;
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "ifOperStatus") {
						//$mensagem = "Interface voltou a ficar operante!";
						$mensagem = $GLOBALS['lang'][392];
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == 'trafegosnmp') {
						//$mensagem = "Interface voltou a ficar operante!";
						$mensagem = $GLOBALS['lang'][392];
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "traceroute") {
						//$mensagem = "Os saltos normalizaram em: " . $valor1;
						$mensagem = $GLOBALS['lang'][392] . " " . $valor1;
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "portscan") {
						//$mensagem = "Quantidade de portas abertas esperadas normalizou";
						$mensagem = $GLOBALS['lang'][394];
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "loadAverageLinux") {
						//$mensagem = "Processos (por core) aguardando na fila normalizou: " . $valor1;
						$mensagem = $GLOBALS['lang'][395] . " " . $valor1;
					}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "ramsnmp" || $Sensores['tag'] == "ramoltparks")) {
						//$mensagem = "<strong>Utilização de memória normalizou em " . $valor1 . $unidade . "</strong>";
						$mensagem = "<strong>" . $GLOBALS['lang'][396] . " " . $valor1 . $unidade . "</strong>";
					}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "temperatura" || $Sensores['tag'] == "tempoltparks" || $Sensores['tag'] == "tempextalgcom" || $Sensores['tag'] == "tempintalgcom" || $Sensores['tag'] == "tempmodvolt"  || $Sensores['tag'] == "tempambvolt" || $Sensores['tag'] == "tempintmpptvolt" || $Sensores['tag'] == "tempnetprobevolt" || $Sensores['tag'] == "tempnetprobeplusvolt" || $Sensores['tag'] == "tempoduceragon" || $Sensores['tag'] == "tempiduceragon" || $Sensores['tag'] == "temperaturacpu" || $Sensores['tag'] == "temprondotec" || $Sensores['tag'] == "tempchassiszte" || $Sensores['tag'] == "tempxpsuscc" || $Sensores['tag'] == "tempsfpmk" || $Sensores['tag'] == "tempsfphuawei" || $Sensores['tag'] == "temperaturajuniper" || $Sensores['tag'] == "temperaturamimosa" || $Sensores['tag'] == "temperaturaponhuawei" || $Sensores['tag'] == "temperaturaponzte" || $Sensores['tag'] == "temperaturanehuawei" || $Sensores['tag'] == "tempplacahuawei" || $Sensores['tag'] == "tempplacazte" || $Sensores['tag'] == "tempslothuawei" || $Sensores['tag'] == "tempinterfacevsol" || $Sensores['tag'] == "temperaturavsol" || $Sensores['tag'] == "temperaturafiberhome" || $Sensores['tag'] == "temperaturaponfiberhome")) {
						//$mensagem = "<strong>A temperatura normalizou em " . $valor1 . $unidade . "</strong>";
						$mensagem = "<strong>" . $GLOBALS['lang'][397] . " " . $valor1 . $unidade . "</strong>";
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "statusredevolt") {
						//$mensagem = "<strong>Rede elétrica religada!</strong>";
						$mensagem = $GLOBALS['lang'][398];
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "coolersnexus") {
						//$mensagem = "Cooler ligado e operante";
						$mensagem = $GLOBALS['lang'][399];
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "tensaocaxpsuscc") {
						//$mensagem = "Tensão CA ligada!";
						$mensagem = $GLOBALS['lang'][443];
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "bateriaxpsuscc") {
						//$mensagem = "Bateria reconectada";
						$mensagem = $GLOBALS['lang'][444];
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "cargabatxpsuscc") {
						//$mensagem = "Bateria normal";
						$mensagem = $GLOBALS['lang'][352];
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "descargabatxpsuscc") {
						//$mensagem = "Bateria normal";
						$mensagem = $GLOBALS['lang'][352];
					}else if(isset($Sensores['tag']) && $Sensores['tag'] == "statuscarralgcom") {
						if($valor1 == 1) {
							//$mensagem = "Bateria desconectada ou tensão incompatível (Cód. 1)";
							$mensagem = $GLOBALS['lang'][445];
						}else if($valor1 == 2) {
							//$mensagem = "Nobreak (Cód. 2)";
							$mensagem = $GLOBALS['lang'][446];
						}else if($valor1 == 3) {
							//$mensagem = "Carregando - Corrente Constante (Cód. 3)";
							$mensagem = $GLOBALS['lang'][447];
						}else if($valor1 == 4) {
							//$mensagem = "Carregando - Equalização (Cód. 4)";
							$mensagem = $GLOBALS['lang'][448];
						}else if($valor1 == 5) {
							//$mensagem = "Carregada - Flutuação (Cód. 5)";
							$mensagem = $GLOBALS['lang'][449];
						}else if($valor1 == 6) {
							//$mensagem = "Nobreak TimeOut (Cód. 6)";
							$mensagem = $GLOBALS['lang'][450];
						}
					}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "velocidadeporta" || $Sensores['tag'] == "velocidadeportaalgcom")) {
						//$mensagem = "Velocidade de negociação da interface normalizou em " . $valor1 . " Mbps!";
						$mensagem = $GLOBALS['lang'][451] . " " . $valor1 . " Mbps!";
					}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "cpusnmp" || $Sensores['tag'] == "cpucoremk" || $Sensores['tag'] == "cpuoltparks")) {
						//$mensagem = "Carga de CPU normalizou em " . $valor1 . "%";
						$mensagem = $GLOBALS['lang'][452] . " " . $valor1 . "%";
					}else if(isset($Sensores['tag']) && ($Sensores['tag'] == "pppoe" || $Sensores['tag'] == "pppoehuawei" || $Sensores['tag'] == "pppoejuniper" || $Sensores['tag'] == "convlanhuawei" || $Sensores['tag'] == "pppoecisco" || $Sensores['tag'] == "dhcpmk" || $Sensores['tag'] == "conexubntsnmp" || $Sensores['tag'] == "conexintelbras")) {
						//$mensagem = "A quantidade de conexões normalizou em " . $valor1;
						$mensagem = $GLOBALS['lang'][453] . " " . $valor1;
					}else {
						//$mensagem = "Problema crítico resolvido!";
						$mensagem = $GLOBALS['lang'][454];
					}
				
				}else if($statusAlert == 14) {
					//$mensagem = "A velocidade de negociação da interface normalizou!";
					$mensagem = $GLOBALS['lang'][455];
				}

				/*
				Verificando se a mensagem deve ser enviada pelo WhatsApp e mantamos a query

				ativaWHATS - tabela system
				1 = Ativo
				2 = Desativado

				ativaWHATSAPP - tabela GrupoMonitor
				NULL | 1 = Herdar
				2 = Custom
				3 = desativar

				ativaWHATSdisp - config dispositivos - tabela system
				1 = Herdar
				2 = Costumizar
				3 = Desativar

				prioridade_whats - Prioridade de envio do whatsapp:
				1 = Offline e Alertas
				2 = Apenas Offline
				*/

				if($Disp['idGrupoPai'] == 0) {
					//$NomeGrupo = "Raiz";
					$NomeGrupo = $GLOBALS['lang'][365];
					// Se em configdispositivos está marcado herdar padrões
					if(!$SysAlertas['ativaWHATSdisp'] || $SysAlertas['ativaWHATSdisp'] == 1) {
						$prioridade_whats = $SysAlertas['prioridadewhats'];
						$resWhats = mysqli_query($db, "SELECT idcontato FROM whats WHERE tipo IS NULL;");
					// Se em configdispositivos está marcado customizar
					}else if($SysAlertas['ativaWHATSdisp'] == 2) {
						$prioridade_whats = $SysAlertas['prioridadewhatsdisp'];
						$resWhats = mysqli_query($db, "SELECT idcontato FROM whats WHERE tipo = 'd';");
					// Se não é nenhuma das opções o whats está desativado
					}else {
						$execwhats = 0;
					}
					
				}else {
					$consultGrupo2 = mysqli_query($db, "SELECT Nome, ativaTELEGRAM, chat_id, token, ativaWHATSAPP, prioridadewhats FROM GrupoMonitor WHERE id = $Disp[idGrupoPai]");
					$Grupo2 = mysqli_fetch_array($consultGrupo2);
					$NomeGrupo = $Grupo2['Nome'];
					if($Grupo2['ativaTELEGRAM'] == 3) { $customizar2 = 3; }else if($Grupo2['ativaTELEGRAM'] == 1) { $customizar2 = 2; }else { $customizar2 = 1; }

					// Se em configdispositivos está marcado herdar padrões
					if(!$SysAlertas['ativaWHATSdisp'] || $SysAlertas['ativaWHATSdisp'] == 1) {
						$prioridade_whats = $SysAlertas['prioridadewhats'];
						// Se no grupo está marcado herdar padrões
						if(!$Grupo2['ativaWHATSAPP'] || $Grupo2['ativaWHATSAPP'] == 1) {
							$resWhats = mysqli_query($db, "SELECT idcontato FROM whats WHERE tipo IS NULL;");
						// Se no grupo está para customizar
						}else if($Grupo2['ativaWHATSAPP'] == 2) {
							$prioridade_whats = $Grupo2['prioridadewhats'];
							$resWhats = mysqli_query($db, "SELECT idcontato FROM whats WHERE tipo = 'g' AND idGrupo = ".$Disp['idGrupoPai'].";");
						// Se não é nenhuma das opções o whats está desativado
						}else {
							$execwhats = 0;
						}
					// Se em configdispositivos está marcado customizar
					}else if($SysAlertas['ativaWHATSdisp'] == 2) {
						$prioridade_whats = $SysAlertas['prioridadewhatsdisp'];
						// Se no grupo está marcado herdar padrões
						if(!$Grupo2['ativaWHATSAPP'] || $Grupo2['ativaWHATSAPP'] == 1) {
							$resWhats = mysqli_query($db, "SELECT idcontato FROM whats WHERE tipo = 'd';");
						// Se no grupo está para customizar
						}else if($Grupo2['ativaWHATSAPP'] == 2) {
							$prioridade_whats = $Grupo2['prioridadewhats'];
							$resWhats = mysqli_query($db, "SELECT idcontato FROM whats WHERE tipo = 'g' AND idGrupo = ".$Disp['idGrupoPai'].";");
						}else {
							$execwhats = 0;
						}
					// Se não é nenhuma das opções o whats está desativado
					}else {
						$execwhats = 0;
					}
				}

				if(isset($Alert3['tag']) && $Alert3['tag'] == 'ping' && $Alert3['valor'] != '') { 
					$ip = $Alert3['valor'];
				}else if(isset($Alert3['tag']) && $Alert3['tag'] == 'jitter' && $Alert3['valor'] != '') {
					$ip = $Alert3['valor'];
				}else if(isset($Alert3['tag']) && $Alert3['tag'] == 'rbl' && $Alert3['valor'] != '') {
					$ip = $Alert3['valor'];
				}else {
					$ip = $Disp['ip'];
				}
				
				//$tempodecorrido = $Alert3['dataresolvido'];
				//$data_problema = $Alert3['data'];
				//$data_atual = date('d/m/Y H:i:s', strtotime($data);
				
				$mensagem_enviar = $fetAlerta1['titulo'];
				$mensagem_enviar .= "

";
				$mensagem_enviar .= $fetAlerta1['mensagem'];
	
				if($Alert3['idSensor']) { $mensagem_enviar = str_replace("#id_sensor", $Alert3['idSensor'], $mensagem_enviar); }

				if($NomeDispositivo) { $mensagem_enviar = str_replace("#nome_dispositivo", $NomeDispositivo, $mensagem_enviar); }
				if($NomeDispositivo) { $mensagem_enviar = str_replace("#device_name", $NomeDispositivo, $mensagem_enviar); }
				if($NomeDispositivo) { $mensagem_enviar = str_replace("#nombre_dispositivo", $NomeDispositivo, $mensagem_enviar); }

				if($NomeGrupo) { $mensagem_enviar = str_replace("#nome_grupo", $NomeGrupo, $mensagem_enviar); }
				if($NomeGrupo) { $mensagem_enviar = str_replace("#group_name", $NomeGrupo, $mensagem_enviar); }
				if($NomeGrupo) { $mensagem_enviar = str_replace("#nombre_grupo", $NomeGrupo, $mensagem_enviar); }

				if($ip) { $mensagem_enviar = str_replace("#ip", $ip, $mensagem_enviar); }

				if($Alert3['nome']) { $mensagem_enviar = str_replace("#nome_sensor", $Alert3['nome'], $mensagem_enviar); }
				if($Alert3['nome']) { $mensagem_enviar = str_replace("#sensor_name", $Alert3['nome'], $mensagem_enviar); }
				if($Alert3['nome']) { $mensagem_enviar = str_replace("#nombre_del_sensor", $Alert3['nome'], $mensagem_enviar); }

				if($Alert3['tag']) { $mensagem_enviar = str_replace("#tag", "#".$Alert3['tag'], $mensagem_enviar); }

				if($mensagem) { $mensagem_enviar = str_replace("#solucao", removehtml($mensagem), $mensagem_enviar); }
				if($mensagem) { $mensagem_enviar = str_replace("#solucion", removehtml($mensagem), $mensagem_enviar); }
				if($mensagem) { $mensagem_enviar = str_replace("#solution", removehtml($mensagem), $mensagem_enviar); }

				if($resLing['linguagem'] == 2) {
					$mensagem_enviar = str_replace("#data_atual", date('Y/m/d H:i:s', strtotime($data)), $mensagem_enviar);
					$mensagem_enviar = str_replace("#data_incidente", date('Y/m/d H:i:s', strtotime($Alert3['data'])), $mensagem_enviar);
					$mensagem_enviar = str_replace("#tempo_decorrido", $Alert3['dataresolvido'], $mensagem_enviar);

					$mensagem_enviar = str_replace("#current_date", date('Y/m/d H:i:s', strtotime($data)), $mensagem_enviar);
					$mensagem_enviar = str_replace("#incident_date", date('Y/m/d H:i:s', strtotime($Alert3['data'])), $mensagem_enviar);
					$mensagem_enviar = str_replace("#elapsed_time", $Alert3['dataresolvido'], $mensagem_enviar);

					$mensagem_enviar = str_replace("#fecha_actual", date('Y/m/d H:i:s', strtotime($data)), $mensagem_enviar);
					$mensagem_enviar = str_replace("#fecha_del_incidente", date('Y/m/d H:i:s', strtotime($Alert3['data'])), $mensagem_enviar);
					$mensagem_enviar = str_replace("#tiempo_transcurrido", $Alert3['dataresolvido'], $mensagem_enviar);
				}else {
					$mensagem_enviar = str_replace("#data_atual", date('d/m/Y H:i:s', strtotime($data)), $mensagem_enviar);
					$mensagem_enviar = str_replace("#data_incidente", date('d/m/Y H:i:s', strtotime($Alert3['data'])), $mensagem_enviar);
					$mensagem_enviar = str_replace("#tempo_decorrido", $Alert3['dataresolvido'], $mensagem_enviar);

					$mensagem_enviar = str_replace("#current_date", date('d/m/Y H:i:s', strtotime($data)), $mensagem_enviar);
					$mensagem_enviar = str_replace("#incident_date", date('d/m/Y H:i:s', strtotime($Alert3['data'])), $mensagem_enviar);
					$mensagem_enviar = str_replace("#elapsed_time", $Alert3['dataresolvido'], $mensagem_enviar);

					$mensagem_enviar = str_replace("#fecha_actual", date('d/m/Y H:i:s', strtotime($data)), $mensagem_enviar);
					$mensagem_enviar = str_replace("#fecha_del_incidente", date('d/m/Y H:i:s', strtotime($Alert3['data'])), $mensagem_enviar);
					$mensagem_enviar = str_replace("#tiempo_transcurrido", $Alert3['dataresolvido'], $mensagem_enviar);
				}

				//$mensagem_enviar_html = "PROBLEMA RESOLVIDO! <br><br>";
				//$mensagem_enviar_html .= " | id Sensor: " . $Alert3['idSensor'] . " <br>";
				//$mensagem_enviar_html .= " | Dispositivo: " . $NomeDispositivo .  " (" . $NomeGrupo . ") <br>";
				//$mensagem_enviar_html .= " | IP: " . $ip . " <br>";
				$mensagem_enviar_html = $GLOBALS['lang'][456] . "<br><br>";
				$mensagem_enviar_html .= " | id " . $GLOBALS['lang'][367] . " " . $Alert3['idSensor'] . " <br>";
				$mensagem_enviar_html .= " | " . $GLOBALS['lang'][368] . " " . $NomeDispositivo .  " (" . $NomeGrupo . ") <br>";
				$mensagem_enviar_html .= " | IP: " . $ip . " <br>";
	
				if(isset($Alert3['tag']) && $Alert3['tag'] != '') {
					//$mensagem_enviar_html .= " | Nome: " . $Alert3['nome'] . " <br>";
					$mensagem_enviar_html .= " | " . $GLOBALS['lang'][369] . " " . $Alert3['nome'] . " <br>";
					$mensagem_enviar_html .= " | Tag: #" . $Alert3['tag'] . " <br>";
				}else {
					$mensagem_enviar_html .= " | Sensor: " . $Alert3['nome'] . " <br>";
					$mensagem_enviar_html .= " | " . $GLOBALS['lang'][367] . " " . $Alert3['nome'] . " <br>";
				}

				//$mensagem_enviar_html .= " | Solução: " . $mensagem . " <br>";
				$mensagem_enviar_html .= " | " . $GLOBALS['lang'][457] . " " . $mensagem . " <br>";
	
				//$mensagem_enviar_html .= " | Data: " . date('d/m/Y H:i:s', strtotime($data)) . " <br>";
				//$mensagem_enviar_html .= " | Tempo decorrido: " . $dataresolvido . " <br><br>";
				//$mensagem_enviar_html .= "® Ravi Monitoramento LTDA";

				if($resLing['linguagem'] == 2) {
					$mensagem_enviar_html .= " | " . $GLOBALS['lang'][371] . " " . date('Y/m/d H:i:s', strtotime($data)) . " <br><br>";
					$mensagem_enviar_html .= " | " . $GLOBALS['lang'][458] . " " . $dataresolvido . " <br><br>";
				}else {
					$mensagem_enviar_html .= " | " . $GLOBALS['lang'][371] . " " . date('d/m/Y H:i:s', strtotime($data)) . " <br><br>";
					$mensagem_enviar_html .= " | " . $GLOBALS['lang'][458] . " " . $dataresolvido . " <br><br>";
				}
				$mensagem_enviar_html .= "® " . $GLOBALS['lang'][372];

				// Enviar problema resolvido para o Whats
				if($execwhats == 1) {
					if($SysAlertas['ativaWHATS'] == 1 && mysqli_num_rows($resWhats) >= 1 && $Alert3['enviadoWHATS'] == 1) {
						if($prioridade_whats == 1 || $prioridade_whats == 2 && ($Alert3['tipo'] == 1 || $Alert3['tipo'] == 10)) {
							$client = new WhatsappClient();
							$update = $client->checkUpdate();
							while ($whats = mysqli_fetch_array($resWhats)) {
								if ($update->authenticated) {
									$client->send($whats['idcontato'], $mensagem_enviar);
									mysqli_query($db, "UPDATE Logalertas SET enviadoWHATS = '2' WHERE id = ".$Alert3['id'].";");
									//echo json_encode($result, JSON_PRETTY_PRINT);
								}
							}
						}
					}
				}

				// Enviar problema resolvido para o Telegram
				if($Alert3['enviado'] == 1 && mysqli_num_rows($resTelegram) >= 1) {
					$mensagem_enviar = str_replace("*", "", $mensagem_enviar);
					$salvar = 0;
					if($customizar2 == 1) {
						if($SysAlertas['ativaTELEGRAMdisp'] == 1 && $SysAlertas['ativaTELEGRAM'] == 1) {
							$resTelegram2 = mysqli_query($db, "SELECT * FROM telegrampadrao;");
						}else if($SysAlertas['ativaTELEGRAMdisp'] == 2) {
							$resTelegram2 = mysqli_query($db, "SELECT * FROM telegramdisp;");
						}
						
						while ($Telegram2 = mysqli_fetch_array($resTelegram2)) {
							$partes1 = explode(':', $Telegram2['inicio']);
							$start = $partes1[0] * 60 + $partes1[1];
							$partes2 = explode(':', $Telegram2['fim']);
							$end = $partes2[0] * 60 + $partes2[1];
							if($end < $start) { $end = $end + 1440; }
							
							if ( $start <= $now && $now <= $end ) {
								$Chat_id = $Telegram2['chat_id'];
								$Token = $Telegram2['token'];
								
								if($Telegram2['prioridade'] == 1 || $Telegram2['prioridade'] == 2 && ($Alert3['tipo'] == 1 || $Alert3['tipo'] == 10)) {
									if($NomeDispositivo && $NomeGrupo && $ip && $Token && $Chat_id && $Alert3['enviado'] == '1' && $Alert3['resolvido'] == '1') {
										$url = "https://api.telegram.org/bot".$Token."/sendMessage?chat_id=".$Chat_id."&text=".urlencode($mensagem_enviar)."";
										$curl = curl_init();
										curl_setopt_array($curl, array(
											CURLOPT_URL => $url,
											CURLOPT_RETURNTRANSFER => true,
											CURLOPT_ENCODING => "",
											CURLOPT_MAXREDIRS => 10,
											CURLOPT_TIMEOUT => 4,
											CURLOPT_FOLLOWLOCATION => false,
											CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
											CURLOPT_CUSTOMREQUEST => "GET",
										));
										$response = curl_exec($curl);
										$err = curl_error($curl);
										curl_close($curl);
										if(!$err) {
											$salvar = 1; 
										}else {
											$execucao = file_get_contents($url);
											if($execucao) { $salvar = 1; }
										}
									}
								}
							}
						}
					}else if($customizar2 == 2 && $Grupo2['chat_id'] != "" && $Grupo2['token'] != "") {
						$Chat_id = $Grupo2['chat_id'];
						$Token = $Grupo2['token'];
						$url = "https://api.telegram.org/bot".$Token."/sendMessage?chat_id=".$Chat_id."&text=".urlencode($mensagem_enviar)."";
						$curl = curl_init();
						curl_setopt_array($curl, array(
							CURLOPT_URL => $url,
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => "",
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 4,
							CURLOPT_FOLLOWLOCATION => false,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => "GET",
						));
						$response = curl_exec($curl);
						$err = curl_error($curl);
						curl_close($curl);
						if(!$err) { 
							$salvar = 1;
						}else {
							$execucao = file_get_contents($url);
							if($execucao) { $salvar = 1; }
						}
					}
					if($salvar == 1) { 
						//$db = mysqli_connect("localhost", "root", "#M0n1t0rR@v1#24.02.2015#", "Ravi");
						mysqli_query($db, "UPDATE Logalertas SET enviado = '2' WHERE id = '".$Alert3['id']."';"); 
					}
				}

				// Enviar problema resolvido para o email
				if($SysAlertas['ativaSMTP'] == 1 && $Alert3['enviadoSMTP'] == 1) {
					$post = [
						'id_log' => $Alert3['id'],
						'mensagem_enviar' => $mensagem_enviar,
						'mensagem_enviar_html'   => $mensagem_enviar_html,
						'tipo' => 2,
					];
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'http://localhost/cron/apoio/envio_smtp.php');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
					curl_exec($ch);
				}
			}
		}
	}
}

mysqli_close($db);
//exit(0);
?>