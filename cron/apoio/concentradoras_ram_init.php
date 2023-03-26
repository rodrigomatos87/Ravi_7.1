<?php
include("/var/www/html/cron/apoio/conexao.php");

if(!is_dir("/var/www/html/ram/ppoe")) { mkdir('/var/www/html/ram/ppoe', 0777, true); }
if(!is_dir("/var/www/html/ram/coletas/ppoe/processando")) { mkdir('/var/www/html/ram/coletas/ppoe/processando/', 0777, true); }
if(!is_dir("/var/www/html/ram/coletas/ppoe/ping")) { mkdir('/var/www/html/ram/coletas/ppoe/ping/', 0777, true); }
if(!is_dir("/var/www/html/ram/coletas/ppoe/users")) { mkdir('/var/www/html/ram/coletas/ppoe/users/', 0777, true); }
/*
chmod ("/var/www/html/ram/ppoe", 0755);
chmod ("/var/www/html/ram/coletas/ppoe/processando", 0755);
chmod ("/var/www/html/ram/coletas/ppoe/ping", 0755);
chmod ("/var/www/html/ram/coletas/ppoe/users", 0755);
*/
$resUsersPPPoE = mysqli_query($db, "SELECT * FROM usersPPPoE;");
if(mysqli_num_rows($resUsersPPPoE)) {
    while($usersPPPoE = mysqli_fetch_array($resUsersPPPoE)) {
        $idUser = $usersPPPoE['id'];
        $mac = $usersPPPoE['mac'];
        $datasinc = $usersPPPoE['datasinc'];
        $dataconect = $usersPPPoE['dataconect'];
        $datadesconect = $usersPPPoE['datadesconect'];
        $uptimeconect = $usersPPPoE['uptimeconect'];
        $ppoe = $usersPPPoE['ppoe'];
        $ip = $usersPPPoE['ip'];
        $vlan = $usersPPPoE['vlan'];
        $down = $usersPPPoE['down'];
        $up = $usersPPPoE['up'];
        $ping = $usersPPPoE['ping'];
        $jitter = $usersPPPoE['jitter'];
        exec("echo '|$mac|$datasinc|$dataconect|$datadesconect|$uptimeconect|$ppoe|$ip|$vlan|$down|$up|$ping|$jitter|' > /var/www/html/ram/coletas/ppoe/users/$idUser");
    }
}

mysqli_close($db);
?>