#!/usr/bin/php
<?php
/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/

include("/var/www/html/cron/apoio/conexao.php");
exec("/sbin/unbound-control stats", $unbound);

for ($i = 0; $i < count($unbound); $i++) {
    $aux = explode('=', $unbound[$i]);
    //echo $aux[0] . "\n";
    if($aux[0] == "total.num.queries") { $queries = (int)$aux[1]; }
    if($aux[0] == "total.num.cachehits") { $cachehits = (int)$aux[1]; }
    if($aux[0] == "total.num.cachemiss") { $cachemiss = (int)$aux[1]; }
    if($aux[0] == "total.num.prefetch") { $prefetch = (int)$aux[1]; }
    if($aux[0] == "total.num.recursivereplies") { $recursivereplies = (int)$aux[1]; }
    if($aux[0] == "time.up") { $up = (int)$aux[1]; }
    if($aux[0] == "num.query.type.A") { $A = (int)$aux[1]; }
    if($aux[0] == "num.query.type.AAAA") { $AAAA = (int)$aux[1]; }
    if($aux[0] == "num.query.type.ANY") { $ANY = (int)$aux[1]; }
    if($aux[0] == "num.query.type.CNAME") { $CNAME = (int)$aux[1]; }
    if($aux[0] == "num.query.type.PTR") { $PTR = (int)$aux[1]; }
    if($aux[0] == "mem.cache.msg") { $msg = (int)$aux[1]; }
    if($aux[0] == "mem.cache.rrset") { $rrset = (int)$aux[1]; }
    if($aux[0] == "total.requestlist.avg") { $avg = (int)$aux[1]; }
    if($aux[0] == "total.requestlist.max") { $max = (int)$aux[1]; }
}

mysqli_query($db, "INSERT INTO LogDNS (total_num_queries, total_num_cachehits, total_num_cachemiss, total_num_prefetch, total_num_recursivereplies, up, A, AAAA, ANY, CNAME, msg, rrset, PTR, avg, max) VALUES ('".$queries."', '".$cachehits."', '".$cachemiss."', '".$prefetch."', '".$recursivereplies."', '".$up."', '".$A."', '".$AAAA."', '".$ANY."', '".$CNAME."', '".$msg."', '".$rrset."', '".$PTR."', '".$avg."', '".$max."')");
mysqli_close($db);
?>