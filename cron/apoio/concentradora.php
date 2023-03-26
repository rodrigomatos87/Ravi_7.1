<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

$idConc = $_GET["id"];
$host = $_GET["ip"];
$comunidade = $_GET['snmp'];
$vsnmp = $_GET["vsnmp"];
$porta = $_GET["porta"];
$marca = $_GET["marca"];
$ativaPing = $_GET["ativaPing"];
$tamanho = $_GET["tamanho"];
$quantidade = $_GET["quantidade"];
$historico = $_GET["historico"];
$cron = $_GET["cron"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];

/*
echo "host: " . $host . "<br>";
echo "comunidade: " . addslashes($comunidade) . "<br>";
echo "vsnmp: " . $vsnmp . "<br>";
echo "porta: " . $porta . "<br><br>";
*/

$pausas = $quantidade + 3;

$datasinc = $data . " " . $hora;
$t1 = strtotime(date("Y-m-d H:i:s"));

if(!is_dir("/var/www/html/ram/coletas/")) { mkdir('/var/www/html/ram/coletas/', 0777, true); }
if(!is_dir("/var/www/html/ram/coletas/ppoe/")) { mkdir('/var/www/html/ram/coletas/ppoe/', 0777, true); }
if(!is_dir("/var/www/html/ram/coletas/ppoe/procesando")) { mkdir('/var/www/html/ram/coletas/ppoe/procesando/', 0777, true); }
if(!is_dir("/var/www/html/ram/coletas/ppoe/ping")) { mkdir('/var/www/html/ram/coletas/ppoe/ping/', 0777, true); }
if(!is_dir("/var/www/html/ram/coletas/ppoe/users")) { mkdir('/var/www/html/ram/coletas/ppoe/users/', 0777, true); }
/*
chmod ("/var/www/html/ram/coletas", 0755);
chmod ("/var/www/html/ram/coletas/ppoe", 0755);
chmod ("/var/www/html/ram/coletas/ppoe/procesando", 0755);
chmod ("/var/www/html/ram/coletas/ppoe/ping", 0755);
chmod ("/var/www/html/ram/coletas/ppoe/users", 0755);
*/
function insert( $idConc, $idinterface, $mac, $ip, $vlan, $ppoe, $datasinc, $dataconect, $uptimeconect, $down, $up, $down_rt, $up_rt ) {
	$timearq = date("H-i");
	$arq = $idConc . "_" . $idinterface . "_" . $uptimeconect . "_". $timearq;
    exec("echo '|$idConc|$idinterface|$mac|$ip|$vlan|$ppoe|$datasinc|$dataconect|$uptimeconect|$down|$up|$down_rt|$up_rt|' > /var/www/html/ram/coletas/ppoe/procesando/$arq");
}

function ajeitatrafego($valor) {
    $s = explode('.', $valor);
    if(!$s[1]) {
        $valor = $s[0];
    }else {
        $valor = $s[0] . '.' . substr($s[1], 0, 3);
    }
    return $valor;
}

