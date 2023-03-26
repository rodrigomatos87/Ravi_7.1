#!/usr/bin/php
<?php
/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/

include("/var/www/html/cron/apoio/conexao.php");

if(!is_dir("/var/www/html/BackupDisp")) { mkdir('/var/www/html/BackupDisp/', 0777, true); }

$ft_system = mysqli_query($db, "SELECT userpadrao, senhapadrao, portapadrao FROM system LIMIT 1;");
$system = mysqli_fetch_array($ft_system);

if(isset($_GET['id'])) {
    $ft_disp = mysqli_query($db, "SELECT id, equipamento, ip, HerdarPaiSSH, sshuser, sshsenha, sshport FROM Dispositivos WHERE id = '" . $_GET['id'] . "' AND backupExec = 1 AND (equipamento = 1 OR equipamento = 2);");
}else {
    $ft_disp = mysqli_query($db, "SELECT id, equipamento, ip, HerdarPaiSSH, sshuser, sshsenha, sshport FROM Dispositivos WHERE backupExec = 1 AND (equipamento = 1 OR equipamento = 2);");
}

while ($disp = mysqli_fetch_array($ft_disp)) {
    $ip = $disp['ip'];

    if($disp['HerdarPaiSSH'] == 1) {
        $user = $system['userpadrao'];
        $senha = $system['senhapadrao'];
        $porta = $system['portapadrao'];
    }else if($disp['HerdarPaiSSH'] == 2) {
        $user = $disp['sshuser'];
        $senha = $disp['sshsenha'];
        $porta = $disp['sshport'];
    }
    
    if(isset($user) && isset($senha) && isset($porta)) {
        // Ubiquiti
        if($disp['equipamento'] == 1) {
            $connection = ssh2_connect($ip, $porta);
            if($connection) {
                if (ssh2_auth_password($connection, $user, $senha)) {
                    ssh2_scp_recv($connection, '/tmp/system.cfg', '/var/www/html/BackupDisp/sys.' . $disp['id'] . '.txt');
                    $dataConecta = date("Y-m-d H:i:s");
                    mysqli_query($db, "UPDATE Dispositivos SET backupExec = '1', backupData = '".$dataConecta."' WHERE id = '".$disp['id']."'");
                    if(isset($_GET['id'])) { echo "Backup realizado com sucesso"; }
                }else {
                    mysqli_query($db, "UPDATE Dispositivos SET backupExec = '0' WHERE id = '".$disp['id']."'");
                    if(isset($_GET['id'])) { echo "Erro de login"; }
                }
            }else {
                mysqli_query($db, "UPDATE Dispositivos SET backupExec = '0' WHERE id = '".$disp['id']."'");
                if(isset($_GET['id'])) { 
                    echo "Conexão não estabelecida com o ip e parta"; 
                }
            }
        // Mikrotik
        }else if($disp['equipamento'] == 2) {
            $connection = ssh2_connect($ip, $porta);
            if($connection) {
                if (ssh2_auth_password($connection, $user, $senha)) {
                    $stream = ssh2_exec($connection, '/export file="backupRavi"');
                    $stream = ssh2_exec($connection, '/system backup save dont-encrypt=yes name="backupRavi"');
                    ssh2_scp_recv($connection, '/backupRavi.rsc', '/var/www/html/BackupDisp/sys.' . $disp['id'] . '.txt');
                    ssh2_scp_recv($connection, '/backupRavi.backup', '/var/www/html/BackupDisp/sys.' . $disp['id'] . '.backup');
                    $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
                    stream_set_blocking($errorStream, true);
                    stream_set_blocking($stream, true);
                    $output = stream_get_contents($stream);
                    fclose($stream);
                    fclose($errorStream);
                    ssh2_exec($connection, 'quit');
                    unset($connection);
                    if(isset($_GET['id'])) {
                        $dataConecta = date("Y-m-d H:i:s");
                        mysqli_query($db, "UPDATE Dispositivos SET backupExec = '1', backupData = '".$dataConecta."' WHERE id = '".$disp['id']."'");
                        if(isset($_GET['id'])) { echo "Backup realizado com sucesso"; }
                    }
                }else {
                    mysqli_query($db, "UPDATE Dispositivos SET backupExec = '0' WHERE id = '".$disp['id']."'");
                    if(isset($_GET['id'])) { echo "Erro de login"; }
                }
            }else {
                mysqli_query($db, "UPDATE Dispositivos SET backupExec = '0' WHERE id = '".$disp['id']."'");
                if(isset($_GET['id'])) { 
                    echo "Conexão não estabelecida com o ip e parta"; 
                }
            }
        }
    }
}
mysqli_close($db);
?>