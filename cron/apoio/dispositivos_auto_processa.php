#!/usr/bin/php
<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

$id_disp = 0;
if(isset($_GET["id"])) { $id_disp = $_GET["id"]; }

include("/var/www/html/cron/apoio/conexao.php");

function sanitizeString($string) {
    $what = array( 'rtt min/avg/max/mdev =', 'ms', ' ' );
    $by   = array( '', '', '/' );
    return str_replace($what, $by, $string);
}

function sanitizeSNMP($string) {
    $what = array( 'Counter64: ', 'Counter32: ', 'INTEGER: ', 'Counter32: ', 'STRING: ', 'STRING:', 'Gauge32: ', ' No Such Object available on this agent at this OID', 'No Such Object available on this agent at this OID', 'Hex-', 'Hex-STRING: ', '"' );
    $by   = array( '', '', '', '', '', '', '', '', '', '', '', '' );
    return str_replace($what, $by, $string);
}

function verifica_tag($tag, $id_disp) {
    $pesquisa_sensor = mysqli_query($GLOBALS['db'], "SELECT id FROM Sensores WHERE idDispositivo = '".$id_disp."' AND tag = '".$tag."'");
    if(mysqli_num_rows($pesquisa_sensor)) { return true; }else { return false; }
}

function verifica_interface($index, $id_disp) {
    $pesquisa_sensor = mysqli_query($GLOBALS['db'], "SELECT id FROM Sensores WHERE idDispositivo = '".$id_disp."' AND tag = 'trafegosnmp' AND valor = '".$index."'");
    if(mysqli_num_rows($pesquisa_sensor)) { return true; }else { return false; }
}

function processa_tag($tag, $nome, $oid, $divisor, $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta) {
    $retorno = snmpget_test($oid, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta);
    // Se a conexão funcionou verificaremos se o oid SNMP existe!
    if($retorno) {
        $exp = explode(': ', $retorno);
        if(isset($exp['1'])) {
            if($divisor > 1) {
                $valor1 = $exp['1'] / $divisor;
            }else {
                $valor1 = $exp['1'];
            }
            add_sensor($id_disp, $tag, $nome, $valor1);
        }
    }
}

function snmpget_test($oid, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $host, $porta) {
    if($vsnmp == 1) {
        $cmd = "snmpget -Ost -r 1 -t 1 -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oid . " 2>/dev/null";
    }else if($vsnmp == 2) {
        $cmd = "snmpget -Ost -r 1 -t 1 -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oid . " 2>/dev/null";
    }else if($vsnmp == 3) {
        $cmd = "snmpget -Ost -r 1 -t 1 -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oid . " 2>/dev/null";
    }

    $stdno = 0;
    $retorno = array();
    exec ($cmd, $retorno, $stdno);
    $stdno = (int)$stdno;

    // Verifica se a conexão SNMP funcionou
    if(!$stdno) { return $retorno['0']; }else { return false; }
}

