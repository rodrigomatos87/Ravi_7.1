#!/usr/bin/php
<?php
$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

$data_atual = date("Y-m-d H:i:s");
$log_file = '/var/log/ravi.log';

include("/var/www/html/functions/sinc_central_ravi.php");
include("/var/www/html/functions/valida_exec_concentradora.php");
include("/var/www/html/cron/apoio/conexao.php");

$dir = '/opt/Ravi/concentradores';
if (!is_dir($dir)) {
    if (!mkdir($dir, 0777, true)) {
        $message = date('Y-m-d H:i:s') . ' - Não foi possível criar o diretório ' . $dir;
        file_put_contents($log_file, $message . "\n", FILE_APPEND);
    }
}

$registroPlano = registro_ravi('lZke4%QQ5y6uo%WPtBXDy9gfv');

// Aqui limitamos quais planos podem executar e quantas execuções por plano!
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
    $resConcentradora = mysqli_query($db, "SELECT * FROM concentradoras WHERE status = 2 AND ativo = 1;");
    while($Concentradora = mysqli_fetch_array($resConcentradora)) {
        // Executar apenas o que está no plano 
        if($qtd_exec <= $limite_plano) {
            if(validaExecConcentradora($Concentradora['marca'], $Concentradora['cron'], $Concentradora['id'], $data_atual, $Concentradora['novo'])) {
                if($marca == 1) { $nome_marca = 'mikrotik'; }
                if($marca == 2) { $nome_marca = 'huawei'; }
                if($marca == 3) { $nome_marca = 'cisco'; }
                if(isset($nome_marca)) {
                    exec("php -f /var/www/html/cron/apoio/concentradoras/$nome_marca.php id=$Concentradora[id] d=\"".$data_atual."\" > /dev/null &");
                }
            }
            $qtd_exec = $qtd_exec + 1;
        }
    }
}

mysqli_close($db);