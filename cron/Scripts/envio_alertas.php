#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/

$id = $_GET["id"];
$data = $_GET["data1"] . " " . $_GET["data2"];
$valor1 = $_GET["valor1"];
$valor2 = $_GET["valor2"];
$statusAlert = $_GET["statusAlert"];

if($id) { exec_alerta($id, $data, $valor1, $valor2, $statusAlert); }

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

function removehtml($string) {
    $what = array( '<strong>', '</strong>' );
    $by   = array( '', '' );
    return str_replace($what, $by, $string);
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

function exec_alerta($id, $data, $valor1, $valor2, $statusAlert) {
    $db = mysqli_connect("localhost", "root", "#H0gGLS3@XeaW702_i51z@yUlN#", "Ravi");

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

    $resModels_alerta1 = mysqli_query($db, "SELECT * FROM models_alerta WHERE tipo = 'down'");
    $fetAlerta1 = mysqli_fetch_array($resModels_alerta1);

    $resSensor = mysqli_query($db, "SELECT id, nome, idDispositivo, tag, valor, banco, media1 FROM Sensores WHERE id = ".$id.";");
    $Sensores = mysqli_fetch_array($resSensor);
    $Pesquisa = mysqli_query($db, "SELECT id, data FROM Logalertas WHERE idSensor = '" . $id . "' AND resolvido = '0';");

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
    
    $enviar_alerta = 0;
    
    if($statusAlert == 1) {
        $mensagem = "<strong>Offline</strong>";
        if(mysqli_num_rows($Pesquisa)) {
            $Alert = mysqli_fetch_array($Pesquisa);
            $id_log = $Alert['id']; 
            $data = $Alert['data'];
            $enviar_alerta = 1; 
        }else {
            mysqli_query($db, "INSERT INTO Logalertas (idSensor, data, idDispositivo, nome, tag, valor, mensagem, tipo, statusAlert) VALUES ('".$Sensores['id']."', '".$data."', '".$Sensores['idDispositivo']."', '".$Sensores['nome']."', '".$Sensores['tag']."', '".$Sensores['valor']."', '".$mensagem."', '1', '".$statusAlert."')");
            if(mysqli_insert_id($db)) { 
                $id_log = mysqli_insert_id($db); 
                $enviar_alerta = 1;
            }
        }
    
    }else if($statusAlert == 4) {
        if(isset($Sensores['tag']) && $Sensores['tag'] == "rbl") {
            //$mensagem = "<strong>" . $valor1 . "</strong> blacklist detectada: <strong>" . $valor2 . "</strong>";
            $mensagem = "<strong>" . $valor1 . "</strong> blacklist " . $GLOBALS['lang'][321] . " <strong>" . $valor2 . "</strong>";
        }else if(isset($Sensores['tag']) && ($Sensores['tag'] == "disksnmp" || $Sensores['tag'] == "ramrouterhuawei" || $Sensores['tag'] == "memlinuxssh")) {
            //$mensagem = "<strong>Capacidade no limite com " . $valor1 . $unidade . " de utilização</strong>";
            $mensagem = "<strong>" . $GLOBALS['lang'][322] . " " . $valor1 . $unidade . " " . $GLOBALS['lang'][323] . "</strong>";
        }else if(isset($Sensores['tag']) && ($Sensores['tag'] == "pppoe" || $Sensores['tag'] == "pppoehuawei" || $Sensores['tag'] == "pppoejuniper" || $Sensores['tag'] == "convlanhuawei" || $Sensores['tag'] == "pppoecisco" || $Sensores['tag'] == "dhcpmk" || $Sensores['tag'] == "conexubntsnmp" || $Sensores['tag'] == "conexintelbras")) {
            //$mensagem = "<strong>Quantidade de conexões incomum para esse horário: " . $valor1 . ".</strong> Média dos últimos dias: $Sensores[media1]";
            $mensagem = "<strong>" . $GLOBALS['lang'][324] . " " . $valor1 . ".</strong> " . $GLOBALS['lang'][325] . " " . $Sensores['media1'];
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "trafegosnmp") {
            //$mensagem = "<strong>Valor muito incomum para esse horário! " . trafegoUnidade($valor1) . ".</strong> Média dos últimos dias: " . trafegoUnidade($Sensores['media1']);
            $mensagem = "<strong>" . $GLOBALS['lang'][326] . " " . trafegoUnidade($valor1) . ".</strong> " . $GLOBALS['lang'][325] . " " . trafegoUnidade($Sensores['media1']);
        }else {
            //$mensagem = "<strong>Valor muito incomum para esse horário! " . $valor1 . $unidade . ".</strong> Média dos últimos dias: " . $Sensores['media1'] . $unidade;
            $mensagem = "<strong>" . $GLOBALS['lang'][326] . " " . $valor1 . $unidade . ".</strong> " . $GLOBALS['lang'][325] . " " . $Sensores['media1'] . $unidade;
        }
        if(mysqli_num_rows($Pesquisa)) {
            $Alert = mysqli_fetch_array($Pesquisa);
            $id_log = $Alert['id'];
            $data = $Alert['data'];
            $enviar_alerta = 1; 
        }else {
            mysqli_query($db, "INSERT INTO Logalertas (idSensor, data, idDispositivo, nome, tag, valor, mensagem, tipo, statusAlert) VALUES ('".$Sensores['id']."', '".$data."', '".$Sensores['idDispositivo']."', '".$Sensores['nome']."', '".$Sensores['tag']."', '".$Sensores['valor']."', '".$mensagem."', '4', '".$statusAlert."')");
            if(mysqli_insert_id($db)) { 
                $id_log = mysqli_insert_id($db); 
                $enviar_alerta = 1; 
            }
        }
    
    }else if($statusAlert == 8) {
        //$mensagem = "<strong>Porta desconectada!</strong>";
        $mensagem = "<strong>" . $GLOBALS['lang'][1090] . "</strong>";
        if(mysqli_num_rows($Pesquisa)) {
            $Alert = mysqli_fetch_array($Pesquisa);
            $id_log = $Alert['id'];
            $data = $Alert['data'];
            $enviar_alerta = 1; 
        }else {
            mysqli_query($db, "INSERT INTO Logalertas (idSensor, data, idDispositivo, nome, tag, valor, mensagem, tipo, statusAlert) VALUES ('".$Sensores['id']."', '".$data."', '".$Sensores['idDispositivo']."', '".$Sensores['nome']."', '".$Sensores['tag']."', '".$Sensores['valor']."', '".$mensagem."', '4', '".$statusAlert."')");
            if(mysqli_insert_id($db)) { 
                $id_log = mysqli_insert_id($db); 
                $enviar_alerta = 1; 
            }
        }
    
    }else if($statusAlert == 10) {
        if(isset($Sensores['tag']) && $Sensores['tag'] == "datarateubntsnmp") {
            //$mensagem = "O limite estipulado em " . $Sensores['banco'] . $unidade . " foi atingido: <strong>" . $valor1 . $unidade . "</strong>";
            $mensagem = $GLOBALS['lang'][328] . " " . $Sensores['banco'] . $unidade . " " . $GLOBALS['lang'][329] . " <strong>" . $valor1 . $unidade . "</strong>";
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "trafegosnmp") {
            //$mensagem = "O limite de tráfego mínimo ou máximo estipulado foi atingido: <strong>" . trafegoUnidade($valor1) . "</strong>";
            $mensagem = $GLOBALS['lang'][330] . " <strong>" . trafegoUnidade($valor1) . "</strong>";
        }else if(isset($Sensores['tag']) && ($Sensores['tag'] == "voltagem" || $Sensores['tag'] == "voltagemceragon" || $Sensores['tag'] == "voltagemnetprobevolt" || $Sensores['tag'] == "voltagemnetprobeplusvolt")) {
            //$mensagem = "O limite de voltagem estipulado em " . $Sensores['valor'] . " V (min-max) foi atingido: <strong>" . $valor1 . $unidade . "</strong>";
            $mensagem = $GLOBALS['lang'][330] . " " . $Sensores['valor'] . " V " . $GLOBALS['lang'][332] . " <strong>" . $valor1 . $unidade . "</strong>";
        }
        if(mysqli_num_rows($Pesquisa)) {
            $Alert = mysqli_fetch_array($Pesquisa);
            $id_log = $Alert['id'];
            $data = $Alert['data'];
            $enviar_alerta = 1; 
        }else {
            mysqli_query($db, "INSERT INTO Logalertas (idSensor, data, idDispositivo, nome, tag, valor, mensagem, tipo, statusAlert) VALUES ('".$Sensores['id']."', '".$data."', '".$Sensores['idDispositivo']."', '".$Sensores['nome']."', '".$Sensores['tag']."', '".$Sensores['valor']."', '".$mensagem."', '10', '".$statusAlert."')");
            if(mysqli_insert_id($db)) { 
                $id_log = mysqli_insert_id($db); 
                $enviar_alerta = 1; 
            }
        }
    
    }else if($statusAlert == 12) {
        if(isset($Sensores['tag']) && $Sensores['tag'] == "statusporta") {
            //$mensagem = "Link Down - Porta desconectada!";
            $mensagem = $GLOBALS['lang'][330];
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "bgpoper") {
            //$mensagem = "Peer desconectado - Status: " . $valor1;
            $mensagem = $GLOBALS['lang'][334] . " " . $valor1;
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "ifOperStatus") {
            //$mensagem = "Interface inoperante!";
            $mensagem = $GLOBALS['lang'][335];
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == 'trafegosnmp') {
            //$mensagem = "Interface inoperante!";
            $mensagem = $GLOBALS['lang'][335];
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "traceroute") {
            //$mensagem = "Os saltos passaram de " . $Sensores['banco'] . " para " . $valor1 . " caracterizando uma possível mudança de rota!";
            $mensagem = $GLOBALS['lang'][336] . " " . $Sensores['banco'] . " " . $GLOBALS['lang'][337] . " " . $valor1 . " " . $GLOBALS['lang'][338];
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "portscan") {
            //$mensagem = "Uma ou mais novas portas abertas foram identificadas para o IP ou domínio!";
            $mensagem = $GLOBALS['lang'][339];
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "loadAverageLinux") {
            //$mensagem = "Processos (por core) aguardando na fila: " . $valor1;
            $mensagem = $GLOBALS['lang'][340] . " " . $valor1;
        }else if(isset($Sensores['tag']) && ($Sensores['tag'] == "ramsnmp" || $Sensores['tag'] == "ramoltparks")) {
            //$mensagem = "<strong>Capacidade no limite com " . $valor1 . $unidade . " de utilização</strong>";
            $mensagem = "<strong>" . $GLOBALS['lang'][322] . " " . $valor1 . $unidade . " " . $GLOBALS['lang'][323] . "</strong>";
        }else if(isset($Sensores['tag']) && ($Sensores['tag'] == "temperatura" || $Sensores['tag'] == "tempoltparks" || $Sensores['tag'] == "tempextalgcom" || $Sensores['tag'] == "tempintalgcom" || $Sensores['tag'] == "tempmodvolt"  || $Sensores['tag'] == "tempambvolt" || $Sensores['tag'] == "tempintmpptvolt" || $Sensores['tag'] == "tempnetprobevolt" || $Sensores['tag'] == "tempnetprobeplusvolt" || $Sensores['tag'] == "tempoduceragon" || $Sensores['tag'] == "tempiduceragon" || $Sensores['tag'] == "temperaturacpu" || $Sensores['tag'] == "temprondotec" || $Sensores['tag'] == "tempchassiszte" || $Sensores['tag'] == "tempxpsuscc" || $Sensores['tag'] == "tempsfpmk" || $Sensores['tag'] == "tempsfphuawei" || $Sensores['tag'] == "temperaturajuniper" || $Sensores['tag'] == "temperaturamimosa" || $Sensores['tag'] == "temperaturaponhuawei" || $Sensores['tag'] == "temperaturaponzte" || $Sensores['tag'] == "temperaturanehuawei" || $Sensores['tag'] == "tempplacahuawei" || $Sensores['tag'] == "tempplacazte" || $Sensores['tag'] == "tempslothuawei" || $Sensores['tag'] == "tempinterfacevsol" || $Sensores['tag'] == "temperaturavsol" || $Sensores['tag'] == "temperaturafiberhome" || $Sensores['tag'] == "temperaturaponfiberhome")) {
            //$mensagem = "<strong>A temperatura atingiu " . $valor1 . $unidade . "</strong>";
            $mensagem = "<strong>" . $GLOBALS['lang'][341] . " " . $valor1 . $unidade . "</strong>";
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "statusredevolt") {
            //$mensagem = "<strong>Rede elétrica desligada!</strong>";
            $mensagem = "<strong>" . $GLOBALS['lang'][342] . "</strong>";
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "coolersnexus") {
            if($valor1 == 1) {
                //$mensagem = "Status desconhecido";
                $mensagem = $GLOBALS['lang'][343];
            }else if($valor1 == 2) {
                //$mensagem = "Cooler ligado e operante";
                $mensagem = $GLOBALS['lang'][344];
            }else if($valor1 == 3) {
                //$mensagem = "Alerta! Cooler parado ou desligado";
                $mensagem = $GLOBALS['lang'][345];
            }else if($valor1 == 4) {
                //$mensagem = "Falha parcial! Cooler precisa ser substituído o mais rápido possível!";
                $mensagem = $GLOBALS['lang'][346];
            }
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "tensaocaxpsuscc") {
            if($valor1 == 1) {
                //$mensagem = "Falha na tensão CA";
                $mensagem = $GLOBALS['lang'][347];
            }else if($valor1 == 0) {
                //$mensagem = "Tensão CA ligada!";
                $mensagem = $GLOBALS['lang'][443];
            }
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "bateriaxpsuscc") {
            if($valor1 == 1) {
                //$mensagem = "Bateria desconectada";
                $mensagem = $GLOBALS['lang'][349];
            }else if($valor1 == 0) {
                //$mensagem = "Bateria conectada";
                $mensagem = $GLOBALS['lang'][350];
            }
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "cargabatxpsuscc") {
            if($valor1 == 1) {
                //$mensagem = "Bateria carregando";
                $mensagem = $GLOBALS['lang'][351];
            }else if($valor1 == 0) {
                //$mensagem = "Bateria normal";
                $mensagem = $GLOBALS['lang'][352];
            }
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "descargabatxpsuscc") {
            if($valor1 == 1) {
                //$mensagem = "Bateria descarregando";
                $mensagem = $GLOBALS['lang'][353];
            }else if($valor1 == 0) {
                //$mensagem = "Bateria normal";
                $mensagem = $GLOBALS['lang'][352];
            }
        }else if(isset($Sensores['tag']) && $Sensores['tag'] == "statuscarralgcom") {
            if($valor1 == 1) {
                //$mensagem = "Bateria desconectada ou tensão incompatível";
                $mensagem = $GLOBALS['lang'][354];
            }else if($valor1 == 2) {
                //$mensagem = "Nobreak";
                $mensagem = $GLOBALS['lang'][355];
            }else if($valor1 == 3) {
                //$mensagem = "Carregando - Corrente Constante";
                $mensagem = $GLOBALS['lang'][356];
            }else if($valor1 == 4) {
                //$mensagem = "Carregando - Equalização";
                $mensagem = $GLOBALS['lang'][357];
            }else if($valor1 == 5) {
                //$mensagem = "Carregada - Flutuação";
                $mensagem = $GLOBALS['lang'][358];
            }else if($valor1 == 6) {
                //$mensagem = "Nobreak TimeOut";
                $mensagem = $GLOBALS['lang'][359];
            }
        }else if(isset($Sensores['tag']) && ($Sensores['tag'] == "velocidadeporta" || $Sensores['tag'] == "velocidadeportaalgcom")) {
            //$mensagem = "Velocidade de negociação da interface caiu de " . $Sensores['banco'] . " Mbps para " . $valor1 . " Mbps!";
            $mensagem = $GLOBALS['lang'][360] . " " . $Sensores['banco'] . " Mbps " . $GLOBALS['lang'][337] . " " . $valor1 . " Mbps!";
        }else if(isset($Sensores['tag']) && ($Sensores['tag'] == "cpusnmp" || $Sensores['tag'] == "cpucoremk" || $Sensores['tag'] == "cpuoltparks")) {
            //$mensagem = "Carga de CPU em " . $valor1 . "%!";
            $mensagem = $GLOBALS['lang'][361] . " " . $valor1 . "%!";
        }else if(isset($Sensores['tag']) && ($Sensores['tag'] == "pppoe" || $Sensores['tag'] == "pppoehuawei" || $Sensores['tag'] == "pppoejuniper" || $Sensores['tag'] == "convlanhuawei" || $Sensores['tag'] == "pppoecisco" || $Sensores['tag'] == "dhcpmk" || $Sensores['tag'] == "conexubntsnmp" || $Sensores['tag'] == "conexintelbras")) {
            //$mensagem = "A quantidade de conexões caiu para " . $valor1 . "!";
            $mensagem = $GLOBALS['lang'][362] . " " . $valor1 . "!";
        }else {
            //$mensagem = "Problema crítico encontrado!";
            $mensagem = $GLOBALS['lang'][363];
        }
        if(mysqli_num_rows($Pesquisa)) {
            $Alert = mysqli_fetch_array($Pesquisa);
            $id_log = $Alert['id'];
            $data = $Alert['data'];
            $enviar_alerta = 1; 
        }else {
            mysqli_query($db, "INSERT INTO Logalertas (idSensor, data, idDispositivo, nome, tag, valor, mensagem, tipo, statusAlert) VALUES ('".$Sensores['id']."', '".$data."', '".$Sensores['idDispositivo']."', '".$Sensores['nome']."', '".$Sensores['tag']."', '".$Sensores['valor']."', '".$mensagem."', '4', '".$statusAlert."')");
            if(mysqli_insert_id($db)) { 
                $id_log = mysqli_insert_id($db); 
                $enviar_alerta = 1; 
            }
        }
    
    }else if($statusAlert == 14) {
        //$mensagem = "A velocidade de negociação da interface caiu!";
        $mensagem = $GLOBALS['lang'][364];
        if(mysqli_num_rows($Pesquisa)) {
            $Alert = mysqli_fetch_array($Pesquisa);
            $id_log = $Alert['id'];
            $data = $Alert['data'];
            $enviar_alerta = 1; 
        }else {
            mysqli_query($db, "INSERT INTO Logalertas (idSensor, data, idDispositivo, nome, tag, valor, mensagem, tipo, statusAlert) VALUES ('".$Sensores['id']."', '".$data."', '".$Sensores['idDispositivo']."', '".$Sensores['nome']."', '".$Sensores['tag']."', '".$Sensores['valor']."', '".$mensagem."', '4', '".$statusAlert."')");
            if(mysqli_insert_id($db)) { 
                $id_log = mysqli_insert_id($db); 
                $enviar_alerta = 1; 
            }
        }
    }

    // Se tem algo para ser enviado devemos verificar se há alguma integração ativa
    if($enviar_alerta == 1) {
        $resSystem = mysqli_query($db, "SELECT ativaTELEGRAM, ativaTELEGRAMdisp, ativaSMTP, ativaWHATS, ativaWHATSdisp, prioridadewhats, prioridadewhatsdisp, prioridadeSMTP, tokenRAVI FROM system;");
        $SysAlertas = mysqli_fetch_array($resSystem);

        $teste_envio_telegram = 0;

        // Verificando se a mensagem deve ser enviada pelo Telegram
        if($SysAlertas['ativaTELEGRAMdisp'] == 1 && $SysAlertas['ativaTELEGRAM'] == 1) {
            $resTelegram = mysqli_query($db, "SELECT * FROM telegrampadrao WHERE prioridade = 1 OR prioridade = 2;");
            if(mysqli_num_rows($resTelegram) >= 1) { $teste_envio_telegram = 1; }
        }else if($SysAlertas['ativaTELEGRAMdisp'] == 2) {
            $resTelegram = mysqli_query($db, "SELECT * FROM telegramdisp WHERE prioridade = 1 OR prioridade = 2;");
            if(mysqli_num_rows($resTelegram) >= 1) { $teste_envio_telegram = 1; }
        }

        // Verifica se tem algum Smartphone ativo que possa receber um alerta em push
        $resUserp = mysqli_query($db, "SELECT device_push_token FROM login WHERE device_push_token IS NOT NULL AND device_push_token != '';");

        // Verifica se a mensagem será enviada por algum meio e monta a mensagem se necessário
        if($teste_envio_telegram == 1 || $SysAlertas['ativaSMTP'] == 1 || $SysAlertas['ativaWHATS'] == 1 || mysqli_num_rows($resUserp) >= 1) {
            $consultDisp = mysqli_query($db, "SELECT Nome, ip, idGrupoPai FROM Dispositivos WHERE id = " . $Sensores['idDispositivo'] . ";");
            //echo "SELECT Nome, ip, idGrupoPai FROM Dispositivos WHERE id = " . $Sensores['idDispositivo'] . ";<br>";
			$Disp = mysqli_fetch_array($consultDisp);
			$NomeDispositivo = $Disp['Nome'];
            $customizar = 1;
            if($Disp['idGrupoPai']) {
                $consultGrupo = mysqli_query($db, "SELECT Nome, ativaTELEGRAM, chat_id, token, ativaWHATSAPP, prioridadewhats FROM GrupoMonitor WHERE id = ".$Disp['idGrupoPai'].";");
                //echo "SELECT Nome, ativaTELEGRAM, chat_id, token, ativaWHATSAPP FROM GrupoMonitor WHERE id = ".$Disp['idGrupoPai'].";<br>";
                $Grupo = mysqli_fetch_array($consultGrupo);
                $NomeGrupo = $Grupo['Nome'];
				if($Grupo['ativaTELEGRAM'] == 3) { $customizar = 3; }else if($Grupo['ativaTELEGRAM'] == 1) { $customizar = 2; }else { $customizar = 1; }
            }else {
                //$NomeGrupo = "Raiz";
                $NomeGrupo = $GLOBALS['lang'][365];
            }

            $execwhats = 1;
            // Se em configdispositivos está marcado herdar padrões
            if(!$SysAlertas['ativaWHATSdisp'] || $SysAlertas['ativaWHATSdisp'] == 1) {
                $prioridade_whats = $SysAlertas['prioridadewhats'];
                // Se no grupo está marcado herdar padrões
                if(!$Grupo['ativaWHATSAPP'] || $Grupo['ativaWHATSAPP'] == 1) {
                    $resWhats = mysqli_query($db, "SELECT idcontato FROM whats WHERE tipo IS NULL;");
                // Se no grupo está para customizar
                }else if($Grupo['ativaWHATSAPP'] == 2) {
                    $prioridade_whats = $Grupo['prioridadewhats'];
                    $resWhats = mysqli_query($db, "SELECT idcontato FROM whats WHERE tipo = 'g' AND idGrupo = ".$Disp['idGrupoPai'].";");
                // Se não é nenhuma das opções o whats está desativado
                }else {
                    $execwhats = 0;
                }
            // Se em configdispositivos está marcado customizar
            }else if($SysAlertas['ativaWHATSdisp'] == 2) {
                $prioridade_whats = $SysAlertas['prioridadewhatsdisp'];
                // Se no grupo está marcado herdar padrões
                if(!$Grupo['ativaWHATSAPP'] || $Grupo['ativaWHATSAPP'] == 1) {
                    $resWhats = mysqli_query($db, "SELECT idcontato FROM whats WHERE tipo = 'd';");
                // Se no grupo está para customizar
                }else if($Grupo['ativaWHATSAPP'] == 2) {
                    $prioridade_whats = $Grupo['prioridadewhats'];
                    $resWhats = mysqli_query($db, "SELECT idcontato FROM whats WHERE tipo = 'g' AND idGrupo = ".$Disp['idGrupoPai'].";");
                // Se não é nenhuma das opções o whats está desativado
                }else {
                    $execwhats = 0;
                }
            }else {
                $execwhats = 0;
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
            NULL | 1 = Herdar
            2 = Costumizar
            3 = Desativar

            $prioridade_whats - Prioridade de envio do whatsapp:
            1 = Offline e Alertas
            2 = Apenas Offline
            */

            if(isset($Sensores['tag']) && $Sensores['tag'] == 'ping' && $Sensores['valor'] != '') { 
				$ip = $Sensores['valor'];
			}else if(isset($Sensores['tag']) && $Sensores['tag'] == 'jitter' && $Sensores['valor'] != '') {
				$ip = $Sensores['valor'];
			}else if(isset($Sensores['tag']) && $Sensores['tag'] == 'rbl' && $Sensores['valor'] != '') {
				$ip = $Sensores['valor'];
			}else {
				$ip = $Disp['ip'];
			}

            $mensagem_enviar = $fetAlerta1['titulo'];
            $mensagem_enviar .= "

";
            $mensagem_enviar .= $fetAlerta1['mensagem'];
            $mensagem_enviar_app = $fetAlerta1['mensagem'];

            if($id) { $mensagem_enviar = str_replace("#id_sensor", $id, $mensagem_enviar); }
            if($id) { $mensagem_enviar_app = str_replace("#id_sensor", $id, $mensagem_enviar_app); }

            if($NomeDispositivo) { $mensagem_enviar = str_replace("#nome_dispositivo", $NomeDispositivo, $mensagem_enviar); }
            if($NomeDispositivo) { $mensagem_enviar = str_replace("#device_name", $NomeDispositivo, $mensagem_enviar); }
            if($NomeDispositivo) { $mensagem_enviar = str_replace("#nombre_dispositivo", $NomeDispositivo, $mensagem_enviar); }
            if($NomeDispositivo) { $mensagem_enviar_app = str_replace("#nome_dispositivo", $NomeDispositivo, $mensagem_enviar_app); }
            if($NomeDispositivo) { $mensagem_enviar_app = str_replace("#device_name", $NomeDispositivo, $mensagem_enviar_app); }
            if($NomeDispositivo) { $mensagem_enviar_app = str_replace("#nombre_dispositivo", $NomeDispositivo, $mensagem_enviar_app); }

            if($NomeGrupo) { $mensagem_enviar = str_replace("#nome_grupo", $NomeGrupo, $mensagem_enviar); }
            if($NomeGrupo) { $mensagem_enviar = str_replace("#group_name", $NomeGrupo, $mensagem_enviar); }
            if($NomeGrupo) { $mensagem_enviar = str_replace("#nombre_grupo", $NomeGrupo, $mensagem_enviar); }
            if($NomeGrupo) { $mensagem_enviar_app = str_replace("#nome_grupo", $NomeGrupo, $mensagem_enviar_app); }
            if($NomeGrupo) { $mensagem_enviar_app = str_replace("#group_name", $NomeGrupo, $mensagem_enviar_app); }
            if($NomeGrupo) { $mensagem_enviar_app = str_replace("#nombre_grupo", $NomeGrupo, $mensagem_enviar_app); }

            if($ip) { $mensagem_enviar = str_replace("#ip", $ip, $mensagem_enviar); }
            if($ip) { $mensagem_enviar_app = str_replace("#ip", $ip, $mensagem_enviar_app); }

            if($Sensores['nome']) { $mensagem_enviar = str_replace("#nome_sensor", $Sensores['nome'], $mensagem_enviar); }
            if($Sensores['nome']) { $mensagem_enviar = str_replace("#sensor_name", $Sensores['nome'], $mensagem_enviar); }
            if($Sensores['nome']) { $mensagem_enviar = str_replace("#nombre_del_sensor", $Sensores['nome'], $mensagem_enviar); }
            if($Sensores['nome']) { $mensagem_enviar_app = str_replace("#nome_sensor", $Sensores['nome'], $mensagem_enviar_app); }
            if($Sensores['nome']) { $mensagem_enviar_app = str_replace("#sensor_name", $Sensores['nome'], $mensagem_enviar_app); }
            if($Sensores['nome']) { $mensagem_enviar_app = str_replace("#nombre_del_sensor", $Sensores['nome'], $mensagem_enviar_app); }

            if($Sensores['tag']) { $mensagem_enviar = str_replace("#tag", "#".$Sensores['tag'], $mensagem_enviar); }
            if($Sensores['tag']) { $mensagem_enviar_app = str_replace("#tag", "#".$Sensores['tag'], $mensagem_enviar_app); }
            
            if($mensagem) { $mensagem_enviar = str_replace("#problema", removehtml($mensagem), $mensagem_enviar); }
            if($mensagem) { $mensagem_enviar = str_replace("#problem", removehtml($mensagem), $mensagem_enviar); }
            if($mensagem) { $mensagem_enviar_app = str_replace("#problema", removehtml($mensagem), $mensagem_enviar_app); }
            if($mensagem) { $mensagem_enviar_app = str_replace("#problem", removehtml($mensagem), $mensagem_enviar_app); }

            if($resLing['linguagem'] == 2) {
                $mensagem_enviar = str_replace("#data", date('Y/m/d H:i:s', strtotime($data)), $mensagem_enviar);
                $mensagem_enviar = str_replace("#date", date('Y/m/d H:i:s', strtotime($data)), $mensagem_enviar);
                $mensagem_enviar = str_replace("#fecha", date('Y/m/d H:i:s', strtotime($data)), $mensagem_enviar);
                $mensagem_enviar_app = str_replace("#data", date('Y/m/d H:i:s', strtotime($data)), $mensagem_enviar_app);
                $mensagem_enviar_app = str_replace("#date", date('Y/m/d H:i:s', strtotime($data)), $mensagem_enviar_app);
                $mensagem_enviar_app = str_replace("#fecha", date('Y/m/d H:i:s', strtotime($data)), $mensagem_enviar_app);
            }else {
                $mensagem_enviar = str_replace("#data", date('d/m/Y H:i:s', strtotime($data)), $mensagem_enviar);
                $mensagem_enviar = str_replace("#date", date('d/m/Y H:i:s', strtotime($data)), $mensagem_enviar);
                $mensagem_enviar = str_replace("#fecha", date('d/m/Y H:i:s', strtotime($data)), $mensagem_enviar);
                $mensagem_enviar_app = str_replace("#data", date('d/m/Y H:i:s', strtotime($data)), $mensagem_enviar_app);
                $mensagem_enviar_app = str_replace("#date", date('d/m/Y H:i:s', strtotime($data)), $mensagem_enviar_app);
                $mensagem_enviar_app = str_replace("#fecha", date('d/m/Y H:i:s', strtotime($data)), $mensagem_enviar_app);
            }

            //$mensagem_enviar_html = "PROBLEMA ENCONTRADO!<br><br>";
			//$mensagem_enviar_html .= " | id Sensor: " . $id . " <br>";
			//$mensagem_enviar_html .= " | Dispositivo: " . $NomeDispositivo .  " (" . $NomeGrupo . ") <br>";
            $mensagem_enviar_html = $GLOBALS['lang'][366] . "<br><br>";
            $mensagem_enviar_html .= " | id " . $GLOBALS['lang'][367] . " " . $id . " <br>";
			$mensagem_enviar_html .= " | " . $GLOBALS['lang'][368] . " " . $NomeDispositivo .  " (" . $NomeGrupo . ") <br>";

			$mensagem_enviar_html .= " | IP: " . $ip . " <br>";
			if(isset($Sensores['tag']) && $Sensores['tag'] != '') {
				//$mensagem_enviar_html .= " | Nome: " . $Sensores['nome'] . " <br>";
                $mensagem_enviar_html .= " | " . $GLOBALS['lang'][369] . " " . $Sensores['nome'] . " <br>";
				$mensagem_enviar_html .= " | Tag: #" . $Sensores['tag'] . " <br>";
			}else {
				//$mensagem_enviar_html .= " | Sensor: " . $Sensores['nome'] . " <br>";
                $mensagem_enviar_html .= " | " . $GLOBALS['lang'][367] . " " . $Sensores['nome'] . " <br>";
			}
			//$mensagem_enviar_html .= " | Problema: " . $mensagem . " <br>";
			//$mensagem_enviar_html .= " | Data: " . date('d/m/Y H:i:s', strtotime($data)) . " <br><br>";
            //$mensagem_enviar_html .= "® Ravi Monitoramento LTDA";
            $mensagem_enviar_html .= " | " . $GLOBALS['lang'][370] . " " . $mensagem . " <br>";
            if($resLing['linguagem'] == 2) {
                $mensagem_enviar_html .= " | " . $GLOBALS['lang'][371] . " " . date('Y/m/d H:i:s', strtotime($data)) . " <br><br>";
            }else {
                $mensagem_enviar_html .= " | " . $GLOBALS['lang'][371] . " " . date('d/m/Y H:i:s', strtotime($data)) . " <br><br>";
            }
            $mensagem_enviar_html .= "® " . $GLOBALS['lang'][372];
        }

        $consultAlertasW = mysqli_query($db, "SELECT id FROM Logalertas WHERE enviadoWHATS = '0' AND resolvido = '0' AND tipo != '2' AND tipo != '3' AND tipo != '7' AND id = $id_log;");
        $consultAlertasApp = mysqli_query($db, "SELECT id FROM Logalertas WHERE enviadopush = '0' AND resolvido = '0' AND tipo != '2' AND tipo != '3' AND tipo != '7' AND id = $id_log;");

        // se tem algum aplicativo ativo vamos fazer o alerta em push
        $app = array();
        if(mysqli_num_rows($resUserp) >= 1 && mysqli_num_rows($consultAlertasApp) == 1) {
            while($mobile = mysqli_fetch_array($resUserp)) {
                if($mobile['device_push_token']) {
                    $app[] = $mobile['device_push_token'];
                }
            }

            if(count($app)) {
                $url = "https://ravimonitor.com.br/app/api.php";
            
                $post = [
                    'acao' => 'push',
                    'playerids' => implode('|||', $app),
                    'token' => $SysAlertas['tokenRAVI'],
                    'titulo' => str_replace('*', '', $fetAlerta1['titulo']),
                    'mensagem' => str_replace('*', '', $mensagem_enviar_app),
                ];
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $response = curl_exec($ch);
                $t = json_decode($response);
                $err = curl_error($ch);
                curl_close($ch);
            
                if(isset($t->msg) == 'true') {
                    mysqli_query($db, "UPDATE Logalertas SET enviadopush = '1' WHERE id = $id_log;");
                    //echo "UPDATE Logalertas SET enviadopush = '1' WHERE id = $id_log;\n";
                }else {
                    echo $t->msg;
                    echo '\nOcorreu um erro inesperado, tente novamente ou procure nosso suporte!';
                }
            
            }
        }

        // Se tem algum WhatsApp ativo no sistema vamos fazer a entrega...
        if($SysAlertas['ativaWHATS'] == 1 && mysqli_num_rows($resWhats) >= 1 && mysqli_num_rows($consultAlertasW) == 1 && $execwhats == 1) {
            if($prioridade_whats == 1 || ($prioridade_whats == 2 && $statusAlert == 1)) {
                $client = new WhatsappClient();
                $update = $client->checkUpdate();
    
                while ($whats = mysqli_fetch_array($resWhats)) {
                    if ($update->authenticated) {
                        $client->send($whats['idcontato'], $mensagem_enviar);
                        mysqli_query($db, "UPDATE Logalertas SET enviadoWHATS = '1' WHERE id = $id_log;");
                        //echo json_encode($result, JSON_PRETTY_PRINT);
                    }
                }
            }
        }
    
        $consultAlertasT = mysqli_query($db, "SELECT id FROM Logalertas WHERE enviado = '0' AND resolvido = '0' AND tipo != '2' AND tipo != '3' AND tipo != '7' AND id = $id_log;");

        // Se tem algum Telegram ativo no sistema vamos fazer a entrega...
        if($teste_envio_telegram == 1 && mysqli_num_rows($consultAlertasT) == 1) {
            $mensagem_enviar = str_replace("*", "", $mensagem_enviar);
            $salvar = 0;
			if($customizar == 1) {
				if($SysAlertas['ativaTELEGRAMdisp'] == 1 && $SysAlertas['ativaTELEGRAM'] == 1) {
					$resTelegram1 = mysqli_query($db, "SELECT * FROM telegrampadrao;");
				}else if($SysAlertas['ativaTELEGRAMdisp'] == 2) {
					$resTelegram1 = mysqli_query($db, "SELECT * FROM telegramdisp;");
				}
				
				while ($Telegram1 = mysqli_fetch_array($resTelegram1)) {
					$partes1 = explode(':', $Telegram1['inicio']);
					$start = $partes1[0] * 60 + $partes1[1];
					$partes2 = explode(':', $Telegram1['fim']);
					$end = $partes2[0] * 60 + $partes2[1];
					if($end < $start) { $end = $end + 1440; }
                    $partes = explode(':', date("H:i"));
                    $now = $partes[0] * 60 + $partes[1];
					
					if ( $start <= $now && $now <= $end ) {
						$Chat_id = $Telegram1['chat_id'];
						$Token = $Telegram1['token'];
						
                        if($NomeDispositivo && $NomeGrupo && $ip && $Token && $Chat_id) {
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
                            curl_exec($curl);
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
			}else if($customizar == 2 && $Grupo['chat_id'] != "" && $Grupo['token'] != "") {
				$Chat_id = $Grupo['chat_id'];
				$Token = $Grupo['token'];
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
				curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
				if(!$err) { 
					$salvar = 1; 
				}else {
					$execucao = file_get_contents($url);
					if($execucao) { $salvar = 1; }
				}
			}
            if($salvar == 1) { mysqli_query($db, "UPDATE Logalertas SET enviado = '1' WHERE id = $id_log;"); }
        }

        // Se tem algum servidor de e-mail ativo no sistema vamos fazer a entrega...
        if($SysAlertas['ativaSMTP'] == 1) {
            // Enviar para offline e alerta
            if($SysAlertas['prioridadeSMTP'] == 1) {
                $consultAlertas6 = mysqli_query($db, "SELECT id FROM Logalertas WHERE enviadoSMTP = '0' AND resolvido = '0' AND tipo != '2' AND tipo != '3' AND tipo != '7'");
            // Enviar para offline
            }else {
                $consultAlertas6 = mysqli_query($db, "SELECT id FROM Logalertas WHERE enviadoSMTP = '0' AND resolvido = '0' AND tipo = '1' OR tipo = '10'");
            }
            if(mysqli_num_rows($consultAlertas6)) {
                $post = [
                    'id_log' => $id_log,
                    'mensagem_enviar' => $mensagem_enviar,
                    'mensagem_enviar_html' => $mensagem_enviar_html,
                    'tipo' => 1,
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'http://localhost/cron/apoio/envio_smtp.php');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
                curl_exec($ch);
            }
        }
    }
    mysqli_close($db);
}

/*
$id = 1445;
$data = date("Y-m-d H:i:s");
$valor1 = ""; 
$valor2 = "";
$statusAlert = 1;
exec_alerta($id, $data, $valor1, $valor2, $statusAlert);
*/

/*
/var/www/html/cron/Scripts/envio_alertas.php id=32 data1=2022-08-08 data2=15:56:32 valor1= valor2= statusAlert=1
/cron/Scripts/envio_alertas.php?id=32&data1=2022-08-08&data2=15:56:32&valor1=&valor2=&statusAlert=1

$db = mysqli_connect("localhost", "root", "#H0gGLS3@XeaW702_i51z@yUlN#", "Ravi");
mysqli_query($db, "DELETE FROM Logalertas WHERE idSensor = 4;");
mysqli_close($db);
*/

exit(0);
?>