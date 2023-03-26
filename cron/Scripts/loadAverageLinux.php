#!/usr/bin/php
<?PHP
parse_str(implode('&', array_slice($argv, 1)), $_GET);

$id = $_GET["id"];
$ip = $_GET["ip"];
$snmp = $_GET["snmp"];
$porta = $_GET["porta"];
$vsnmp = $_GET["vsnmp"];
$nivelsegsnmp = $_GET["nivelsegsnmp"];
$protocoloauthsnmp = $_GET["protocoloauthsnmp"];
$protocolocripsnmp = $_GET["protocolocripsnmp"];
$authsnmp = $_GET["authsnmp"];
$criptosnmp = $_GET["criptosnmp"];
$falhas = $_GET["ad"];
$StErro = $_GET["erro"];
$hora = $_GET["hora"];
$data = $_GET["data"];
$data1 = $_GET["data1"];
$alertar = $_GET["alertar"];

$data = ''.$data.' '.$hora.'';

if(!$falhas) { $falhas = 1; }

function insert( $data, $data1, $idSensor, $QtdCores, $valor2, $statusAlert, $StErro ) {
	$timearq = date("H-i-s");
	$arq = $idSensor . "_" . $timearq;
	// |$data|$data1|$idSensor|$valor1|$valor2|$valor3|$statusAlert|erro|
	system("echo '|$data|$data1|$idSensor|$QtdCores|$valor2||$statusAlert|$StErro|' > /var/www/html/ram/coletas/valores/$arq");
}

function sanitizeSNMP($string) {
    $what = array( 'STRING: ', 'INTEGER: ', '"' );
    $by   = array( '', '', '' );
    return str_replace($what, $by, $string);
}

if($vsnmp == 1) {
    $min1 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.2021.10.1.3.1", 1000000, 30));
    $min5 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.2021.10.1.3.2", 1000000, 30));
    $min15 = sanitizeSNMP(snmpget("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.2021.10.1.3.3", 1000000, 30));
    $cores = sanitizeSNMP(snmpwalk("{$ip}:{$porta}", $snmp, "1.3.6.1.2.1.25.3.3.1.2", 1000000, 30));
}else if($vsnmp == 2) {
    $min1 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.2021.10.1.3.1", 1000000, 30));
    $min5 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.2021.10.1.3.2", 1000000, 30));
    $min15 = sanitizeSNMP(snmp2_get("{$ip}:{$porta}", $snmp, "1.3.6.1.4.1.2021.10.1.3.3", 1000000, 30));
    $cores = sanitizeSNMP(snmp2_walk("{$ip}:{$porta}", $snmp, "1.3.6.1.2.1.25.3.3.1.2", 1000000, 30));
}else if($vsnmp == 3) {
    $min1 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.2021.10.1.3.1", 1000000, 30));
    $min5 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.2021.10.1.3.2", 1000000, 30));
    $min15 = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.4.1.2021.10.1.3.3", 1000000, 30));
    $cores = sanitizeSNMP(snmp3_get("{$ip}:{$porta}", $snmp, $nivelsegsnmp, $protocoloauthsnmp, $authsnmp, $protocolocripsnmp, $criptosnmp, "1.3.6.1.2.1.25.3.3.1.2", 1000000, 30));
}

$QtdCores = count(array_filter($cores));

if($min1 >= 0 && $min1 != "" && $min5 >= 0 && $min5 != "" && $min15 >= 0 && $min15 != "" && $QtdCores >= 1) {
    if($min1 > $QtdCores) {
        $filaTotal = $min1 - $QtdCores;
        $valor1 = $filaTotal;
        if($filaTotal >= 10) {
            if($QtdCores > 1) {
                $fila = $filaTotal / $QtdCores;
                $valor1 = $fila;
                if($fila >= 10) {
                    if($StErro >= $falhas) {
                        if($alertar == 1) {
                            $statusAlert = 11;
                        }else if($alertar == 2) {
                            $statusAlert = 12;
                        }else {
                            $statusAlert = 11;
                        }
                    }else {
                        $StErro = $StErro + 1;
                        $statusAlert = 11;
                    }
                    // Alera de muito gargalo
                    // Processos (por core)<br>na fila: " . $fila
                }else {
                    $statusAlert = 11;
                    // Alerta de gargalo
                    // Processos (por core)<br>na fila: " . $fila
                }
            }else if($filaTotal >= 10) {
                if($StErro >= $falhas) {
                    if($alertar == 1) {
                        $statusAlert = 11;
                    }else if($alertar == 2) {
                        $statusAlert = 12;
                    }else {
                        $statusAlert = 11;
                    }
                }else {
                    $StErro = $StErro + 1;
                    $statusAlert = 11;
                }
                // Alera de muito gargalo
                // Processos na fila: " . $filaTotal
            }
        }else {
            $statusAlert = 11;
            // Alerta de gargalo
            // Processos na fila: " . $filaTotal
        }
    }else {
        $statusAlert = 6;
        // Tudo certo... 
    }
    $valor2 = $min1 . "/" . $min5 . "/" . $min15;
}else {
    $statusAlert = 7;
}

insert($data, $data1, $id, $QtdCores, $valor2, $statusAlert, $StErro);

$valor1 = $mimosaLastRebootTime;
if($statusAlert == 1 || $statusAlert == 4 || $statusAlert == 8 || $statusAlert == 10 || $statusAlert == 12) {
    $aux = explode(' ', $data);
    $cmd = "php -f /var/www/html/cron/Scripts/envio_alertas.php id=" . $id . " data1=" . $aux[0] . " data2=" . $aux[1] . " valor1=" . $valor1 . " valor2=" . $valor2 . " statusAlert=" . $statusAlert . " &";
    exec($cmd);
}

?>