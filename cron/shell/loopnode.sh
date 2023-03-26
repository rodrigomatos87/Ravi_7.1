#!/bin/bash
PATH=$PATH:/usr/bin:/usr/local/bin

# Define a função para reiniciar o servidor ravi-node
restart_ravi_node() {
    if [ -f /var/www/html/ram/rebootnode ]; then
        cd /var/www/html/node
        pm2 delete all
        pm2 save --force
        pm2 start /var/www/html/node/server.js --name ravi-node --max-restarts 10 --restart-delay 5000 --watch false
        sleep 2
        pm2 save
        pm2 startup
        rm -f /var/www/html/ram/rebootnode
        rm -f /var/www/html/ram/reinstallnode
        echo "$(date +'%Y-%m-%d %H:%M:%S') - pm2 reiniciou o processo ravi-node por solicitação" >> /var/log/ravi.log
    fi
}

# Loop principal
while : ; do
    # Usa o flock para impedir execução simultânea
    (
        flock -n 9 || exit 1

        # Chama a função para reiniciar o servidor ravi-node
        restart_ravi_node
    ) 9>/tmp/lockfile_loopnode

    sleep 1
done