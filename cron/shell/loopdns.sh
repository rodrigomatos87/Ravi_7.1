#!/bin/bash

# Define a função para reiniciar o DNS e realizar outras ações relacionadas
manage_dns() {
    # Reiniciar DNS
    if [ -f /var/www/html/ram/rebootDNS ]; then
        /etc/init.d/unbound stop
        for CLOSED in $(ps aux | grep 'unbound' | grep -v grep | awk '{print $2}'); do kill $CLOSED; done
        mv -f /var/www/html/ram/unbound.conf /etc/unbound/unbound.conf
        mv -f /var/www/html/ram/tunning.conf /etc/unbound/local.d/tunning.conf
        mv -f /var/www/html/ram/extra-security.conf /etc/unbound/local.d/extra-security.conf
        mv -f /var/www/html/ram/reserva.conf /etc/unbound/conf.d/reserva.conf
        rm -rf /etc/unbound/dominios.d/*

        if [ "$(ls /var/www/html/ram/dominios/ | wc -l)" -ge 1 ]; then
            mv -f /var/www/html/ram/dominios/* /etc/unbound/dominios.d/
        fi

        chown -R root:root /etc/unbound/dominios.d
        chown root:root /etc/unbound/unbound.conf
        chown root:root /etc/unbound/conf.d/reserva.conf
        chown root:root /etc/unbound/local.d/extra-security.conf
        chown root:root /etc/unbound/local.d/tunning.conf
        /etc/init.d/unbound start
        /etc/init.d/cron start
        echo "$(date +'%Y-%m-%d %H:%M:%S') - Unbound reiniciado por solicitação" >> /var/log/ravi.log
        rm -f /var/www/html/ram/rebootDNS
        rm -f /var/www/html/ram/temp_reversos.txt
    fi

    # Processa DNS
    if [ -f /var/www/html/ram/processa_dns ]; then
        /etc/init.d/cron stop
        for CLOSED in $(ps aux | grep 'php -f' | grep -v grep | awk '{print $2}'); do kill $CLOSED; done
        rm -rf /var/www/html/ram/html/cron/dirs/olt/*
        rm -rf /var/www/html/ram/html/cron/dirs/valores/*
        php -f /var/www/html/processa_dns.php &
        rm -f /var/www/html/ram/processa_dns
    fi

    # Limpar DNS
    if [ -f /var/www/html/ram/limparDNS ]; then
        unbound-control flush $(cat /var/www/html/ram/limparDNS)
        echo "$(date +'%Y-%m-%d %H:%M:%S') - $(cat /var/www/html/ram/limparDNS) Removido do cache DNS por solicitação" >> /var/log/ravi.log
        rm -f /var/www/html/ram/limparDNS
    fi
}

# Loop principal
while : ; do
    # Usa o flock para impedir execução simultânea
    (
        flock -n 9 || exit 1

        # Chama a função para gerenciar o DNS
        manage_dns
    ) 9>/tmp/lockfile_loopdns

    sleep 1
done