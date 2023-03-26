#!/usr/bin/php
<?php
include("/var/www/html/cron/apoio/conexao.php");

//$Chat_id = "377754547";
//$Token = "974848628:AAH8V3dyP45TZBAfcaA7zZ-RNoO-LADjGPY";

/*
Status:
	   0) Offline por rompimento
	   1) Online
	   2) Offline
	   3) Online sem dados
	   4) Offline por desligamento de energia
*/

$resSystem = mysqli_query($db, "SELECT ativaTELEGRAM, ativaTELEGRAMolt, telegramolt FROM system;");
$fetSystem = mysqli_fetch_array($resSystem);

if($fetSystem['ativaTELEGRAMolt'] == 1 && $fetSystem['ativaTELEGRAM'] == 1) {
    $resTelegram = mysqli_query($db, "SELECT * FROM telegrampadrao;");
}else if($fetSystem['ativaTELEGRAMolt'] == 2) {
    $resTelegram = mysqli_query($db, "SELECT * FROM telegramolt;");
}

if(mysqli_num_rows($resTelegram) >= 1) {
    
    // Alertar ONU Offline por perda óptica
    if($fetSystem['telegramolt'] >= 2) {
        $resONU1 = mysqli_query($db, "SELECT * FROM onus WHERE stats = '0'");
        if(mysqli_num_rows($resONU1) > 0) { 
            while($fetONU1 = mysqli_fetch_array($resONU1)) {
                $SELECT1 = mysqli_query($db, "SELECT id FROM alertaonus WHERE onu = '".$fetONU1['id']."'");
                if(mysqli_num_rows($SELECT1) == 0) {
                    $resOLT1 = mysqli_query($db, "SELECT nome FROM olts WHERE id = '".$fetONU1['idOLT']."'");
                    $fetOLT1 = mysqli_fetch_array($resOLT1);

                    $mensagem = "ONU OFFLINE POR PERDA ÓPTICA! \n\n";
                    $mensagem .= " | OLT: " . $fetOLT1['nome'] . "\n";
                    if($fetONU1['provisionamento']) { $mensagem .= " | " . $fetONU1['provisionamento'] . "\n"; }
                    if($fetONU1['descr']) { $mensagem .= " | Descrição: " . $fetONU1['descr'] . "\n"; }
                    if($fetONU1['mac']) { $mensagem .= " | MAC / SN: " . $fetONU1['mac'] . "\n"; }
                    $mensagem .= "\n";

                    if($fetOLT1['nome'] && $fetONU1['provisionamento']) {
                        if($fetSystem['ativaTELEGRAMolt'] == 1 && $fetSystem['ativaTELEGRAM'] == 1) {
                            $resTelegram1 = mysqli_query($db, "SELECT * FROM telegrampadrao;");
                        }else if($fetSystem['ativaTELEGRAMolt'] == 2) {
                            $resTelegram1 = mysqli_query($db, "SELECT * FROM telegramolt;");
                        }
                        while ($Telegram1 = mysqli_fetch_array($resTelegram1)) {
                            $partes1 = explode(':', $Telegram1['inicio']);
                            $start = $partes1[0] * 60 + $partes1[1];
                            $partes2 = explode(':', $Telegram1['fim']);
                            $end = $partes2[0] * 60 + $partes2[1];
                            if($end < $start) { $end = $end + 1440; }
                            
                            if ($start <= $now && $now <= $end) {
                                $Chat_id = $Telegram1['chat_id'];
                                $Token = $Telegram1['token'];
                                $url = "https://api.telegram.org/bot".$Token."/sendMessage?chat_id=".$Chat_id."&text=".urlencode($mensagem)."";
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
                                if($err) { $execucao = file_get_contents($url); }
                            }
                        }
                        mysqli_query($db, "INSERT INTO alertaonus (onu) VALUES ('".$fetONU1['id']."')");
                    }
                    
                }
            }
        }
    }

    // Alertar por qualquer motivo
    if($fetSystem['telegramolt'] == 3) {
        // Alertar quando offline sem motivo aparente
        $resONU2 = mysqli_query($db, "SELECT * FROM onus WHERE stats = '4'");
        if(mysqli_num_rows($resONU2) > 0) {
            while($fetONU2 = mysqli_fetch_array($resONU2)) {
                $SELECT2 = mysqli_query($db, "SELECT id FROM alertaonus WHERE onu = '".$fetONU2['id']."'");
                if(mysqli_num_rows($SELECT2) == 0) {
                    $resOLT2 = mysqli_query($db, "SELECT nome FROM olts WHERE id = '".$fetONU2['idOLT']."'");
                    $fetOLT2 = mysqli_fetch_array($resOLT2);
                    $mensagem = "ONU OFFLINE! Interrupção no fornecimento de energia \n\n";
                    $mensagem .= " | OLT: " . $fetOLT2['nome'] . "\n";
                    if($fetONU2['provisionamento']) { $mensagem .= " | " . $fetONU2['provisionamento'] . "\n"; }
                    if($fetONU2['descr']) { $mensagem .= " | Descrição: " . $fetONU2['descr'] . "\n"; }
                    if($fetONU2['mac']) { $mensagem .= " | MAC / SN: " . $fetONU2['mac'] . "\n"; }
                    $mensagem .= "\n";

                    if($fetOLT2['nome'] && $fetONU2['provisionamento']) {
                        if($fetSystem['ativaTELEGRAMolt'] == 1 && $fetSystem['ativaTELEGRAM'] == 1) {
                            $resTelegram2 = mysqli_query($db, "SELECT * FROM telegrampadrao;");
                        }else if($fetSystem['ativaTELEGRAMolt'] == 2) {
                            $resTelegram2 = mysqli_query($db, "SELECT * FROM telegramolt;");
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
                                $url = "https://api.telegram.org/bot".$Token."/sendMessage?chat_id=".$Chat_id."&text=".urlencode($mensagem)."";
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
                                if($err) { $execucao = file_get_contents($url); }
                            }
                        }
                        mysqli_query($db, "INSERT INTO alertaonus (onu) VALUES ('".$fetONU2['id']."')");
                    }
                    
                }
            }
        }

        // Alertar quando offline sem motivo informado
        $resONU3 = mysqli_query($db, "SELECT * FROM onus WHERE stats = '2'");
        if(mysqli_num_rows($resONU3) > 0) {
            while($fetONU3 = mysqli_fetch_array($resONU3)) {
                $SELECT3 = mysqli_query($db, "SELECT id FROM alertaonus WHERE onu = '".$fetONU3['id']."'");
                if(mysqli_num_rows($SELECT3) == 0) {
                    $resOLT3 = mysqli_query($db, "SELECT nome FROM olts WHERE id = '".$fetONU3['idOLT']."'");
                    $fetOLT3 = mysqli_fetch_array($resOLT3);
                    $mensagem = "ONU OFFLINE! \n\n";
                    $mensagem .= " | OLT: " . $fetOLT3['nome'] . "\n";
                    if($fetONU3['provisionamento']) { $mensagem .= " | " . $fetONU3['provisionamento'] . "\n"; }
                    if($fetONU3['descr']) { $mensagem .= " | Descrição: " . $fetONU3['descr'] . "\n"; }
                    if($fetONU3['mac']) { $mensagem .= " | MAC / SN: " . $fetONU3['mac'] . "\n"; }
                    $mensagem .= "\n";

                    if($fetOLT3['nome'] && $fetONU3['provisionamento']) {
                        if($fetSystem['ativaTELEGRAMolt'] == 1 && $fetSystem['ativaTELEGRAM'] == 1) {
                            $resTelegram3 = mysqli_query($db, "SELECT * FROM telegrampadrao;");
                        }else if($fetSystem['ativaTELEGRAMolt'] == 2) {
                            $resTelegram3 = mysqli_query($db, "SELECT * FROM telegramolt;");
                        }
                        while ($Telegram3 = mysqli_fetch_array($resTelegram3)) {
                            $partes1 = explode(':', $Telegram3['inicio']);
                            $start = $partes1[0] * 60 + $partes1[1];
                            $partes2 = explode(':', $Telegram3['fim']);
                            $end = $partes2[0] * 60 + $partes2[1];
                            if($end < $start) { $end = $end + 1440; }
                            
                            if ( $start <= $now && $now <= $end ) {
                                $Chat_id = $Telegram3['chat_id'];
                                $Token = $Telegram3['token'];
                                $url = "https://api.telegram.org/bot".$Token."/sendMessage?chat_id=".$Chat_id."&text=".urlencode($mensagem)."";
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
                                if($err) { $execucao = file_get_contents($url); }
                            }
                        }
                        mysqli_query($db, "INSERT INTO alertaonus (onu) VALUES ('".$fetONU3['id']."')");
                    }
                    
                }
            }
        }
    }

    $resALERTA = mysqli_query($db, "SELECT id, onu FROM alertaonus");
    if(mysqli_num_rows($resALERTA) > 0) {
        while($fetALERTA = mysqli_fetch_array($resALERTA)) {
            $resONU4 = mysqli_query($db, "SELECT idOLT, provisionamento, descr, mac FROM onus WHERE id = '".$fetALERTA['onu']."' AND stats = '1'");
            if(mysqli_num_rows($resONU4) == 1) {
                $fetAlertONU = mysqli_fetch_array($resONU4);
                $resOLT4 = mysqli_query($db, "SELECT nome FROM olts WHERE id = '".$fetAlertONU['idOLT']."'");
                $fetOLT4 = mysqli_fetch_array($resOLT4);
                $mensagem = "ONU RECONECTADO! \n\n";
                $mensagem .= " | OLT: " . $fetOLT4['nome'] . "\n";
                if($fetAlertONU['provisionamento']) { $mensagem .= " | " . $fetAlertONU['provisionamento'] . "\n"; }
                if($fetAlertONU['descr']) { $mensagem .= " | Descrição: " . $fetAlertONU['descr'] . "\n"; }
                if($fetAlertONU['mac']) { $mensagem .= " | MAC / SN: " . $fetAlertONU['mac'] . "\n"; }
                $mensagem .= "\n";
                
                if($fetOLT4['nome'] && $fetAlertONU['provisionamento']) {
                    if($fetSystem['ativaTELEGRAMolt'] == 1 && $fetSystem['ativaTELEGRAM'] == 1) {
                        $resTelegram4 = mysqli_query($db, "SELECT * FROM telegrampadrao;");
                    }else if($fetSystem['ativaTELEGRAMolt'] == 2) {
                        $resTelegram4 = mysqli_query($db, "SELECT * FROM telegramolt;");
                    }
                    while ($Telegram4 = mysqli_fetch_array($resTelegram4)) {
                        $partes1 = explode(':', $Telegram4['inicio']);
                        $start = $partes1[0] * 60 + $partes1[1];
                        $partes2 = explode(':', $Telegram4['fim']);
                        $end = $partes2[0] * 60 + $partes2[1];
                        if($end < $start) { $end = $end + 1440; }
                        
                        if ( $start <= $now && $now <= $end ) {
                            $Chat_id = $Telegram4['chat_id'];
                            $Token = $Telegram4['token'];
                            $url = "https://api.telegram.org/bot".$Token."/sendMessage?chat_id=".$Chat_id."&text=".urlencode($mensagem)."";
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
                            if($err) { $execucao = file_get_contents($url); }
                        }
                    }
                    mysqli_query($db, "DELETE FROM alertaonus WHERE onu = '".$fetALERTA['onu']."'");
                }
            }
        }
    }
}

?>