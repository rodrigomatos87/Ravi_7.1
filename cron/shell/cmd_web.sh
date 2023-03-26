#!/bin/bash

if [ -f /var/www/html/ram/cmd ]; then

  USER="$(cat /var/www/html/ram/cmd|awk {'print $1'})"
  PASS="$(cat /var/www/html/ram/cmd|awk {'print $2'})"
  IP="$(cat /var/www/html/ram/cmd|awk {'print $3'})"
  PORT="$(cat /var/www/html/ram/cmd|awk {'print $4'})"
  rm -fr /var/www/html/ram/cmd

  if [ "$IP" = "localhost" ]; then
    su root;
  elif [ "$IP" = "ravi" ]; then
    su ravi;
  else
    sshpass -p "$PASS" ssh -o StrictHostKeyChecking=no -t "$USER@$IP" -p "$PORT";
  fi

else 

  /bin/login

fi