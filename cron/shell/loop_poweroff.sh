#!/bin/bash

# Função para reiniciar ou desligar o servidor Ravi
manage_power_state() {
    # Reiniciar
    if [ -f /var/www/html/ram/rebootRavi ]; then
        rm -rf /var/www/html/ram/rebootRavi
        echo "$(date +'%Y-%m-%d %H:%M:%S') - Servidor Ravi será reiniciado por solicitação" >> /var/log/ravi.log
        for CLOSED in $(ps aux | grep 'unbound' | grep -v grep | awk '{print $2}'); do kill $CLOSED; done
        /etc/init.d/php7.2-fpm stop
        /etc/init.d/nginx stop
        cd /var/www/html/node
        pm2 stop ravi-node
        /sbin/shutdown -r now
    fi

    # Desligar
    if [ -f /var/www/html/ram/poweroffRavi ]; then
        rm -rf /var/www/html/ram/poweroffRavi
        echo "$(date +'%Y-%m-%d %H:%M:%S') - Servidor Ravi será desligado por solicitação" >> /var/log/ravi.log
        for CLOSED in $(ps aux | grep 'unbound' | grep -v grep | awk '{print $2}'); do kill $CLOSED; done
        /etc/init.d/php7.2-fpm stop
        /etc/init.d/nginx stop
        cd /var/www/html/node
        pm2 stop ravi-node
        /sbin/shutdown -h now
    fi
}

# Loop principal
while : ; do
    # Usa o flock para impedir execução simultânea
    (
        flock -n 9 || exit 1

        # Chama a função para gerenciar o estado de energia do servidor Ravi
        manage_power_state
    ) 9>/tmp/lockfile_loop_poweroff

    sleep 1
done
