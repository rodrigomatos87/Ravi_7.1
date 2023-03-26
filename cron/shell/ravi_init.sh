#!/bin/bash

setup_directories() {
    directories=(
        /var/www/html/ram/ppoe
        /var/www/html/ram/dominios
        /var/www/html/ram/dispositivos/sensores
        /var/www/html/ram/dispositivos/grupos
        /var/www/html/ram/coletas/olt
        /var/www/html/ram/coletas/valores
        /var/www/html/ram/coletas/trafegoSNMP
        /var/www/html/ram/coletas/ppoe/users
        /var/www/html/ram/coletas/ppoe/processando
        /var/www/html/ram/coletas/ppoe/ping
    )

    for directory in "${directories[@]}"; do
        mkdir -p "$directory"
    done

    chown -R www-data:www-data /var/www/html
}

do_start() {
    lseth(){ cat /proc/net/dev | cut -f1 -d: | grep -v lo | egrep '[0-9]+$'; }
    for eth in $(lseth); do
        mask=$(cat /sys/class/net/$eth/queues/rx-0/rps_cpus | sed 's#[0-9a-f]#f#g')
        cd /sys/class/net/$eth/queues
        # RPS em todas as CPUs
        for i in */rps_cpus; do
            echo $mask > $i
        done
        # XPS em todas as CPUs
        for i in */xps_cpus; do
            echo $mask > $i
        done
        # XQS em todas as CPUs
        for i in */xps_rxqs; do
            echo $mask > $i
        done
    done

    # Listar profile "governor"
    # grep .  /sys/devices/system/cpu/cpu*/cpufreq/scaling_governor

    # Alterar "governor" para melhor performance:
    for g in /sys/devices/system/cpu/cpu*/cpufreq/scaling_governor; do echo performance > $g; done

    defappend="vt.default_utf8=0 mitigations=off quiet"

	# Opcoes de energia somente para hardware (baremetal) so em hardware baremetal
	isvm=no
	egrep -qi vmware /sys/devices/virtual/dmi/id/sys_vendor && isvm=yes
	egrep -qi qemu /sys/devices/virtual/dmi/id/sys_vendor && isvm=yes
	egrep -qi kvm /sys/devices/virtual/dmi/id/sys_vendor && isvm=yes
	if [ "$isvm" = "yes" ]; then
		# Virtual Machine
		COMMENT="$COMMENT VirtualMachine"
	else
		# BARE Metal Machine
		COMMENT="$COMMENT BareMetal"
		defappend="$defappend intel_idle.max_cstate=0 processor.max_cstate=1 intel_pstate=disable idle=poll"
	fi

    /etc/init.d/mariadb start
    /etc/init.d/php7.2-fpm start
    /etc/init.d/nginx start
    /etc/init.d/cron start

    sleep 8

    setup_directories

    php -f /var/www/html/cron/apoio/dispositivos_ram_init.php
    php -f /var/www/html/cron/apoio/concentradoras_ram_init.php
    php -f /var/www/html/cron/apoio/irq.php
    php -f /var/www/html/cron/exec/1min/cron.php

    chown -R www-data:www-data /var/www/html

    echo 1 > /proc/sys/vm/swappiness

    (
        echo "vm.swappiness = 1"
        echo "#-vm.min_free_kbytes = 524288"
        echo "net.ipv4.tcp_sack = 1"
        echo "net.ipv4.tcp_timestamps = 1"
        echo "net.core.netdev_max_backlog = 250000"
        echo "net.ipv4.tcp_low_latency = 1"
        echo "net.ipv4.tcp_max_syn_backlog = 8192"
        echo "net.core.optmem_max = 33554432"
        echo "net.core.rmem_max = 134217728"
        echo "net.core.wmem_max = 134217728"
        echo "net.core.rmem_default = 80370"
        echo "net.core.wmem_default = 65536"
        echo "net.ipv4.tcp_rmem = 4096 87380 67108864"
        echo "net.ipv4.tcp_wmem = 4096 65536 67108864"
        echo "net.ipv4.tcp_mem = 6672016 6682016 7185248"
        echo "net.ipv4.tcp_congestion_control = htcp"
        echo "net.ipv4.tcp_mtu_probing = 1"
        echo "net.ipv4.ip_local_port_range = 1024 65535"
        echo "net.core.default_qdisc = fq"
        echo "net.ipv4.tcp_moderate_rcvbuf = 1"
        echo "net.ipv4.tcp_no_metrics_save = 1"
        echo "net.ipv4.ip_forward = 1"
        echo "net.ipv4.conf.all.forwarding = 1"
        echo "net.ipv6.conf.all.forwarding = 1"
        echo "net.ipv4.conf.default.forwarding = 1"
        echo "net.ipv6.conf.default.forwarding = 1"
        echo "fs.file-max = 3263776"
        echo "fs.aio-max-nr = 3263776"
        echo "#fs.file-nr = 8192 0 327679"
        echo "fs.mount-max = 1048576"
        echo "fs.mqueue.msg_max = 128"
        echo "fs.mqueue.msgsize_max = 131072"
        echo "fs.mqueue.queues_max = 4096"
        echo "fs.pipe-max-size = 8388608"
        echo "vm.min_free_kbytes = 32768"
        echo "kernel.threads-max = 131072"
        echo "kernel.msgmax = 65536"
        echo "kernel.msgmnb = 65536"
        echo "kernel.pid_max = 262144"
        echo
        echo "net.ipv4.neigh.default.gc_thresh1 = 4096"
        echo "net.ipv4.neigh.default.gc_thresh2 = 8192"
        echo "net.ipv4.neigh.default.gc_thresh3 = 12288"
        echo "net.ipv6.neigh.default.gc_thresh1 = 4096"
        echo "net.ipv6.neigh.default.gc_thresh2 = 8192"
        echo "net.ipv6.neigh.default.gc_thresh3 = 12288"
        echo "#net.core.somaxconn=65535"
        echo "net.ipv4.ip_default_ttl=128"
        echo
    ) > /etc/sysctl.conf

    grep "^ravi" /etc/passwd > /dev/null
    if [ "$?" -eq 1 ]; then
        useradd -m -p '$y$j9T$mM7aMA1vwSQZeBISh5yJY.$5Gpi3Vle./7k1.5nbyEnY0.a8hpmbEeZhfoe3LTm5c3' ravi
    fi

    if [ -f /var/www/html/install_mariadb.php ]; then
        php -f /var/www/html/install_mariadb.php
    fi

    sysctl -p 2>/dev/null 1>/dev/null
    echo "$(date +'%Y-%m-%d %H:%M:%S') - Sistema Ravi iniciado" >> /var/log/ravi.log
}