// Mikrotik
if($marca == 1) {
    if(!is_dir("/var/www/html/ram/coletas/ppoe/temp")) { mkdir('/var/www/html/ram/coletas/ppoe/temp/', 0777, true); }
    if(!is_dir("/var/www/html/ram/coletas/ppoe/temp/$idConc")) { mkdir("/var/www/html/ram/coletas/ppoe/temp/$idConc", 0777, true); }
    //chmod ("/var/www/html/ram/coletas/ppoe/temp", 0755);
    //chmod ("/var/www/html/ram/coletas/ppoe/temp/$idConc", 0755);
    $path = "/var/www/html/ram/coletas/ppoe/temp/$idConc/";
    $diretorio = dir($path);
    $timearq1 = date("H-i-s");

    function encodeBase64($string) {
        $what = array( '+', '/', '=' );
        $by   = array( '-', '_', '' );
        return str_replace($what, $by, base64_encode($string));
    }
    
    function sanitizeSNMP($string1) {
        $what = array( 'STRING: ', 'INTEGER: ', 'IpAddress: ', 'Counter64: ', 'Gauge32: ', '"' );
        $by   = array( '', '', '', '', '', '' );
        return str_replace($what, $by, $string1);
    }
    function sanitizeSNMP2($string2) {
        $what = array( ' ', '"', '>');
        $by   = array( '', '' );
        return str_replace($what, $by, $string2);
    }

    if($vsnmp == 1) {
        exec("snmpwalk -Os -v1 -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.150.1.1.3.1.2 | sed \"s/iso.3.6.1.4.1.9.9.150.1.1.3.1.2.//g\" | sed \"s/ = STRING: /|/g\"", $usersPPPoE_tab1);
        exec("snmpwalk -Os -v1 -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.150.1.1.3.1.3 | cut -d '=' -f2 | sed \"s/ IpAddress: //g\"", $IPs);
        exec("snmpwalk -Os -v1 -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.14988.1.1.2.1.1.2 | sed \"s/iso.3.6.1.4.1.14988.1.1.2.1.1.2.//g\" | grep \"<pppoe-\" | sed \"s/ = STRING: /|/g\" | sed \"s/<pppoe-//g\"", $usersPPPoE_tab2);
        if($historico == 1) {
            $trafDown = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.8", 1000000, 30));
            $trafUp = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.9", 1000000, 30));
        }else if($historico == 2) {
            $trafDown = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.8", 1000000, 30));
            $delay1 = strtotime(date("Y-m-d H:i:s"));
            $trafUp = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.9", 1000000, 30));
            $delay = strtotime(date("Y-m-d H:i:s")) - $delay1;
            if($delay < 15 ) { 
                $delay = 15 - $delay;
                sleep($delay);
            }
            $trafDown2 = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.8", 1000000, 30));
            $trafUp2 = sanitizeSNMP(snmpwalk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.9", 1000000, 30));
            $delay = strtotime(date("Y-m-d H:i:s")) - $delay1;
        }

    }else if($vsnmp == 2) {
        exec("snmpwalk -Os -v2c -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.150.1.1.3.1.2 | sed \"s/iso.3.6.1.4.1.9.9.150.1.1.3.1.2.//g\" | sed \"s/ = STRING: /|/g\"", $usersPPPoE_tab1);
        exec("snmpwalk -Os -v2c -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.150.1.1.3.1.3 | cut -d '=' -f2 | sed \"s/ IpAddress: //g\"", $IPs);
        exec("snmpwalk -Os -v2c -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.14988.1.1.2.1.1.2 | sed \"s/iso.3.6.1.4.1.14988.1.1.2.1.1.2.//g\" | grep \"<pppoe-\" | sed \"s/ = STRING: /|/g\" | sed \"s/<pppoe-//g\"", $usersPPPoE_tab2);
        
        if($historico == 1) {
            $trafDown = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.8", 1000000, 30));
            $trafUp = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.9", 1000000, 30));
        }else if($historico == 2) {
            $trafDown = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.8", 1000000, 30));
            $delay1 = strtotime(date("Y-m-d H:i:s"));
            $trafUp = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.9", 1000000, 30));
            $delay = strtotime(date("Y-m-d H:i:s")) - $delay1;
            if($delay < 15 ) { 
                $delay = 15 - $delay;
                sleep($delay);
            }
            $trafDown2 = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.8", 1000000, 30));
            $trafUp2 = sanitizeSNMP(snmp2_walk("{$host}:{$porta}", $comunidade, "1.3.6.1.4.1.14988.1.1.2.1.1.9", 1000000, 30));
            $delay = strtotime(date("Y-m-d H:i:s")) - $delay1;
        }
    }

    //print_r($usersPPPoE_tab2);
    //echo "<br><br>Tab1: " . count($usersPPPoE_tab2) . "<br>Tab2: " . count($usersPPPoE_tab1) . "<br><br>";
    
    for ($i=0; $i<count($usersPPPoE_tab2); $i++) {
        $exp2 = explode("|", $usersPPPoE_tab2[$i]);
        $id3 = $exp2[0];
        $pppoe2 = sanitizeSNMP2($exp2[1]);
        $arq = encodeBase64($pppoe2 . "_". $timearq1);
        $down = (int)$trafDown[$i] / 1024;
        $up = (int)$trafUp[$i] / 1024;
        if($historico == 1) {
            if($id3 && $pppoe2 && isset($down) && isset($up)) {
                exec("echo '|$id3|$down|$up|||' > /var/www/html/ram/coletas/ppoe/temp/$idConc/$arq");
            }
        }else if($historico == 2) {
            $down2 = (int)$trafDown2[$i] / 1024;
            $up2 = (int)$trafUp2[$i] / 1024;
            if($id3 && $pppoe2 && isset($down) && isset($up)) {
                exec("echo '|$id3|$down|$up|$down2|$up2|' > /var/www/html/ram/coletas/ppoe/temp/$idConc/$arq");
            }
        }
    }
    
    for ($i=0; $i<count($usersPPPoE_tab1); $i++) {
        $exp1 = explode("|", $usersPPPoE_tab1[$i]);
        $id1 = $exp1[0];
        $pppoe1 = sanitizeSNMP2($exp1[1]);
        $arq = encodeBase64($pppoe1 . "_". $timearq1);
        if(file_exists("/var/www/html/ram/coletas/ppoe/temp/$idConc/$arq")) {
            $info1 = file_get_contents("/var/www/html/ram/coletas/ppoe/temp/$idConc/$arq");
            $aux3 = explode('|', $info1);
            $id3 = $aux3['1'];
            $down = $aux3['2'];
            $up = $aux3['3'];
            $ip = $IPs[$i];
            if($historico == 1) {
                if($id1 && $id3 && $pppoe1 && $ip && isset($down) && isset($up)) {
                    exec("echo '|$id1|$id3|$pppoe1|$ip|$down|$up|||' > /var/www/html/ram/coletas/ppoe/temp/$idConc/$arq");
                }
            }else if($historico == 2) {
                $down_2 = $aux3['4'];
                $up_2 = $aux3['5'];
                $down_rt = ajeitatrafego((($down_2 - $down) / $delay) * 8);
                $up_rt = ajeitatrafego((($up_2 - $up) / $delay) * 8);
                if($id1 && $id3 && $pppoe1 && $ip && isset($down) && isset($up)) {
                    exec("echo '|$id1|$id3|$pppoe1|$ip|$down|$up|$down_rt|$up_rt|' > /var/www/html/ram/coletas/ppoe/temp/$idConc/$arq");
                }
            }
        }
    }

    // id1 = indexação da tabela PPPoE
    // id2 = indexação da interface virtual gerada (nela que pegamos o uptime)
    // id3 = indexação da tabela Queue
    
    while($arquivo = $diretorio -> read()){
        if($arquivo != "." && $arquivo != "..") {
            $info = exec("cat $path$arquivo");
            $aux = explode('|', $info);
            $id1 = $aux['1'];
            $id3 = $aux['2'];
            $ppoe = $aux['3'];
            $ip = $aux['4'];
            $down = $aux['5'];
            $up = $aux['6'];
            $down_rt = $aux['7'];
            $up_rt = $aux['8'];
            if($id1 && $id3 && $ppoe && $ip && isset($down) && isset($up)) {
                if($vsnmp == 1) {
                    $id2 = sanitizeSNMP(snmpget("{$host}:{$porta}", $comunidade, "ipRouteIfIndex.{$ip}", 1000000, 30));
                    $buscaUptime = sanitizeSNMP(snmpget("{$host}:{$porta}", $comunidade, "ifLastChange.{$id2}", 1000000, 30));
                }else if($vsnmp == 2) {
                    $id2 = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $comunidade, "ipRouteIfIndex.{$ip}", 1000000, 30));
                    $buscaUptime = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $comunidade, "ifLastChange.{$id2}", 1000000, 30));
                }
                $exp1 = explode("(", $buscaUptime);
                $exp2 = explode(")", $exp1[1]);
                $uptimeconect = (int)$exp2[0] / 100;
                $data_atual = strtotime(date("Y-m-d H:i:s"));
                $timeconexao = round(abs($data_atual - $uptimeconect));
                $dataconect = date("Y-m-d H:i:s", $timeconexao);
                $idinterface = $id1 . "." . $id2 . "." . $id3;
                $mac = '';
                $vlan = '';
                /*echo "User PPPoE: <strong>" . $ppoe . "</strong><br>";
                echo " interface: " . $idinterface . "<br>";
                echo " IpAddress: " . $ip . "<br>";
                echo " Download: " . $down . " kB<br>";
                echo " Upload: " . $up . " KB<br>";
                echo " Download real-time: " . $down_rt . " kbps<br>";
                echo " Upload real-time: " . $up_rt . " Kbps<br>";
                echo " Data da Conexão: " . $dataconect . "<br>";
                echo " Tempo Online: " . $uptimeconect . "<br><br>";*/
                insert( $idConc, $idinterface, $mac, $ip, $vlan, $ppoe, $datasinc, $dataconect, $uptimeconect, $down, $up, $down_rt, $up_rt );
                exec("rm -fr $path$arquivo");
                if($ip && $ativaPing == 2) {
                    $nLoop = $nLoop + 1;
                    if($nLoop == 100) {
                        $nLoop = 0; 
                        sleep($pausas);
                    }
                    exec("php -f /var/www/html/cron/apoio/PingPPPoE.php idC=$idConc int=$idinterface ip=$ip tamanho=$tamanho qtd=$quantidade hora=$hora data=$data > /dev/null &");
                }
            }
        }
    }
    exec("rm -fr /var/www/html/ram/coletas/ppoe/temp/$idConc");

