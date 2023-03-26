<?php
include("/var/www/html/conexao.php");
$dataConecta = date("Y-m-d H:i:s");
mysqli_query($db, "update system set datavpn = '".$dataConecta."'");
mysqli_close($db);
exit;
?>