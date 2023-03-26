<?php
parse_str(implode('&', array_slice($argv, 1)), $_GET);

// obtém o ID do usuário e do grupo do usuário nginx
$user_info = posix_getpwnam('www-data');
$user_id = $user_info['uid'];
$group_id = $user_info['gid'];

// altera o usuário e o grupo do processo atual para o usuário www-data
posix_setgid($group_id);
posix_setuid($user_id);

$retries = 5;
$timeout = 2;

$log_file = '/var/log/ravi.log';
$sensores = explode(',', $_GET['s']);

$dir1 = '/opt/Ravi/sensores';
if (!is_dir($dir1)) {
    if (!mkdir($dir1, 0777, true)) {
        $message = date('Y-m-d H:i:s') . ' - Não foi possível criar o diretório ' . $dir1;
        file_put_contents($log_file, $message . "\n", FILE_APPEND);
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

include("/var/www/html/funcoes_sensores.php");

function transform_oid($valor) {
    if(substr($valor, 0, 1) != '.') { $valor = '.' . $valor; }
    $exp = explode('.', $valor);
    //print_r($exp);
    $novo_valor = 'iso';
    for($c=2;$c<count($exp);$c++) {
        $novo_valor = $novo_valor . '.' . $exp[$c];
    }
    return $novo_valor;
}

$PesquisaDisp = mysqli_query($db, "SELECT id, ip, HerdarPai, idGrupoPai, snmpcomunit, versaosnmp_d, nivelsegsnmp_d, protocoloauthsnmp_d, protocolocripsnmp_d, authsnmp_d, criptosnmp_d, portasnmp_d FROM Dispositivos WHERE id = '" . $_GET['i'] . "';");
if(mysqli_num_rows($PesquisaDisp)) {
	$resDisp = mysqli_fetch_array($PesquisaDisp);
    $host = $resDisp['ip'];

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

    if($resDisp['HerdarPai'] == 2) {
        $community = $resDisp['snmpcomunit'];
        $porta = $resDisp['portasnmp_d'];
        $vsnmp = $resDisp['versaosnmp_d'];
        $nivelsegsnmp = $resDisp['nivelsegsnmp_d'];
        $protocoloauthsnmp = $resDisp['protocoloauthsnmp_d'];
        $protocolocripsnmp = $resDisp['protocolocripsnmp_d'];
        $authsnmp = $resDisp['authsnmp_d'];
        $criptosnmp = $resDisp['criptosnmp_d'];
    }else if($resDisp['idGrupoPai'] != 0) {
        $PesquisaGrupoPai = mysqli_query($db, "SELECT comunidadesnmp_g, versaosnmp_g, nivelsegsnmp_g, protocoloauthsnmp_g, protocolocripsnmp_g, authsnmp_g, criptosnmp_g, portasnmp_g FROM GrupoMonitor WHERE id = '".$resDisp['idGrupoPai']."' AND ativasnmp = '2'");
        if(mysqli_num_rows($PesquisaGrupoPai) == 1) {
            $resGpo = mysqli_fetch_array($PesquisaGrupoPai);
            $community = $resGpo['comunidadesnmp_g'];
            $porta = $resGpo['portasnmp_g'];
            $vsnmp = $resGpo['versaosnmp_g'];
            $nivelsegsnmp = $resGpo['nivelsegsnmp_g'];
            $protocoloauthsnmp = $resGpo['protocoloauthsnmp_g'];
            $protocolocripsnmp = $resGpo['protocolocripsnmp_g'];
            $authsnmp = $resGpo['authsnmp_g'];
            $criptosnmp = $resGpo['criptosnmp_g'];
        }
    }

    $coletores = array();
    $oids = array();
    $sensor_model = array();
    $falhas_permitidas = array();
    $falhas_atuais = array();
    for ($a=0; $a<count($sensores); $a++) {
        $PesquisaSensor = mysqli_query($db, "SELECT tag, erro, adicionais FROM Sensores WHERE id = '" . $sensores[$a] . "';");
        $restag = mysqli_fetch_array($PesquisaSensor);

        $falhas_permitidas[$sensores[$a]] = $restag['adicionais'];
        $falhas_atuais[$sensores[$a]] = $restag['erro'];
        
        $PesquisaSonda = mysqli_query($db, "SELECT id FROM sensores_disp WHERE tag = '".$restag['tag']."';");
        $resSonda = mysqli_fetch_array($PesquisaSonda);
        $sensor_model[$sensores[$a]] = $resSonda['id'];

        $PesquisaColetor = mysqli_query($db, "SELECT * FROM coletores_sensores WHERE idsensor = '".$resSonda['id']."';");
        if(mysqli_num_rows($PesquisaColetor)) {
            while($resColetor = mysqli_fetch_array($PesquisaColetor)) {
                $coletores[$sensores[$a]][] = array(
                    'coletor' => $resColetor['id'],
                    'oid' => $resColetor['oid_snmp'],
                    'valor' => $resColetor['valor']
                );
                $oids[] = $resColetor['oid_snmp'];
            }
        }
    }

    $oids = array_unique($oids);
    $oidspart = implode(' ', $oids);
    
    if($vsnmp == 1) {
        $cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v1 -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
    }else if($vsnmp == 2) {
        $cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v2c -c " . addslashes($community) . " " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
    }else if($vsnmp == 3) {
        $cmd = "snmpget -Ost -r " . $retries . " -t " . $timeout . " -v3 -l " . $nivelsegsnmp . " -u " . addslashes($community) . " -a " . $protocoloauthsnmp . " -A \"" . $authsnmp . "\" -x " . $protocolocripsnmp . " -X \"" . $criptosnmp . "\" " . $host . ":" . $porta . " " . $oidspart . " 2>/dev/null";
    }

    //echo $cmd;

    $stdno = 0;
    $analysis = array();
    exec ($cmd, $analysis, $stdno);
    $stdno = (int)$stdno;

    if(!$stdno) {
        $process_analysis = array();
        $results = array();

        for($d=0;$d<count($analysis);$d++) {
            $processa = explode(' = ', $analysis[$d]);
            $process_analysis[$processa[0]] = sanitizeSNMP($processa[1]);
        }

        foreach ($coletores as $sensor => $value) {
            for($b=0;$b<count($value);$b++) {
                $busca = transform_oid($value[$b]['oid']);
                if($process_analysis[$busca] == 'No Such Object available on this agent at this OID') {
                    $valor = 'No Such Object available on this agent at this OID';
                }else {
                    $valor = processa_cond($process_analysis[$busca], $value[$b]['valor']);
                }
                //echo "o retorno do coletor " . $value[$b]['coletor'] . " é: (".$process_analysis[$busca].") " . $valor . " \n";
                $coletores[$sensor][$b]['retorno'] = $valor;
                $results[$value[$b]['coletor']] = $valor;
            }
        }
/*
        echo "\ncoletores\n";
        print_r($coletores);
        echo "\nresults\n";
        print_r($results);
        echo "\nsensor_model\n";
        print_r($sensor_model);
        echo "\nfalhas_atuais\n";
        print_r($falhas_atuais);
        echo "\nfalhas_permitidas\n";
        print_r($falhas_permitidas);
        echo "\n\n";
*/
        foreach ($coletores as $sensor => $value) {
            $PesquisaCondicoes = mysqli_query($db, "SELECT * FROM condicoes_sensores WHERE idsensor = '".$sensor_model[$sensor]."';");
            if(mysqli_num_rows($PesquisaCondicoes)) {
                $problema = array();
                while ($resCond = mysqli_fetch_array($PesquisaCondicoes)) {
                    $statusAlert = 6;
                    $Pesquisa_regras_Cond = mysqli_query($db, "SELECT * FROM regra_condicoes_sensores WHERE formato <= 4 AND idcondicao = '".$resCond['id']."';");
                    if(mysqli_num_rows($Pesquisa_regras_Cond)) {
                        while ($resRegra = mysqli_fetch_array($Pesquisa_regras_Cond)) {
                            if($resRegra['tipo_regra'] == 1) {
                                $codigo = 'if(';
                                if($resRegra['formato'] == 1) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " > " . $resRegra['valor'];
                                }else if($resRegra['formato'] == 2) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " < " . $resRegra['valor'];
                                }else if($resRegra['formato'] == 3) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " == " . $resRegra['valor'];
                                }else if($resRegra['formato'] == 4) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " != " . $resRegra['valor'];
                                }
                            }else if($resRegra['tipo_regra'] == 2) {
                                $codigo = $codigo . ' && ';
                                if($resRegra['formato'] == 1) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " > " . $resRegra['valor'];
                                }else if($resRegra['formato'] == 2) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " < " . $resRegra['valor'];
                                }else if($resRegra['formato'] == 3) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " == " . $resRegra['valor'];
                                }else if($resRegra['formato'] == 4) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " != " . $resRegra['valor'];
                                }
                            }else if($resRegra['tipo_regra'] == 3) {
                                $codigo = $codigo . ' || ';
                                if($resRegra['formato'] == 1) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " > " . $resRegra['valor'];
                                }else if($resRegra['formato'] == 2) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " < " . $resRegra['valor'];
                                }else if($resRegra['formato'] == 3) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " == " . $resRegra['valor'];
                                }else if($resRegra['formato'] == 4) {
                                    $codigo = $codigo . $results[$resRegra['idcoletor']] . " != " . $resRegra['valor'];
                                }
                            }
                        }
                        $codigo = $codigo . ") { \$novafalha = 1; }else { \$novafalha = 0; }";
            
                        eval($codigo);
                    }else {
                        // Não tem nenhuma regra na condição cadastrada!
                        $novafalha = 0;
                    }

                    if($novafalha == 1) {
                        if($falhas_atuais[$sensor] >= $falhas_permitidas[$sensor]) {
                            // Gravidade: 
                            // 1 = Problema simples
                            // 2 = Problema crítico
                            if($resRegra['gravidade'] == 1) {
                                $statusAlert = 3;
                            }else if($resRegra['gravidade'] == 2) {
                                $statusAlert = 4;
                            }
                        }else {
                            $falhas_atuais[$sensor] = $falhas_atuais[$sensor] + 1;
                            $statusAlert = 3;
                        }
                    }else {
                        $falhas_atuais[$sensor] = 0;
                        $statusAlert = 6;
                    }

                    $mensagem = '';
                    if($statusAlert == 3) {
                        if($resLing['linguagem'] == 1) {
                            $descr_problema = $resCond['descr_problema_pt'];
                        }else if($resLing['linguagem'] == 2) {
                            $descr_problema = $resCond['descr_problema_en'];
                        }else if($resLing['linguagem'] == 3) {
                            $descr_problema = $resCond['descr_problema_es'];
                        }
        
                        if($descr_problema) {
                            if($mensagem) { $mensagem .= $descr_problema; }else { $mensagem = $descr_problema; }
                        }else {
                            if($resLing['linguagem'] == 1) {
                                if(!$mensagem) { $mensagem = "Alerta sem descrição informativa!"; }
                            }else if($resLing['linguagem'] == 2) {
                                if(!$mensagem) { $mensagem = "Alert without informative description!"; }
                            }else if($resLing['linguagem'] == 3) {
                                if(!$mensagem) { $mensagem = "¡Alerta sin descripción informativa!"; }
                            }
                        }
                    }

                }
            }else {
                $falhas_atuais[$sensor] = 0;
                $statusAlert = 6;
                $mensagem = '';
            }

            $n = 1;
            $valor1 = ''; $valor2 = ''; $valor3 = ''; $valor4 = '';
            for($e=0;$e<count($value);$e++) {
                if($n == 1) { $valor1 = $coletores[$sensor][$e]['retorno']; }else if($n == 2) { $valor2 = $coletores[$sensor][$e]['retorno']; }else if($n == 3) { $valor3 = $coletores[$sensor][$e]['retorno']; }else if($n == 4) { $valor4 = $coletores[$sensor][$e]['retorno']; }
                $n++;
            }

            /*
            echo "Sensor: " . $sensor . "\n";
            echo "Status: " . $statusAlert . "\n";
            echo "Falhas atuais: " . $falhas_atuais[$sensor] . "\n";
            echo "valor1: " . $valor1 . "\n";
            echo "valor2: " . $valor2 . "\n";
            echo "valor3: " . $valor3 . "\n";
            echo "valor4: " . $valor4 . "\n";
            echo "Mensagem: " . $mensagem . "\n\n\n";
            */

            mysqli_query($db, "UPDATE Sensores SET valor1 = '".$valor1."', valor2 = '".$valor2."', valor3 = '".$valor3."', valor4 = '".$valor4."', statusAlert = '".$statusAlert."', erro = '".$falhas_atuais[$sensor]."' WHERE id = '$sensor';");
            
            $resultSensores = mysqli_query($db, "SELECT tag, nome, banco, unidade, display FROM Sensores WHERE id = '$sensor';");
            $detalhes = mysqli_fetch_array($resultSensores);
            $tag = $detalhes['tag'];
            $nome = $detalhes['nome'];
            $banco = $detalhes['banco'];
            $unidade = $detalhes['unidade'];
            $display = $detalhes['display'];
            exec("echo '|$statusAlert|$valor1|$valor2|$valor3|$tag|$nome|$banco|$unidade|$display|' > /var/www/html/ram/dispositivos/sensores/$sensor");

            $dir2 = '/opt/Ravi/sensores/'.$sensor;
            if (!is_dir($dir2)) {
                if (!mkdir($dir2, 0777, true)) {
                    $message = date('Y-m-d H:i:s') . ' - Não foi possível criar o diretório ' . $dir2;
                    file_put_contents($log_file, $message . "\n", FILE_APPEND);
                }
            }
            if(file_exists('/opt/Ravi/sensores/'.$sensor.'/d-'.date('Ymd', strtotime($_GET['d'])).'.csv')) {
                $arquivo = fopen('/opt/Ravi/sensores/'.$sensor.'/d-'.date('Ymd', strtotime($_GET['d'])).'.csv', 'a');
            }else {
                $arquivo = fopen('/opt/Ravi/sensores/'.$sensor.'/d-'.date('Ymd', strtotime($_GET['d'])).'.csv', 'w');
            }
            fputcsv($arquivo, array($_GET['d'], $statusAlert, $valor1, $valor2, $valor3, $valor4));
            fclose($arquivo);
        }
    }
}else {
	// Se o dispositivo em que o sensor faz parte não existe 
    // algum problema deve ter acontecido, temos que tratar isso futuramente!
}

//exec('chown -R www-data:www-data /opt/Ravi/');
?>