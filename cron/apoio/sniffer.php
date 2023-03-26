<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$inicio = date("H:i:s");
/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/
//$idG = 15;
$idG = $_GET["idG"];

include("/var/www/html/cron/apoio/conexao.php");
include("/var/www/html/cron/apoio/icmp.php");

function validaBlocos($string) {
    $what = array( '0.0.0.0/0', ',' );
    $by   = array( '', '' );
    return str_replace($what, $by, $string);
}

function sanitizeSNMP($string) {
    $what = array( 'STRING: ', 'INTEGER: ', 'Gauge32: ', 'Counter32: ', 'days', 'day', 'year', 'years', 'month', 'months', '"' );
    $by   = array( '', '', '', '', 'dias', 'dia', 'ano', 'anos', 'mês', 'meses', '' );
    return str_replace($what, $by, $string);
}

function sanitizeSNMP2($string) {
    $what = array( 'Counter64: ', 'Counter32: ', 'INTEGER: ', 'Counter32: ', 'STRING: ', 'STRING:', 'Gauge32: ', ' No Such Object available on this agent at this OID', 'No Such Object available on this agent at this OID', 'Hex-', 'Hex-STRING: ', '"' );
    $by   = array( '', '', '', '', '', '', '', '', '', '', '', '' );
    return str_replace($what, $by, $string);
}

function addSensor($id, $valor, $valor1, $valor2, $tag, $nome, $statusAlert) {
    $db = mysqli_connect("localhost", "root", "#H0gGLS3@XeaW702_i51z@yUlN#", "Ravi");
    $resultSensores = mysqli_query($db, "SELECT ordem FROM Sensores WHERE idDispositivo = '".$id."' ORDER BY ordem DESC LIMIT 1;");
    $UltimoSensor = mysqli_fetch_array($resultSensores);
    $ordem = $UltimoSensor['ordem'] + 1;
    mysqli_query($db, "INSERT INTO Sensores (tag, nome, valor, valor1, valor2, adicionais, idDispositivo, statusAlert, cronograma, ordem) VALUES ('".$tag."', '".$nome."', '".$valor."', '".$valor1."', '".$valor2."', '32-20-20--2', '".$id."', '".$statusAlert."', '1m', '".$ordem."')");
    $novoid = mysqli_insert_id($db);
    if($statusAlert == 6) {
        $Pesquisa = mysqli_query($db, "SELECT id, ok, total FROM ResumoSensores ORDER BY id DESC LIMIT 1;");
        $dat = mysqli_fetch_array($Pesquisa);
        $ok = $dat['ok'] + 1;
        $total = $dat['total'] + 1;
        mysqli_query($db, "UPDATE ResumoSensores SET ok = '".$ok."', total = '".$total."' WHERE id = $dat[id];");
    }
    exec("echo '|$statusAlert|$valor1|$valor2||$tag|$nome|||1|' > /var/www/html/ram/dispositivos/sensores/$novoid");
}

function addSensorTrafego($id, $valor, $nome, $alias, $descr, $mac, $ifSpeed) {
    $db = mysqli_connect("localhost", "root", "#H0gGLS3@XeaW702_i51z@yUlN#", "Ravi");
    $resultSensores = mysqli_query($db, "SELECT ordem FROM Sensores WHERE idDispositivo = '".$id."' ORDER BY ordem DESC LIMIT 1;");
    $UltimoSensor = mysqli_fetch_array($resultSensores);
    $ordem = $UltimoSensor['ordem'] + 1;
    mysqli_query($db, "INSERT INTO Sensores (tag, nome, alias, descr, mac, ifSpeed, valor, idDispositivo, statusAlert, ordem, cronograma, adicionais) VALUES ('trafegosnmp', '".$nome."', '".$alias."', '".$descr."', '".$mac."', '".$ifSpeed."', '".$valor."', ".$id.", '5', '".$ordem."', '1m', '2')");
    $novoid = mysqli_insert_id($db);
    $Pesquisa = mysqli_query($db, "SELECT id, novos, total FROM ResumoSensores ORDER BY id DESC LIMIT 1;");
    $dat = mysqli_fetch_array($Pesquisa);
    $novos = $dat['novos'] + 1;
    $total = $dat['total'] + 1;
    $update = mysqli_query($db, "UPDATE ResumoSensores SET novos = '".$novos."', total = '".$total."' WHERE id = $dat[id];");
    exec("echo '|5||||trafegosnmp|$nome|||1|' > /var/www/html/ram/dispositivos/sensores/$novoid");
}

$PesquisaGrupo = mysqli_query($db, "SELECT id, Nome, autoscan, baseIP, autosensor, modelo_auto, repetir, ignorar, ativasnmp, comunidadesnmp_g, portasnmp_g, versaosnmp_g, nivelsegsnmp_g, protocoloauthsnmp_g, protocolocripsnmp_g, authsnmp_g, criptosnmp_g FROM GrupoMonitor WHERE id = '".$idG."';");
$Grupo = mysqli_fetch_array($PesquisaGrupo);

mysqli_query($db, "UPDATE GrupoMonitor SET status = '1' WHERE id = '".$idG."';");
exec("echo '|".$Grupo['autoscan']."|1||".$Grupo['Nome']."|' > /var/www/html/ram/dispositivos/grupos/".$idG);

$bloco = validaBlocos($Grupo['baseIP']);

$scan_ips = array();
exec("nmap -sP -n -T4 " . $bloco, $scan_ips);

// Buscando IPs
$ips = array();
for ($i=0; $i<count($scan_ips); $i++) {
    if(preg_match('/Nmap scan report for/', $scan_ips[$i])) { 
        $ips[] = str_replace('Nmap scan report for ', '', $scan_ips[$i]);
    }
}

