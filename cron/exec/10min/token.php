#!/usr/bin/php
<?php
include("/var/www/html/cron/apoio/conexao.php");

$dataAtual = date("Y-m-d H:i:s");
$key = 'lZke4%QQ5y6uo%WPtBXDy9gfv';
$dataCadastro = exec('date -r /var "+%Y-%m-%d %H:%M:%S"');
//touch -t 202006202027 /var

function encodeBase64($string) {
	$what = array( '+', '/', '=' );
	$by   = array( '-', '_', '' );
	return str_replace($what, $by, base64_encode($string));
}

function executacurl($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "RaviMonitor");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$contents = curl_exec($ch);
	curl_close($ch);
	return($contents);
}

$consulta = mysqli_query($db, "SELECT * FROM system");
$resposta = mysqli_fetch_array($consulta);

$token = $resposta['tokenRAVI'];
$versaoA = $resposta['versao'];
$debugA = (int)$resposta['debug'];
$dataCadastro_bd = $resposta['dataCadastro'];

$ipservidor = shell_exec("ip a | grep 'inet ' | grep -v 127.0.0.1 | awk '{print $2}' | cut -f1 -d/ | tr '\n' ',' | sed 's/,$//'");
if(!$ipservidor) {
    sleep(5);
    $ipservidor = shell_exec("ip a | grep 'inet ' | grep -v 127.0.0.1 | awk '{print $2}' | cut -f1 -d/ | tr '\n' ',' | sed 's/,$//'");
}

$consulta_login = mysqli_query($db, "SELECT nome, telefone FROM login WHERE tipo = 'Master' LIMIT 1");
if(mysqli_num_rows($consulta_login)) {
    $resposta_login = mysqli_fetch_array($consulta_login);
    $nome = $resposta_login['nome'];
    $tel = $resposta_login['telefone'];
}else {
    $nome = '';
    $tel = '';
}

if(is_file("/var/www/html/css/colorbox.css")) {
    $info = file_get_contents("/var/www/html/css/colorbox.css");
    $aux = explode('}', $info);
    for ($i=0; $i<count($aux); $i++) {
        if(preg_match('/#Grafico1 .Graf5 {width: 100%; height: 100%; background:/', $aux[$i])) {
            $aux1 = explode('#', $aux[$i]);
            $codCSS = str_replace(';', '', $aux1[2]);
            $codCSS = str_replace(' ', '', $codCSS);
        }
    }
}else {
    $codCSS = '';
}

if(!$codCSS || $codCSS == 'xxxxxx') {
    $n1 = rand(0, 9);
    $n2 = rand(0, 9);
    $n3 = rand(0, 9);
    $l1 = exec("pwgen 50 1 | sed 's/[g-z]//g' | sed 's/[G-Z]//g' | sed 's/[0-9]//g' | cut -b 1");
    $l2 = exec("pwgen 50 1 | sed 's/[g-z]//g' | sed 's/[G-Z]//g' | sed 's/[0-9]//g' | cut -b 1");
    $l3 = exec("pwgen 50 1 | sed 's/[g-z]//g' | sed 's/[G-Z]//g' | sed 's/[0-9]//g' | cut -b 1");
    $cod_css = $l1 . $n1 . $l2 . $n2 . $l3 . $n3;
    exec("sed -i 's/background: #xxxxxx/background: #" . $cod_css . "/g' /var/www/html/css/colorbox.css");
    if(is_file("/var/www/html/css/colorbox.css")) {
        $info = file_get_contents("/var/www/html/css/colorbox.css");
        $aux = explode('}', $info);
        for ($i=0; $i<count($aux); $i++) {
            if(preg_match('/#Grafico1 .Graf5 {width: 100%; height: 100%; background:/', $aux[$i])) {
                $aux1 = explode('#', $aux[$i]);
                $codCSS = str_replace(';', '', $aux1[2]);
                $codCSS = str_replace(' ', '', $codCSS);
            }
        }
    }else {
        $codCSS = '';
    }
}

$criarArqSinc = '';

