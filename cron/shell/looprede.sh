#!/bin/bash

# Define a função para sincronizar arquivos e reiniciar a rede
sync_files_and_restart_network() {
    # Sincronizar arquivos
    if [ -f /var/www/html/ram/interfaces ]; then
        chown root:root /var/www/html/ram/interfaces
        mv -f /etc/network/interfaces /etc/network/interfaces-bkp2
        mv -f /var/www/html/ram/interfaces /etc/network/interfaces
    fi
    if [ -f /var/www/html/ram/resolv.conf ]; then
        chown root:root /var/www/html/ram/resolv.conf
        mv -f /var/www/html/ram/resolv.conf /etc/resolv.conf
    fi
    # Reiniciar rede
    if [ -f /var/www/html/ram/rebootNETWORK ]; then
        rm -f /var/www/html/ram/rebootNETWORK
        /etc/init.d/networking restart
        /etc/init.d/unbound restart
        echo "$(date +'%Y-%m-%d %H:%M:%S') - Network e Unbound reiniciado por solicitação" >> /var/log/ravi.log
    fi
}

# Loop principal
while : ; do
    # Usa o flock para impedir execução simultânea
    (
        flock -n 9 || exit 1

        # Chama a função para sincronizar arquivos e reiniciar a rede
        sync_files_and_restart_network
    ) 9>/tmp/lockfile_looprede

    sleep 1
done