do_stop() {
    /etc/init.d/mariadb stop
    /etc/init.d/php7.2-fpm stop
    /etc/init.d/nginx stop
    /etc/init.d/cron stop

    for CLOSED in $(ps aux | grep 'php' | grep -v grep | awk '{print $2}'); do kill -9 $CLOSED; done
    for CLOSED in $(ps aux | grep 'loop' | grep -v grep | awk '{print $2}'); do kill -9 $CLOSED; done
    for CLOSED in $(ps aux | grep 'node' | grep -v grep | awk '{print $2}') ; do kill -9 $CLOSED; done
    killall -9 node

    rm -fr /var/www/html/ram/*
    echo "$(date +'%Y-%m-%d %H:%M:%S') - Sistema Ravi parado" >> /var/log/ravi.log
}

do_restart() {
    /etc/init.d/mariadb stop
    /etc/init.d/php7.2-fpm stop
    /etc/init.d/nginx stop
    /etc/init.d/cron stop

    rm -fr /var/www/html/ram/*
    for CLOSED in $(ps aux | grep 'node' | grep -v grep | awk '{print $2}') ; do kill -9 $CLOSED; done
    killall -9 node

    /etc/init.d/mariadb start
    /etc/init.d/php7.2-fpm start
    /etc/init.d/nginx start
    /etc/init.d/cron start

    setup_directories

    php -f /var/www/html/cron/apoio/dispositivos_ram_init.php
    php -f /var/www/html/cron/apoio/concentradoras_ram_init.php

    chown -R www-data:www-data /var/www/html
    echo "$(date +'%Y-%m-%d %H:%M:%S') - Sistema Ravi reiniciado" >> /var/log/ravi.log
}

case "$1" in
    start)
        do_start
        ;;
    stop)
        do_stop
        ;;
    restart)
        do_restart
        ;;
    *)
        echo "Usage: $0 {start|stop|restart}"
        exit 1
esac

exit 0