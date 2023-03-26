<?php
include("/var/www/html/cron/apoio/conexao.php");

//ini_set('display_errors',1);
//ini_set('display_startup_erros',1);
//error_reporting(E_ALL);

$sel = mysqli_query($db, "select ativaSSL, certificado_crt, certificado_key, certificado_crt_cli, certificado_key_cli from system");
$reg = mysqli_fetch_array($sel);

// Certificado SSL do próprio cliente
if($reg['ativaSSL'] == 1) {
    $crt = $reg['certificado_crt_cli'];
    $key = $reg['certificado_key_cli'];
    ativar_ssl_nginx($crt, $key);

// Certificado SSL da Ravi
}else if($reg['ativaSSL'] == 2) {
    $crt = $reg['certificado_crt'];
    $key = $reg['certificado_key'];
    ativar_ssl_nginx($crt, $key);

// Desativar SSL
}else if($reg['ativaSSL'] == 3) {
    if(is_dir("/var/www/html/ram/nginx")) { unlink('/var/www/html/ram/nginx'); }
    $conn_nginx = fopen('/var/www/html/ram/nginx','w+');
    fwrite($conn_nginx, "server {
    listen 443;
    listen [::]:443 ipv6only=on;

    root /var/www/html;

    return 301 http://\$host\$request_uri;
    rewrite ^/(.*)/$ /$1 permanent;

    index index.html;

    server_name ravisystems.com.br;
    
    access_log  off;
    #access_log  /var/log/nginx/http-ravisystems-access.log;
    error_log   /var/log/nginx/http-ravisystems-error.log;

    server_tokens off;

    location / {
	try_files \$uri \$uri/ =404;
    }
}

server {
    listen 80;
    listen [::]:80 ipv6only=on;

    server_name ravisystems.com.br;

    # Log files for Debugging
    access_log  off;
    #access_log  /var/log/nginx/https-ravisystems-access.log;
    error_log   /var/log/nginx/https-ravisystems-error.log;

    add_header Strict-Transport-Security 'max-age=31536000; includeSubDomains; preload' always;
    #add_header \"X-Frame-Options\" \"DENY\" always; -> disable iframes
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection \"1; mode=block\";
    add_header Referrer-Policy \"no-referrer\";
    add_header \"Referrer-Policy\" \"strict-origin\";
    server_tokens off;

    root /var/www/html;
    index index index.php index.html index.htm index.nginx-debian.html;
    autoindex off;

    location ~ \.php$ {
        fastcgi_split_path_info             ^(.+\.php)(/.+)$;
        fastcgi_index                       index.php;
        try_files                           \$uri = 404;
        fastcgi_pass                        unix:/run/php/php7.2-fpm.sock;
        fastcgi_buffers 16                  16k;
        fastcgi_buffer_size                 32k;
        include                             fastcgi_params;
        fastcgi_param  PATH_INFO            \$fastcgi_path_info;
        fastcgi_param  SCRIPT_FILENAME      \$document_root\$fastcgi_script_name;
        fastcgi_param  QUERY_STRING         \$query_string;
        fastcgi_param  REQUEST_METHOD       \$request_method;
        fastcgi_param  CONTENT_TYPE         \$content_type;
        fastcgi_param  CONTENT_LENGTH       \$content_length;

        fastcgi_param  SCRIPT_NAME          \$fastcgi_script_name;
        fastcgi_param  REQUEST_URI          \$request_uri;
        fastcgi_param  DOCUMENT_URI         \$document_uri;
        fastcgi_param  DOCUMENT_ROOT        \$document_root;
        fastcgi_param  SERVER_PROTOCOL      \$server_protocol;
        fastcgi_param  REQUEST_SCHEME       \$scheme;
        fastcgi_param  HTTPS                \$https if_not_empty;

        fastcgi_param  GATEWAY_INTERFACE    CGI/1.1;
        fastcgi_param  SERVER_SOFTWARE      nginx/\$nginx_version;

        fastcgi_param  REMOTE_ADDR          \$remote_addr;
        fastcgi_param  REMOTE_PORT          \$remote_port;
        fastcgi_param  SERVER_ADDR          \$server_addr;

        fastcgi_param  SERVER_PORT          \$server_port;
        fastcgi_param  SERVER_NAME          \$host;

        # PHP only, required if PHP was built with --enable-force-cgi-redirect
        fastcgi_param  REDIRECT_STATUS      200;
        fastcgi_param  HTTP_PROXY           \"\";
        client_max_body_size                50M;
        client_body_buffer_size             128k;

        fastcgi_read_timeout                1200;
        proxy_connect_timeout               1200;
		proxy_send_timeout                  1200;
		proxy_read_timeout                  1200;
        send_timeout                        1200;
    }

    location ~* /shell/ { 
		proxy_pass http://127.0.0.1:4243;
		proxy_redirect default;

		proxy_set_header Host \$host;
		proxy_set_header X-Real-IP \$remote_addr;
		proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;

		client_max_body_size 10m;
		client_body_buffer_size 128k;

		proxy_connect_timeout 90;
		proxy_send_timeout 90;
		proxy_read_timeout 90;

		proxy_buffer_size 4k;
		proxy_buffers 4 32k;
		proxy_busy_buffers_size 64k;
		proxy_temp_file_write_size 64k;

        access_log  off;
		#access_log /var/log/nginx/shellinabox.access.log;
		error_log /var/log/nginx/shellinabox.error.log;
    }

    location ~ /whatsapp(?<route>.+) { 
		proxy_pass http://127.0.0.1:9050\$route;

		proxy_set_header Host \$host;
		proxy_set_header X-Real-IP \$remote_addr;
		proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;

		client_max_body_size 10m;
		client_body_buffer_size 128k;

		proxy_connect_timeout 90;
		proxy_send_timeout 90;
		proxy_read_timeout 90;

		proxy_buffer_size 4k;
		proxy_buffers 4 32k;
		proxy_busy_buffers_size 64k;
		proxy_temp_file_write_size 64k;

        access_log  off;
		#access_log /var/log/nginx/whatsapp.access.log;
		error_log /var/log/nginx/whatsapp.error.log;
	}

    location / {
	try_files \$uri \$uri/ =404;
    }
}"); 
    fclose($conn_nginx);
}

