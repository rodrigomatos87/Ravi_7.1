#!/usr/bin/php
<?php
/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/

include("/var/www/html/cron/apoio/conexao.php");

$ft_system = mysqli_query($db, "SELECT * FROM system LIMIT 1;");
$system = mysqli_fetch_array($ft_system);

if($system['versaoNova'] == 1) {
    $ft_ServUpdates = mysqli_query($db, "SELECT * FROM ServUpdates LIMIT 1;");
    $update = mysqli_fetch_array($ft_ServUpdates);
    $ip = $update['ip'];
    $porta = $update['porta'];

    $connection = ssh2_connect($ip, $porta);
    if (ssh2_auth_password($connection, 'root', 'ravi2402')) {
        ssh2_scp_recv($connection, '/root/' . $update['versaoN'] . '/arquivo.zip', '/var/www/html/ram/arquivo.zip');
        $zip = new ZipArchive;
        $res = $zip->open('/var/www/html/ram/arquivo.zip');
        if ($res === TRUE) {
            $zip->extractTo('/var/www/html/');
            $zip->close();
            exec("rm -fr /var/www/html/__MACOSX");
            exec("find /var/www/html/ -name '.DS_Store' -type f -delete");

            if($update['shellN'] == 1) {
                exec("php -f /var/www/html/cron/apoio/update.php");
                exec("sh /var/www/html/cron/shell/atualizar.sh");
            }
        }
        mysqli_query($db, "UPDATE system SET versao = '".$update['versaoN']."', versaoData = '".$update['dataN']."', versaoNova = '0', debug = '".$update['debugN']."';");
    }
}

exec("rm -fr /var/www/html/ram/arquivo.zip");
mysqli_close($db);
?>