function snmpget_test_interface($index, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $host, $porta) {
    $oids1 = array();
    $oids2 = array();
    $oids3 = array();
    $oids1[] = ".1.3.6.1.2.1.2.2.1.8." . $index;                   // ifOperStatus
    $oids3[] = ".1.3.6.1.2.1.2.2.1.2." . $index;                   // ifDescr
    $oids3[] = ".1.3.6.1.2.1.31.1.1.1.18." . $index;               // ifAlias
    $oids3[] = ".1.3.6.1.2.1.2.2.1.6." . $index;                   // ifPhysAddress
    $oids3[] = ".1.3.6.1.2.1.31.1.1.1.1." . $index;                // ifName
    $oids2[] = ".1.3.6.1.2.1.2.2.1.5." . $index;                   // ifSpeed
    if($vsnmp != 1) {
        $oids2[] = ".1.3.6.1.2.1.31.1.1.1.15." . $index;           // ifHighSpeed
    }
    $oidspart1 = implode(' ', $oids1);
    $oidspart2 = implode(' ', $oids2);
    $oidspart3 = implode(' ', $oids3);

    if($vsnmp == 1) {
        $cmd1 = "snmpget -Ost -r 1 -t 1 -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
        $cmd2 = "snmpget -Ost -r 1 -t 1 -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart2 . " 2>/dev/null";
        $cmd3 = "snmpget -Ost -r 1 -t 1 -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart3 . " 2>/dev/null";
    }else if($vsnmp == 2) {
        $cmd1 = "snmpget -Ost -r 1 -t 1 -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
        $cmd2 = "snmpget -Ost -r 1 -t 1 -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart2 . " 2>/dev/null";
        $cmd3 = "snmpget -Ost -r 1 -t 1 -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart3 . " 2>/dev/null";
    }else if($vsnmp == 3) {
        $cmd1 = "snmpget -Ost -r 1 -t 1 -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart1 . " 2>/dev/null";
        $cmd2 = "snmpget -Ost -r 1 -t 1 -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart2 . " 2>/dev/null";
        $cmd3 = "snmpget -Ost -r 1 -t 1 -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart3 . " 2>/dev/null";
    }

    $stdno = 0;
    $analysis1 = array();
    exec($cmd1, $analysis1, $stdno);
    $stdno = (int)$stdno;

    // Verifica se a conexão SNMP funcionou
    if(!$stdno) {
        $a = explode('= ', $analysis1[0]);
        $status = sanitizeSNMP($a[1]);
        $analysis2 = array();
        exec($cmd2, $analysis2);
        $b = explode('= ', $analysis2[0]);
        $ifSpeed = sanitizeSNMP($b[1]) / 1000000;
        if($vsnmp != 1) {
            $c = explode('= ', $analysis2[1]);
            $ifHighSpeed = sanitizeSNMP($c[1]);
            if(!$ifHighSpeed) { $ifHighSpeed = $ifSpeed; }
        }else {
            $ifHighSpeed = $ifSpeed;
        }
        $analysis3 = array();
        exec($cmd3, $analysis3);
        if(count($analysis3) == 4) {
            $d = explode('= ', $analysis3[0]);
            $ifDescr = sanitizeSNMP($d[1]);
            $e = explode('= ', $analysis3[1]);
            $ifAlias = sanitizeSNMP($e[1]);
            $f = explode('= ', $analysis3[2]);
            $ifPhysAddress = sanitizeSNMP($f[1]);
            $g = explode('= ', $analysis3[3]);
            $ifName = sanitizeSNMP($g[1]);
        }else {
            $d = explode('= ', $analysis3[0]);
            $e = explode('= ', $analysis3[1]);
            $f = explode('= ', $analysis3[2]);
            $g = explode('= ', $analysis3[3]);
            if(preg_match('/ifDescr./', $d[0])) { $ifDescr = sanitizeSNMP($d[1]); }
            if(preg_match('/ifAlias./', $d[0])) { $ifAlias = sanitizeSNMP($d[1]); }
            if(preg_match('/ifPhysAddress./', $d[0])) { $ifPhysAddress = sanitizeSNMP($d[1]); }
            if(preg_match('/ifName./', $d[0])) { $ifName = sanitizeSNMP($d[1]); }
            if(preg_match('/ifDescr./', $e[0])) { $ifDescr = sanitizeSNMP($e[1]); }
            if(preg_match('/ifAlias./', $e[0])) { $ifAlias = sanitizeSNMP($e[1]); }
            if(preg_match('/ifPhysAddress./', $e[0])) { $ifPhysAddress = sanitizeSNMP($e[1]); }
            if(preg_match('/ifName./', $e[0])) { $ifName = sanitizeSNMP($e[1]); }
            if(preg_match('/ifDescr./', $f[0])) { $ifDescr = sanitizeSNMP($f[1]); }
            if(preg_match('/ifAlias./', $f[0])) { $ifAlias = sanitizeSNMP($f[1]); }
            if(preg_match('/ifPhysAddress./', $f[0])) { $ifPhysAddress = sanitizeSNMP($f[1]); }
            if(preg_match('/ifName./', $f[0])) { $ifName = sanitizeSNMP($f[1]); }
        }
        // Verifica se a interface está operante
        // Syntaxe enumeration (1-up, 2-down, 3-testing, 4-unknown, 5-dormant, 6-notPresent, 7-lowerLayerDown)
        if($status == 1) {
            return array($ifHighSpeed, $ifDescr, $ifAlias, $ifPhysAddress, $ifName);
        }else {
            return false;
        }
    }
}

