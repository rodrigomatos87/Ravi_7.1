#!/bin/bash

# Função para gerenciar o Ookla Speedtest
manage_ookla() {
    local action="$1"
    local log_message="$2"

    cd /var/www/html/ookla
    sh ooklaserver.sh "$action"
    echo "$(date +'%Y-%m-%d %H:%M:%S') - $log_message" >> /var/log/ravi.log
}

while : ; do
    # Usa flock para impedir execução simultânea,
    # caso a execução leve mais do que dez segundos.
    (
        flock -n 200 || exit 1

        # Reiniciar Ookla
        if [ -f /var/www/html/ram/rebootookla ]; then
            manage_ookla restart "Speedtest Ookla reiniciado por solicitação"
            rm -f /var/www/html/ram/rebootookla
        fi

        # Parar Ookla
        if [ -f /var/www/html/ram/stopookla ]; then
            manage_ookla stop "Speedtest Ookla parado por solicitação"
            rm -f /var/www/html/ram/stopookla
        fi

        # Iniciar Ookla
        if [ -f /var/www/html/ram/startookla ]; then
            manage_ookla start "Speedtest Ookla iniciado"
            rm -f /var/www/html/ram/startookla
        fi

    ) 200>/tmp/lockfile_loopspeedtest

    sleep 1
done