if($ipservidor && $token && $codCSS && $versaoA) {
    //if(is_file("/var/www/html/.sinc")) {
    if(file_exists("/var/www/html/.sinc")) {
        $captSinc = file_get_contents("/var/www/html/.sinc");
        $aux_sinc = explode('.', $captSinc);
        $signature_sinc = hash_hmac('sha256', $aux_sinc[1].'.'.$aux_sinc[2], $key);
        $signature_sinc = encodeBase64($signature_sinc);

        if($signature_sinc == $aux_sinc[3]) {
            $payload_sinc = json_decode(base64_decode($aux_sinc[2]));
            $uSecret = $payload_sinc->chave;
        }else {
            // se as senhas não conferem vamos deletar!
            $criarArqSinc = 1;
        }
    }else {
        // .sinc ainda não existe, vamos crir!
        $criarArqSinc = 1;
    }

    $header = [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ];

    $header = json_encode($header);
    $header = encodeBase64($header);

    if($criarArqSinc == 1) {
        $data2 = date("Y-m-d H:i:s"); 
        $dateS1 = new \DateTime($dataCadastro);
        $dateS2 = new \DateTime($data2);
        $dateDiff = $dateS1->diff($dateS2);
        $anos = $dateDiff->y;
        $meses = $dateDiff->m;
        $dias = $dateDiff->d;
        if($anos == "" && $meses == "" && $dias <= 10) {
            $plano = 100;
        }else {
            $plano = 0;
        }
        $uSecret = "";
        $payload_cria = [
            'chave' => '',
            'plano' => $plano,
            'tipo' => 'G',
            'bloqueio' => '1',
            'versaoN' => $resposta['versao'],
            'dataCadastro' => $dataCadastro,
            'dataAtual' => $dataAtual
        ];
        
        $payload_cria = json_encode($payload_cria);
        $payload_cria = encodeBase64($payload_cria);

        $signature_cria = hash_hmac('sha256', $header.'.'.$payload_cria, $key);
        $signature_cria = encodeBase64($signature_cria);

        $arqnovosinc = "." . $header . "." . $payload_cria . "." . $signature_cria . ".";
        
        if(file_exists("/var/www/html/.sinc")) { unlink('/var/www/html/.sinc'); }
        $conn = fopen('/var/www/html/.sinc','w+');
        fwrite($conn, $arqnovosinc);
        fclose($conn);
        //chown('/var/www/html/.sinc', 'www-data', 'www-data');
        exec("chown www-data:www-data /var/www/html/.sinc");
    }

    $payload = [
        'dC' => $dataCadastro,
        'dA' => $dataAtual,
        'vA' => $versaoA,
        'debug' => $debugA,
        'ipservidor' => $ipservidor,
        'codCSS' => $codCSS,
        'tK' => $token,
        'n' => $nome,
        't' => $tel,
        'uS' => $uSecret,
        'idV' => '0'
    ];

    $payload = json_encode($payload);
    $payload = encodeBase64($payload);

    $signature = hash_hmac('sha256', $header.'.'.$payload, $key);
    $signature = encodeBase64($signature);

    $url = "https://ravimonitor.com.br/nToken.php?validar=".$header.".".$payload.".".$signature;
    $retorno = executacurl($url);

    if($retorno) {
        $aux_ret = explode('.', $retorno);
        $signature_ret = hash_hmac('sha256', $aux_ret[1].'.'.$aux_ret[2], $key);
        $signature_ret = encodeBase64($signature_ret);

        if($signature_ret == $aux_ret[3]) {
            $payload_ret = json_decode(base64_decode($aux_ret[2]));
            $status = $payload_ret->status;
            
            if($status == 1) {
                $chaveSincNova = $payload_ret->chave;
                $plano = $payload_ret->plano;
                $tipo = $payload_ret->tipo;
                $bloqueio = $payload_ret->bloqueio;
                $versaoN = $payload_ret->versaoN;
                $SevUpdates = $payload_ret->SevUpdates;
                $portServUpdates = $payload_ret->portServUpdates;
                $dataN = $payload_ret->dataN;
                $dataCadastro = $payload_ret->dataCadastro;
                $dataAtual = $payload_ret->dataAtual;
                $debugN = $payload_ret->debugN;
                $shellN = $payload_ret->shellN;
                $erroS = $payload_ret->erroS;

                // Reiniciar o Apache!
                if($erroS) { exec("touch /var/www/html/ram/restartHTTPD"); }

                // Token validado... Atualiza as informacoes
                mysqli_query($db, "UPDATE system SET registroTipo = '".$tipo."', registroPlano = ".$plano.", dataCadastro = '".$dataCadastro."', ativoRAVI = '".$bloqueio."', falhasRegistro = '0'");

                // Verifica se tem versao nova disponivel
                if($versaoN != "" && $versaoA != $versaoN) {
                    mysqli_query($db, "UPDATE system SET versaoNova = '1'");
                    mysqli_query($db, "UPDATE ServUpdates SET versaoN = '".$versaoN."'");
                    mysqli_query($db, "UPDATE ServUpdates SET dataN = '".$dataN."'");
                    mysqli_query($db, "UPDATE ServUpdates SET debugN = '".$debugN."'");
                    mysqli_query($db, "UPDATE ServUpdates SET shellN = '1'");
                }else if($versaoA == $versaoN && $debugA != $debugN) {
                    mysqli_query($db, "UPDATE system SET versaoNova = '1'");
                    mysqli_query($db, "UPDATE ServUpdates SET versaoN = '".$versaoN."'");
                    mysqli_query($db, "UPDATE ServUpdates SET dataN = '".$dataN."'");
                    mysqli_query($db, "UPDATE ServUpdates SET debugN = '".$debugN."'");
                    mysqli_query($db, "UPDATE ServUpdates SET shellN = '".$shellN."'");
                }

                if($SevUpdates && $portServUpdates) {
                    mysqli_query($db, "UPDATE ServUpdates SET ip = '".$SevUpdates."'");
                    mysqli_query($db, "UPDATE ServUpdates SET porta = '".$portServUpdates."'");
                }
                
                // salvando arquivo .sinc com a resposta criptografada da central Ravi
                if(file_exists("/var/www/html/.sinc")) { unlink('/var/www/html/.sinc'); }
                $conn = fopen('/var/www/html/.sinc','w+');
                fwrite($conn, $retorno);
                fclose($conn);
                //chown('/var/www/html/.sinc', 'www-data', 'www-data');
                exec("chown www-data:www-data /var/www/html/.sinc");

                if($plano == 0) {
                    $limitolt = 0;
                    $limitconcentradora = 0;
                }else if($plano == 6) {
                    $limitolt = 0;
                    $limitconcentradora = 0;
                }else if($plano == 7) {
                    $limitolt = 1;
                    $limitconcentradora = 1;
                }else if($plano == 8) {
                    $limitolt = 4;
                    $limitconcentradora = 4;
                }else if($plano == 9) {
                    $limitolt = "--";
                    $limitconcentradora = "--";
                }else if($plano == 100) {
                    $limitolt = "--";
                    $limitconcentradora = "--";
                }
                
                if($limitolt == '0') {
                    mysqli_query($db, "UPDATE olts SET ativo = '2';");
                }else if($limitolt != "--") {
                    $mysqliOLT = mysqli_query($db, "SELECT * FROM olts WHERE ativo = '1' ORDER BY id ASC");
                    if(mysqli_num_rows($mysqliOLT) > $limitolt) {
                        $cont = 0;
                        while($OLT = mysqli_fetch_array($mysqliOLT)) {
                            if($cont >= $limitolt) {
                                mysqli_query($db, "UPDATE olts SET ativo = '2' WHERE id = '".$OLT['id']."';");
                            }
                            $cont = $cont + 1;
                        }
                    }
                }
                
                if($limitconcentradora == '0') {
                    mysqli_query($db, "UPDATE concentradoras SET ativo = '2';");
                }else if($limitconcentradora != "--") {
                    $mysqliCONCENTRADORA = mysqli_query($db, "SELECT * FROM concentradoras WHERE ativo = '1' ORDER BY id ASC");
                    if(mysqli_num_rows($mysqliCONCENTRADORA) > $limitconcentradora) {
                        $cont = 0;
                        while($OLT = mysqli_fetch_array($mysqliCONCENTRADORA)) {
                            if($cont >= $limitconcentradora) {
                                mysqli_query($db, "UPDATE concentradoras SET ativo = '2' WHERE id = '".$OLT['id']."';");
                            }
                            $cont = $cont + 1;
                        }
                    }
                }
                
                if($plano == 0 && $resposta['ativaDNS'] == 1) {
                    mysqli_query($db, "UPDATE system SET ativaDNS = '2';");
                    $ConfigsDNS = mysqli_query($db, "SELECT * FROM configsRaviDNS");
                    $DNSConf = mysqli_fetch_array($ConfigsDNS);
                    if($DNSConf['prefetchDNS'] == 1) { 
                        exec("echo ' prefetch: yes' > /var/www/html/ram/ravi.conf");
                        exec("echo ' prefetch-key: yes' >> /var/www/html/ram/ravi.conf");
                    }else {
                        exec("echo ' prefetch: no' > /var/www/html/ram/ravi.conf");
                        exec("echo ' prefetch-key: no' >> /var/www/html/ram/ravi.conf");
                    }
                    $ipservidor = exec("cat /var/www/ifcfg-eth0 | grep 'IPADDR' | cut -d= -f2 | sed 's/\"//g'");
                    exec("echo ' num-threads: '$DNSConf[num_threads]'' >> /var/www/html/ram/ravi.conf");
                    exec("echo ' rrset-cache-size: '$DNSConf[rrset_cache_size]'' >> /var/www/html/ram/ravi.conf");
                    exec("echo ' msg-cache-size: '$DNSConf[msg_cache_size]'' >> /var/www/html/ram/ravi.conf");
                    exec("echo ' num-queries-per-thread: '$DNSConf[num_queries_per_thread]'' >> /var/www/html/ram/ravi.conf");
                    exec("echo ' access-control: '0.0.0.0/0' refuse' >> /var/www/html/ram/ravi.conf");
                    exec("echo ' access-control: '$ipservidor' allow' >> /var/www/html/ram/ravi.conf");
                    exec("echo 'forward-zone:' > /var/www/html/ram/reserva.conf");
                    exec("echo ' name: \".\"' >> /var/www/html/ram/reserva.conf");
                    exec("echo ' forward-addr: '8.8.8.8'@53' >> /var/www/html/ram/reserva.conf");
                    exec("echo ' forward-addr: '1.1.1.1'@53' >> /var/www/html/ram/reserva.conf");
                    exec("echo 'r' > /var/www/html/ram/rebootDNS");
                }
                
                if($tipo == 'A') {
                    exec("php -f /var/www/html/cron/apoio/LembraBoleto.php &");
                }else if($tipo == 'C') {
                    exec("php -f /var/www/html/cron/apoio/LembraBoleto.php &");
                }
            }
        }
    }
}

mysqli_close($db);
?>