function add_sensor($id_disp, $tag, $nome, $valor1) {
    $resultSensores = mysqli_query($GLOBALS['db'], "SELECT ordem FROM Sensores WHERE idDispositivo = '".$id_disp."' ORDER BY ordem DESC LIMIT 1;");
    $UltimoSensor = mysqli_fetch_array($resultSensores);
    $ordem = $UltimoSensor['ordem'] + 1;
    mysqli_query($GLOBALS['db'], "INSERT INTO Sensores (tag, nome, valor1, adicionais, idDispositivo, statusAlert, cronograma, ordem) VALUES ('".$tag."', '".$nome."', '".$valor1."', '2', '".$id_disp."', '6', '1m', '".$ordem."')");
    $novoid = mysqli_insert_id($GLOBALS['db']);
    $Pesquisa = mysqli_query($GLOBALS['db'], "SELECT id, ok, total FROM ResumoSensores ORDER BY id DESC LIMIT 1;");
    $dat = mysqli_fetch_array($Pesquisa);
    $ok = $dat['ok'] + 1;
    $total = $dat['total'] + 1;
    mysqli_query($GLOBALS['db'], "UPDATE ResumoSensores SET ok = '".$ok."', total = '".$total."' WHERE id = $dat[id];");
    if($novoid) { touch_ram($novoid); }
}

function add_sensor_interface($id_disp, $index, $nome, $ifName, $ifDescr, $ifAlias, $ifPhysAddress, $ifHighSpeed) {
    $resultSensores = mysqli_query($GLOBALS['db'], "SELECT ordem FROM Sensores WHERE idDispositivo = '".$id_disp."' ORDER BY ordem DESC LIMIT 1;");
    $UltimoSensor = mysqli_fetch_array($resultSensores);
    $ordem = $UltimoSensor['ordem'] + 1;
    mysqli_query($GLOBALS['db'], "INSERT INTO Sensores (tag, nome, nomeReal, alias, descr, mac, ifSpeed, valor, idDispositivo, statusAlert, ordem, cronograma, adicionais) VALUES ('trafegosnmp', '".$nome."', '".$ifName."', '".$ifAlias."', '".$ifDescr."', '".$ifPhysAddress."', '".$ifHighSpeed."', '".$index."', ".$id_disp.", '5', '".$ordem."', '1m', '2')");
    $novoid = mysqli_insert_id($GLOBALS['db']);
    $Pesquisa = mysqli_query($GLOBALS['db'], "SELECT id, novos, total FROM ResumoSensores ORDER BY id DESC LIMIT 1;");
    $dat = mysqli_fetch_array($Pesquisa);
    $novos = $dat['novos'] + 1;
    $total = $dat['total'] + 1;
    mysqli_query($GLOBALS['db'], "UPDATE ResumoSensores SET novos = '".$novos."', total = '".$total."' WHERE id = $dat[id];");
    if($novoid) { touch_ram($novoid); }
}

function touch_ram($novoid) {
    $resultSensores = mysqli_query($GLOBALS['db'], "SELECT tag, nome, banco, unidade, display, statusAlert, valor1, valor2, valor3 FROM Sensores WHERE id = $novoid;");
    $detalhes = mysqli_fetch_array($resultSensores);
    $tag = $detalhes['tag'];
    $nome = $detalhes['nome'];
    $banco = $detalhes['banco'];
    $unidade = $detalhes['unidade'];
    $display = $detalhes['display'];
    $statusAlert = $detalhes['statusAlert'];
    $valor1 = $detalhes['valor1'];
    $valor2 = $detalhes['valor2'];
    $valor3 = $detalhes['valor3'];
    exec("echo '|$statusAlert|$valor1|$valor2|$valor3|$tag|$nome|$banco|$unidade|$display|' > /var/www/html/ram/dispositivos/sensores/$novoid");
}

