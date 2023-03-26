<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

// Obtém o ID do usuário e do grupo do usuário nginx
$user_info = posix_getpwnam('www-data');
$user_id = $user_info['uid'];
$group_id = $user_info['gid'];

// Altera o usuário e o grupo do processo atual para o usuário www-data 'Nginx'
posix_setgid($group_id);
posix_setuid($user_id);

include("/var/www/html/functions/icmp.php");
include("/var/www/html/functions/utils.php");
include("/var/www/html/cron/apoio/conexao.php");

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

$host = '177.105.241.2';
$community = 'mwfgrsuporte1';
$porta = 161;
$vsnmp = 2;
$nivelsegsnmp = '';
$protocoloauthsnmp = '';
$protocolocripsnmp = '';
$authsnmp = '';
$criptosnmp = '';

$retries = 1;
$timeout = 5;

if($vsnmp == 1) {
    $comando1 = "snmpwalk -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.4.1.9.9.150.1.1.3.1.2";
    $comando2 = "snmpwalk -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.2.1.2.2.1.2";
    $comando3 = "snmpwalk -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.4.1.14988.1.1.2.1.1.2";
}else if($vsnmp == 2) {
    $comando1 = "snmpwalk -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.4.1.9.9.150.1.1.3.1.2";
    $comando2 = "snmpwalk -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.2.1.2.2.1.2";
    $comando3 = "snmpwalk -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " .1.3.6.1.4.1.14988.1.1.2.1.1.2";
}else if($vsnmp == 3) {
    $comando1 = "snmpwalk -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " .1.3.6.1.4.1.9.9.150.1.1.3.1.2";
    $comando2 = "snmpwalk -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " .1.3.6.1.2.1.2.2.1.2";
    $comando3 = "snmpwalk -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " .1.3.6.1.4.1.14988.1.1.2.1.1.2";
}

$stdno = 0;
exec($comando1, $busca_ppp1, $stdno);
$stdno = (int)$stdno;

if(!$stdno) {
    $stdno = 0;
    exec($comando2, $busca_ppp2, $stdno);
    $stdno = (int)$stdno;

    $stdno = 0;
    exec($comando3, $busca_ppp3, $stdno);
    $stdno = (int)$stdno;
}

