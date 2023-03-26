<?PHP
$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

include("/var/www/html/cron/apoio/conexao.php");

if(!is_dir("/var/www/html/ram/dispositivos")) { mkdir('/var/www/html/ram/dispositivos/', 0777, true); }
if(!is_dir("/var/www/html/ram/dispositivos/grupos")) { mkdir('/var/www/html/ram/dispositivos/grupos/', 0777, true); }
if(!is_dir("/var/www/html/ram/dispositivos/sensores")) { mkdir('/var/www/html/ram/dispositivos/sensores/', 0777, true); }

chown("/var/www/html/ram/dispositivos", "www-data");
chown("/var/www/html/ram/dispositivos/grupos", "www-data");
chown("/var/www/html/ram/dispositivos/sensores", "www-data");

/*
chmod ("/var/www/html/ram/dispositivos", 0755);
chmod ("/var/www/html/ram/dispositivos/grupos", 0755);
chmod ("/var/www/html/ram/dispositivos/sensores", 0755);
*/
$buscaGrupos = mysqli_query($db, "SELECT id, autoscan, status, Nome FROM GrupoMonitor;");
while($grupos = mysqli_fetch_array($buscaGrupos)) {
    $id = $grupos['id'];
    $autoscan = $grupos['autoscan'];
    $status = $grupos['status'];
    $Nome = $grupos['Nome'];
    exec("echo '|$autoscan|$status||$Nome|' > /var/www/html/ram/dispositivos/grupos/$id");
}

// Buscando os Dispositivos
$buscaDispositivos = mysqli_query($db, "SELECT idGrupoPai, Nome, ip, id, Link, backupExec, backupData FROM Dispositivos;");
while($dispositivos = mysqli_fetch_array($buscaDispositivos)) {
    $idGrupoPai = $dispositivos['idGrupoPai'];
    $Nome = $dispositivos['Nome'];
    $ip = $dispositivos['ip'];
    $id = $dispositivos['id'];
    $Link = $dispositivos['Link'];
    $backupExec = $dispositivos['backupExec'];
    $backupData = $dispositivos['backupData'];
    exec("echo '|$idGrupoPai|$Nome|$ip|$Link|$backupExec|$backupData|' > /var/www/html/ram/dispositivos/$id");
}

mysqli_close($db);
?>