// Se encongrou algum IP vamos verificar a necessidade de buscar mais informações
if(count($ips)) {
    $retries = 1;
    $timeout = 1;

    $oids = array();
    $oids[] = ".1.3.6.1.2.1.1.5.0";                 // sysName
    $oids[] = ".1.3.6.1.2.1.1.1.0";                 // sysDescr
    $oidspart = implode(' ', $oids);

    // Herdar configurações SNMP Padrão
    if($Grupo['ativasnmp'] == 1) {
        $PesquisaSys = mysqli_query($db, "SELECT snmppadrao, portasnmppadrao, versaosnmppadrao, nivelsegsnmppadrao, protocoloauthsnmppadrao, protocolocripsnmppadrao, authsnmppadrao, criptosnmppadrao FROM system");
        $resSys = mysqli_fetch_array($PesquisaSys);

        $community = $resSys['snmppadrao'];
        $porta = $resSys['portasnmppadrao'];
        $vsnmp = $resSys['versaosnmppadrao'];
        $nivelsegsnmp = $resSys['nivelsegsnmppadrao'];
        $protocoloauthsnmp = $resSys['protocoloauthsnmppadrao'];
        $protocolocripsnmp = $resSys['protocolocripsnmppadrao'];
        $authsnmp = $resSys['authsnmppadrao'];
        $criptosnmp = $resSys['criptosnmppadrao'];

    // Customizar configurações SNMP
    }else if($Grupo['ativasnmp'] == 2) {
        $community = $Grupo['comunidadesnmp_g'];
        $porta = $Grupo['portasnmp_g'];
        $vsnmp = $Grupo['versaosnmp_g'];
        $nivelsegsnmp = $Grupo['nivelsegsnmp_g'];
        $protocoloauthsnmp = $Grupo['protocoloauthsnmp_g'];
        $protocolocripsnmp = $Grupo['protocolocripsnmp_g'];
        $authsnmp = $Grupo['authsnmp_g'];
        $criptosnmp = $Grupo['criptosnmp_g'];
    }

    $disp_search = array();
    for ($e=0; $e<count($ips); $e++) {
        $host = $ips[$e];
        $nomeDisp = "";
        
        // Verifica se é para ignorar hosts conhecidos ou não
        $salvar = 0;
        if($Grupo['ignorar'] == 1) {
            $PesquisaDisp = mysqli_query($db, "SELECT id FROM Dispositivos WHERE ip = '".$host."';");
            if(!mysqli_num_rows($PesquisaDisp)) { $salvar = 1; }
        }else { $salvar = 1; }

        if($salvar == 1) {
            $conexao_snmp = 0;
            // Verificamos se o SNMP funciona e de quebra tentamos coletar o nome do equipamento
            // Caso tenha sido adicionado mais de uma comunidade testaremos isso também
            $aux = explode(',', str_replace(' ', '', $community));
            //print_r($aux);
            for ($o=0; $o<count($aux); $o++) {
                $community = $aux[$o];
                // Verificar as demais comunidades SNMP se a comunição ainda não funcionou
                if($conexao_snmp == 0) {
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
        
                    // Verifica se a conexão SNMP funcionou
                    if(!$stdno) {
                        $conexao_snmp = 1;
                    }else {
                        // Se a conexão SNMP não funcionou tentaremos conectar com outra versão
                        if($vsnmp != 2) { $vsnmp = 2; }else if($vsnmp != 1) { $vsnmp = 1; }

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
                
                        // Verifica se a conexão SNMP funcionou
                        if(!$stdno) { $conexao_snmp = 1; }
                    }

                    // Se a conexão SNMP funcionou vamos seguir...
                    if($conexao_snmp == 1) {
                        if(count($analysis) == 2) {
                            $a = explode('= ', $analysis[0]);
                            $sysName = sanitizeSNMP($a[1]);
                            $b = explode('= ', $analysis[1]);
                            $sysDescr = sanitizeSNMP($b[1]);
                        }else {
                            $a = explode('= ', $analysis[0]);
                            $b = explode('= ', $analysis[1]);
                            if(preg_match('/sysName./', $a[0])) { $sysName = sanitizeSNMP($a[1]); }
                            if(preg_match('/sysName./', $b[0])) { $sysName = sanitizeSNMP($b[1]); }
                            if(preg_match('/sysDescr./', $a[0])) { $sysDescr = sanitizeSNMP($a[1]); }
                            if(preg_match('/sysDescr./', $b[0])) { $sysDescr = sanitizeSNMP($b[1]); }
                        }

                        // Algumas costumizações legais para monitar o nome do dispositivo quando possível
                        if(isset($sysName) || isset($sysDescr)) {
                            $nomeDisp = $sysName;
                            if(preg_match('/Linux 2.6.32./', $sysDescr)) {
                                if($vsnmp == 1) {
                                    $SSID = sanitizeSNMP(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.5.1.2.1", 100000, 1));
                                    $Frequencia = sanitizeSNMP(snmpget("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.1.1.4.1", 100000, 1));
                                }else if($vsnmp == 2) {
                                    $SSID = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.5.1.2.1", 100000, 1));
                                    $Frequencia = sanitizeSNMP(snmp2_get("{$host}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.1.1.4.1", 100000, 1));
                                }
                                if(isset($SSID) && isset($Frequencia)) { $nomeDisp = $SSID . "-" . $Frequencia; }
                            }
                            if($sysName == "MikroTik") { $nomeDisp = str_replace('RouterOS ', 'Mk ', $sysDescr); $nomeDisp = substr($nomeDisp, 0, 200); }
                            if($sysName == "CambiumNetworks") { $nomeDisp = substr(($sysName . " - " . $sysDescr), 0, 200); }
                            if($sysDescr == "Mimosa Firmware") { $nomeDisp = substr(("Mimosa - " . $sysName), 0, 200); }
                            if($sysDescr == "Huawei Integrated Access Software") { $nomeDisp = substr(("OLT Huawei - " . $sysName), 0, 200); }
                            if(preg_match('/Huawei Versatile Routing Platform/', $sysDescr)) { $nomeDisp = substr(("Router Huawei - " . $sysName), 0, 200); }
                            if($sysDescr == "AN5516-01") { $nomeDisp = "OLT Fiberhome AN5516"; }
                            if($sysDescr == "DM4370") { $nomeDisp = $sysDescr . " - " . $sysName; $nomeDisp = substr($nomeDisp, 0, 200); }
                            if(preg_match('/Dell Networking/', $sysDescr)) { $exp = explode(",", $sysDescr); $nomeDisp = str_replace('Dell Networking', 'Dell', $exp[0]) . " - " . $sysName; $nomeDisp = substr($nomeDisp, 0, 200); }
                            if(preg_match('/Juniper Networks/', $sysDescr)) { $nomeDisp = "Juniper - " . $sysName; $nomeDisp = substr($nomeDisp, 0, 200); }
                            if($sysDescr == "V1600D") { $nomeDisp = "OLT VSOL V1600D"; }
                        }

                        // Se depois de tudo isso ainda não temos um nome não vamos mais perder tempo buscando sensores SNMP
                        if(!$nomeDisp) {
                            $buscar_sensores_snmp = 0;
                            $aux2 = explode('.', $host);
                            $nomeDisp = "Dispositivo " . $aux2['3'];
                            mysqli_query($db, "INSERT INTO Dispositivos (Nome, ip, idGrupoPai) VALUES ('".$nomeDisp."', '".$host."', '".$Grupo['id']."');");
                        }else {
                            $buscar_sensores_snmp = 1;
                            mysqli_query($db, "INSERT INTO Dispositivos (Nome, ip, idGrupoPai, HerdarPai, snmpcomunit, portasnmp_d, versaosnmp_d, nivelsegsnmp_d, protocoloauthsnmp_d, protocolocripsnmp_d, authsnmp_d, criptosnmp_d) VALUES ('".$nomeDisp."', '".$host."', '".$Grupo['id']."', '2', '".$community."', '".$porta."', '".$vsnmp."', '".$nivelsegsnmp."', '".$protocoloauthsnmp."', '".$protocolocripsnmp."', '".$authsnmp."', '".$criptosnmp."');");
                        }
                        $idDisp = mysqli_insert_id($db);
                        $disp_search[] = array(
                            'id' => $idDisp,
                            'id_grupo_pai' => $Grupo['id'],
                            'address' => $host,
                            'buscar_sensores_snmp' => $buscar_sensores_snmp,
                            'snmpcomunit' => $community,
                            'portasnmp_d' => $porta,
                            'versaosnmp_d' => $vsnmp,
                            'nivelsegsnmp_d' => $nivelsegsnmp,
                            'protocoloauthsnmp_d' => $protocoloauthsnmp,
                            'protocolocripsnmp_d' => $protocolocripsnmp,
                            'authsnmp_d' => $authsnmp,
                            'criptosnmp_d' => $criptosnmp
                        );
                        exec("echo '|".$Grupo['id']."|".$nomeDisp."|".$host."|443|0||' > /var/www/html/ram/dispositivos/$idDisp");
                    }
                }
            }

            // Aqui os hosts encontrados que não possuem conexão SNMP
            if(!$conexao_snmp) {
                $aux2 = explode('.', $host);
                $nomeDisp = "Dispositivo " . $aux2['3'];
                mysqli_query($db, "INSERT INTO Dispositivos (Nome, ip, idGrupoPai) VALUES ('".$nomeDisp."', '".$host."', '".$Grupo['id']."');");
                $idDisp = mysqli_insert_id($db);
                $disp_search[] = array(
                    'id' => $idDisp,
                    'id_grupo_pai' => $Grupo['id'],
                    'address' => $host,
                    'buscar_sensores_snmp' => 0,
                );
                exec("echo '|".$Grupo['id']."|".$nomeDisp."|".$host."|443|0||' > /var/www/html/ram/dispositivos/$idDisp");
            }
        }
    }
}

// Se encongrou algum IP vamos verificar a necessidade de buscar mais informações
if(count($disp_search)) {
    // Adicionar sensores automaticamente 
    if($Grupo['autosensor'] == 2) {
        // Primeiro verificamos se tem sensor de Ping para utilizarmos o fping
        $result_tags1 = mysqli_query($db, "SELECT tag FROM tags_modelo_sensores WHERE id_modelo = '" . $Grupo['modelo_auto'] . "' AND tag = 'ping';");
        if(mysqli_num_rows($result_tags1)) {
            $tamanho = 32;     // Tamanho do pacote
            $quantidade = 5;   // Quantidade de pacotes
            $resposta_fping = ping_list($ips, $tamanho, $quantidade);

            for($i=0;$i<count($disp_search);$i++) {
                $id = $disp_search[$i]['id'];
                $id_grupo_pai = $disp_search[$i]['id_grupo_pai'];
                $ip = $disp_search[$i]['address'];
                $ping = $resposta_fping[$ip]['ping'];
                $loss = $resposta_fping[$ip]["loss"];
                if($loss < 100) {
                    $tag = "ping";
                    $nome = "Ping";
                    addSensor($id, $ip, $ping, $loss, $tag, $nome, 6);
                }
            }
        }

        $result_tags2_test = mysqli_query($db, "SELECT id FROM tags_modelo_sensores WHERE id_modelo = '" . $Grupo['modelo_auto'] . "' AND tag != 'ping' AND tag != 'trafegosnmp';");
        if(mysqli_num_rows($result_tags2_test)) {
            for($a=0;$a<count($disp_search);$a++) {
                echo "loop: " . $a . "\n";
                $id = $disp_search[$a]['id'];
                $id_grupo_pai = $disp_search[$a]['id_grupo_pai'];
                $ip = $disp_search[$a]['address'];
                $buscar_sensores_snmp = $disp_search[$a]['buscar_sensores_snmp'];
                
                // Vamos agora testar alguns sensores SNMP se a conexão estiver disponível
                if($buscar_sensores_snmp == 1) {
                    $result_tags2 = mysqli_query($db, "SELECT tag, valor FROM tags_modelo_sensores WHERE id_modelo = '" . $Grupo['modelo_auto'] . "' AND tag != 'ping' AND tag != 'trafegosnmp';");
                    while($sensores = mysqli_fetch_array($result_tags2)) {
                        $result_nome_sonda = mysqli_query($db, "SELECT nome FROM Sondas WHERE tag = '" . $sensores['tag'] . "';");
                        $Sonda = mysqli_fetch_array($result_nome_sonda);
                        addSensor($id, $sensores['valor'], '', '', $sensores['tag'], $Sonda['nome'], 5);
                        //echo "addSensor(" . $id . ", " . $sensores['valor'] . ", '', '', " . $sensores['tag'] . ", " . $Sonda['nome'] . ", 5);\n";
                    }
                }
            }
        }

        $result_tags3_test = mysqli_query($db, "SELECT id FROM tags_modelo_sensores WHERE id_modelo = '" . $Grupo['modelo_auto'] . "' AND tag = 'trafegosnmp';");
        if(mysqli_num_rows($result_tags3_test)) {
            for($b=0;$b<count($disp_search);$b++) {
                $id = $disp_search[$b]['id'];
                $id_grupo_pai = $disp_search[$b]['id_grupo_pai'];
                $ip = $disp_search[$b]['address'];
                $buscar_sensores_snmp = $disp_search[$b]['buscar_sensores_snmp'];

                // Vamos agora testar alguns sensores SNMP se a conexão estiver disponível
                if($buscar_sensores_snmp == 1) {
                    $community = $disp_search[$b]['snmpcomunit'];
                    $porta = $disp_search[$b]['portasnmp_d'];
                    $vsnmp = $disp_search[$b]['versaosnmp_d'];
                    if($vsnmp == 3) {
                        $nivelsegsnmp = $disp_search[$b]['nivelsegsnmp_d'];
                        $protocoloauthsnmp = $disp_search[$b]['protocoloauthsnmp_d'];
                        $protocolocripsnmp = $disp_search[$b]['protocolocripsnmp_d'];
                        $authsnmp = $disp_search[$b]['authsnmp_d'];
                        $criptosnmp = $disp_search[$b]['criptosnmp_d'];
                    }

                    $result_tags3 = mysqli_query($db, "SELECT tag, valor FROM tags_modelo_sensores WHERE id_modelo = '" . $Grupo['modelo_auto'] . "' AND tag = 'trafegosnmp';");
                    while($sensores = mysqli_fetch_array($result_tags3)) {
                        if($sensores['valor']) {
                            $oids1 = array();
                            $oids2 = array();
                            $oids3 = array();
                            $oids1[] = ".1.3.6.1.2.1.2.2.1.8." . $sensores['valor'];                   // ifOperStatus
                            $oids3[] = ".1.3.6.1.2.1.31.1.1.1.1." . $sensores['valor'];                // ifName
                            $oids3[] = ".1.3.6.1.2.1.2.2.1.2." . $sensores['valor'];                   // ifDescr
                            $oids3[] = ".1.3.6.1.2.1.31.1.1.1.18." . $sensores['valor'];               // ifAlias
                            $oids3[] = ".1.3.6.1.2.1.2.2.1.6." . $sensores['valor'];                   // ifPhysAddress
                            $oids2[] = ".1.3.6.1.2.1.2.2.1.5." . $sensores['valor'];                   // ifSpeed
                            $oidspart1 = implode(' ', $oids1);
                            $oidspart2 = implode(' ', $oids2);
                            $oidspart3 = implode(' ', $oids3);

                            if($vsnmp == 1) {
                                $cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $ip . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
                                $cmd2 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $ip . ":" . $porta . " " . $oidspart2 . " 2>/dev/null";
                                $cmd3 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $ip . ":" . $porta . " " . $oidspart3 . " 2>/dev/null";
                            }else if($vsnmp == 2) {
                                $cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $ip . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
                                $cmd2 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $ip . ":" . $porta . " " . $oidspart2 . " 2>/dev/null";
                                $cmd3 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $ip . ":" . $porta . " " . $oidspart3 . " 2>/dev/null";
                            }else if($vsnmp == 3) {
                                $cmd1 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $ip . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
                                $cmd2 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $ip . ":" . $porta . " " . $oidspart2 . " 2>/dev/null";
                                $cmd3 = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $ip . ":" . $porta . " " . $oidspart3 . " 2>/dev/null";
                            }

                            $stdno = 0;
                            $analysis1 = array();
                            exec($cmd1, $analysis1, $stdno);
                            $stdno = (int)$stdno;
                        
                            // Verifica se a conexão SNMP funcionou
                            if(!$stdno) {
                                $a = explode('= ', $analysis1[0]);
                                $status = sanitizeSNMP2($a[1]);
                                $analysis2 = array();
                                exec($cmd2, $analysis2);
                                $b = explode('= ', $analysis2[0]);
                                $ifSpeed = sanitizeSNMP2($b[1]) / 1000000;
                                if($vsnmp != 1) {
                                    $c = explode('= ', $analysis2[1]);
                                    $ifHighSpeed = sanitizeSNMP2($c[1]);
                                    if(!$ifHighSpeed) { $ifHighSpeed = $ifSpeed; }
                                }else {
                                    $ifHighSpeed = $ifSpeed;
                                }
                                $analysis3 = array();
                                exec($cmd3, $analysis3);
                                if(count($analysis3) == 4) {
                                    $d = explode('= ', $analysis3[0]);
                                    $ifName = sanitizeSNMP2($d[1]);
                                    $e = explode('= ', $analysis3[1]);
                                    $ifDescr = sanitizeSNMP2($e[1]);
                                    $f = explode('= ', $analysis3[2]);
                                    $ifAlias = sanitizeSNMP2($f[1]);
                                    $g = explode('= ', $analysis3[3]);
                                    $ifPhysAddress = sanitizeSNMP2($g[1]);
                                }else {
                                    $d = explode('= ', $analysis3[0]);
                                    $e = explode('= ', $analysis3[1]);
                                    $f = explode('= ', $analysis3[2]);
                                    $g = explode('= ', $analysis3[3]);
                                    if(preg_match('/ifName./', $d[0])) { $ifName = sanitizeSNMP2($d[1]); }
                                    if(preg_match('/ifDescr./', $d[0])) { $ifDescr = sanitizeSNMP2($d[1]); }
                                    if(preg_match('/ifAlias./', $d[0])) { $ifAlias = sanitizeSNMP2($d[1]); }
                                    if(preg_match('/ifPhysAddress./', $d[0])) { $ifPhysAddress = sanitizeSNMP2($d[1]); }
                                    if(preg_match('/ifName./', $e[0])) { $ifName = sanitizeSNMP2($e[1]); }
                                    if(preg_match('/ifDescr./', $e[0])) { $ifDescr = sanitizeSNMP2($e[1]); }
                                    if(preg_match('/ifAlias./', $e[0])) { $ifAlias = sanitizeSNMP2($e[1]); }
                                    if(preg_match('/ifPhysAddress./', $e[0])) { $ifPhysAddress = sanitizeSNMP2($e[1]); }
                                    if(preg_match('/ifName./', $f[0])) { $ifName = sanitizeSNMP2($f[1]); }
                                    if(preg_match('/ifDescr./', $f[0])) { $ifDescr = sanitizeSNMP2($f[1]); }
                                    if(preg_match('/ifAlias./', $f[0])) { $ifAlias = sanitizeSNMP2($f[1]); }
                                    if(preg_match('/ifPhysAddress./', $f[0])) { $ifPhysAddress = sanitizeSNMP2($f[1]); }
                                    if(preg_match('/ifName./', $g[0])) { $ifName = sanitizeSNMP2($g[1]); }
                                    if(preg_match('/ifDescr./', $g[0])) { $ifDescr = sanitizeSNMP2($g[1]); }
                                    if(preg_match('/ifAlias./', $g[0])) { $ifAlias = sanitizeSNMP2($g[1]); }
                                    if(preg_match('/ifPhysAddress./', $g[0])) { $ifPhysAddress = sanitizeSNMP2($g[1]); }
                                }
                                // Verifica se a interface está operante
                                if($status == 1 || $status == 6) {
                                    $nome = $ifName;
                                    if (!$nome) { $nome = substr($ifDescr, 0, 250); }
                                    if (!$nome) { $nome = substr($ifAlias, 0, 250); }
                                    if (!$nome) { $nome = "Interface " . $sensores['valor']; }
                                    addSensorTrafego($id, $sensores['valor'], $nome, $ifAlias, $ifDescr, $ifPhysAddress, $ifHighSpeed);
                                }
                            }
                        }
                    }
                }
            }
        }

    }else if($Grupo['autosensor'] == 3) {
        $tamanho = 32;     // Tamanho do pacote
        $quantidade = 5;   // Quantidade de pacotes
        $resposta_fping = ping_list($ips, $tamanho, $quantidade);

        for($i=0;$i<count($disp_search);$i++) {
            $id = $disp_search[$i]['id'];
            $id_grupo_pai = $disp_search[$i]['id_grupo_pai'];
            $ip = $disp_search[$i]['address'];
            $buscar_sensores_snmp = $disp_search[$i]['buscar_sensores_snmp'];
            $ping = $resposta_fping[$ip]['ping'];
            $loss = $resposta_fping[$ip]["loss"];
            if($loss < 100) {
                $tag = "ping";
                $nome = "Ping";
                addSensor($id, $ip, $ping, $loss, $tag, $nome, 6);
            }

            // Vamos agora testar alguns sensores SNMP se a conexão estiver disponível
            if($buscar_sensores_snmp == 1) {
                $community = $disp_search[$i]['snmpcomunit'];
                $porta = $disp_search[$i]['portasnmp_d'];
                $vsnmp = $disp_search[$i]['versaosnmp_d'];
                if($vsnmp == 3) {
                    $nivelsegsnmp = $disp_search[$i]['nivelsegsnmp_d'];
                    $protocoloauthsnmp = $disp_search[$i]['protocoloauthsnmp_d'];
                    $protocolocripsnmp = $disp_search[$i]['protocolocripsnmp_d'];
                    $authsnmp = $disp_search[$i]['authsnmp_d'];
                    $criptosnmp = $disp_search[$i]['criptosnmp_d'];
                }

                $voltagem = '';
                $temperatura = '';
                $temperaturacpu = '';
                $cpu = '';
                $uptime = '';
    
                $pppoe = '';
                $dhcpmk = '';
                $ccqmksnmp = '';
                $sinalmksnmp = '';
                $noisefloormksnmp = '';
                $conexmikrotiksnmp = '';
    
                $ccqubnt = '';
                $sinalubnt = '';
                $noisefloorubnt = '';
                $airmaxcubnt = '';
                $airmaxqubnt = '';
                $conexubnt = '';
    
                $cpucambium = '';
                $ganhocambium = '';
                $potenciacambium = '';
                $associedcambium = '';
                $capacidadecambium = '';
                $qualidadecambium = '';
    
                $sinalmimosa = '';
                $noisemimosa1 = '';
                $noisemimosa2 = '';
                $temperaturamimosa = '';
                $txphy1 = '';
                $txphy2 = '';
                $txphy3 = '';
                $txphy4 = '';
                $rxphy1 = '';
                $rxphy2 = '';
                $rxphy3 = '';
                $rxphy4 = '';
                $mimosaTxMacTotal = '';
                $mimosaRxMacTotal = '';
                $wanmimosa = '';
    
                $totalONUfiberhome = '';
                $temperaturafiberhome = '';
    
                $totalonuponhuawei = '';
                $cpurouterhuawei = '';
    
                $cpujuniper = '';
                $temperaturajuniper = '';
                $ramjuniper = '';
    
                $temperaturavsol = '';
                $cpuvsol = '';
                $ramvsol = '';

                if($vsnmp == 1) {
                    $voltagem = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.3.8.0", 100000, 1)) / 10;
                    $temperatura = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.3.10.0", 100000, 1)) / 10;
                    $temperaturacpu = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.3.11.0", 100000, 1)) / 10;
                    $cpu = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2021.11.10.0", 100000, 1));
    
                    // Mikrotik
                    if(preg_match('/RouterOS/', $sysDescr)) {
                        $pppoe = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.9.9.150.1.1.1.0", 100000, 1));
                        $dhcpmk = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.6.1.0", 100000, 1));
                        $ccqmksnmp = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.1.3.1.10.2", 100000, 1));
                        $sinalmksnmp = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.1.1.1.4.6", 100000, 1));
                        $noisefloormksnmp = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.1.3.1.9.2", 100000, 1));
                        //$conexmikrotiksnmp = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.1.3.1.6.0", 100000, 1));
    
                    // Ubiquiti (POSSIVELMENTE)
                    }else if(preg_match('/Linux 2.6.32./', $sysDescr)) {
                        $ccqubnt = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.5.1.7.1", 100000, 1));
                        $sinalubnt = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.5.1.5.1", 100000, 1));
                        $noisefloorubnt = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.5.1.8.1", 100000, 1));
                        $airmaxcubnt = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.6.1.4.1", 100000, 1));
                        $airmaxqubnt = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.6.1.3.1", 100000, 1));
                        $conexubnt = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.5.1.15.1", 100000, 1));
                    
                    // Cambium Networks
                    }else if($sysName == "CambiumNetworks") { 
                        $cpucambium = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.2.1.64.0", 100000, 1)) / 10;
                        $ganhocambium = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.1.1.9.0", 100000, 1));
                        $potenciacambium = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.1.2.5.0", 100000, 1));
                        $associedcambium = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.1.2.10.0", 100000, 1));
                        $capacidadecambium = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.1.2.30.1.19.1", 100000, 1));
                        $qualidadecambium = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.1.2.30.1.20.1", 100000, 1));
    
                    // Mimosa
                    }else if($sysDescr == "Mimosa Firmware") {
                        $sinalmimosa = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.6.0", 100000, 1)) / 10;
                        $noisemimosa1 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.1", 100000, 1));
                        $noisemimosa2 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.3", 100000, 1));
                        $temperaturamimosa = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.1.8.0", 100000, 1)) / 10;
                        $txphy1 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.1", 100000, 1));
                        $txphy2 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.2", 100000, 1));
                        $txphy3 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.3", 100000, 1));
                        $txphy4 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.4", 100000, 1));
                        $rxphy1 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.1", 100000, 1));
                        $rxphy2 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.2", 100000, 1));
                        $rxphy3 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.3", 100000, 1));
                        $rxphy4 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.4", 100000, 1));
                        if($txphy1 && $txphy2 && $txphy3 && $txphy4 && $rxphy1 && $rxphy2 && $rxphy3 && $rxphy4) {
                            $mimosaTxPhyTotal = $txphy1 + $txphy2 + $txphy3 + $txphy4;
                            $mimosaRxPhyTotal = $rxphy1 + $rxphy2 + $rxphy3 + $rxphy4;
                            $mimosaTxMacTotal = porcentagem_xn(60, $mimosaTxPhyTotal);
                            $mimosaRxMacTotal = porcentagem_xn(60, $mimosaRxPhyTotal);
                        }
                        $wanmimosa = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.3.3.0", 100000, 1));
    
                    // OLT Fiberhome
                    }else if($sysDescr == "AN5516-01") { 
                        $totalONUfiberhome = array_sum(sanitizeSNMP(snmpwalk("{$ip}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.4.1.12", 100000, 1)));
                        $temperaturafiberhome = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.4.5.0", 100000, 1));
                    
                    // OLT Huawei
                    }else if($sysDescr == "Huawei Integrated Access Software") {
                        $totalonuponhuawei = array_sum(sanitizeSNMP(snmpwalk("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2011.6.128.1.1.2.21.1.16", 100000, 1)));
    
                    // ROUTER Huawei
                    }else if(preg_match('/Huawei Versatile Routing Platform/', $sysDescr)) {
                        $cpurouterhuawei = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2011.6.3.4.1.2.0.0.0", 100000, 1));
    
                    // Juniper
                    }else if(preg_match('/Juniper Networks/', $sysDescr)) {
                        $cpujuniper = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2636.3.1.13.1.8.9.1.0.0", 100000, 1));
                        $temperaturajuniper = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2636.3.1.13.1.7.9.1.0.0", 100000, 1));
                        $ramjuniper = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2636.3.1.13.1.11.9.1.0.0", 100000, 1));
    
                    // OLT V-Solution
                    }else if($sysDescr == "V1600D") {
                        $temperaturavsol = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.10.12.5.9.0", 100000, 1));
                        $cpuvsol = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.10.12.3.0", 100000, 1));
                        $ramvsol = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.10.12.4.0", 100000, 1));
                        $totalonuvsol = count(array_filter(snmpwalk("{$ip}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5", 100000, 1)));
                    }
    
                    $uptime = sanitizeSNMP(snmpget("{$ip}:{$porta}", $community, "1.3.6.1.2.1.1.3.0", 100000, 1));
    
                }else if($vsnmp == 2) {
                    $voltagem = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.3.8.0", 100000, 1)) / 10;
                    $temperatura = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.3.10.0", 100000, 1)) / 10;
                    $temperaturacpu = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.3.11.0", 100000, 1)) / 10;
                    $cpu = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2021.11.10.0", 100000, 1));
                    
                    // Mikrotik
                    if(preg_match('/RouterOS/', $sysDescr)) {
                        $pppoe = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.9.9.150.1.1.1.0", 100000, 1));
                        $dhcpmk = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.6.1.0", 100000, 1));
                        $ccqmksnmp = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.1.3.1.10.2", 100000, 1));
                        $sinalmksnmp = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.1.1.1.4.6", 100000, 1));
                        $noisefloormksnmp = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.1.3.1.9.2", 100000, 1));
                        //$conexmikrotiksnmp = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.14988.1.1.1.3.1.6.0", 100000, 1));
    
                    // Ubiquiti (POSSIVELMENTE)
                    /* Comentado por que até onde sei a Ubiquiti não trabalhar com SNMP v2c para essas oids
                    }else if(preg_match('/Linux 2.6.32./', $sysDescr)) {
                        $ccqubnt = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.5.1.7.1", 100000, 1));
                        $sinalubnt = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.5.1.5.1", 100000, 1));
                        $noisefloorubnt = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.5.1.8.1", 100000, 1));
                        $airmaxcubnt = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.6.1.4.1", 100000, 1));
                        $airmaxqubnt = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.6.1.3.1", 100000, 1));
                        $conexubnt = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.41112.1.4.5.1.15.1", 100000, 1));
                    */
                    // Cambium Networks
                    }else if($sysName == "CambiumNetworks") { 
                        $cpucambium = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.2.1.64.0", 100000, 1)) / 10;
                        $ganhocambium = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.1.1.9.0", 100000, 1));
                        $potenciacambium = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.1.2.5.0", 100000, 1));
                        $associedcambium = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.1.2.10.0", 100000, 1));
                        $capacidadecambium = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.1.2.30.1.19.1", 100000, 1));
                        $qualidadecambium = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.17713.21.1.2.30.1.20.1", 100000, 1));
                    
                    // Mimosa
                    }else if($sysDescr == "Mimosa Firmware") {
                        $sinalmimosa = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.6.0", 100000, 1)) / 10;
                        $noisemimosa1 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.1", 100000, 1));
                        $noisemimosa2 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.3", 100000, 1));
                        $temperaturamimosa = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.1.8.0", 100000, 1)) / 10;
                        $txphy1 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.1", 100000, 1));
                        $txphy2 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.2", 100000, 1));
                        $txphy3 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.3", 100000, 1));
                        $txphy4 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.4", 100000, 1));
                        $rxphy1 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.1", 100000, 1));
                        $rxphy2 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.2", 100000, 1));
                        $rxphy3 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.3", 100000, 1));
                        $rxphy4 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.4", 100000, 1));
                        if($txphy1 && $txphy2 && $txphy3 && $txphy4 && $rxphy1 && $rxphy2 && $rxphy3 && $rxphy4) {
                            $mimosaTxPhyTotal = $txphy1 + $txphy2 + $txphy3 + $txphy4;
                            $mimosaRxPhyTotal = $rxphy1 + $rxphy2 + $rxphy3 + $rxphy4;
                            $mimosaTxMacTotal = porcentagem_xn(60, $mimosaTxPhyTotal);
                            $mimosaRxMacTotal = porcentagem_xn(60, $mimosaRxPhyTotal);
                        }
                        $wanmimosa = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.43356.2.1.2.3.3.0", 100000, 1));
    
                    // OLT Fiberhome
                    }else if($sysDescr == "AN5516-01") { 
                        $totalONUfiberhome = array_sum(sanitizeSNMP(snmp2_walk("{$ip}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.4.1.12", 100000, 1)));
                        $temperaturafiberhome = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.4.5.0", 100000, 1));                  
                    
                    // OLT Huawei
                    }else if($sysDescr == "Huawei Integrated Access Software") {
                        $totalonuponhuawei = array_sum(sanitizeSNMP(snmp2_walk("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2011.6.128.1.1.2.21.1.16", 100000, 1)));
                    
                    // ROUTER Huawei
                    }else if(preg_match('/Huawei Versatile Routing Platform/', $sysDescr)) {
                        $cpurouterhuawei = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2011.6.3.4.1.2.0.0.0", 100000, 1));
    
                    // Juniper
                    }else if(preg_match('/Juniper Networks/', $sysDescr)) {
                        $cpujuniper = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2636.3.1.13.1.8.9.1.0.0", 100000, 1));
                        $temperaturajuniper = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2636.3.1.13.1.7.9.1.0.0", 100000, 1));
                        $ramjuniper = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2636.3.1.13.1.11.9.1.0.0", 100000, 1));
    
                    // OLT V-Solution
                    }else if($sysDescr == "V1600D") {
                        $temperaturavsol = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.10.12.5.9.0", 100000, 1));
                        $cpuvsol = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.10.12.3.0", 100000, 1));
                        $ramvsol = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.10.12.4.0", 100000, 1));
                        $totalonuvsol = count(array_filter(snmp2_walk("{$ip}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5", 100000, 1)));
                    }
    
                    $uptime = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $community, "1.3.6.1.2.1.1.3.0", 100000, 1));

                }else if($vsnmp == 3) {
                    $voltagem = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.3.8.0", 100000, 1)) / 10;
                    $temperatura = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.3.10.0", 100000, 1)) / 10;
                    $temperaturacpu = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.3.11.0", 100000, 1)) / 10;
                    $cpu = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.2021.11.10.0", 100000, 1));
                    
                    // Mikrotik
                    if(preg_match('/RouterOS/', $sysDescr)) {
                        $pppoe = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.9.9.150.1.1.1.0", 100000, 1));
                        $dhcpmk = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.6.1.0", 100000, 1));
                        $ccqmksnmp = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.1.3.1.10.2", 100000, 1));
                        $sinalmksnmp = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.1.1.1.4.6", 100000, 1));
                        $noisefloormksnmp = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.1.3.1.9.2", 100000, 1));
                        //$conexmikrotiksnmp = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.14988.1.1.1.3.1.6.0", 100000, 1));
    
                    // Ubiquiti (POSSIVELMENTE)
                    }else if(preg_match('/Linux 2.6.32./', $sysDescr)) {
                        $ccqubnt = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.41112.1.4.5.1.7.1", 100000, 1));
                        $sinalubnt = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.41112.1.4.5.1.5.1", 100000, 1));
                        $noisefloorubnt = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.41112.1.4.5.1.8.1", 100000, 1));
                        $airmaxcubnt = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.41112.1.4.6.1.4.1", 100000, 1));
                        $airmaxqubnt = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.41112.1.4.6.1.3.1", 100000, 1));
                        $conexubnt = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.41112.1.4.5.1.15.1", 100000, 1));
    
                    // Cambium Networks
                    }else if($sysName == "CambiumNetworks") { 
                        $cpucambium = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.17713.21.2.1.64.0", 100000, 1)) / 10;
                        $ganhocambium = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.17713.21.1.1.9.0", 100000, 1));
                        $potenciacambium = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.17713.21.1.2.5.0", 100000, 1));
                        $associedcambium = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.17713.21.1.2.10.0", 100000, 1));
                        $capacidadecambium = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.17713.21.1.2.30.1.19.1", 100000, 1));
                        $qualidadecambium = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.17713.21.1.2.30.1.20.1", 100000, 1));
                    
                    // Mimosa
                    }else if($sysDescr == "Mimosa Firmware") {
                        $sinalmimosa = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.6.0", 100000, 1)) / 10;
                        $noisemimosa1 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.1", 100000, 1));
                        $noisemimosa2 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.1.1.4.3", 100000, 1));
                        $temperaturamimosa = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.1.8.0", 100000, 1)) / 10;
                        $txphy1 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.1", 100000, 1));
                        $txphy2 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.2", 100000, 1));
                        $txphy3 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.3", 100000, 1));
                        $txphy4 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.2.4", 100000, 1));
                        $rxphy1 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.1", 100000, 1));
                        $rxphy2 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.2", 100000, 1));
                        $rxphy3 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.3", 100000, 1));
                        $rxphy4 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.6.2.1.5.4", 100000, 1));
                        if($txphy1 && $txphy2 && $txphy3 && $txphy4 && $rxphy1 && $rxphy2 && $rxphy3 && $rxphy4) {
                            $mimosaTxPhyTotal = $txphy1 + $txphy2 + $txphy3 + $txphy4;
                            $mimosaRxPhyTotal = $rxphy1 + $rxphy2 + $rxphy3 + $rxphy4;
                            $mimosaTxMacTotal = porcentagem_xn(60, $mimosaTxPhyTotal);
                            $mimosaRxMacTotal = porcentagem_xn(60, $mimosaRxPhyTotal);
                        }
                        $wanmimosa = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.43356.2.1.2.3.3.0", 100000, 1));
    
                    // OLT Fiberhome
                    }else if($sysDescr == "AN5516-01") { 
                        $totalONUfiberhome = array_sum(sanitizeSNMP(snmp2_walk("{$ip}:{$porta}", $community, "1.3.6.1.4.1.5875.800.3.9.3.4.1.12", 100000, 1)));
                        $temperaturafiberhome = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.5875.800.3.9.4.5.0", 100000, 1));                  
                    
                    // OLT Huawei
                    }else if($sysDescr == "Huawei Integrated Access Software") {
                        $totalonuponhuawei = array_sum(sanitizeSNMP(snmp2_walk("{$ip}:{$porta}", $community, "1.3.6.1.4.1.2011.6.128.1.1.2.21.1.16", 100000, 1)));
                    
                    // ROUTER Huawei
                    }else if(preg_match('/Huawei Versatile Routing Platform/', $sysDescr)) {
                        $cpurouterhuawei = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.2011.6.3.4.1.2.0.0.0", 100000, 1));
    
                    // Juniper
                    }else if(preg_match('/Juniper Networks/', $sysDescr)) {
                        $cpujuniper = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.2636.3.1.13.1.8.9.1.0.0", 100000, 1));
                        $temperaturajuniper = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.2636.3.1.13.1.7.9.1.0.0", 100000, 1));
                        $ramjuniper = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.2636.3.1.13.1.11.9.1.0.0", 100000, 1));
    
                    // OLT V-Solution
                    }else if($sysDescr == "V1600D") {
                        $temperaturavsol = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.37950.1.1.5.10.12.5.9.0", 100000, 1));
                        $cpuvsol = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.37950.1.1.5.10.12.3.0", 100000, 1));
                        $ramvsol = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.37950.1.1.5.10.12.4.0", 100000, 1));
                        $totalonuvsol = count(array_filter(snmp2_walk("{$ip}:{$porta}", $community, "1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5", 100000, 1)));
                    }
    
                    $uptime = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $community, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.2.1.1.3.0", 100000, 1));
                }

                $aux = explode(')', $uptime); 
                $aux2 = explode('.', $aux[1]); 
                $uptime = $aux2[0];

                $valor = ""; $valor2 = "";

                if(isset($voltagem) && $voltagem != "") {
                    $tag = "voltagem";
                    $nome = "Voltagem";
                    addSensor($id, $valor, $voltagem, $valor2, $tag, $nome, 6);
                }
                if(isset($temperatura) && $temperatura != "") {
                    $tag = "temperatura";
                    $nome = "Temperatura";
                    addSensor($id, $valor, $temperatura, $valor2, $tag, $nome, 6);
                }
                if(isset($temperaturacpu) && $temperaturacpu != "") {
                    $tag = "temperaturacpu";
                    $nome = "Temperatura CPU";
                    addSensor($id, $valor, $temperaturacpu, $valor2, $tag, $nome, 6);
                }
                if(isset($cpu) && $cpu != "") {
                    $tag = "cpusnmp";
                    $nome = "Carga CPU";
                    addSensor($id, $valor, $cpu, $valor2, $tag, $nome, 6);
                }
                if(isset($uptime) && $uptime != "") {
                    $tag = "uptime";
                    $nome = "Uptime";
                    addSensor($id, $valor, $uptime, $valor2, $tag, $nome, 6);
                }
    
                if(isset($pppoe) && $pppoe != "" && $pppoe >= 1) {
                    $tag = "pppoe";
                    $nome = "Conexões PPPoE";
                    addSensor($id, $valor, $pppoe, $valor2, $tag, $nome, 6);
                }
                if(isset($dhcpmk) && $dhcpmk != "" && $dhcpmk >= 1) {
                    $tag = "dhcpmk";
                    $nome = "Conexões DHCP";
                    addSensor($id, $valor, $dhcpmk, $valor2, $tag, $nome, 6);
                }
                if(isset($ccqmksnmp) && $ccqmksnmp != "") {
                    $tag = "ccqmksnmp";
                    $nome = "Overall CCQ";
                    addSensor($id, $valor, $ccqmksnmp, $valor2, $tag, $nome, 6);
                }
                if(isset($sinalmksnmp) && $sinalmksnmp != "") {
                    $tag = "sinalmksnmp";
                    $nome = "Sinal";
                    addSensor($id, $valor, $sinalmksnmp, $valor2, $tag, $nome, 6);
                }
                if(isset($noisefloormksnmp) && $noisefloormksnmp != "") {
                    $tag = "sinalmksnmp";
                    $nome = "Noise Floor";
                    addSensor($id, $valor, $noisefloormksnmp, $valor2, $tag, $nome, 6);
                }
                if(isset($conexmikrotiksnmp) && $conexmikrotiksnmp != "" && $conexmikrotiksnmp >= 1) {
                    $tag = "conexmikrotiksnmp";
                    $nome = "Conexões WAN";
                    addSensor($id, $valor, $conexmikrotiksnmp, $valor2, $tag, $nome, 6);
                }
                
                if(isset($ccqubnt) && $ccqubnt != "") {
                    $tag = "ccqubntsnmp";
                    $nome = "CCQ";
                    addSensor($id, $valor, $ccqubnt, $valor2, $tag, $nome, 6);
                }
                if(isset($sinalubnt) && $sinalubnt != "") {
                    $tag = "sinalubntsnmp";
                    $nome = "Sinal";
                    addSensor($id, $valor, $sinalubnt, $valor2, $tag, $nome, 6);
                }
                if(isset($noisefloorubnt) && $noisefloorubnt != "") {
                    $tag = "noisefloorubntsnmp";
                    $nome = "Noise Floor";
                    addSensor($id, $valor, $noisefloorubnt, $valor2, $tag, $nome, 6);
                }
                if(isset($airmaxcubnt) && $airmaxcubnt > 1) {
                    $tag = "airmaxcubntsnmp";
                    $nome = "Capacidade Airmax";
                    addSensor($id, $valor, $airmaxcubnt, $valor2, $tag, $nome, 6);
                }
                if(isset($airmaxqubnt) && $airmaxqubnt > 1) {
                    $tag = "airmaxqubntsnmp";
                    $nome = "Qualidade Airmax";
                    addSensor($id, $valor, $airmaxqubnt, $valor2, $tag, $nome, 6);
                }
                if(isset($conexubnt) && $conexubnt >= 1) {
                    $tag = "conexubntsnmp";
                    $nome = "Conexões WAN";
                    addSensor($id, $valor, $conexubnt, $valor2, $tag, $nome, 6);
                }
    
                if(isset($cpucambium) && $cpucambium != "") {
                    $tag = "cpucambium";
                    $nome = "Carga CPU";
                    addSensor($id, $valor, $cpucambium, $valor2, $tag, $nome, 6);
                }
                if(isset($ganhocambium) && $ganhocambium != "") {
                    $tag = "ganhocambium";
                    $nome = "Ganho efetivo";
                    addSensor($id, $valor, $ganhocambium, $valor2, $tag, $nome, 6);
                }
                if(isset($potenciacambium) && $potenciacambium != "") {
                    $tag = "potenciacambium";
                    $nome = "Potência atual";
                    addSensor($id, $valor, $potenciacambium, $valor2, $tag, $nome, 6);
                }
                if(isset($associedcambium) && $associedcambium != "") {
                    $tag = "associedcambium";
                    $nome = "Conexões WAN";
                    addSensor($id, $valor, $associedcambium, $valor2, $tag, $nome, 6);
                }
                if(isset($capacidadecambium) && $capacidadecambium != "") {
                    $tag = "capacidadecambium";
                    $nome = "Capacidade";
                    addSensor($id, $valor, $capacidadecambium, $valor2, $tag, $nome, 6);
                }
                if(isset($qualidadecambium) && $qualidadecambium != "") {
                    $tag = "qualidadecambium";
                    $nome = "Qualidade";
                    addSensor($id, $valor, $qualidadecambium, $valor2, $tag, $nome, 6);
                }
    
                if(isset($sinalmimosa) && $sinalmimosa != "") {
                    $tag = "sinalmimosa";
                    $nome = "Sinal";
                    addSensor($id, $valor, $sinalmimosa, $valor2, $tag, $nome, 6);
                }
                if(isset($noisemimosa1) && $noisemimosa1 != "") {
                    $tag = "noisemimosa";
                    $nome = "Noise Floor";
                    addSensor($id, $valor, $noisemimosa1, $valor2, $tag, $nome, 6);
                }
                if(isset($temperaturamimosa) && $temperaturamimosa != "") {
                    $tag = "temperaturamimosa";
                    $nome = "Temperatura CPU";
                    addSensor($id, $valor, $temperaturamimosa, $valor2, $tag, $nome, 6);
                }
                if(isset($mimosaTxMacTotal) && $mimosaTxMacTotal != "") {
                    $tag = "macmimosa";
                    $nome = "MAC Speed";
                    addSensor($id, $valor, $mimosaTxMacTotal, $valor2, $tag, $nome, 6);
                }
                if(isset($mimosaTxPhyTotal) && $mimosaTxPhyTotal != "") {
                    $tag = "phymimosa";
                    $nome = "PHY Speed";
                    addSensor($id, $valor, $mimosaTxPhyTotal, $valor2, $tag, $nome, 6);
                }
                if(isset($wanmimosa) && $wanmimosa == 1) {
                    $tag = "wanmimosa";
                    $nome = "Conexão WAN";
                    addSensor($id, $valor, $wanmimosa, $valor2, $tag, $nome, 6);
                }
    
                if(isset($totalONUfiberhome) && $totalONUfiberhome >= 1) {
                    $tag = "totalonuponfiberhome";
                    $nome = "Total ONUs";
                    addSensor($id, $valor, $totalONUfiberhome, $valor2, $tag, $nome, 6);
                }
                if(isset($temperaturafiberhome) && $temperaturafiberhome != "") {
                    $tag = "temperaturafiberhome";
                    $nome = "Temperatura";
                    addSensor($id, $valor, $temperaturafiberhome, $valor2, $tag, $nome, 6);
                }
    
                if(isset($totalonuponhuawei) && $totalonuponhuawei >= 1) {
                    $tag = "totalonuponhuawei";
                    $nome = "Total ONUs";
                    addSensor($id, $valor, $totalonuponhuawei, $valor2, $tag, $nome, 6);
                }
                if(isset($cpurouterhuawei) && $cpurouterhuawei != "") {
                    $tag = "cpurouterhuawei";
                    $nome = "Carga CPU";
                    addSensor($id, $valor, $cpurouterhuawei, $valor2, $tag, $nome, 6);
                }
    
                if(isset($cpujuniper) && $cpujuniper != "") {
                    $tag = "cpujuniper";
                    $nome = "Carga CPU";
                    addSensor($id, $valor, $cpujuniper, $valor2, $tag, $nome, 6);
                }
                if(isset($temperaturajuniper) && $temperaturajuniper != "") {
                    $tag = "temperaturajuniper";
                    $nome = "Temperatura";
                    addSensor($id, $valor, $temperaturajuniper, $valor2, $tag, $nome, 6);
                }
                if(isset($ramjuniper) && $ramjuniper != "") {
                    $tag = "ramjuniper";
                    $nome = "Memória RAM";
                    addSensor($id, $valor, $ramjuniper, $valor2, $tag, $nome, 6);
                }
    
                if(isset($temperaturavsol) && $temperaturavsol != "") {
                    $tag = "temperaturavsol";
                    $nome = "Temperatura";
                    addSensor($id, $valor, $temperaturavsol, $valor2, $tag, $nome, 6);
                }
                if(isset($cpuvsol) && $cpuvsol != "") {
                    $tag = "cpuvsol";
                    $nome = "Carga CPU";
                    addSensor($id, $valor, $cpuvsol, $valor2, $tag, $nome, 6);
                }
                if(isset($ramvsol) && $ramvsol != "") {
                    $tag = "ramvsol";
                    $nome = "Memória RAM";
                    addSensor($id, $valor, $ramvsol, $valor2, $tag, $nome, 6);
                }
                if(isset($totalonuvsol) && $totalonuvsol >= 1) {
                    $tag = "totalonuvsol";
                    $nome = "Total ONUs";
                    addSensor($id, $valor, $totalonuvsol, $valor2, $tag, $nome, 6);
                }
            }
        }
    }
}
//echo "\n" . $inicio . "\n" . date("H:i:s") . "\n";

if($Grupo['repetir'] == 1) {
    mysqli_query($db, "UPDATE GrupoMonitor SET status = '0', autoscan = '0' WHERE id = '".$Grupo['id']."';");
}else {
    mysqli_query($db, "UPDATE GrupoMonitor SET status = '0' WHERE id = '".$Grupo['id']."';");
}

exec("echo '|".$Grupo['autoscan']."|0||".$Grupo['Nome']."|' > /var/www/html/ram/dispositivos/grupos/".$Grupo['id']);
mysqli_close($db);
exit(0);
?>