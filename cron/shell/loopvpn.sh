#!/bin/bash

# Função para gerenciar conexões VPN
manage_vpn() {
    # Sincronizar arquivos
    if [ -f /var/www/html/ram/connection ]; then
        mv -f /var/www/html/ram/connection /etc/ppp/peers/connection
        mv -f /var/www/html/ram/chap-secrets /etc/ppp/chap-secrets
        mv -f /var/www/html/ram/vpn-route /etc/ppp/ip-up.d/vpn-route
        chown -R root:root /etc/ppp
        chmod +x /etc/ppp/ip-up.d/vpn-route

        # Reiniciar
        for CLOSED in $(ps aux | grep 'ppp' | grep -v grep | awk '{print $2}'); do kill -9 $CLOSED; done
        sleep 1
        modprobe nf_conntrack_pptp
        modprobe ppp_mppe
        pppd call connection
        sleep 3

        INTERFACE=$(ifconfig | egrep '^ppp' | cut -d ':' -f1 | tail -1)
        if [ "$INTERFACE" != '' ]; then
            sed -i "s/ppp0/$INTERFACE/g" /etc/ppp/ip-up.d/vpn-route
        fi
        sh /etc/ppp/ip-up.d/vpn-route
    fi

    if [ -f /var/www/html/ram/desconect_vpn ]; then
        rm -fr /etc/ppp/peers/connection
        rm -fr /etc/ppp/chap-secrets
        rm -fr /etc/ppp/ip-up.d/vpn-route
        for CLOSED in $(ps aux | grep 'ppp' | grep -v grep | awk '{print $2}'); do kill -9 $CLOSED; done
        rm -fr /var/www/html/ram/desconect_vpn
    fi
}

# Loop principal
while : ; do
    # Usa o flock para impedir execução simultânea
    (
        flock -n 9 || exit 1

        # Chama a função para gerenciar conexões VPN
        manage_vpn
    ) 9>/tmp/lockfile_loop_manage_vpn

    sleep 1
done