// Huawei
}else if($marca == 2) {
    function sanitizeSNMP($string1) {
        $what = array( 'STRING: ', 'INTEGER: ', 'IpAddress: ', 'Counter64: ', 'Gauge32: ', '"' );
        $by   = array( '', '', '', '', '', '' );
        return str_replace($what, $by, $string1);
    }
    function sanitizeSNMP2($string2) {
        $what = array( ' ', '"' );
        $by   = array( '', '' );
        return str_replace($what, $by, $string2);
    }

    // em outras marcas eu tive que substituir enterprises por iso.3.6.1.4.1
    if($vsnmp == 1) {
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.3 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.3.//g\" | sed \"s/ = STRING: /|/g\"", $user_pppoe);
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.15 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.15.//g\" | sed \"s/ = IpAddress: /|/g\"", $ip_pppoe);
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.17 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.17.//g\" | sed \"s/ = Hex-STRING: /|/g\"", $mac_pppoe);
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.11 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.11.//g\" | sed \"s/ = INTEGER: /|/g\"", $vlan_pppoe);
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.16.1.18 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.16.1.18.//g\" | sed \"s/ = Gauge32: /|/g\"", $uptime_pppoe);
        if($historico == 1) {
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.36 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.36.//g\" | sed \"s/ = Counter32: /|/g\"", $UpV4_pppoe);
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.70 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.70.//g\" | sed \"s/ = Counter32: /|/g\"", $UpV6_pppoe);
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.37 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.37.//g\" | sed \"s/ = Counter32: /|/g\"", $DownV4_pppoe);
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.71 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.71.//g\" | sed \"s/ = Counter32: /|/g\"", $DownV6_pppoe);
        }else if($historico == 2) {
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.36 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.36.//g\" | sed \"s/ = Counter32: /|/g\"", $UpV4_pppoe);
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.70 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.70.//g\" | sed \"s/ = Counter32: /|/g\"", $UpV6_pppoe);
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.37 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.37.//g\" | sed \"s/ = Counter32: /|/g\"", $DownV4_pppoe);
            $delay1 = strtotime(date("Y-m-d H:i:s"));
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.71 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.71.//g\" | sed \"s/ = Counter32: /|/g\"", $DownV6_pppoe);
            $delay = strtotime(date("Y-m-d H:i:s")) - $delay1;
            if($delay < 15 ) { 
                $delay = 15 - $delay;
                sleep($delay);
            }
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.36 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.36.//g\" | sed \"s/ = Counter32: /|/g\"", $UpV4_pppoe2);
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.70 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.70.//g\" | sed \"s/ = Counter32: /|/g\"", $UpV6_pppoe2);
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.37 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.37.//g\" | sed \"s/ = Counter32: /|/g\"", $DownV4_pppoe2);
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.71 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.71.//g\" | sed \"s/ = Counter32: /|/g\"", $DownV6_pppoe2);
        }
    }else if($vsnmp == 2) {
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.3 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.3.//g\" | sed \"s/ = STRING: /|/g\"", $user_pppoe);
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.15 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.15.//g\" | sed \"s/ = IpAddress: /|/g\"", $ip_pppoe);
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.17 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.17.//g\" | sed \"s/ = Hex-STRING: /|/g\"", $mac_pppoe);
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.11 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.11.//g\" | sed \"s/ = INTEGER: /|/g\"", $vlan_pppoe);
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.16.1.18 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.16.1.18.//g\" | sed \"s/ = Gauge32: /|/g\"", $uptime_pppoe);
        if($historico == 1) {
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.36 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.36.//g\" | sed \"s/ = Counter64: /|/g\"", $UpV4_pppoe);
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.70 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.70.//g\" | sed \"s/ = Counter64: /|/g\"", $UpV6_pppoe);
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.37 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.37.//g\" | sed \"s/ = Counter64: /|/g\"", $DownV4_pppoe);
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.71 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.71.//g\" | sed \"s/ = Counter64: /|/g\"", $DownV6_pppoe);
        }else if($historico == 2) {
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.36 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.36.//g\" | sed \"s/ = Counter64: /|/g\"", $UpV4_pppoe);
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.70 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.70.//g\" | sed \"s/ = Counter64: /|/g\"", $UpV6_pppoe);
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.37 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.37.//g\" | sed \"s/ = Counter64: /|/g\"", $DownV4_pppoe);
            $delay1 = strtotime(date("Y-m-d H:i:s"));
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.71 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.71.//g\" | sed \"s/ = Counter64: /|/g\"", $DownV6_pppoe);
            $delay = strtotime(date("Y-m-d H:i:s")) - $delay1;
            if($delay < 15 ) { 
                $delay = 15 - $delay;
                sleep($delay);
            }
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.36 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.36.//g\" | sed \"s/ = Counter64: /|/g\"", $UpV4_pppoe2);
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.70 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.70.//g\" | sed \"s/ = Counter64: /|/g\"", $UpV6_pppoe2);
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.37 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.37.//g\" | sed \"s/ = Counter64: /|/g\"", $DownV4_pppoe2);
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.2011.5.2.1.15.1.71 | sed \"s/iso.3.6.1.4.1.2011.5.2.1.15.1.71.//g\" | sed \"s/ = Counter64: /|/g\"", $DownV6_pppoe2);
        }
        //print_r($DownV4_pppoe);
        //if($historico == 1) {}else if($historico == 2) {}
    }
    $ips = array();
    for ($i=0; $i<count($ip_pppoe); $i++) {
        $exp = explode("|", $ip_pppoe[$i]);
        $idint = $exp[0];
        $ip = sanitizeSNMP($exp[1]);
        $ips[$idint] = $ip;
    }
    $macs = array();
    for ($i=0; $i<count($mac_pppoe); $i++) {
        $exp = explode("|", $mac_pppoe[$i]);
        $idint = $exp[0];
        $mac = sanitizeSNMP($exp[1]);
        $macs[$idint] = $mac;
    }
    $vlans = array();
    for ($i=0; $i<count($vlan_pppoe); $i++) {
        $exp = explode("|", $vlan_pppoe[$i]);
        $idint = $exp[0];
        $vlan = sanitizeSNMP($exp[1]);
        $vlans[$idint] = $vlan;
    }
    $UpV4s = array();
    for ($i=0; $i<count($UpV4_pppoe); $i++) {
        $exp = explode("|", $UpV4_pppoe[$i]);
        $idint = $exp[0];
        $upv4 = sanitizeSNMP($exp[1]);
        $UpV4s[$idint] = $upv4;
    }
    $UpV6s = array();
    for ($i=0; $i<count($UpV6_pppoe); $i++) {
        $exp = explode("|", $UpV6_pppoe[$i]);
        $idint = $exp[0];
        $upv6 = sanitizeSNMP($exp[1]);
        $UpV6s[$idint] = $upv6;
    }
    $DownV4s = array();
    for ($i=0; $i<count($DownV4_pppoe); $i++) {
        $exp = explode("|", $DownV4_pppoe[$i]);
        $idint = $exp[0];
        $downv4 = sanitizeSNMP($exp[1]);
        $DownV4s[$idint] = $downv4;
    }
    $DownV6s = array();
    for ($i=0; $i<count($DownV6_pppoe); $i++) {
        $exp = explode("|", $DownV6_pppoe[$i]);
        $idint = $exp[0];
        $downv6 = sanitizeSNMP($exp[1]);
        $DownV6s[$idint] = $downv6;
    }
    $uptimes = array();
    for ($i=0; $i<count($uptime_pppoe); $i++) {
        $exp = explode("|", $uptime_pppoe[$i]);
        $idint = $exp[0];
        $uptime = sanitizeSNMP($exp[1]);
        $uptimes[$idint] = $uptime;
    }

    if($historico == 2) {
        $UpV4s2 = array();
        for ($i=0; $i<count($UpV4_pppoe2); $i++) {
            $exp = explode("|", $UpV4_pppoe2[$i]);
            $idint = $exp[0];
            $upv4 = sanitizeSNMP($exp[1]);
            $UpV4s2[$idint] = $upv4;
        }
        $UpV6s2 = array();
        for ($i=0; $i<count($UpV6_pppoe2); $i++) {
            $exp = explode("|", $UpV6_pppoe2[$i]);
            $idint = $exp[0];
            $upv6 = sanitizeSNMP($exp[1]);
            $UpV6s2[$idint] = $upv6;
        }
        $DownV4s2 = array();
        for ($i=0; $i<count($DownV4_pppoe2); $i++) {
            $exp = explode("|", $DownV4_pppoe2[$i]);
            $idint = $exp[0];
            $downv4 = sanitizeSNMP($exp[1]);
            $DownV4s2[$idint] = $downv4;
        }
        $DownV6s2 = array();
        for ($i=0; $i<count($DownV6_pppoe2); $i++) {
            $exp = explode("|", $DownV6_pppoe2[$i]);
            $idint = $exp[0];
            $downv6 = sanitizeSNMP($exp[1]);
            $DownV6s2[$idint] = $downv6;
        }
    }

    $nLoop = 1;
    for ($i=0; $i<count($user_pppoe); $i++) {
        $exp = explode("|", $user_pppoe[$i]);
        $idinterface = $exp[0];
        $ppoe = sanitizeSNMP2($exp[1]);
        $ip = $ips[$idinterface];
        $mac = $macs[$idinterface];
        $vlan = $vlans[$idinterface];
        $tempUpV4 = $UpV4s[$idinterface];
        $tempUpV6 = $UpV6s[$idinterface];
        $tempDownV4 = $DownV4s[$idinterface];
        $tempDownV6 = $DownV6s[$idinterface];
        $uptimeconect = $uptimes[$idinterface];
        $down = ($tempDownV4 + $tempDownV6) / 1024;
        $up = ($tempUpV4 + $tempUpV6) / 1024;
        $data_atual = strtotime(date("Y-m-d H:i:s"));
        $timeconexao = round(abs($data_atual - $uptimeconect));
        $dataconect = date("Y-m-d H:i:s", $timeconexao);
        if($historico == 1) {
            $down_rt = "";
            $up_rt = "";
        }else if($historico == 2) {
            $down_rt = ajeitatrafego(((($DownV4s2[$idinterface] + $DownV6s2[$idinterface]) - ($DownV4s[$idinterface] + $DownV6s[$idinterface])) / $delay) * 8);
            $up_rt = ajeitatrafego(((($UpV4s2[$idinterface] + $UpV6s2[$idinterface]) - ($UpV4s[$idinterface] + $UpV6s[$idinterface])) / $delay) * 8);
        }
        insert( $idConc, $idinterface, $mac, $ip, $vlan, $ppoe, $datasinc, $dataconect, $uptimeconect, $down, $up, $down_rt, $up_rt );
        if($ip && $ativaPing == 2) {
            $nLoop = $nLoop + 1;
            if($nLoop == 100) {
                $nLoop = 0; 
                sleep($pausas);
            }
            exec("php -f /var/www/html/cron/apoio/PingPPPoE.php idC=$idConc int=$idinterface ip=$ip tamanho=$tamanho qtd=$quantidade hora=$hora data=$data > /dev/null &");
        }
    }

