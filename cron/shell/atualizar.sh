#!/bin/bash

chown -R www-data:www-data /var/www/html

chmod +x /var/www/html/cron/exec/*/*
chmod +x /var/www/html/cron/shell/*
chmod +x /var/www/html/cron/Scripts/*

echo '[server]

[mysqld]
user = mysql
pid-file = /run/mysqld/mysqld.pid
basedir = /usr
datadir = /var/lib/mysql
tmpdir = /tmp
lc-messages-dir = /usr/share/mysql
lc-messages = en_US
skip-external-locking

performance_schema = off

skip-name-resolve
bind-address = 127.0.0.1
key_buffer_size = 128M
max_allowed_packet = 64M
#thread_stack = 192K
thread_cache_size = 15121
max_connections = 2400
max_user_connections = 2400
table_cache = 6500
myisam_sort_buffer_size = 64M
join_buffer_size = 4M
read_buffer_size = 4M
sort_buffer_size = 4M
wait_timeout = 3600
connect_timeout = 1000
tmp_table_size = 256M
max_heap_table_size = 51M
max_connect_errors = 1000
read_rnd_buffer_size = 300000
bulk_insert_buffer_size = 512M
query_cache_limit = 512M
query_cache_size = 512M
query_cache_type = 1
query_prealloc_size = 65536
query_alloc_block_size = 131072
innodb_buffer_pool_size = 3G
innodb_flush_log_at_trx_commit = 2
innodb_log_buffer_size = 4M
open_files_limit = 8192

#log_error = /var/log/mysql/error.log
# Habilite o log de consultas lentas para ver consultas com duração especialmente longa
#slow_query_log_file = /var/log/mysql/mariadb-slow.log
#long_query_time = 10
#log_slow_verbosity = query_plan,explain
#log-queries-not-using-indexes
#min_examined_row_limit = 1000

expire_logs_days = 10
#max_binlog_size = 100M

character-set-server = utf8mb4
collation-server = utf8mb4_general_ci

[embedded]

[mariadb]

[mariadb-10.5]
' > /etc/mysql/mariadb.conf.d/50-server.cnf

sed -i "s/; max_input_vars = 1000/max_input_vars = 5000/g" /etc/php/7.2/fpm/php.ini

/etc/init.d/mariadb restart
/etc/init.d/php7.2-fpm restart
/etc/init.d/nginx restart

rm -fr /var/www/html/cron/exec/5min/procuraAutorizarONU.php
rm -fr /var/www/html/ContagemFTP.php
rm -fr /var/www/html/deleteonuhuawei.php
rm -fr /var/www/html/testessh.php
rm -fr /var/www/html/teste.php
rm -fr /var/www/html/install.php
rm -fr /var/www/html/rxsfphuawei-.php
rm -fr /var/www/html/Scripts/pacoteserroSNMP.php-
rm -fr /var/www/html/updateUsuarios_temp.php
rm -fr /var/www/html/install_mariadb.php
rm -fr /var/www/html/nginx.php
rm -fr /var/www/html/cron/exec/1min/update.php
rm -fr /var/www/html/speedtest/index*.html
rm -fr /var/www/html/grupos.php
rm -fr /var/www/html/node.php
rm -fr /var/www/html/node/phpsysinfo
rm -fr /var/www/html/cron/apoio/loopnode.php
rm -fr /var/www/html/cron/shell/services_monitor.sh
rm -fr /var/www/html/js/submenu.js
rm -fr /var/www/html/js/jquery-1.12.4.js
rm -fr /var/www/html/js/jquery-1.11.0.min.js
rm -fr /var/www/html/index.nginx-debian.html
rm -fr /root/temp

rm -fr /var/www/html/ram/stop_reinstallnode
rm -fr /var/www/html/ram/reinstallnode
rm -fr /var/www/html/ram/rebootnode

for CLOSED in $(ps aux | grep 'loop' | grep -v grep | awk '{print $2}'); do kill -9 $CLOSED; done
for CLOSED in $(ps aux | grep 'cmd_kill.sh' | grep -v grep | awk '{print $2}'); do kill -9 $CLOSED; done

rm -fr /var/www/html/cron/shell/cmd_kill.sh
rm -fr /var/www/html/node/google_drive.js
rm -fr /var/www/html/cron/exec/1min/services_monitor.sh
rm -fr /tmp/lockfilePowerRavi
rm -fr /tmp/lockfile_dns
rm -fr /tmp/lockfile_node
rm -fr /tmp/lockfile
rm -fr /tmp/lockfile_speedtest
rm -fr /tmp/lockfile_timezone
rm -fr /tmp/lockfile_vpn
rm -fr /root/nohup.out
rm -fr /var/www/html/node/kill.sh
rm -fr /var/www/html/cron/apoio/sincArquivosBkp.php
rm -fr /var/www/html/push_notification.php

echo > /var/log/ravi.log
echo "$(date +'%Y-%m-%d %H:%M:%S') - Sistema Ravi atualizado" >> /var/log/ravi.log

find /var/www/html/ -name 'nohup.out' -type f -delete
exit 0;