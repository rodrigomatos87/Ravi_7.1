#!/usr/bin/php
<?php
putenv('PATH=' . getenv('PATH') . ':/usr/bin:/usr/local/bin');

exec('pkill loop');

// obtém o ID do usuário e do grupo do usuário nginx
$user_info = posix_getpwnam('www-data');
$user_id = $user_info['uid'];
$group_id = $user_info['gid'];

// altera o usuário e o grupo do processo atual para o usuário www-data
posix_setgid($group_id);
posix_setuid($user_id);

$log_file = '/var/log/ravi.log';

// Para garantir que o backup seja bem sucedido vamos matar todos os processos que possam estar usando o banco de dados
$patterns = array(
    'mariadb_ppoe.php',
    'mariadb_olt.php',
    'mariadb_dispositivos.php',
    'mariadb_manutencao.php',
    'ppoe.php',
    'execSensores.php',
    'AtualizaResumoGrupos.php'
);

$processes = array();
exec("ps aux | grep -E '" . implode('|', $patterns) . "' | grep -v grep", $processes);

// Mata os processos correspondentes aos processos listados
foreach ($processes as $process) {
    $columns = preg_split('/\s+/', $process);
    $pid = $columns[1];
    $cmd = $columns[10];
    if ($pid && $cmd) {
        $kill_cmd = "kill -9 " . intval($pid);
        exec($kill_cmd);
    }
}

