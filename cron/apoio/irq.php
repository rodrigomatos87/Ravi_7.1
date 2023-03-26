<?php
    
    /*
    Responsavel em direcionar os IRQs de uma placa de rede
    a uma CPU especifica
    */
    
    
    /*
    Funcao - transforma valor recebido em peso binario
    Exemplo:
        valor recebido -> 8
        coloca 1 na primeira posicao e completa com zero -> 100000000
        logo converte de binario para decimal e por fim em hexadecimal
        1 0000 0000 => 100
    */
    
    function bin_hex($number){
    
	// Converter para inteiro
	$number = (int)$number;
    
	// Completar com zeros segundo o valor enviado, acrescentando um digito a mais
	$number = str_pad(1,($number+1), 0,STR_PAD_RIGHT);
    
	// Converter para decimal, por fim hexadecimal
	$number = dechex(bindec($number));
    
	return $number;

    }
    
    // Obter lista de IRQs que se referem as placas de rede
    $l_irqs = shell_exec("cat /proc/interrupts | egrep 'eth|ens' | cut -f1 -d:");

    // Transformar em array
    $l_irqs = explode(' ', $l_irqs);


    // Tratar array, caso tenha posicao vazia, descarta posicao
    $c = 0;
    $list_irqs = array();
    foreach($l_irqs as $irq){
	if($irq != '') $list_irqs[$c] = trim($irq);
	else continue;
	$c++;
    }

    // Obter quantidade de CPUs existente no Servidor
    $t_cpus = trim(shell_exec('awk "{print \$NF}" /proc/interrupts | grep "CPU" | grep -o "\([0-9]\{1,2\}\)"'));


    // Verificar se existe mais de uma CPU
    if($t_cpus > 0){
	/*
        Deve ser necessario fazer conversao
        Logo se a quantidade de CPUs, forem 8
        teremos: 
        8 => adiciona 8+1 digitos, completando com zeros
        na primeira posicao incluir o 1 como peso
        Resultado => 1 0000 0000 => 100
	*/ 
	// Array para armazenar CPUs, apos a conversao
	$list_cpus = array();
	for($i=$t_cpus; $i>=0;$i--) $list_cpus[$i]=bin_hex($i);
    
    }else{
	echo "Nao e possivel aplicar afinidade entre IRQs e CPUs. Existe somente uma CPU implantada.\n ";
	exit;
    }

    // Total de interrupcoes, que se referem as placas de rede
    $t_irqs = (count($list_irqs) - 1);

    // Variaveis auxiliares
    $total_irqs = $t_irqs;
    $total_cpus = $t_cpus;


    // Verificar se existe mais de uma CPU
    if($t_cpus > 0 && $t_irqs){
        // Enquanto existir IRQ, entao interar com uma CPU
        while($t_irqs>=0){
        
            // IRQ a ser direcionado
            $dir = "/proc/irq/$list_irqs[$t_irqs]/smp_affinity";
            
            // Abrir o arquivo
            $current = file_get_contents($dir);
        
            // CPU que sera responsavel pelo IRQ
            $current = $list_cpus[$total_cpus];
        
            // Atribuindo IRQ a uma CPU
            // Quando maior que 1, atribuir e logo descer para a proxima CPU
            if($total_cpus > 1){
            // Escrever no arquivo, qual CPU sera responsavel pelo IRQ
            file_put_contents($dir, $current);
            $total_cpus--;
            }else{ // Quando chegar na 2Âª CPU, atualizar o total_cpus para o fim 
            // Escrever no arquivo, qual CPU sera responsavel pelo IRQ
            file_put_contents($dir,$current);
            $total_cpus = $t_cpus;
            }
        
            $t_irqs--;
        
        }
    }


?>