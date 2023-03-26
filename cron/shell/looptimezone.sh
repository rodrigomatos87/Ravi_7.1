#!/bin/bash

while : ; do
    # Verifica se o arquivo de timezone existe.
    if [ -f /var/www/html/ram/timezone ]; then
        {
            # Usa um arquivo de lock para impedir execução simultânea.
            if flock -n /tmp/lockfile_looptimezone -c true; then
                TIMEATUAL=$(grep 'date.timezone = ' /etc/php/7.2/fpm/php.ini)
                TIMECAT=$(cat /var/www/html/ram/timezone)
                TIMENOVO="date.timezone = $TIMECAT"
                sed -i "s|$TIMEATUAL|$TIMENOVO|g" /etc/php/7.2/fpm/php.ini
                timedatectl set-timezone "$TIMECAT"
                /etc/init.d/php7.2-fpm restart > /dev/null
                /etc/init.d/nginx restart > /dev/null
                rm -f /var/www/html/ram/timezone
                echo "$(date +'%Y-%m-%d %H:%M:%S') - Timezone sync executado com sucesso" >> /var/log/ravi.log
            else
                echo "$(date +'%Y-%m-%d %H:%M:%S') - Não é possível adquirir o arquivo de bloqueio /tmp/lockfile_looptimezone" >> /var/log/ravi.log
            fi
        } 2>&1
    fi
    sleep 1
done

