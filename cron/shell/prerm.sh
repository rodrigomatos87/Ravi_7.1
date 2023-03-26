#!/bin/bash

for CLOSED in $(ps aux | grep 'loop' | grep -v grep | awk '{print $2}'); do
    kill -9 $CLOSED
done

umount /var/www/html/ram


exit 0