<?PHP
function test_latency($ips, $tamanho = 32, $quantidade = 20) {
    $results = [];

    if($quantidade > 30) { $quantidade = 30; }
    if($tamanho < 10) { $tamanho = 10; }

    $pconf = array(
        'count' => $quantidade,
        'size' => $tamanho,
        'ttl' => 128,
        'interval' => 1,
        'timeout' => 800
    );

    $ips = array_unique($ips);
	$ips = array_values($ips);

    $args = array();
    $args[] = '-c ' . $pconf['count'];
    $args[] = '-b ' . $pconf['size'];
    $args[] = '-H ' . $pconf['ttl'];
    $args[] = '-B 1';
    $args[] = '-r 1';
    $args[] = '-i ' . $pconf['interval'];
    $args[] = '-t ' . $pconf['timeout'];
    $args[] = implode(' ', array_map('escapeshellarg', $ips));
    $args[] = '2>&1';
    
    $cmd = 'fping '.implode(' ', $args);

    $fping_output = [];
    exec($cmd, $fping_output);
    echo '<pre>';
    print_r($fping_output);
    echo '</pre>';

    $fping_output = array_filter($fping_output, function($line) {
        return !empty(trim($line));
    });

    $ip_info = [];

    foreach ($fping_output as $line) {
        if (preg_match('/^ *([0-9a-fA-F\.:]+) +: +\[(\d+)]+, +\d+ +bytes, +(\d+\.\d+) +ms/', $line, $matches)) {
    
            $ip = $matches[1];
            $latency = (float)$matches[3];
    
            if (!isset($ip_info[$ip])) {
                $ip_info[$ip] = [
                    'status' => 'online',
                    'min_latency' => $latency,
                    'max_latency' => $latency,
                    'packet_loss' => 0,
                    'total_packets' => 0
                ];
            }
    
            $ip_info[$ip]['min_latency'] = min($ip_info[$ip]['min_latency'], $latency);
            $ip_info[$ip]['max_latency'] = max($ip_info[$ip]['max_latency'], $latency);
            $ip_info[$ip]['total_packets']++;
    
        } else if (preg_match('/^([0-9a-fA-F\.:]+) : xmt\/rcv\/%loss = \d+\/\d+\/(\d+)/', $line, $matches)) {
            $ip = $matches[1];
            $packet_loss = (int)$matches[2];
    
            if (isset($ip_info[$ip])) {
                $ip_info[$ip]['packet_loss'] = $packet_loss;
            }
        
        } else if (preg_match('/^([0-9a-fA-F\.:]+) +: +xmt\/rcv\/%loss = \d+\/\d+\/100%/', $line, $matches)) {
            $ip = $matches[1];
        
            if (!isset($ip_info[$ip])) {
                $ip_info[$ip] = [
                    'status' => 'offline',
                    'packet_loss' => 100
                ];
            }
        }
    }

    foreach ($ip_info as $ip => $info) {
        if($info['status'] == 'online') {
            $results[$ip] = [
                'status' => $info['status'],
                'packet_loss' => $info['packet_loss'],
                'jitter' => $info['max_latency'] - $info['min_latency'],
                'latency' => $info['min_latency']
            ];
        } else {
            $results[$ip] = [
                'status' => $info['status'],
                'packet_loss' => $info['packet_loss'],
                'jitter' => null,
                'latency' => null
            ];
        }
    }
    
    return $results;
}

/*
Exemplo de uso:

$ips = ['8.8.8.8', '8.8.4.4', '1.2.3.4', '208.67.222.222', '8.8.8.8', '208.67.220.220'];
$results = test_latency($ips);
print_r($results);

Exemplo de retorno:

Array
(
    [8.8.8.8] => Array
        (
            [status] => online
            [packet_loss] => 0
            [jitter] => 1.36
            [latency] => 6.42
        )
    [1.2.3.4] => Array
        (
            [status] => offline
            [packet_loss] => 100
            [jitter] => 
            [latency] => 
        )
)
*/



// legado
function ping_list($list, $tamanho, $quantidade) {
    function sanitize($string) {
        $what = array( ' ms', '% loss)', ' avg', 'Counter32: ' );
        $by   = array( '', '', '', '' );
        return str_replace($what, $by, $string);
    }
    
    $pconf = array(
        'size' => $tamanho,
        'count' => $quantidade,
        'ttl' => 128,
        'retries' => 1,
        'fragment' => 1,
        'msinterval' => 1,
        'timeout' => 800,
        'source_address' => ''
    );
    
    $size = $pconf['size'];
    if($size < 8) { $size = 8; }
    
    foreach($list as $k=>$ip){
        $ip = trim($ip);
        if($p=strpos($ip, '/')){ $ip=substr($ip, 0, $p); }
        if($ip==''){ unset($list[$k]); continue; }
        $list[$k] = $ip;
    }

	$list = array_unique($list);
	$list = array_values($list);
    
    $args = array();
    if(!$pconf['fragment']) $args[] = '--dontfrag';
    $args[] = '-C ' . $pconf['count'];
    $args[] = '-b ' . $size;
    $args[] = '--ttl ' . $pconf['ttl'];
    $args[] = '-B1 -r1';
    $args[] = '-i' . $pconf['msinterval'];
    $args[] = '--timeout='.$pconf['timeout'];
    $args[] = implode(' ', $list);
    
    $cmd = 'fping '.implode(' ', $args);
    $areg = shell_exec($cmd);

    $linhas = explode("\n", $areg);
    for($i=0;$i<count($linhas);$i++) {
        echo $linhas[$i] . "\n";
        if(!isset($losss[$ip])) {
            $losss[$ip] = array();
        }
        if($linhas[$i]) {
            $exp1 = explode(' : ', $linhas[$i]);
            $ip = $exp1[0];
            $exp2 = explode(', ', $exp1[1]);
            $ip = str_replace(" ", "", $ip);
            
            if(preg_match('/timed out/', $exp2[1])) {
                $latencia = "";
                //$avg = "";
                $loss = sanitize($exp2[2]);
            }else {
                $exp3 = explode(' (', $exp2[2]);
                $latencia = sanitize($exp3[0]);
                //$avg = sanitize($exp3[1]);
                $loss = sanitize($exp2[3]);
            }
            if(!isset($latencias[$ip])) { $latencias[$ip] = array(); }
            //if(!isset($jitters[$ip])) { $jitters[$ip] = array(); }
            if(!isset($loss_s[$ip])) { $loss_s[$ip] = array(); }
            if($latencia) { array_push($latencias[$ip], $latencia); }
            //if($avg) { array_push($jitters[$ip], $avg); }
            if(isset($loss)) { array_push($loss_s[$ip], $loss); }
        }
    }
    for($l=0;$l<count($list);$l++) {
        if(isset($list[$l])) {
            $ip = $list[$l];
            $results[$ip]['address'] = $list[$l];
            if(count($latencias[$list[$l]])) {
                $results[$ip]['ping'] = min($latencias[$list[$l]]);
                //$results[$ip]['jitter'] = min($jitters[$list[$l]]);
            }
			$results[$ip]['loss'] = end($loss_s[$list[$l]]);
        }
    }
    return $results;
}
?>