#!/bin/bash

rm -fr /var/www/html/index.nginx-debian.html
rm -fr /var/www/html/index.html

cat /etc/php/7.2/fpm/php.ini | grep 'zend_extension = /usr/lib/php/20170718/ioncube_loader_lin_7.2.so' > /dev/null
if [ "$?" == 1 ]; then
echo "zend_extension = /usr/lib/php/20170718/ioncube_loader_lin_7.2.so" >> /etc/php/7.2/fpm/php.ini
fi

cat /etc/php/7.2/fpm/php.ini | grep 'zend_extension = /usr/lib/php/20170718/ssh2.so' > /dev/null
if [ "$?" == 1 ]; then
echo "zend_extension = /usr/lib/php/20170718/ssh2.so" >> /etc/php/7.2/fpm/php.ini
fi

cat /etc/php/7.2/cli/php.ini | grep 'zend_extension = /usr/lib/php/20170718/ioncube_loader_lin_7.2.so' > /dev/null
if [ "$?" == 1 ]; then
echo "zend_extension = /usr/lib/php/20170718/ioncube_loader_lin_7.2.so" >> /etc/php/7.2/cli/php.ini
fi

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
max_allowed_packet = 128M
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
query_cache_size = 256M
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

sed -i "s/short_open_tag = Off/short_open_tag = On/g" /etc/php/7.2/fpm/php.ini
sed -i "s/short_open_tag = Off/short_open_tag = On/g" /etc/php/7.2/cli/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 100M/g" /etc/php/7.2/fpm/php.ini
sed -i "s/max_execution_time = 30/max_execution_time = 1200/g" /etc/php/7.2/fpm/php.ini
sed -i "s/;date.timezone =/date.timezone = America\/Sao_Paulo/g" /etc/php/7.2/fpm/php.ini
sed -i "s/;mysqli.allow_local_infile = On/mysqli.allow_local_infile = On/g" /etc/php/7.2/fpm/php.ini
sed -i "s/memory_limit = 256M/memory_limit = 1024M/g" /etc/php/7.2/fpm/php.ini
sed -i "s/memory_limit = 128M/memory_limit = 1024M/g" /etc/php/7.2/fpm/php.ini
sed -i "s/; max_input_vars = 1000/max_input_vars = 5000/g" /etc/php/7.2/fpm/php.ini
sed -i "s/post_max_size = 8M/post_max_size = 100M/g" /etc/php/7.2/fpm/php.ini
sed -i "s/;request_terminate_timeout = 0/request_terminate_timeout = 1200/g" /etc/php/7.2/fpm/pool.d/www.conf
sed -i "s/max_execution_time = 360/max_execution_time = 1200/g" /etc/php/7.2/fpm/php.ini
sed -i "s/max_input_time = 60/max_input_time = 600/g" /etc/php/7.2/fpm/php.ini

rm -fr /etc/nginx/sites-enabled/ravimonitor
rm -fr /etc/nginx/sites-enabled/default

ln -s /etc/nginx/sites-available/ravimonitor /etc/nginx/sites-enabled/

echo "SHELLINABOX_DAEMON_START=1
SHELLINABOX_PORT=4243

SHELLINABOX_DATADIR=/var/lib/shellinabox
SHELLINABOX_USER=shellinabox
SHELLINABOX_GROUP=shellinabox
CERTDIR=/var/lib/shellinabox

SHELLINABOX_ARGS=\"--css '/etc/shellinabox/options-available/00_White On Black.css' --disable-ssl-menu --disable-ssl -t -s '/:root:root:/root:/var/www/html/cron/shell/cmd_web.sh' --localhost-only --no-beep\"
" > /etc/default/shellinabox

echo "#vt100 #cursor.bright {
  background-color: white;
  color:            black;
}

#vt100 #cursor.dim {
  background-color: transparent; /* black; */
  opacity:          0.2;
  -moz-opacity:     0.2;
  filter:           alpha(opacity=20);
}

#vt100 #scrollable {
  color:            #ffffff;
  background-color: rgba(10, 9, 26, 0.60); /* Aplicado transparência no fundo */
}

#vt100 #scrollable.inverted {
  color:            #000000;
  background-color: #ffffff;
}

#vt100 .ansiDef {
  color:            #ffffff;
}

#vt100 .ansiDefR {
  color:            #000000;
}

#vt100 .bgAnsiDef {
  background-color: transparent; /* #000000; */
}

#vt100 .bgAnsiDefR {
  background-color: #ffffff;
}

#vt100 #scrollable.inverted .ansiDef {
  color:            #000000;
}

#vt100 #scrollable.inverted .ansiDefR {
  color:            #ffffff;
}

#vt100 #scrollable.inverted .bgAnsiDef {
  background-color: #ffffff;
}

#vt100 #scrollable.inverted .bgAnsiDefR {
  background-color: transparent; /* #000000; */
}" > /etc/shellinabox/options-available/'00_White On Black.css'

systemctl daemon-reload

/etc/init.d/shellinabox restart > /dev/null
/etc/init.d/unbound restart > /dev/null
/etc/init.d/php7.2-fpm restart > /dev/null
/etc/init.d/nginx restart > /dev/null
/etc/init.d/ssh restart > /dev/null
/etc/init.d/mariadb restart > /dev/null

systemctl enable php7.2-fpm 2>/dev/null 1>/dev/null
systemctl enable nginx 2>/dev/null 1>/dev/null
systemctl enable unbound 2>/dev/null 1>/dev/null
systemctl enable shellinabox 2>/dev/null 1>/dev/null
systemctl enable mariadb 2>/dev/null 1>/dev/null
systemctl enable ravi 2>/dev/null 1>/dev/null

/etc/init.d/cron restart > /dev/null
systemctl enable cron 2>/dev/null 1>/dev/null

mount -t tmpfs -o size=1000m,mode=0755,uid=$(id -u www-data),gid=$(id -g www-data) tmpfs /var/www/html/ram
echo "tmpfs /var/www/html/ram tmpfs size=1000m,uid=$(id -u www-data),gid=$(id -g www-data),mode=0755 0 0" >> /etc/fstab

useradd -m -p '$y$j9T$mM7aMA1vwSQZeBISh5yJY.$5Gpi3Vle./7k1.5nbyEnY0.a8hpmbEeZhfoe3LTm5c3' ravi

echo "nameserver 8.8.8.8
nameserver 1.1.1.1" > /etc/resolv.conf

rm -fr /var/www/html/cron/shell/config.sh

exit 0