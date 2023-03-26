#!/usr/bin/php
<?PHP
// obtém o ID do usuário e do grupo do usuário nginx
$user_info = posix_getpwnam('www-data');
$user_id = $user_info['uid'];
$group_id = $user_info['gid'];

// altera o usuário e o grupo do processo atual para o usuário www-data
posix_setgid($group_id);
posix_setuid($user_id);

$diretorio = "/opt/Ravi/sensores/";

// obtem uma lista de todas as pastas no diretorio
$pastas = array_filter(scandir($diretorio), function($item) use($diretorio) {
    return is_dir($diretorio . $item) && !in_array($item, array('.', '..'));
});

// percorre cada pasta e exibe o conteudo
foreach($pastas as $pasta) {
    $arquivos = array_filter(scandir($diretorio . $pasta), function($item) use($diretorio, $pasta) {
        return !is_dir($diretorio . $pasta . '/' . $item) && preg_match('/\.csv$/', $item) && !in_array($item, array('.', '..'));
    });
    foreach($arquivos as $arquivo) {
        // não compactar arquivos do dia atual
        if($arquivo != 'd-'.date('Ymd').'.csv') {
            // Versao comprimida ja existe
            $zpath = $diretorio.$pasta.'/'.$arquivo.'.gz';
            if(is_file($zpath)) {
                // Se o tamanho do arquivo for zero, apagar e gerar novo
                $zlen = filesize($zpath);
                if($zlen){
                    // Ok, ignorar
                    // Se o arquivo texto existir, apaga-lo
                    unlink($diretorio.$pasta.'/'.$arquivo);
                    continue;
                }else {
                    unlink($zpath);
                    continue;
                }
            }

            $arquivo_compactado = $zpath;
            $arquivo_original = $diretorio.$pasta.'/'.$arquivo;
            // Obter conteúdo do arquivo CSV
            $csvcontent = file_get_contents($arquivo_original);
            // Compactar conteúdo
            $gzcontent = zlib_encode($csvcontent, ZLIB_ENCODING_GZIP, 9);
            // Salvar conteúdo compactado
            file_put_contents($arquivo_compactado, $gzcontent);
            // Excluindo o arquivo original
            unlink($arquivo_original);
        }
    }
}

?>