function ativar_ssl_nginx ($crt, $key) {
    if(is_dir("/var/www/html/ram/certificado.crt")) { unlink('/var/www/html/ram/certificado.crt'); }
    $conn_crt = fopen('/var/www/html/ram/certificado.crt','w+');
    fwrite($conn_crt, $crt);
    fclose($conn_crt);
    
    if(is_dir("/var/www/html/ram/certificado.key")) { unlink('/var/www/html/ram/certificado.key'); }
    $conn_key = fopen('/var/www/html/ram/certificado.key','w+');
    fwrite($conn_key, $key);
    fclose($conn_key);

    if(is_dir("/var/www/html/ram/nginx")) { unlink('/var/www/html/ram/nginx'); }
    $conn_nginx = fopen('/var/www/html/ram/nginx','w+');
    fwrite($conn_nginx, "server {
    listen 80;
    listen [::]:80 ipv6only=on;

    root /var/www/html;

    return 301 https://\$host\$request_uri;
    rewrite ^/(.*)/$ /$1 permanent;

    index index.html;

    server_name www.ravisystems.com.br;
    
    access_log  off;
    #access_log  /var/log/nginx/http-ravisystems-access.log;
    error_log   /var/log/nginx/http-ravisystems-error.log;

    server_tokens off;

    location / {
	try_files \$uri \$uri/ =404;
    }
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2 ipv6only=on;

    server_name  ravisystems.com.br;

    # Log files for Debugging
    access_log  /var/log/nginx/https-ravisystems-access.log;
    error_log   /var/log/nginx/https-ravisystems-error.log;

    ssl_certificate             /etc/nginx/ssl/certificado.crt;
    ssl_certificate_key         /etc/nginx/ssl/certificado.key;
    ssl_protocols               TLSv1.2 TLSv1.3;
    ssl_ciphers                 EECDH+ECDSA+AESGCM:EECDH+aRSA+AESGCM:EECDH+ECDSA+SHA512:EECDH+ECDSA+SHA384:EECDH+ECDSA+SHA256:ECDH+AESGCM:ECDH+AES256:DH+AESGCM:DH+AES256:RSA+AESGCM:!aNULL:!eNULL:!LOW:!RC4:!3DES:!MD5:!EXP:!PSK:!SRP:!DSS:!SHA384:!SHA256;
    ssl_prefer_server_ciphers   off;
    ssl_session_timeout         1d;
    ssl_session_cache           shared:SSL:2m;
    ssl_buffer_size             4k;

    ssl_stapling        on;
    ssl_stapling_verify on;
    #resolver            8.8.8.8 8.8.4.4 [2606:4700:4700::1111] [2606:4700:4700::1001];
    resolver            8.8.8.8 8.8.4.4;
    add_header Strict-Transport-Security 'max-age=31536000; includeSubDomains; preload' always;
    #add_header \"X-Frame-Options\" \"DENY\" always; -> disable iframes
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection \"1; mode=block\";
    add_header Referrer-Policy \"no-referrer\";
    add_header \"Referrer-Policy\" \"strict-origin\";
    server_tokens off;

    root /var/www/html;
    index index index.php index.html index.htm index.nginx-debian.html;
    autoindex off;

    location ~ \.php$ {
        fastcgi_split_path_info             ^(.+\.php)(/.+)$;
        fastcgi_index                       index.php;
        try_files                           \$uri = 404;
        fastcgi_pass                        unix:/run/php/php7.2-fpm.sock;
        fastcgi_buffers 16                  16k;
        fastcgi_buffer_size                 32k;
        include                             fastcgi_params;
        fastcgi_param  PATH_INFO            \$fastcgi_path_info;
        fastcgi_param  SCRIPT_FILENAME      \$document_root\$fastcgi_script_name;
        fastcgi_param  QUERY_STRING         \$query_string;
        fastcgi_param  REQUEST_METHOD       \$request_method;
        fastcgi_param  CONTENT_TYPE         \$content_type;
        fastcgi_param  CONTENT_LENGTH       \$content_length;

        fastcgi_param  SCRIPT_NAME          \$fastcgi_script_name;
        fastcgi_param  REQUEST_URI          \$request_uri;
        fastcgi_param  DOCUMENT_URI         \$document_uri;
        fastcgi_param  DOCUMENT_ROOT        \$document_root;
        fastcgi_param  SERVER_PROTOCOL      \$server_protocol;
        fastcgi_param  REQUEST_SCHEME       \$scheme;
        fastcgi_param  HTTPS                \$https if_not_empty;

        fastcgi_param  GATEWAY_INTERFACE    CGI/1.1;
        fastcgi_param  SERVER_SOFTWARE      nginx/\$nginx_version;

        fastcgi_param  REMOTE_ADDR          \$remote_addr;
        fastcgi_param  REMOTE_PORT          \$remote_port;
        fastcgi_param  SERVER_ADDR          \$server_addr;

        fastcgi_param  SERVER_PORT          \$server_port;
        fastcgi_param  SERVER_NAME          \$host;

        # PHP only, required if PHP was built with --enable-force-cgi-redirect
        fastcgi_param  REDIRECT_STATUS      200;
        fastcgi_param  HTTP_PROXY           \"\";
        client_max_body_size                50M;
        client_body_buffer_size             128k;

        fastcgi_read_timeout                1200;
        proxy_connect_timeout               1200;
		proxy_send_timeout                  1200;
		proxy_read_timeout                  1200;
        send_timeout                        1200;
        fastcgi_send_timeout                1200;
    }

    location ~* /shell/ { 
		proxy_pass http://127.0.0.1:4243;
		proxy_redirect default;

		proxy_set_header Host \$host;
		proxy_set_header X-Real-IP \$remote_addr;
		proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;

		client_max_body_size 10m;
		client_body_buffer_size 128k;

		proxy_connect_timeout 90;
		proxy_send_timeout 90;
		proxy_read_timeout 90;

		proxy_buffer_size 4k;
		proxy_buffers 4 32k;
		proxy_busy_buffers_size 64k;
		proxy_temp_file_write_size 64k;

        access_log  off;
		#access_log /var/log/nginx/shellinabox.access.log;
		error_log /var/log/nginx/shellinabox.error.log;
    }

    location ~ /whatsapp(?<route>.+) { 
		proxy_pass http://127.0.0.1:9050\$route;

		proxy_set_header Host \$host;
		proxy_set_header X-Real-IP \$remote_addr;
		proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;

		client_max_body_size 10m;
		client_body_buffer_size 128k;

		proxy_connect_timeout 90;
		proxy_send_timeout 90;
		proxy_read_timeout 90;

		proxy_buffer_size 4k;
		proxy_buffers 4 32k;
		proxy_busy_buffers_size 64k;
		proxy_temp_file_write_size 64k;

        access_log  off;
		#access_log /var/log/nginx/whatsapp.access.log;
		error_log /var/log/nginx/whatsapp.error.log;
	}

    location / {
	try_files \$uri \$uri/ =404;
    }
}"); 
    fclose($conn_nginx);
}

mysqli_close($db);
?>