function excluir_sensor($id) {
    $sel = mysqli_query($GLOBALS['db'], "SELECT statusAlert FROM Sensores WHERE id = ".$id.";");
    $pesq = mysqli_fetch_array($sel);
    if($pesq['statusAlert'] == 1) {
        $status = "off";
    }else if($pesq['statusAlert'] == 2) {
        $status = "pausado";
    }else if($pesq['statusAlert'] == 3 || $pesq['statusAlert'] == 7) {
        $status = "alerta";
    }else if($pesq['statusAlert'] == 4) {
        $status = "erro";
    }else if($pesq['statusAlert'] == 5) {
        $status = "novos";
    }else if($pesq['statusAlert'] == 6) {
        $status = "ok";
    }
    $Pesquisa = mysqli_query($GLOBALS['db'], "SELECT id, ".$status.", total FROM ResumoSensores ORDER BY id DESC LIMIT 1;");
    $dat = mysqli_fetch_array($Pesquisa);
    $atualiza = $dat[$status] - 1;
    $total = $dat['total'] - 1;
    mysqli_query($GLOBALS['db'], "UPDATE ResumoSensores SET ".$status." = '".$atualiza."', total = '".$total."' WHERE id = $dat[id];");
    mysqli_query($GLOBALS['db'], "DELETE FROM log2h WHERE idSensor = ".$id.";");
    mysqli_query($GLOBALS['db'], "DELETE FROM log24h WHERE idSensor = ".$id.";");
    mysqli_query($GLOBALS['db'], "DELETE FROM log30d WHERE idSensor = ".$id.";");
    mysqli_query($GLOBALS['db'], "DELETE FROM log1a WHERE idSensor = ".$id.";");
    mysqli_query($GLOBALS['db'], "DELETE FROM Logalertas WHERE idSensor = ".$id.";");
    mysqli_query($GLOBALS['db'], "DELETE FROM Sensores WHERE id=".$id."");
    exec("rm -fr /var/www/html/ram/dispositivos/sensores/" . $id);
}

