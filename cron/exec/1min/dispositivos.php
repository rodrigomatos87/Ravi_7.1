#!/usr/bin/php
<?php

$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

$h = date("H");
$m = date("i");
$s = date("s");

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

// Executar apenas se não estiver com licença gratuita vencida
if($registroPlano) {
    include("/var/www/html/cron/apoio/conexao.php");

    $resultCronogramas = mysqli_query($db, "SELECT valor, ordem FROM cronograma ORDER BY ABS(ordem) ASC");
    while($cron = mysqli_fetch_array($resultCronogramas)) {
        // cronograma menor ou igual a 1 minuto
        if($cron['ordem'] <= 60) {
            $segundo = $s;
            $limite = 59;
        // cronograma menor ou igual a 1 hora
        }else if($cron['ordem'] <= 3600) {
            $segundo = $m * 60;
            $limite = 3599;
        // cronograma menor ou igual a 24 horas
        }else if($cron['ordem'] <= 86400) {
            $segundo = ($h * 3600) + ($m * 60);
            $limite = 86399;
        // cronograma menor ou igual a 7 dias
        }else if($cron['ordem'] <= 604800) {
            $segundo = ($h * 3600) + ($m * 60);
            $limite = 604799;
        }
        // Verifica se exite algum sensor para ser executado no cronograma
        $resultSensores = mysqli_query($db, "SELECT idDispositivo FROM Sensores WHERE cronograma = '" . $cron['valor'] . "' GROUP BY idDispositivo;");
        if(mysqli_num_rows($resultSensores)) {
            for ($x = 0; $x <= $limite; $x+=$cron['ordem']) {
                if($x == $segundo || $cron['ordem'] <= 60) {
                    if ($x && $x < 60) {
                        if(!$x) { $time = "00"; }else { $time = $x; }
                    }else {
                        $time = '00';
                        $x = '0';
                    }
                    while($resSensor = mysqli_fetch_array($resultSensores)) {
                        // Verifica se já está em execução
                        $pid = exec("ps aux | grep 'php -f /var/www/html/cron/apoio/exec_sensores.php id=$resSensor[idDispositivo] valor=$cron[valor] time=$time sleep=$x' | grep -v grep | awk '{print $2}'");
                        if(!$pid) { exec("php -f /var/www/html/cron/apoio/exec_sensores.php id=" . $resSensor['idDispositivo'] . " valor=" . $cron['valor'] . " time=" . $time . " sleep=" . $x . " > /dev/null &"); }
                    }
                }
            }
        }
    }
}
?>