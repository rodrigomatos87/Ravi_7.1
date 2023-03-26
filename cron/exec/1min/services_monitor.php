#!/usr/bin/php
<?php
putenv('PATH=' . getenv('PATH') . ':/usr/bin:/usr/local/bin');

$pid_bkp = exec("ps aux | grep 'GeraBackupRavi.php' | grep -v grep");
if($pid_bkp) { exit; }

$log_file = '/var/log/ravi.log';

// Lockfile
$filename = '/tmp/service_monitor_exec';

if (!file_exists($filename)) {
    if (touch($filename)) {
        
        // Monitorando a montagem da partição de memória RAM
        exec('df', $output, $return);
        $ram_mounted = false;
        foreach ($output as $line) {
            if (strpos($line, '/var/www/html/ram') !== false) {
                $ram_mounted = true;
                break;
            }
        }
        // A partição RAM não está montada, vamos montar...
        if (!$ram_mounted) {
            $command = "mount -t tmpfs -o size=1000m,mode=0755,uid=$(id -u www-data),gid=$(id -g www-data) tmpfs /var/www/html/ram";
            //$command = "mount -t ramfs -o size=1000m,mode=0755,uid=$(id -u www-data),gid=$(id -g www-data) ramfs /var/www/html/ram";
            exec($command, $output, $return_val);
            if ($return_val === 0) {
                // Partição de memória RAM montada com sucesso!
            } else {
                $message = date('Y-m-d H:i:s') . ' - Erro ao montar a partição de memória RAM';
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            }
        }

        // Monitorando se o mariadb está ativo
        $output = shell_exec('systemctl is-active mariadb');
        if (trim($output) == 'active') {
            // MariaDB está em execução!
        } else {
            exec('systemctl start mariadb');
            sleep(5); // espera 5 segundos para o serviço iniciar completamente
            $status = shell_exec('systemctl status mariadb');
            if (strpos($status, 'Active: active (running)') !== false) {
                $message = date('Y-m-d H:i:s') . ' - MariaDB iniciado com sucesso';
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            } else {
                $message = date('Y-m-d H:i:s') . ' - Falha ao iniciar o MariaDB';
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            }
        }

        // Monitora se o Unbound está ativo
        $output = shell_exec('systemctl is-active unbound');
        if (trim($output) == 'active') {
            // Unbound está em execução!
        } else {
            exec('systemctl start unbound');
            sleep(5); // espera 5 segundos para o serviço iniciar completamente
            $status = shell_exec('systemctl status unbound');
            if (strpos($status, 'Active: active (running)') !== false) {
                $message = date('Y-m-d H:i:s') . ' - Unbound foi iniciado com sucesso';
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            } else {
                $message = date('Y-m-d H:i:s') . ' - Falha ao iniciar o Unbound';
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            }
        }

        // Monitora se o Nginx está ativo
        $output = shell_exec('systemctl is-active nginx');
        if (trim($output) == 'active') {
            // Nginx está em execução!
        } else {
            exec('systemctl start nginx');
            sleep(5); // espera 5 segundos para o serviço iniciar completamente
            $status = shell_exec('systemctl status nginx');
            if (strpos($status, 'Active: active (running)') !== false) {
                $message = date('Y-m-d H:i:s') . ' - Nginx foi iniciado com sucesso';
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            } else {
                $message = date('Y-m-d H:i:s') . ' - Falha ao iniciar o Nginx';
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            }
        }

        // Monitora se o Shellinabox está ativo
        $output1 = shell_exec('ps aux | grep shellinaboxd | grep -v grep | wc -l');
        if (trim($output1) == '0') {
            exec('/etc/init.d/shellinabox stop');
            $closed_processes = shell_exec("ps aux | grep 'shellinaboxd' | grep -v grep | awk '{print $2}'");
            $closed_processes = explode("\n", trim($closed_processes));
            foreach ($closed_processes as $process) {
                if (!empty($process)) {
                    exec("kill -9 $process");
                }
            }
            exec('/etc/init.d/shellinabox start');
            sleep(5); // espera 5 segundos para o serviço iniciar completamente
            $output2 = shell_exec('ps aux | grep shellinaboxd | grep -v grep | wc -l');
            if (trim($output2) == '0') {
                $message = date('Y-m-d H:i:s') . ' - Falha ao iniciar o Shellinabox';
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            } else {
                $message = date('Y-m-d H:i:s') . ' - Shellinabox foi iniciado com sucesso';
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            }
        }

        // Monitora scripts de apoio que executam em blackground
        $output = shell_exec('ps aux | grep "loop" | grep -v grep');
        $scripts = array('loopdns', 'loop_cmd_kill', 'looprede', 'loopnginx', 'loop_poweroff', 'loopvpn', 'loopspeedtest', 'looptimezone', 'loopnode');
        for($i=0;$i<count($scripts);$i++) {
            if (strpos($output, $scripts[$i]) === false) {
                if (file_exists('/tmp/lockfile_'.$scripts[$i])) { unlink('/tmp/lockfile_'.$scripts[$i]); }
                $output2 = array();
                exec('/var/www/html/cron/shell/'.$scripts[$i].'.sh > /dev/null 2>&1 &', $output2);
                if (empty($output2)) {
                    // echo "Comando executado com sucesso! " . $scripts[$i] . "\n";
                } else {
                    // echo "Erro ao executar o comando: " . implode("\n", $output2);
                    $message = date('Y-m-d H:i:s') . ' - Falha ao iniciar o script ' . $scripts[$i] . '. Erro no comando: ' . implode("\n", $output2);
                    file_put_contents($log_file, $message . "\n", FILE_APPEND);
                }
            }
        }

        // Monitorando os arquivos de Log do sistema, limpar arquivos muito grandes....
        /*$limit = 50 * 1024 * 1024; //Tamanho do arquivo em bytes (50 MB = 50 * 1024 * 1024)
        $dir = new DirectoryIterator('/var/log');
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getSize() >= $limit) {
                // Esvaziar o conteúdo do arquivo de log
                file_put_contents($fileInfo->getPathname(), '');
                $message = date('Y-m-d H:i:s') . ' - O conteúdo do arquivo ' . $fileInfo->getPathname() . ' foi esvaziado';
                file_put_contents($log_file, $message . "\n", FILE_APPEND);
            }
        }*/

        $limit = 50 * 1024 * 1024; // Tamanho do arquivo em bytes (50 MB = 50 * 1024 * 1024)
        $files = glob('/var/log/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                $fileSize = filesize($file);

                if ($fileSize === false) {
                    $message = date('Y-m-d H:i:s') . ' - Não foi possível determinar o tamanho do arquivo ' . $file;
                    file_put_contents($log_file, $message . "\n", FILE_APPEND);
                    continue;
                }

                if ($fileSize >= $limit) {
                    // Esvaziar o conteúdo do arquivo de log
                    $result = file_put_contents($file, '');

                    if ($result === false) {
                        $message = date('Y-m-d H:i:s') . ' - Não foi possível esvaziar o conteúdo do arquivo ' . $file;
                    } else {
                        $message = date('Y-m-d H:i:s') . ' - O conteúdo do arquivo ' . $file . ' foi esvaziado';
                    }

                    file_put_contents($log_file, $message . "\n", FILE_APPEND);
                }
            }
        }

        // Monitorando os pacotes e serviços que envolvem o bom funcionamento do Whatsapp
        $pidexec = exec("ps aux | grep 'whatsapp_monitor.sh' | grep -v grep | awk '{print $2}'");
        if(!$pidexec) { exec("/var/www/html/cron/shell/whatsapp_monitor.sh &"); }
    }
}

unlink($filename);
?>