// Cisco
}else if($marca == 3) {
    function sanitizeSNMP($string1) {
        $what = array( 'STRING: ', 'INTEGER: ', 'IpAddress: ', 'Counter64: ', 'Gauge32: ', '"' );
        $by   = array( '', '', '', '', '', '' );
        return str_replace($what, $by, $string1);
    }
    function sanitizeSNMP2($string2) {
        $what = array( ' ', '"' );
        $by   = array( '', '' );
        return str_replace($what, $by, $string2);
    }

    $t1 = strtotime(date("Y-m-d H:i:s"));

    if($vsnmp == 1) {
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.150.1.1.3.1.2 | sed \"s/iso.3.6.1.4.1.9.9.150.1.1.3.1.2.//g\" | sed \"s/ = STRING: /|/g\"", $user_pppoe);
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.150.1.1.3.1.3 | sed \"s/iso.3.6.1.4.1.9.9.150.1.1.3.1.3.//g\" | sed \"s/ = IpAddress: /|/g\"", $ip_pppoe);
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.786.1.1.1.1.34 | sed \"s/iso.3.6.1.4.1.9.9.786.1.1.1.1.34.//g\" | sed \"s/ = Hex-STRING: /|/g\"", $mac_pppoe);
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.786.1.1.1.1.25 | sed \"s/iso.3.6.1.4.1.9.9.786.1.1.1.1.25.//g\" | sed \"s/ = Gauge32: /|/g\"", $index_enterprise);
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.150.1.1.3.1.8 | sed \"s/iso.3.6.1.4.1.9.9.150.1.1.3.1.8.//g\" | sed \"s/ = INTEGER: /|/g\"", $index_pppoe);
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.31.1.1.1.19 | sed \"s/ifCounterDiscontinuityTime.//g\" | sed \"s/ = Timeticks: /|/g\"", $uptime_pppoe);
        exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.492.1.2.2.1.12 | sed \"s/iso.3.6.1.4.1.9.9.492.1.2.2.1.12.7026.//g\" | sed \"s/.1.4.0.0.0.0.0.1//g\" | sed \"s/ = Counter64: /|/g\"", $traff_pppoe);
    }else if($vsnmp == 2) {
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.150.1.1.3.1.2 | sed \"s/iso.3.6.1.4.1.9.9.150.1.1.3.1.2.//g\" | sed \"s/ = STRING: /|/g\"", $user_pppoe);
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.150.1.1.3.1.3 | sed \"s/iso.3.6.1.4.1.9.9.150.1.1.3.1.3.//g\" | sed \"s/ = IpAddress: /|/g\"", $ip_pppoe);
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.786.1.1.1.1.34 | sed \"s/iso.3.6.1.4.1.9.9.786.1.1.1.1.34.//g\" | sed \"s/ = Hex-STRING: /|/g\"", $mac_pppoe);
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.786.1.1.1.1.25 | sed \"s/iso.3.6.1.4.1.9.9.786.1.1.1.1.25.//g\" | sed \"s/ = Gauge32: /|/g\"", $index_enterprise);
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.150.1.1.3.1.8 | sed \"s/iso.3.6.1.4.1.9.9.150.1.1.3.1.8.//g\" | sed \"s/ = INTEGER: /|/g\"", $index_pppoe);
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.31.1.1.1.19 | sed \"s/ifCounterDiscontinuityTime.//g\" | sed \"s/ = Timeticks: /|/g\"", $uptime_pppoe);
        exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.4.1.9.9.492.1.2.2.1.12 | sed \"s/iso.3.6.1.4.1.9.9.492.1.2.2.1.12.7026.//g\" | sed \"s/.1.4.0.0.0.0.0.1//g\" | sed \"s/ = Counter64: /|/g\"", $traff_pppoe);
    }

    if($historico == 2) {
        if($vsnmp == 1) {
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.2.2.1.10 | sed \"s/ifInOctets.//g\" | sed \"s/ = Counter32: /|/g\"", $ifInOctets1);
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.2.2.1.16 | sed \"s/ifOutOctets.//g\" | sed \"s/ = Counter32: /|/g\"", $ifOutOctets1);
        }else if($vsnmp == 2) {
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.2.2.1.10 | sed \"s/ifInOctets.//g\" | sed \"s/ = Counter32: /|/g\"", $ifInOctets1);
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.2.2.1.16 | sed \"s/ifOutOctets.//g\" | sed \"s/ = Counter32: /|/g\"", $ifOutOctets1);
        }
        sleep(25);
        if($vsnmp == 1) {
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.2.2.1.10 | sed \"s/ifInOctets.//g\" | sed \"s/ = Counter32: /|/g\"", $ifInOctets2);
            exec("snmpwalk -Os -v1 -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.2.2.1.16 | sed \"s/ifOutOctets.//g\" | sed \"s/ = Counter32: /|/g\"", $ifOutOctets2);
        }else if($vsnmp == 2) {
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.2.2.1.10 | sed \"s/ifInOctets.//g\" | sed \"s/ = Counter32: /|/g\"", $ifInOctets2);
            exec("snmpwalk -Os -v2c -t 60 -r 30 -Cc -c " . addslashes($comunidade) . " " . $host . ":" . $porta . " 1.3.6.1.2.1.2.2.1.16 | sed \"s/ifOutOctets.//g\" | sed \"s/ = Counter32: /|/g\"", $ifOutOctets2);
        }

        $down_pppoe1 = array();
        for ($i=0; $i<count($ifInOctets1); $i++) {
            $exp = explode("|", $ifInOctets1[$i]);
            $idint = $exp[0];
            $down = sanitizeSNMP($exp[1]);
            $down_pppoe1[$idint] = $down;
        }

        $down_pppoe2 = array();
        for ($i=0; $i<count($ifInOctets2); $i++) {
            $exp = explode("|", $ifInOctets2[$i]);
            $idint = $exp[0];
            $down = sanitizeSNMP($exp[1]);
            $down_pppoe2[$idint] = $down;
        }

        $up_pppoe1 = array();
        for ($i=0; $i<count($ifOutOctets1); $i++) {
            $exp = explode("|", $ifOutOctets1[$i]);
            $idint = $exp[0];
            $up = sanitizeSNMP($exp[1]);
            $up_pppoe1[$idint] = $up;
        }

        $up_pppoe2 = array();
        for ($i=0; $i<count($ifOutOctets2); $i++) {
            $exp = explode("|", $ifOutOctets2[$i]);
            $idint = $exp[0];
            $up = sanitizeSNMP($exp[1]);
            $up_pppoe2[$idint] = $up;
        }
    }

    $traff_sessao = array();
    for ($i=0; $i<count($traff_pppoe); $i++) {
        $exp = explode("|", $traff_pppoe[$i]);
        $idint = $exp[0];
        $otraff = sanitizeSNMP($exp[1]);
        $traff_sessao[$idint] = $otraff;
    }

    $ips = array();
    for ($i=0; $i<count($ip_pppoe); $i++) {
        $exp = explode("|", $ip_pppoe[$i]);
        $idint = $exp[0];
        $ip = sanitizeSNMP($exp[1]);
        $ips[$idint] = $ip;
    }

    $macs = array();
    for ($i=0; $i<count($mac_pppoe); $i++) {
        $exp = explode("|", $mac_pppoe[$i]);
        $idint = $exp[0];
        $mac = sanitizeSNMP($exp[1]);
        $macs[$idint] = $mac;
    }

    $users = array();
    for ($i=0; $i<count($user_pppoe); $i++) {
        $exp = explode("|", $user_pppoe[$i]);
        $idint = $exp[0];
        $user = sanitizeSNMP($exp[1]);
        $users[$idint] = $user;
    }

    $index = array();
    for ($i=0; $i<count($index_pppoe); $i++) {
        $exp = explode("|", $index_pppoe[$i]);
        $idint = $exp[0];
        $oindex = sanitizeSNMP($exp[1]);
        $index[$idint] = $oindex;
    }

    $uptimes = array();
    for ($i=0; $i<count($uptime_pppoe); $i++) {
        $exp = explode("|", $uptime_pppoe[$i]);
        $exp1 = explode("(", $exp[1]);
        $exp2 = explode(")", $exp1[1]);
        $idint = $exp[0];
        $ouptime = $exp2[0];
        $uptimes[$idint] = $ouptime;

    }

    for ($i=0; $i<count($index_enterprise); $i++) {
        $exp = explode("|", $index_enterprise[$i]);
        $index1 = $exp[0];
        $index2 = $exp[1];
        $index3 = $index[$index2];
        $idinterface = $index1 . "." . $index2 . "." . $index3;

        $ppoe = $users[$index2];
        $ip = $ips[$index2];
        $mac = $macs[$index1];

        if(isset($index3) && $index3 >= 1 && $ppoe) {
            $uptimeconect = $uptimes[$index3];
            $trafego = $traff_sessao[$index3];
            $down = $trafego / 1024;
            $up = 0;
            $vlan = "";

            $data_atual = strtotime(date("Y-m-d H:i:s"));
            $timeconexao = round(abs($data_atual - $uptimeconect));
            $dataconect = date("Y-m-d H:i:s", $timeconexao);

            if($historico == 1) {
                $down_rt = "";
                $up_rt = "";
            }else if($historico == 2) {
                $down_1 = (int)$down_pppoe1[$index3];
                $down_2 = (int)$down_pppoe2[$index3];
                $up_1 = (int)$up_pppoe1[$index3];
                $up_2 = (int)$up_pppoe2[$index3];
                $up_rt = intval(((((($down_2 - $down_1) / 25) * 8) / 1024)));
                $down_rt = intval(((((($up_2 - $up_1) / 25) * 8) / 1024)));
            }
            insert( $idConc, $idinterface, $mac, $ip, $vlan, $ppoe, $datasinc, $dataconect, $uptimeconect, $down, $up, $down_rt, $up_rt );
            if($ip && $ativaPing == 2) {
                $nLoop = $nLoop + 1;
                if($nLoop == 100) {
                    $nLoop = 0;
                    sleep($pausas);
                }
                exec("php -f /var/www/html/cron/apoio/PingPPPoE.php idC=$idConc int=$idinterface ip=$ip tamanho=$tamanho qtd=$quantidade hora=$hora data=$data > /dev/null &");
            }
        }
    }
}

