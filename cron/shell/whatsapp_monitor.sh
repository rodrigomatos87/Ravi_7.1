#!/bin/bash
PATH=$PATH:/usr/bin:/usr/local/bin

log_file="/var/log/ravi.log"

# Verificando as dependências do Whatsapp
if [ ! -d "/var/www/html/node/node_modules" ]; then
    # Instala as dependências no diretório
    cd /var/www/html/node
    output=$(npm install 2>&1)
    # Verifica se a saída contém mensagens de erro ou de sucesso
    if [[ "$output" == *"error"* ]]; then
        message=$(date +"%Y-%m-%d %H:%M:%S")" - Erro ao instalar Dependências do Whatsapp: $output"
        echo "$message" >> $log_file
    else
        echo $(date +"%Y-%m-%d %H:%M:%S")" - Dependências do Whatsapp instaladas com sucesso" >> $log_file
    fi
fi

# Verifica se o pm2 está instalado
if command -v pm2 &> /dev/null; then
    # Verifica se o processo "ravi-node" está em execução
    cd /var/www/html/node
    output=$(pm2 list)
    if ! echo "$output" | grep -Eq "ravi-node.*(online|enabled)"; then
        pm2 delete all
        pm2 save --force
        pm2 start /var/www/html/node/server.js --name ravi-node --max-restarts 10 --restart-delay 5000 --watch false
        sleep 1
        pm2 save
        pm2 startup
        echo $(date +"%Y-%m-%d %H:%M:%S")" - pm2 iniciou o servidor ravi-node com sucesso!" >> $log_file
    fi
else
    # Muda o diretório atual para o diretório da aplicação
    cd /var/www/html/node
    output=$(npm install pm2@latest -g 2>&1)
    if [[ "$output" == *"error"* ]]; then
        message=$(date +"%Y-%m-%d %H:%M:%S")" - Erro ao instalar o pm2 $output"
        echo "$message" >> $log_file
    else
        pm2 delete all
        pm2 start /var/www/html/node/server.js --name ravi-node --max-restarts 10 --restart-delay 5000 --watch false
        sleep 1
        pm2 save
        pm2 startup
        echo $(date +"%Y-%m-%d %H:%M:%S")" - pm2 instalado e servidor ravi-node iniciado com sucesso!" >> $log_file
    fi
fi

# Verifica se o servidor está respondendo na porta 9050
if ! curl --fail -s http://localhost:9050/check-update > /dev/null; then
    cd /var/www/html/node
    pm2 restart ravi-node
    echo $(date +"%Y-%m-%d %H:%M:%S")" - Processo ravi-node parou de responder na porta 9050 e foi reiniciado" >> $log_file
fi

exit 0