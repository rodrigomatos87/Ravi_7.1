<?PHP
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

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

$buscaSensores = mysqli_query($db, "SELECT id, idDispositivo, valor, valor1, valor2, valor3, statusAlert, tag, nome, banco, unidade, display FROM Sensores;");
while($sensores = mysqli_fetch_array($buscaSensores)) {
    $idSensor = $sensores['id'];
    $idDispositivo = $sensores['idDispositivo'];
    $valor = $sensores['valor'];
    $valor1 = $sensores['valor1'];
    $valor2 = $sensores['valor2'];
    $valor3 = $sensores['valor3'];
    $statusAlert = $sensores['statusAlert'];
    $tag = $sensores['tag'];
    $nome = $sensores['nome'];
    $banco = $sensores['banco'];
    $unidade = $sensores['unidade'];
    $display = $sensores['display'];
    exec("echo '|$statusAlert|$valor1|$valor2|$valor3|$tag|$nome|$banco|$unidade|$display|' > /var/www/html/ram/dispositivos/sensores/$idSensor");
}

//exec("chown -R www-data:www-data /var/www/html");

mysqli_close($db);
?>