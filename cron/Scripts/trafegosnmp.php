#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$host = $_GET["ip"];
$index = $_GET["v"];
$valor1 = $_GET["v1"];
$banco = $_GET["banco"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$community = $_GET["snmp"];
$porta = $_GET["porta"];
$vsnmp = $_GET["vsnmp"];
$nivelsegsnmp = $_GET["nivelsegsnmp"];
$protocoloauthsnmp = $_GET["protocoloauthsnmp"];
$protocolocripsnmp = $_GET["protocolocripsnmp"];
$authsnmp = $_GET["authsnmp"];
$criptosnmp = $_GET["criptosnmp"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$media1 = $_GET["media1"];
$minPer = $_GET["minPer"];
$alertar = $_GET["alertar"];
$ifSpeedd = $_GET["speed"];
$data = ''.$data.' '.$hora.'';
$sleep = 10;
$retries = 5;
$timeout = 30;

if(!$falhas) { $falhas = 1; }
if(!$porta) { $porta = 161; }
//if(!$retries) { $retries = 3; }
//if(!$timeout) { $timeout = 10; }
//$problema = 0;

function sanitizeSNMP($string) {
    $what = array( 'Counter64: ', 'Counter32: ', 'INTEGER: ', 'Counter32: ', 'STRING: ', 'STRING:', 'Gauge32: ', ' No Such Object available on this agent at this OID', 'No Such Object available on this agent at this OID', 'Hex-', 'Hex-STRING: ', '"' );
    $by   = array( '', '', '', '', '', '', '', '', '', '', '', '' );
    return str_replace($what, $by, $string);
}

function gravidade($alertar, $StErro, $falhas, $alerta, $critico) {
    if($StErro >= $falhas) {
        if($alertar == 1) {
            $statusAlert = $alerta;
        }else {
            $statusAlert = $critico;
        }
    }else {
        $StErro = $StErro + 1;
        $statusAlert = $alerta;
    }
    return array($statusAlert, $StErro);
}

function trafego($id, $host, $community, $vsnmp, $porta, $index, $media1, $banco, $StErro, $falhas, $minPer, $alertar, $ifSpeedd, $retries, $timeout) {
    // Preparando valores de tráfego minimo e máximo aceitável
    $divide = explode('-', $banco);
    if($divide[4]) {
        $mintipo = $divide[3];
        $minv = $divide[4];
        $minunidade = $divide[5];
    }else {
        $mintipo = '';
        $minv = '';
        $minunidade = '';
    }
    if(!$divide[1] && !$divide[2]) {
        $maxtipo = "total";
        $maxv = $banco;
        $maxunidade = "kbps";
    }else {
        $maxtipo = $divide[0];
        $maxv = $divide[1];
        $maxunidade = $divide[2];
    }
    if($minunidade == "mbps") {
        $minv = $minv * 1024;
    }else if($minunidade == "gbps") {
        $minv = ($minv * 1024) * 1024;
    }
    if($maxunidade == "mbps") {
        $maxv = $maxv * 1024;
    }else if($maxunidade == "gbps") {
        $maxv = ($maxv * 1024) * 1024;
    }

    $oids1 = array();
    $oids2 = array();
    $oids3 = array();
    $oidsCollect = array();
    $oids1[] = ".1.3.6.1.2.1.2.2.1.8." . $index;                   // ifOperStatus
    $oids3[] = ".1.3.6.1.2.1.2.2.1.2." . $index;                   // ifDescr
    $oids3[] = ".1.3.6.1.2.1.31.1.1.1.18." . $index;               // ifAlias
    $oids3[] = ".1.3.6.1.2.1.2.2.1.6." . $index;                   // ifPhysAddress
    //$oids2[] = ".1.3.6.1.2.1.2.2.1.5." . $index;                 // ifSpeed
    $oids2[] = ".1.3.6.1.2.1.31.1.1.1.15." . $index;               // ifHighSpeed
    
    $oidsCollect[] = ".1.3.6.1.2.1.2.2.1.10." . $index;            // ifInOctets
    $oidsCollect[] = ".1.3.6.1.2.1.2.2.1.16." . $index;            // ifOutOctets
    if($vsnmp != 1) {
        $oidsCollect[] = ".1.3.6.1.2.1.31.1.1.1.6." . $index;      // ifHCInOctets
        $oidsCollect[] = ".1.3.6.1.2.1.31.1.1.1.10." . $index;     // ifHCOutOctets
    }
    //$oidsCollect[] = ".1.3.6.1.2.1.1.3.0";                         // sysUpTime

    $oidspart1 = implode(' ', $oids1);
    $oidspart2 = implode(' ', $oids2);
    $oidspart3 = implode(' ', $oids3);
    $oidspart4 = implode(' ', $oidsCollect);

    if($vsnmp == 1) {
        $cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
        $cmd2 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart2 . " 2>/dev/null";
        $cmd3 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart3 . " 2>/dev/null";
    }else if($vsnmp == 2) {
        $cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
        $cmd2 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart2 . " 2>/dev/null";
        $cmd3 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart3 . " 2>/dev/null";
    }else if($vsnmp == 3) {
        $cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
        $cmd2 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart2 . " 2>/dev/null";
        $cmd3 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart3 . " 2>/dev/null";
    }

    $stdno = 0;
    $analysis1 = array();
    exec ($cmd1, $analysis1, $stdno);
    $stdno = (int)$stdno;

    // Verifica se a conexão SNMP funcionou
    if(!$stdno) {
        $a = explode('= ', $analysis1[0]);
        $status = sanitizeSNMP($a[1]);
        $analysis2 = array();
        exec ($cmd2, $analysis2);
        $b = explode('= ', $analysis2[0]);
        $ifHighSpeed = sanitizeSNMP($b[1]);
        /*if($vsnmp != 1) {
            $c = explode('= ', $analysis2[1]);
            $ifHighSpeed = sanitizeSNMP($c[1]);
            if(!$ifHighSpeed) { $ifHighSpeed = $ifSpeed; }
        }else {
            $ifHighSpeed = $ifSpeed;
        }*/
        $analysis3 = array();
        exec ($cmd3, $analysis3);
        if(count($analysis3) == 3) {
            $d = explode('= ', $analysis3[0]);
            $ifDescr = sanitizeSNMP($d[1]);
            $e = explode('= ', $analysis3[1]);
            $ifAlias = sanitizeSNMP($e[1]);
            $f = explode('= ', $analysis3[2]);
            $ifPhysAddress = sanitizeSNMP($f[1]);
        }else {
            $d = explode('= ', $analysis3[0]);
            $e = explode('= ', $analysis3[1]);
            $f = explode('= ', $analysis3[2]);
            if(preg_match('/ifDescr./', $d[0])) { $ifDescr = sanitizeSNMP($d[1]); }
            if(preg_match('/ifAlias./', $d[0])) { $ifAlias = sanitizeSNMP($d[1]); }
            if(preg_match('/ifPhysAddress./', $d[0])) { $ifPhysAddress = sanitizeSNMP($d[1]); }
            if(preg_match('/ifDescr./', $e[0])) { $ifDescr = sanitizeSNMP($e[1]); }
            if(preg_match('/ifAlias./', $e[0])) { $ifAlias = sanitizeSNMP($e[1]); }
            if(preg_match('/ifPhysAddress./', $e[0])) { $ifPhysAddress = sanitizeSNMP($e[1]); }
            if(preg_match('/ifDescr./', $f[0])) { $ifDescr = sanitizeSNMP($f[1]); }
            if(preg_match('/ifAlias./', $f[0])) { $ifAlias = sanitizeSNMP($f[1]); }
            if(preg_match('/ifPhysAddress./', $f[0])) { $ifPhysAddress = sanitizeSNMP($f[1]); }
        }

        // Verifica se a interface está operante
        if($status == 1 || $status == 6) {
            // Verificando se tem informações em cache que ainda podem ser aproveitadas
            if(file_exists("/var/www/html/ram/coletas/trafegoSNMP/" . $id)) {
                $info = file_get_contents("/var/www/html/ram/coletas/trafegoSNMP/" . $id);
                $aux = explode('|', $info);
                $down_1_32b = $aux['1'];
                $up_1_32b = $aux['2'];
                $down_1 = $aux['3'];
                $up_1 = $aux['4'];
                $t1 = $aux['5'];
                //$uptime_1 = $aux['6'];
                $t2 = microtime(true) * 100;
                $valida_cache = 0;
                $data_uptime = (intval(round($t2 - $t1, 2))) / 100;
                if(($down_1_32b && $up_1_32b) || ($down_1 && $up_1) && $t1 && $data_uptime <= 65) { $valida_cache = 1; }
            }
            // Sem informações válidas em cache!
            if(!$valida_cache) {
                $coleta1 = array();
                if($vsnmp == 1) {
                    exec("snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart4 . " | cut -d '=' -f2", $coleta1);
                    $t1 = microtime(true) * 100;
                }else if($vsnmp == 2) {
                    exec("snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart4 . " | cut -d '=' -f2", $coleta1);
                    $t1 = microtime(true) * 100;
                }else if($vsnmp == 3) {
                    exec("snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart4 . " | cut -d '=' -f2", $coleta1);
                    $t1 = microtime(true) * 100;
                }
                $down_1_32b = (int) sanitizeSNMP($coleta1[0]);
                $up_1_32b = (int) sanitizeSNMP($coleta1[1]);
                $down_1 = (int) sanitizeSNMP($coleta1[2]);
                $up_1 = (int) sanitizeSNMP($coleta1[3]);
                //$uptime_1 = (int) sanitizeSNMP($coleta1[4]);
                sleep($sleep);
            }
            $coleta2 = array();
            if($vsnmp == 1) {
                exec("snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart4 . " | cut -d '=' -f2", $coleta2);
                $t2 = microtime(true) * 100;
            }else if($vsnmp == 2) {
                exec("snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart4 . " | cut -d '=' -f2", $coleta2);
                $t2 = microtime(true) * 100;
            }else if($vsnmp == 3) {
                exec("snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart4 . " | cut -d '=' -f2", $coleta2);
                $t2 = microtime(true) * 100;
            }
            $down_2_32b = (int) sanitizeSNMP($coleta2[0]);
            $up_2_32b = (int) sanitizeSNMP($coleta2[1]);
            $down_2 = (int) sanitizeSNMP($coleta2[2]);
            $up_2 = (int) sanitizeSNMP($coleta2[3]);
            //$uptime_2 = (int) sanitizeSNMP($coleta2[4]);

            // Valida as informações para garantir que são válidas uma vez que a interface pode ter reiniciado no intervalo das coletas
            $valido = 0;
            if($down_2 >= $down_1 && $up_2 >= $up_1) {
                $valido = 1;
            }else if($down_2_32b >= $down_1_32b && $up_2_32b >= $up_1_32b) {
                $valido = 1;
            }else {
                // Informações em cache não foram validadas!
                $down_1_32b = $down_2_32b;
                $up_1_32b = $up_2_32b;
                $down_1 = $down_2;
                $up_1 = $up_2;
                $t1 = $t2;
                //$uptime_1 = $uptime_2;
                sleep($sleep);
                $coleta2 = array();
                if($vsnmp == 1) {
                    exec("snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart4 . " | cut -d '=' -f2", $coleta2);
                    $t2 = microtime(true) * 100;
                }else if($vsnmp == 2) {
                    exec("snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart4 . " | cut -d '=' -f2", $coleta2);
                    $t2 = microtime(true) * 100;
                }else if($vsnmp == 3) {
                    exec("snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart4 . " | cut -d '=' -f2", $coleta2);
                    $t2 = microtime(true) * 100;
                }
                $down_2_32b = (int) sanitizeSNMP($coleta2[0]);
                $up_2_32b = (int) sanitizeSNMP($coleta2[1]);
                $down_2 = (int) sanitizeSNMP($coleta2[2]);
                $up_2 = (int) sanitizeSNMP($coleta2[3]);
                //$uptime_2 = (int) sanitizeSNMP($coleta2[4]);

                if($down_2 >= $down_1 && $up_2 >= $up_1) {
                    $valido = 1;
                }else if($down_2_32b >= $down_1_32b && $up_2_32b >= $up_1_32b) {
                    $valido = 1;
                }
            }

            if($valido) {
                // Armazenando em cache as últimas informações
                exec("echo '|$down_2_32b|$up_2_32b|$down_2|$up_2|$t2|' > /var/www/html/ram/coletas/trafegoSNMP/$id");

                // Verificando se é melhor utilizarmos o dalay entre as verificações com base no timestam ou uptime do dispositivo
                /*$delay1 = (intval(round($t2 - $t1, 2))) / 100;
                $delay2 = round(($uptime_2 - $uptime_1) / 1000000, 2);
                if($delay2 && $delay2 <= ($sleep + 2)) {
                    $percentual = (($delay1 / $delay2) - 1 ) * 100;
                    if($percentual <= 50) { $delay = $delay2; }else { $delay = $delay1; }
                }else {
                    $delay = $delay1;
                }*/

                $delay = (intval(round($t2 - $t1, 2))) / 100;

                $Down = intval(((((($down_2 - $down_1) / $delay) * 8) / 1024)));
                $Up = intval(((((($up_2 - $up_1) / $delay) * 8) / 1024)));
                $valDown32b = intval(((((($down_2_32b - $down_1_32b) / $delay) * 8) / 1024)));
                $valUp32b = intval(((((($up_2_32b - $up_1_32b) / $delay) * 8) / 1024)));

                if(!$Down && !$Up && ($valDown32b && $valUp32b)) {
                    $Down = $valDown32b;
                    $Up = $valUp32b;
                }
                $total = $Down + $Up;
                $statusAlert = 6;

                // Verifica se a velocidade de negociação da interface não caiu
                if($ifSpeedd && $ifHighSpeed && $ifHighSpeed < $ifSpeedd) {
                    $busca = gravidade(2, $StErro, $falhas, 13, 14);
                    $statusAlert = $busca[0];
                    $StErro = $busca[1];
                }else {
                    // Verifica se o mínimo ou máximo aceitável não foi atingido
                    if($minv) {
                        if($mintipo == "total" && $minv > $total) {
                            $busca = gravidade(2, $StErro, $falhas, 9, 10);
                            $statusAlert = $busca[0];
                            $StErro = $busca[1];
                        }else if($mintipo == "down" && $minv > $Down) {
                            $busca = gravidade(2, $StErro, $falhas, 9, 10);
                            $statusAlert = $busca[0];
                            $StErro = $busca[1];
                        }else if($mintipo == "up" && $minv > $Up) {
                            $busca = gravidade(2, $StErro, $falhas, 9, 10);
                            $statusAlert = $busca[0];
                            $StErro = $busca[1];
                        }
                    }
                    if($maxv) {
                        if($maxtipo == "total" && $maxv < $total) {
                            $busca = gravidade(2, $StErro, $falhas, 9, 10);
                            $statusAlert = $busca[0];
                            $StErro = $busca[1];
                        }else if($maxtipo == "down" && $maxv < $Down) {
                            $busca = gravidade(2, $StErro, $falhas, 9, 10);
                            $statusAlert = $busca[0];
                            $StErro = $busca[1];
                        }else if($maxtipo == "up" && $maxv < $Up) {
                            $busca = gravidade(2, $StErro, $falhas, 9, 10);
                            $statusAlert = $busca[0];
                            $StErro = $busca[1];
                        }
                    }
                }
                // Verifica se o tráfego é ou não incomum para o horário levando em consideração a média
                if($media1 >= 30720 && $statusAlert == 6 && $minPer >= 1) {
                    $maxima = $media1 - ($media1 / 100 * $minPer);
                    $media = $maxima + ($maxima / 100 * 10);
                    if($total < $maxima) {
                        $busca = gravidade($alertar, $StErro, $falhas, 3, 4);
                        $statusAlert = $busca[0];
                        $StErro = $busca[1];
                    }else if($total > $maxima && $total <= $media) {
                        $statusAlert = 3;
                    }
                }
            }else {
                // Falha na coleta SNMP!
                $Down = 0; $Up = 0; $total = 0;
                $statusAlert = 7;
            }
        }else {
            // Interface inoperante!
            $Down = 0; $Up = 0; $total = 0;
            $busca = gravidade(2, $StErro, $falhas, 11, 12);
            $statusAlert = $busca[0];
            $StErro = $busca[1];
        }
    }else {
        // Falha na coleta SNMP!
        $Down = 0; $Up = 0; $total = 0;
        $statusAlert = 7;
    }

    if ($statusAlert == 6 && $StErro != 1) { $StErro = 1; }
    return array($Down, $Up, $total, $statusAlert, $StErro, $ifHighSpeed, $ifDescr, $ifAlias, $ifPhysAddress);
}

$trafego = trafego($id, $host, $community, $vsnmp, $porta, $index, $media1, $banco, $StErro, $falhas, $minPer, $alertar, $ifSpeedd, $retries, $timeout);

if($valor1 > 5120 || $trafego['3'] == 1 || $trafego['3'] == 3 || $trafego['3'] == 7 || $trafego['3'] == 9 || $trafego['3'] == 11 || $trafego['3'] == 12 || $trafego['3'] == 13 || ($valor1 > 128 && !$trafego['2'])) {
    //echo "entrou 1 <br><br><pre>"; print_r($trafego); echo "</pre><br><br>";
    $Percent = ( $trafego[2] * 100 ) / $valor1;
    $Percent = number_format($Percent, 0);

    if($trafego['3'] == 1 || $trafego['3'] == 3 || $trafego['3'] == 7 || $trafego['3'] == 9 || $trafego['3'] == 11 || $trafego['3'] == 12 || $trafego['3'] == 13 || $Percent < 20 || $Percent > 180 || ($valor1 > 128 && !$trafego['2'])) {
        //echo "entrou 2 - Percent: " . $Percent . "<br><br><pre>"; print_r($trafego); echo "</pre><br><br>";
        sleep(5);
        $trafego = trafego($id, $host, $community, $vsnmp, $porta, $index, $media1, $banco, $StErro, $falhas, $minPer, $alertar, $ifSpeedd, $retries, $timeout);
        $Percent = ( $trafego[2] * 100 ) / $valor1;
        $Percent = number_format($Percent, 0);

        if($trafego['3'] == 1 || $trafego['3'] == 7 || $trafego['3'] == 11 || $trafego['3'] == 12 || $Percent < 20 || $Percent > 180 || ($valor1 > 128 && !$trafego['2'])) {
            //echo "entrou 3 - Percent: " . $Percent . "<br><br><pre>"; print_r($trafego); echo "</pre><br><br>";
            sleep(5);
            $trafego = trafego($id, $host, $community, $vsnmp, $porta, $index, $media1, $banco, $StErro, $falhas, $minPer, $alertar, $ifSpeedd, $retries, $timeout);
        }
    }
}

$Down = $trafego[0];
$Up = $trafego[1];
$total = $trafego[2];
$statusAlert = $trafego[3];
$StErro = $trafego[4];
$ifHighSpeed = $trafego[5];
$ifDescr = $trafego[6];
$ifAlias = $trafego[7];
$ifPhysAddress = $trafego[8];

//echo "d:" . $Down . " up:" . $Up . " t:" . $total . " sa:" . $statusAlert . " se:" . $StErro . "<br><br>";
//echo "ifDescr: " . $ifDescr . "<br>ifAlias: " . $ifAlias . "<br>mac: " . $ifPhysAddress . "<br><br>";
//echo $valor1 . "<br>" . $total;

// Se está tudo ok salvar as informações
if($statusAlert && isset($total) && isset($Down) && isset($Up) && $Down >= 0 && $Up >= 0) {
    $timearq = date("H-i-s");
	$arq = $id . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|erro|banco|
	exec("echo '|$data|$data1|$id|$total|$Down|$Up|$statusAlert|$StErro|$ifHighSpeed|$ifDescr|$ifAlias|$ifPhysAddress|' > /var/www/html/ram/coletas/valores/$arq");
    //echo "echo '|$data|$data1|$id|$total|$Down|$Up|$statusAlert|$StErro|$ifHighSpeed|$ifDescr|$ifAlias|$ifPhysAddress|' > /var/www/html/ram/coletas/valores/$arq<br>";
}

$valor1 = $total;
$valor2 = $Down;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12 || $statusAlert == 14) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>