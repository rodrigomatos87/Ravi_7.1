#!/usr/bin/php
<?php
$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

if(!is_dir("/var/www/html/ram/coletas/")) { mkdir('/var/www/html/ram/coletas/', 0777, true); }
if(!is_dir("/var/www/html/ram/coletas/olt/")) { mkdir('/var/www/html/ram/coletas/olt/', 0777, true); }

function encodeBase64($string) {
	$what = array( '+', '/', '=' );
	$by   = array( '-', '_', '' );
	return str_replace($what, $by, base64_encode($string));
}

$key = 'lZke4%QQ5y6uo%WPtBXDy9gfv';
$captSinc = exec("cat /var/www/html/.sinc");

if($captSinc) {
	$aux_sinc = explode('.', $captSinc);
	$signature_sinc = hash_hmac('sha256', $aux_sinc[1].'.'.$aux_sinc[2], $key);
	$signature_sinc = encodeBase64($signature_sinc);
	if($signature_sinc == $aux_sinc[3]) {
		$payload_ret = json_decode(base64_decode($aux_sinc[2]));
		$status = $payload_ret->status;
		if($status == 1) {
            $registroPlano = $payload_ret->plano;
		}else {
			//echo "o que fazer quando a senha de sincronização não bater";
            $pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/exec/10min/token.php' | grep -v grep | awk '{print $2}'");
            if(!$pidexec) { exec("php -f /var/www/html/cron/exec/10min/token.php &"); }
		}
	}else {
		//echo "o que fazer quando a senha não bater";
        $pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/exec/10min/token.php' | grep -v grep | awk '{print $2}'");
        if(!$pidexec) { exec("php -f /var/www/html/cron/exec/10min/token.php &"); }
	}
}else {
	//echo "o que fazer quando não existe arquivo";
    $pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/exec/10min/token.php' | grep -v grep | awk '{print $2}'");
    if(!$pidexec) { exec("php -f /var/www/html/cron/exec/10min/token.php &"); }
}

if(!$registroPlano) { $registroPlano = 0; }

$data = date("Y-m-d");
$hor = date("H:i"); 
$hora = $hor . ":00";
$data1 = date("i");
$data2 = date("H");
$dataatual = $data . " " . $hora;

include("/var/www/html/cron/apoio/conexao.php");

$resSystem = mysqli_query($db, "SELECT snmppadrao_olt, versaosnmppadrao_olt, portasnmppadrao_olt, userpadrao_olt, senhapadrao_olt, portapadrao_olt FROM system");
$System = mysqli_fetch_array($resSystem);

