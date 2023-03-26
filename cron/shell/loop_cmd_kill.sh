#!/bin/bash

# Função para gerenciar processos
manage_processes() {
    if [ -f /var/www/html/ram/killG.pid ]; then
        for CLOSED in $(cat /var/www/html/ram/killG.pid); do kill -9 $CLOSED; done
        rm -fr /var/www/html/ram/killG.pid
    fi

    if [ -f /var/www/html/ram/killcmd.pid ]; then
        for CLOSED in $(cat /var/www/html/ram/killcmd.pid); do
            kill -9 $CLOSED
            if [ $(ps aux | grep '/bin/bash /var/www/html/cron/shell/cmd_web.sh' | grep -v grep | awk '{print $2}' | grep $CLOSED | wc -l) = 0 ]; then
                cat /var/www/html/ram/killcmd.pid | grep -v $CLOSED | grep -v "^$" > /var/www/html/ram/killcmd.pid
            fi
            if [ $(cat /var/www/html/ram/killcmd.pid | wc -l) = 0 ]; then
                rm -fr /var/www/html/ram/killcmd.pid
            fi
        done
    fi
}

# Loop principal
while : ; do
    # Usa o flock para impedir execução simultânea
    (
        flock -n 9 || exit 1

        # Chama a função para gerenciar processos
        manage_processes
    ) 9>/tmp/lockfile_loop_manage_processes

    sleep 1
done