if(!$stdno) {
    $users = array();
    for ($a=0; $a<count($busca_ppp1); $a++) {
        $index1 = preg_match('/^iso\..*\.(.*) = .*$/', $busca_ppp1[$a], $matches) ? $matches[1] : null;
        $user_ppp1 = preg_match('/^.*: "(.*)"$/', $busca_ppp1[$a], $matches) ? $matches[1] : null;
        $users[$user_ppp1][0] = $index1;
    }

    for ($b=0; $b<count($busca_ppp2); $b++) {
        if(preg_match('/<pppoe-/',  $busca_ppp2[$b])) {
            $index2 = preg_match('/^iso\..*\.(.*) = .*$/', $busca_ppp2[$b], $matches) ? $matches[1] : null;
            $user_ppp2 = preg_replace('/<pppoe-|>/', '', preg_match('/^.*: "(.*)"$/', $busca_ppp2[$b], $matches) ? $matches[1] : null);
            $users[$user_ppp2][1] = $index2;
        }
    }

    for ($c=0; $c<count($busca_ppp3); $c++) {
        if(preg_match('/<pppoe-/',  $busca_ppp3[$c])) {
            $index3 = preg_match('/^iso\..*\.(.*) = .*$/', $busca_ppp3[$c], $matches) ? $matches[1] : null;
            $user_ppp3 = preg_replace('/<pppoe-|>/', '', preg_match('/^.*: "(.*)"$/', $busca_ppp3[$c], $matches) ? $matches[1] : null);
            $users[$user_ppp3][2] = $index3;
        }
    }

    if($vsnmp == 1) {
        $cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.1.3.0 2>/dev/null";
    }else if($vsnmp == 2) {
        $cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.1.3.0 2>/dev/null";
    }else if($vsnmp == 3) {
        $cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " 1.3.6.1.2.1.1.3.0 2>/dev/null";
    }

    $stdno = 0;
    exec ($cmd, $sysUpTimeInstance, $stdno);
    $stdno = (int)$stdno;

    if(!$stdno) {
        $processa = explode(' = ', $sysUpTimeInstance[0]);
        $sys_uptime = $processa[1];
        echo "sys_uptime: <br>" . $sys_uptime . "<br><br>";
    } else {
        $sys_uptime = '';
    }

    // $value[0] = índice da tabela PPPoE
    // $value[1] = índice da interface virtual gerada (nela que pegamos o uptime)
    // $value[2] = índice da tabela Queue

    foreach ($users as $user => $value) {
        $oids = array();
        $oids[] = ".1.3.6.1.4.1.9.9.150.1.1.3.1.3." . $value[0];      // IpAddress
        $oids[] = ".1.3.6.1.2.1.2.2.1.9." . $value[1];                // ifLastChange
        if(isset($value[2])) { 
            $oids[] = ".1.3.6.1.4.1.14988.1.1.2.1.1.8." . $value[2];  // mtxrQueueSimpleBytesIn
            $oids[] = ".1.3.6.1.4.1.14988.1.1.2.1.1.9." . $value[2];  // mtxrQueueSimpleBytesOut
        }

        $oidspart = implode(' ', $oids);

        if($vsnmp == 1) {
            $cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
        }else if($vsnmp == 2) {
            $cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
        }else if($vsnmp == 3) {
            $cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
        }

        $stdno = 0;
        $analysis = array();
        exec ($cmd, $analysis, $stdno);
        $stdno = (int)$stdno;

        if(!$stdno) {
            $process_analysis = array();
            for($d=0;$d<count($analysis);$d++) {
                $processa = explode(' = ', $analysis[$d]);
                if(preg_match('/iso.3.6.1.4.1.9.9.150.1.1.3.1.3./', $processa[0])) {
                    $users[$user]['ip'] = sanitizeSNMP($processa[1]);
                } else if(preg_match('/iso.3.6.1.2.1.2.2.1.9./', $processa[0])) {
                    $uptime_seconds = timeticks_to_seconds(sanitizeSNMP($processa[1]));
                    $users[$user]['uptime'] = human_time($uptime_seconds);
                    //$users[$user]['uptime2'] = $uptime_seconds;
                    // Calcula a data e hora exatas da última mudança do estado do link da interface
                    $last_change_time = time() - ($sys_uptime / 100); // converte sysUpTime em segundos
                    $last_change_time = $last_change_time - ($uptime_seconds / 100); // subtrai ifLastChange em segundos
                    $last_change_date = date('Y-m-d H:i:s', $last_change_time); // converte em formato de data e hora
                    $users[$user]['dataconect'] = $last_change_date;
                } else if(preg_match('/iso.3.6.1.4.1.14988.1.1.2.1.1.8./', $processa[0])) {
                    $users[$user]['download'] = printBytes(sanitizeSNMP($processa[1]));
                } else if(preg_match('/iso.3.6.1.4.1.14988.1.1.2.1.1.9./', $processa[0])) {
                    $users[$user]['upload'] = printBytes(sanitizeSNMP($processa[1]));
                }
            }
        }
    }

    $ip_addresses = array_column($users, 'ip');

    $tamanho_pacote = 32; // Bytes
    $quantidade_pacotes = 10;
    $latencies = test_latency($ip_addresses, $tamanho_pacote, $quantidade_pacotes);

    foreach ($users as $user => $value) {

    }

    echo "<pre>";
    print_r($users);
    print_r($latencies);
    echo "</pre>";

} else {
    echo "Erro na conexão snmp";
}



?>