if($registroPlano == '7' || $registroPlano == '8' || $registroPlano == '9' || $registroPlano == '100') {
    $limite_plano = 0;
    if($registroPlano == '7') {
        $limite_plano = 1;
    }else if($registroPlano == '8') {
        $limite_plano = 4;
    }else if($registroPlano == '9' || $registroPlano == '100') {
        $limite_plano = 9999;
    }
    $qtd_exec = 1;
	$resOLT = mysqli_query($db, "SELECT * FROM olts WHERE ativo = 1 AND status = 2");
	while($olt = mysqli_fetch_array($resOLT)) {
        // Executar apenas o que está no plano
        if($qtd_exec <= $limite_plano) { 
            $pidexec = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/OLT_SNMP.php id=$olt[id]' | grep -v grep | awk '{print $2}'");
            $monitorar = 0;
            if(!$pidexec) {
                if($olt['novo'] == 1) {
                    mysqli_query($db, "UPDATE olts SET novo = '2' WHERE id = '$olt[id]'");
                    $monitorar = 1;
                }else if($olt['ocronolt'] == 1) {
                    if($data1 == '00' || $data1 == '05' || $data1 == '10' || $data1 == '15' || $data1 == '20' || $data1 == '25' || $data1 == '30' || $data1 == '35' || $data1 == '40' || $data1 == '45' || $data1 == '50' || $data1 == '55') {
                        $monitorar = 1;
                    }
                }else if($olt['ocronolt'] == 2) {
                    if($data1 == '00' || $data1 == '10' || $data1 == '20' || $data1 == '30' || $data1 == '40' || $data1 == '50') {
                        $monitorar = 1;
                    }
                }else if($olt['ocronolt'] == 3) {
                    if($data1 == '00' || $data1 == '15' || $data1 == '30' || $data1 == '45') {
                        $monitorar = 1;
                    }
                }else if($olt['ocronolt'] == 4) {
                    if($data1 == '00' || $data1 == '30') {
                        $monitorar = 1;
                    }
                }else if($olt['ocronolt'] == 5 && $data1 == '00') {
                    if($data2 == '00' || $data2 == '01' || $data2 == '02' || $data2 == '03' || $data2 == '04' || $data2 == '05' || $data2 == '06' || $data2 == '07' || $data2 == '08' || $data2 == '09' || $data2 == '10' || $data2 == '11' || $data2 == '12' || $data2 == '13' || $data2 == '14' || $data2 == '15' || $data2 == '16' || $data2 == '17' || $data2 == '18' || $data2 == '19' || $data2 == '20' || $data2 == '21' || $data2 == '22' || $data2 == '23') {
                        $monitorar = 1;
                    }
                }else if($olt['ocronolt'] == 6 && $data1 == '00') {
                    if($data2 == '00' || $data2 == '02' || $data2 == '04' || $data2 == '06' || $data2 == '08' || $data2 == '10' || $data2 == '12' || $data2 == '14' || $data2 == '16' || $data2 == '18' || $data2 == '20' || $data2 == '22') {
                        $monitorar = 1;
                    }
                }else if($olt['ocronolt'] == 7) {
                    if($data1 == '00' && $data2 == '00' || $data2 == '03' || $data2 == '06' || $data2 == '09' || $data2 == '12' || $data2 == '15' || $data2 == '18' || $data2 == '21') {
                        $monitorar = 1;
                    }
                }else if($olt['ocronolt'] == 8) {
                    if($data1 == '00' && $data2 == '00' || $data2 == '06' || $data2 == '12' || $data2 == '18') {
                        $monitorar = 1;
                    }
                }else if($olt['ocronolt'] == 9) {
                    if($data1 == '00' && $data2 == '00' || $data2 == '12') {
                        $monitorar = 1;
                    }
                }else if($olt['ocronolt'] == 10) {
                    if($data1 == '00' && $data2 == '00') {
                        $monitorar = 1;
                    }
                }
            }
            if($monitorar == 1) {
                // SNMP
                if($olt['tipo'] == 1) {
                    if($olt['HerdarPai'] == 1) {
                        $comunidade = $System['snmppadrao_olt'];
                        $vsnmp = $System['versaosnmppadrao_olt'];
                        $porta = $System['portasnmppadrao_olt'];
                    }else if($olt['HerdarPai'] == 2) {
                        $comunidade = $olt['snmp'];
                        $vsnmp = $olt['versaosnmp'];
                        $porta = $olt['portasnmp'];
                    }
                    exec("php -f /var/www/html/cron/apoio/OLT_SNMP.php id=$olt[id] ip=$olt[ip] snmp=$comunidade vsnmp=$vsnmp porta=$porta marca=$olt[marca] cron=$olt[ocronolt] hora=$hora data=$data > /dev/null &");
                    //echo "php -f /var/www/html/cron/apoio/OLT_SNMP.php id=$olt[id] ip=$olt[ip] snmp=$comunidade vsnmp=$vsnmp porta=$porta marca=$olt[marca] cron=$olt[ocronolt] hora=$hora data=$data &> /dev/null &<br>";
                    
                // Telnet
                }else if($olt['tipo'] == 2) {
                    if($olt['HerdarPaiSSH'] == 1) {
                        $user = $System['userpadrao_olt'];
                        $senha = $System['senhapadrao_olt'];
                        $porta = $System['portapadrao_olt'];
                    }else if($olt['HerdarPaiSSH'] == 2) {
                        $user = $olt['login'];
                        $senha = $olt['senha'];
                        $porta = $olt['porta'];
                    }
                    exec("php -f /var/www/html/cron/apoio/OLT_Telnet.php id=$olt[id] ip=$olt[ip] port=$porta user=$user senha=$senha marca=$olt[marca] cron=$olt[ocronolt] hora=$hora data=$data > /dev/null &");
                }
            }
            $qtd_exec = $qtd_exec + 1;
        }else {
            // Desativar concentradora além do limitado no plano!
            // mysqli_query($db, "UPDATE olts SET ativo = '2' WHERE id = '".$olt['id']."';");
        }
	}
}else {
    // Desativar todas as olts!
    // mysqli_query($db, "UPDATE olts SET ativo = '2';");
}

mysqli_close($db);
?>