exec('systemctl restart mariadb');
sleep(5); // espera 5 segundos para o serviço iniciar completamente
$status = shell_exec('systemctl status mariadb');
if (strpos($status, 'Active: active (running)') !== false) {
    include("/var/www/html/cron/apoio/conexao.php");

    // Bloqueando todas as tabelas
    mysqli_query($db, "FLUSH TABLES WITH READ LOCK");

    if(!is_dir("/var/www/html/bkpRavi")) { mkdir('/var/www/html/bkpRavi', 0777, true); }

    $ft_system = mysqli_query($db, "SELECT * FROM system;");
    $system = mysqli_fetch_array($ft_system);

    $arq = "Ravi_v" . $system['versao'] . "_" . date("Y-m-d_H-i") . '.sql';
    $connect = fopen('/var/www/html/bkpRavi/'.$arq, 'w+');

    fwrite($connect, "# ################################################ #\n#\n");
    fwrite($connect, "# Backup Ravi " . $system['versao'] . "\n");
    fwrite($connect, "# Token: " . $system['tokenRAVI'] . "\n");
    fwrite($connect, "# Date: ". date("Y-m-d H:i:s") . "\n#\n");
    fwrite($connect, "# ################################################ #\n");

    function backup($tabela) {
        $result = '';
        $result = "\ntruncate table ".$tabela.";\n";
        $busca = mysqli_query($GLOBALS['db'], "SELECT * FROM ".$tabela." LIMIT 1;");
        if(mysqli_num_rows($busca)) {
            $sel = mysqli_query($GLOBALS['db'], "SELECT group_concat(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$tabela."';");
            $reg = mysqli_fetch_array($sel);
            $dados = exec("mysqldump -h localhost -u root -p#H0gGLS3@XeaW702_i51z@yUlN# --quick --no-create-info --default-character-set=latin1 Ravi ".$tabela." | grep INSERT | sed 's/INSERT INTO `".$tabela."` //'");
            if(preg_match('/VALUES /', $dados)) {
                $exp = explode("),(", $dados);
                $num = count($exp) - 1;
                if($num) {
                    $v1 = str_replace('VALUES (', '', $exp[0]);
                    $v2 = str_replace(');', '', $exp[$num]);
                    $replace = array(0 => $v1, $num => $v2);
                    $exp = array_replace($exp, $replace);
                }else {
                    $v1 = str_replace('VALUES (', '', $exp[0]);
                    $v1 = str_replace(');', '', $v1);
                    $replace = array(0 => $v1);
                    $exp = array_replace($exp, $replace);
                }
                for ($i=0; $i<count($exp); $i++) {
                    $result .= "INSERT INTO `".$tabela."` (" . str_replace(',', ', ', $reg['0']) . ") VALUES (" . str_replace(',', ', ', $exp[$i]) . ");\n";
                }
            }else {
                $result = "erro";
            }
        }
        return $result;
    }

    function sincroniza_bkp($token, $arq) {
        $path = '/var/www/html/bkpRavi/'.$arq.'.gz';

        $dados = array(
            "host" => "ftp.ravimonitor.com.br",
            "usuario" => "backup@ravimonitor.com.br",
            "senha" => "Ale32236279"
        );

        $fconn = ftp_connect($dados["host"]);
        ftp_login($fconn, $dados["usuario"], $dados["senha"]);
        ftp_put($fconn, $token."--".$arq.".gz", $path, FTP_BINARY);

        // Enviando cópia do backup para o servidor Ravi
        $url = "https://ravimonitor.com.br/servidor_backup.php?token=".$token;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        function remove_utf8_bom($text) {
            $bom = pack('H*','EFBBBF');
            $text = preg_replace("/^$bom/", '', $text);
            return $text;
        }

        $json = json_decode(remove_utf8_bom($response));
        if($json->status == 1) {
            $ip = $json->ip;
            $porta = $json->porta;
            $user = $json->user;
            $senha = $json->senha;
            $connection = ssh2_connect($ip, $porta);
            
            if (ssh2_auth_password($connection, $user, $senha)) {
                ssh2_scp_send($connection, '/var/www/html/bkpRavi/'.$arq.'.gz', '/root/bkpCli_Debian/'.$token.'--'.$arq.'.gz', 0644);
            }
        }
    }

    if($system['versao']) {
        // Tabelas de backup obrigatório!
        $tabelas = array( 
            'GrupoMonitor', 
            'Dispositivos', 
            'Sensores',
            'accessControlDNS', 
            'redirectControlDNS', 
            'adicionalDNS', 
            'dnsAutoritativo', 
            'dnsAutoritativoAdicional', 
            'dnsReverso',
            'login', 
            'grupoUser', 
            'dispAuxRaiz', 
            'dispAux', 
            'userGrupoDispositivos', 
            'userGrupoConcentradora', 
            'userGrupoMonitor', 
            'userGrupoOlt',
            'concentradoras',
            'olts',
            'lineprofile', 
            'srvprofile', 
            'vlan',
            'models_auth_onu',
            'telegrampadrao', 
            'telegramdisp', 
            'telegramolt',
            'whats',
            'api_whats',
            'models_alerta',
            'log_auth_onu'
        );

        if($system['backupOLTs'] == 3) { array_push($tabelas, 'onus'); }

        if($system['backupHistoricos'] == 2) {
            array_push($tabelas, 'Log2h');
        }else if($system['backupHistoricos'] == 3) {
            array_push($tabelas, 'Log2h');
            array_push($tabelas, 'Log24h');
        }else if($system['backupHistoricos'] == 4) {
            array_push($tabelas, 'Log2h');
            array_push($tabelas, 'Log24h');
            array_push($tabelas, 'Log30d');
        }else if($system['backupHistoricos'] == 5) {
            array_push($tabelas, 'Log2h');
            array_push($tabelas, 'Log24h');
            array_push($tabelas, 'Log30d');
            array_push($tabelas, 'Log1a');
        }
        
        for($i=0;$i<count($tabelas);$i++) {
            $valor = backup($tabelas[$i]);
            if($valor && $valor != "erro") { 
                fwrite($connect, $valor); 
            }else if($valor == "erro") {
                $message = date('Y-m-d H:i:s') . " - Erro ao gerar backup da tabela " . $tabelas[$i];
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            }
        }
        
        $args = array();
        $args[] = "userpadrao = '" . $system['userpadrao'] . "'";
        $args[] = "senhapadrao = '" . $system['senhapadrao'] . "'";
        $args[] = "portapadrao = '" . $system['portapadrao'] . "'";
        $args[] = "snmppadrao = '" . $system['snmppadrao'] . "'";
        $args[] = "portasnmppadrao = '" . $system['portasnmppadrao'] . "'";
        $args[] = "versaosnmppadrao = '" . $system['versaosnmppadrao'] . "'";
        $args[] = "nivelsegsnmppadrao = '" . $system['nivelsegsnmppadrao'] . "'";
        $args[] = "protocoloauthsnmppadrao = '" . $system['protocoloauthsnmppadrao'] . "'";
        $args[] = "protocolocripsnmppadrao = '" . $system['protocolocripsnmppadrao'] . "'";
        $args[] = "authsnmppadrao = '" . $system['authsnmppadrao'] . "'";
        $args[] = "criptosnmppadrao = '" . $system['criptosnmppadrao'] . "'";
        $args[] = "SMTPtls = '" . $system['SMTPtls'] . "'";
        $args[] = "padraotrafegodisp = '" . $system['padraotrafegodisp'] . "'";
        $args[] = "padraotrafegocon = '" . $system['padraotrafegocon'] . "'";
        $args[] = "historicotrafegocon = '" . $system['historicotrafegocon'] . "'";
        $args[] = "userpadrao_olt = '" . $system['userpadrao_olt'] . "'";
        $args[] = "senhapadrao_olt = '" . $system['senhapadrao_olt'] . "'";
        $args[] = "portapadrao_olt = '" . $system['portapadrao_olt'] . "'";
        $args[] = "snmppadrao_olt = '" . $system['snmppadrao_olt'] . "'";
        $args[] = "portasnmppadrao_olt = '" . $system['portasnmppadrao_olt'] . "'";
        $args[] = "versaosnmppadrao_olt = '" . $system['versaosnmppadrao_olt'] . "'";
        $args[] = "snmppadrao_pppoe = '" . $system['snmppadrao_pppoe'] . "'";
        $args[] = "portasnmppadrao_pppoe = '" . $system['portasnmppadrao_pppoe'] . "'";
        $args[] = "versaosnmppadrao_pppoe = '" . $system['versaosnmppadrao_pppoe'] . "'";
        $args[] = "userpadrao_pppoe = '" . $system['userpadrao_pppoe'] . "'";
        $args[] = "senhapadrao_pppoe = '" . $system['senhapadrao_pppoe'] . "'";
        $args[] = "portapadrao_pppoe = '" . $system['portapadrao_pppoe'] . "'";
        $args[] = "ativaPing_pppoe = '" . $system['ativaPing_pppoe'] . "'";
        $args[] = "ativaLinkDown_pppoe = '" . $system['ativaLinkDown_pppoe'] . "'";
        $args[] = "tamanhopacotes_pppoe = '" . $system['tamanhopacotes_pppoe'] . "'";
        $args[] = "quantidadepacotes_pppoe = '" . $system['quantidadepacotes_pppoe'] . "'";
        $args[] = "diasoff_pppoe = '" . $system['diasoff_pppoe'] . "'";
        $args[] = "qtdpiores_pppoe = '" . $system['qtdpiores_pppoe'] . "'";
        $args[] = "qtdpiores_olt = '" . $system['qtdpiores_olt'] . "'";
        $args[] = "api_olt = '" . $system['api_olt'] . "'";
        $args[] = "api_dns = '" . $system['api_dns'] . "'";
        $args[] = "api_whats = '" . $system['api_whats'] . "'";
        $args[] = "dadoshistoricos_pppoe = '" . $system['dadoshistoricos_pppoe'] . "'";
        $args[] = "zoomsensor = '" . $system['zoomsensor'] . "'";
        $args[] = "mostraip = '" . $system['mostraip'] . "'";
        $args[] = "widthSensor = '" . $system['widthSensor'] . "'";
        $args[] = "alinhasensores = '" . $system['alinhasensores'] . "'";
        $args[] = "iconaddsensor = '" . $system['iconaddsensor'] . "'";
        $args[] = "ativaSMTP = '" . $system['ativaSMTP'] . "'";
        $args[] = "userSMTP = '" . $system['userSMTP'] . "'";
        $args[] = "senhaSMTP = '" . $system['senhaSMTP'] . "'";
        $args[] = "servidorSMTP = '" . $system['servidorSMTP'] . "'";
        $args[] = "portaSMTP = '" . $system['portaSMTP'] . "'";
        $args[] = "emailSMTP = '" . $system['emailSMTP'] . "'";
        $args[] = "prioridadeSMTP = '" . $system['prioridadeSMTP'] . "'";
        $args[] = "ativaTELEGRAM = '" . $system['ativaTELEGRAM'] . "'";
        $args[] = "ativaTELEGRAMdisp = '" . $system['ativaTELEGRAMdisp'] . "'";
        $args[] = "ativaTELEGRAMolt = '" . $system['ativaTELEGRAMolt'] . "'";
        $args[] = "ativaSOM = '" . $system['ativaSOM'] . "'";
        $args[] = "numSOM = '" . $system['numSOM'] . "'";
        $args[] = "telegramolt = '" . $system['telegramolt'] . "'";
        $args[] = "ativaWHATS = '" . $system['ativaWHATS'] . "'";
        $args[] = "prioridadewhats = '" . $system['prioridadewhats'] . "'";
        $args[] = "backupOLTs = '" . $system['backupOLTs'] . "'";
        $args[] = "ativaDNS = '" . $system['ativaDNS'] . "'";
        $args[] = "ip_config_avancado = '" . $system['ip_config_avancado'] . "'";
        $args[] = "ativaVPN = '" . $system['ativaVPN'] . "'";
        $args[] = "datavpn = '" . $system['datavpn'] . "'";
        $args[] = "ipvpn = '" . $system['ipvpn'] . "'";
        $args[] = "uservpn = '" . $system['uservpn'] . "'";
        $args[] = "senhavpn = '" . $system['senhavpn'] . "'";
        $args[] = "updateRede = '" . $system['updateRede'] . "'";
        $args[] = "atualizacaoauto = '" . $system['atualizacaoauto'] . "'";
        $args[] = "dataCadastro = '" . $system['dataCadastro'] . "'";
        $args[] = "ativaWHATSdisp = '" . $system['ativaWHATSdisp'] . "'";
        $args[] = "ativaWHATSolt = '" . $system['ativaWHATSolt'] . "'";
        $args[] = "prioridadewhatsdisp = '" . $system['prioridadewhatsdisp'] . "'";
        $args[] = "prioridadewhatsolt = '" . $system['prioridadewhatsolt'] . "'";
        $args[] = "timezone = '" . $system['timezone'] . "'";
        $args[] = "linguagem = '" . $system['linguagem'] . "'";
        
        fwrite($connect, "\nUPDATE `system` SET " . implode(', ', $args) . ";");
        fclose($connect);
        
        //exec("openssl enc -aes256 -salt -pbkdf2 -in /var/www/html/bkpRavi/$arq -out /var/www/html/bkpRavi/$arq.sql -pass pass:ovBrTp39ty39tnhqriuVy0O59t8hwwp@");
        //unlink('/var/www/html/bkpRavi/' . $arq);

        $arquivo_compactado = '/var/www/html/bkpRavi/'.$arq.'.gz';
        $arquivo_original = '/var/www/html/bkpRavi/'.$arq;
        // Obter conteúdo do arquivo CSV
        $csvcontent = file_get_contents($arquivo_original);
        // Compactar conteúdo
        $gzcontent = zlib_encode($csvcontent, ZLIB_ENCODING_GZIP, 9);
        // Salvar conteúdo compactado
        file_put_contents($arquivo_compactado, $gzcontent);
        // Excluindo o arquivo original
        unlink($arquivo_original);
        
        sincroniza_bkp($system['tokenRAVI'], $arq);

        echo "Backup $arq gerado com sucesso!";
    }else {
        $message = date('Y-m-d H:i:s') . ' - Não foi possível gerar backup do sistema! Erro 02';
        file_put_contents($log_file, $message . "\n", FILE_APPEND);
        echo "Erro ao gerar backup do sistema. Entre em contato com o suporte!";
    }

    // Desbloqueando as tabelas
    mysqli_query($db, "UNLOCK TABLES;");
}else {
    $message = date('Y-m-d H:i:s') . ' - Não foi possível gerar backup do sistema! Erro 01';
    file_put_contents($log_file, $message . "\n", FILE_APPEND);
    echo "Erro ao gerar backup do sistema. Entre em contato com o suporte!";
}

mysqli_close($db);
?>