$t2 = strtotime(date("Y-m-d H:i:s"));
$segundos = round(abs($t1 - $t2));
$mudacron = 1;

// 5 minutos
if($cron == 1 && $segundos > 300) {
    $mudacron = 2;
// 10 minutos
}else if(($cron == 2 || $mudacron == 2) && $segundos > 600) {
    $mudacron = 3;
// 15 minutos
}else if(($cron == 3 || $mudacron == 3) && $segundos > 900) {
    $mudacron = 4;
// 30 minutos
}else if(($cron == 4 || $mudacron == 4) && $segundos > 1800) {
    $mudacron = 5;
// 1 hora
}else if(($cron == 5 || $mudacron == 5) && $segundos > 3600) {
    $mudacron = 6;
// 2 horas
}else if(($cron == 6 || $mudacron == 6) && $segundos > 7200) {
    $mudacron = 7;
// 3 horas
}else if(($cron == 7 || $mudacron == 7) && $segundos > 10800) {
    $mudacron = 8;
}

include("/var/www/html/conexao.php");
if($mudacron > 1) {
    mysqli_query($db, "UPDATE concentradoras SET datasinc = '".$datasinc."', temposinc = '".$segundos."', cron = '".$mudacron."' WHERE id = '".$idConc."';");
}else {
    mysqli_query($db, "UPDATE concentradoras SET datasinc = '".$datasinc."', temposinc = '".$segundos."' WHERE id = '".$idConc."';");
}
mysqli_close($db);

exec("php -f /var/www/html/cron/apoio/mariadb_ppoe.php idConc=$idConc hora=$hora data=$data > /dev/null &");
?>