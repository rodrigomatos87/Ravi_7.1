#!/bin/bash

# Define a função para sincronizar arquivos e reiniciar o Nginx
sync_files_and_restart_nginx() {
    if [ -f /var/www/html/ram/nginx ]; then
        chown root:root /var/www/html/ram/nginx
        cp /etc/nginx/sites-available/ravimonitor /tmp/ravimonitor
        mv -f /var/www/html/ram/nginx /etc/nginx/sites-available/ravimonitor
        if [ -f /var/www/html/ram/certificado.crt ]; then
            chown root:root /var/www/html/ram/certificado.crt
            mv -f /var/www/html/ram/certificado.crt /etc/nginx/ssl/certificado.crt
        fi
        if [ -f /var/www/html/ram/certificado.key ]; then
            chown root:root /var/www/html/ram/certificado.key
            mv -f /var/www/html/ram/certificado.key /etc/nginx/ssl/certificado.key
        fi
        # Reiniciar nginx
        /etc/init.d/nginx restart
    fi
}

# Loop principal
while : ; do
    # Usa o flock para impedir execução simultânea
    (
        flock -n 9 || exit 1

        # Chama a função para sincronizar arquivos e reiniciar o Nginx
        sync_files_and_restart_nginx
    ) 9>/tmp/lockfile_loopnginx

    sleep 1
done