function processa_disp($id_disp) {
    $PesquisaDisp = mysqli_query($GLOBALS['db'], "SELECT ip, HerdarPai, idGrupoPai, ativa_auto, snmpcomunit, versaosnmp_d, nivelsegsnmp_d, protocoloauthsnmp_d, protocolocripsnmp_d, authsnmp_d, criptosnmp_d, portasnmp_d FROM Dispositivos WHERE ativa_auto >= 1 AND id = '".$id_disp."';");
    $resDisp = mysqli_fetch_array($PesquisaDisp);
    $IP = $resDisp['ip'];

    if($resDisp['HerdarPai'] == 1) {
        $PesquisaGrupoPai = mysqli_query($GLOBALS['db'], "SELECT comunidadesnmp_g, versaosnmp_g, nivelsegsnmp_g, protocoloauthsnmp_g, protocolocripsnmp_g, authsnmp_g, criptosnmp_g, portasnmp_g FROM GrupoMonitor WHERE id = '".$resDisp['idGrupoPai']."' AND ativasnmp = '2'");
        if(mysqli_num_rows($PesquisaGrupoPai) == 1) {
            $resGpo = mysqli_fetch_array($PesquisaGrupoPai);
            $community = $resGpo['comunidadesnmp_g'];
            $porta = $resGpo['portasnmp_g'];
            $vsnmp = $resGpo['versaosnmp_g'];
            $nivelsegsnmp = $resDisp['nivelsegsnmp_g'];
            $protocoloauthsnmp = $resDisp['protocoloauthsnmp_g'];
            $protocolocripsnmp = $resDisp['protocolocripsnmp_g'];
            $authsnmp = $resDisp['authsnmp_g'];
            $criptosnmp = $resDisp['criptosnmp_g'];
        }else {
            $PesquisaSys = mysqli_query($GLOBALS['db'], "SELECT snmppadrao, portasnmppadrao, versaosnmppadrao, nivelsegsnmppadrao, protocoloauthsnmppadrao, protocolocripsnmppadrao, authsnmppadrao, criptosnmppadrao FROM system");
            $resSys = mysqli_fetch_array($PesquisaSys);
            $community = $resSys['snmppadrao'];
            $porta = $resSys['portasnmppadrao'];
            $vsnmp = $resSys['versaosnmppadrao'];
            $nivelsegsnmp = $resSys['nivelsegsnmppadrao'];
            $protocoloauthsnmp = $resSys['protocoloauthsnmppadrao'];
            $protocolocripsnmp = $resSys['protocolocripsnmppadrao'];
            $authsnmp = $resSys['authsnmppadrao'];
            $criptosnmp = $resSys['criptosnmppadrao'];
        }

    }else if($resDisp['HerdarPai'] == 2) {
        $community = $resDisp['snmpcomunit'];
        $porta = $resDisp['portasnmp_d'];
        $vsnmp = $resDisp['versaosnmp_d'];
        $nivelsegsnmp = $resDisp['nivelsegsnmp_d'];
        $protocoloauthsnmp = $resDisp['protocoloauthsnmp_d'];
        $protocolocripsnmp = $resDisp['protocolocripsnmp_d'];
        $authsnmp = $resDisp['authsnmp_d'];
        $criptosnmp = $resDisp['criptosnmp_d'];
    }

    // Se ativa_auto foi igual a 1 ou 2 precisaremos criar novos sensores dinamicamente!
    if($resDisp['ativa_auto']) {
        $pesquisa_tags = mysqli_query($GLOBALS['db'], "SELECT tag FROM dispositivos_auto WHERE idDisp = '".$id_disp."'");
        if(mysqli_num_rows($pesquisa_tags)) {
            $novoid = 0;
            $teste_retorno = array();
            $teste_retorno = snmpget_test('1.3.6.1.2.1.1.3.0', $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta);
            while($tags = mysqli_fetch_array($pesquisa_tags)) {
                // Verifica se o sensor já existe ou não
                if(!verifica_tag($tags['tag'], $id_disp)) {
                    if($tags['tag'] == 'ping') {
                        $auxping = explode(':', $IP);
                        if(isset($auxping['1'])) {
                            $pingexec = sanitizeString(exec("/bin/ping6 -c 2 -s 32 -w 2 -i 1 ".$IP." | tail -2 | tr -d '\n' | grep -v exceeded | grep -v errors | grep -v pipe"));
                        }else {
                            $pingexec = sanitizeString(exec("/bin/ping -c 2 -s 32 -w 2 -i 1 ".$IP." | tail -2 | tr -d '\n' | grep -v exceeded | grep -v errors | grep -v pipe"));
                        }
                        $packetLoss = '';
                        $aux = explode('/', $pingexec);
                        $packetloss = $aux['5'];
                        $ping = $aux['10'];
                        if($ping && $packetLoss != '100%') {
                            $resultSensores = mysqli_query($GLOBALS['db'], "SELECT ordem FROM Sensores WHERE idDispositivo = '".$id_disp."' ORDER BY ordem DESC LIMIT 1;");
                            $UltimoSensor = mysqli_fetch_array($resultSensores);
                            $ordem = $UltimoSensor['ordem'] + 1;
                            mysqli_query($GLOBALS['db'], "INSERT INTO Sensores (tag, nome, valor, valor1, valor2, adicionais, idDispositivo, statusAlert, cronograma, ordem) VALUES ('ping', 'Ping', '".$IP."', '".$ping."', '".$packetloss."', '32-20-20--2', '".$id_disp."', '6', '1m', '".$ordem."')");
                            $novoid = mysqli_insert_id($GLOBALS['db']);
                            $Pesquisa = mysqli_query($GLOBALS['db'], "SELECT id, ok, total FROM ResumoSensores ORDER BY id DESC LIMIT 1;");
                            $dat = mysqli_fetch_array($Pesquisa);
                            $ok = $dat['ok'] + 1;
                            $total = $dat['total'] + 1;
                            mysqli_query($GLOBALS['db'], "UPDATE ResumoSensores SET ok = '".$ok."', total = '".$total."' WHERE id = $dat[id];");
                            if($novoid) { touch_ram($novoid); }
                        }
                    }
                }

                // Se a conexão funciona verificaremos os modelos de sensor SNMP
                if($teste_retorno) {
                    if($tags['tag'] == 'temperatura') {
                        if(!verifica_tag('temperatura', $id_disp)) { processa_tag('temperatura', 'Temperatura', '1.3.6.1.4.1.14988.1.1.3.10.0', '10', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('temperaturacpu', $id_disp)) { processa_tag('temperaturacpu', 'Temperatura CPU', '1.3.6.1.4.1.14988.1.1.3.11.0', '10', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('temperaturamimosa', $id_disp)) { processa_tag('temperaturamimosa', 'Temperatura CPU', '1.3.6.1.4.1.43356.2.1.2.1.8.0', '10', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('tempxpsuscc', $id_disp)) { processa_tag('tempxpsuscc', 'Temperatura Bat', '1.3.6.1.4.1.34252.1.1.38', '10', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('temperaturavsol', $id_disp)) { processa_tag('temperaturavsol', 'Temperatura', '1.3.6.1.4.1.37950.1.1.5.10.12.5.9.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('temperaturanehuawei', $id_disp)) { processa_tag('temperaturanehuawei', 'Temperatura', '1.3.6.1.4.1.2011.5.25.31.1.1.1.1.11.17039361', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('temperaturafiberhome', $id_disp)) { processa_tag('temperaturafiberhome', 'Temperatura', '1.3.6.1.4.1.5875.800.3.9.4.5.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('temperaturajuniper', $id_disp)) { processa_tag('temperaturajuniper', 'Temperatura', '1.3.6.1.4.1.2636.3.1.13.1.7.9.1.0.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                    }else if($tags['tag'] == 'voltagem') {
                        if(!verifica_tag('voltagem', $id_disp)) { processa_tag('voltagem', 'Voltagem', '1.3.6.1.4.1.14988.1.1.3.8.0', '10', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('voltagemceragon', $id_disp)) { processa_tag('voltagemceragon', 'Voltagem', '1.3.6.1.4.1.2281.10.1.1.10.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('voltagemnetprobeplusvolt', $id_disp)) { processa_tag('voltagemnetprobeplusvolt', 'Voltagem', '1.3.6.1.4.1.17095.1.3.2.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('voltagemnetprobevolt', $id_disp)) { processa_tag('voltagemnetprobevolt', 'Voltagem', '1.3.6.1.4.1.17095.1.3.9.0', '10', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                    }else if($tags['tag'] == 'cpu') {
                        if(!verifica_tag('cpusnmp', $id_disp)) { processa_tag('cpusnmp', 'Carga CPU', '1.3.6.1.4.1.2021.11.10.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('cpuoltparks', $id_disp)) { processa_tag('cpuoltparks', 'Carga CPU', '1.3.6.1.4.1.3893.4.4.1.2.0', '10', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('cpucambium', $id_disp)) { processa_tag('cpucambium', 'Carga CPU', '1.3.6.1.4.1.17713.21.2.1.64.0', '10', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('cpuvsol', $id_disp)) { processa_tag('cpuvsol', 'Carga CPU', '1.3.6.1.4.1.37950.1.1.5.10.12.3.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('cpunehuawei', $id_disp)) { processa_tag('cpunehuawei', 'Carga CPU', '1.3.6.1.4.1.2011.6.3.4.1.3.1.3.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('cpurouterhuawei', $id_disp)) { processa_tag('cpurouterhuawei', 'Carga CPU', '1.3.6.1.4.1.2011.6.3.4.1.2.0.0.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('cpunexus', $id_disp)) { processa_tag('cpunexus', 'Carga CPU', '1.3.6.1.4.1.9.9.305.1.1.1.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('cpujuniper', $id_disp)) { processa_tag('cpujuniper', 'Carga CPU', '1.3.6.1.4.1.2636.3.1.13.1.8.9.1.0.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                    }else if($tags['tag'] == 'ram') {
                        if(!verifica_tag('ramvsol', $id_disp)) { processa_tag('ramvsol', 'Memória RAM', '1.3.6.1.4.1.37950.1.1.5.10.12.4.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('ramjuniper', $id_disp)) { processa_tag('ramjuniper', 'Memória RAM', '1.3.6.1.4.1.2636.3.1.13.1.11.9.1.0.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('ramsnmp', $id_disp)) { processa_tag('ramsnmp', 'Memória RAM', '1.3.6.1.4.1.2021.4.5.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('ramoltparks', $id_disp)) { processa_tag('ramoltparks', 'Memória RAM', '1.3.6.1.4.1.3893.4.4.2.1.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                    }else if($tags['tag'] == 'conexoes') {
                        if(!verifica_tag('conexmikrotiksnmp', $id_disp)) { processa_tag('conexmikrotiksnmp', 'Conexões WAN', '1.3.6.1.4.1.14988.1.1.1.3.1.6', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('conexubntsnmp', $id_disp)) { processa_tag('conexubntsnmp', 'Conexões WAN', '1.3.6.1.4.1.41112.1.4.5.1.15.1', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('conexintelbras', $id_disp)) { processa_tag('conexintelbras', 'Conexões WAN', '1.3.6.1.4.1.32750.3.10.1.2.1.1.16.6', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('associedcambium', $id_disp)) { processa_tag('associedcambium', 'Conexões WAN', '1.3.6.1.4.1.17713.21.1.2.10.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('pppoehuawei', $id_disp)) { processa_tag('pppoehuawei', 'Conexões PPPoE', '1.3.6.1.4.1.2011.5.2.1.14.1.2.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('pppoe', $id_disp)) { processa_tag('pppoe', 'Conexões PPPoE', '1.3.6.1.4.1.9.9.150.1.1.1.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('pppoejuniper', $id_disp)) { processa_tag('pppoejuniper', 'Conexões PPPoE', '1.3.6.1.4.1.2636.3.63.1.1.1.2.1.5.44', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                        if(!verifica_tag('pppoecisco', $id_disp)) { processa_tag('pppoecisco', 'Conexões PPPoE', '1.3.6.1.4.1.9.9.194.1.1.1.0', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                    }else if($tags['tag'] == 'uptime') {
                        if(!verifica_tag('uptime', $id_disp)) { processa_tag('uptime', 'Uptime', '1.3.6.1.2.1.1.3', '1', $id_disp, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta); }
                    }else if($tags['tag'] == 'interfaces') {
                        if($vsnmp == 1) {
                            exec("snmpwalk -v1 -c " . $community . " " . $IP . ":" . $porta . " .1.3.6.1.2.1.2.2.1.3", $ifType);
                        }else if($vsnmp == 2) {
                            exec("snmpwalk -v2c -c " . $community . " " . $IP . ":" . $porta . " .1.3.6.1.2.1.2.2.1.3", $ifType);
                        }else if($vsnmp == 3) {
                            exec("snmpwalk -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $IP . ":" . $porta . " .1.3.6.1.2.1.2.2.1.3", $ifType);
                        }
                        if(count(array_filter($ifType))) {
                            for ($i=0; $i<count($ifType); $i++) {
                                $aux_type = explode(' = INTEGER: ', $ifType[$i]);
                                $index = str_replace('iso.3.6.1.2.1.2.2.1.3.', '', $aux_type['0']);
                                $type = $aux_type['1'];
                                // Syntaxe enumeration (1-other, 2-regular1822, 3-hdh1822, 4-ddn-x25, 5-rfc877-x25, 6-ethernet-csmacd, 7-iso88023-csmacd, 8-iso88024-tokenBus, 9-iso88025-tokenRing, 10-iso88026-man, 11-starLan, 12-proteon-10Mbit, 13-proteon-80Mbit, 14-hyperchannel, 15-fddi, 16-lapb, 17-sdlc, 18-ds1, 19-e1, 20-basicISDN, 21-primaryISDN, 22-propPointToPointSerial, 23-ppp, 24-softwareLoopback, 25-eon, 26-ethernet-3Mbit, 27-nsip, 28-slip, 29-ultra, 30-ds3, 31-sip, 32-frame-relay)
                                if($type == 6) {
                                    if(!verifica_interface($index, $id_disp)) { 
                                        $retorno = snmpget_test_interface($index, $vsnmp, $nivelsegsnmp, $community, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, $IP, $porta);
                                        // Se a conexão funcionou verificaremos o retorno!
                                        if($retorno) {
                                            $ifHighSpeed = $retorno['0'];
                                            $ifDescr = $retorno['1'];
                                            $ifAlias = $retorno['2'];
                                            $ifPhysAddress = $retorno['3'];
                                            $ifName = $retorno['4'];
                                            $nome = substr(sanitizeSNMP($ifName), 0, 250);
                                            if(!$nome) { $nome = substr(sanitizeSNMP($ifDescr), 0, 250); }
                                            if(!$nome) { $nome = substr(sanitizeSNMP($ifAlias), 0, 250); }
                                            if(!$nome) { $nome = "Interface " . $index; }
                                            add_sensor_interface($id_disp, $index, $nome, $ifName, $ifDescr, $ifAlias, $ifPhysAddress, $ifHighSpeed);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Adicionar e remover sensores dinamicamente
    if($resDisp['ativa_auto'] == 2) {
        $pesquisa_log_alertas = mysqli_query($GLOBALS['db'], "SELECT idSensor FROM Logalertas WHERE idDispositivo = '".$id_disp."' AND tag = 'trafegosnmp' AND resolvido = 0 AND tipo = 4 AND data < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        if(mysqli_num_rows($pesquisa_log_alertas)) {
            while($alertas = mysqli_fetch_array($pesquisa_log_alertas)) {
                excluir_sensor($alertas['idSensor']);
            }
        }
    }
}

if($id_disp) {
    processa_disp($id_disp);
}else {
    $PesquisaDisp = mysqli_query($db, "SELECT id FROM Dispositivos WHERE ativa_auto >= 1;");
    if(mysqli_num_rows($PesquisaDisp)) {
        while($o_disp = mysqli_fetch_array($PesquisaDisp)) {
            processa_disp($o_disp['id']);
        }
    }
}
?>