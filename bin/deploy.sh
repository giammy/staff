#!/bin/bash

if [ ! -f .env.local ]; then
  exit 0;
fi 

DEPLOY_TO=`grep DEPLOY_HOST .env.local | cut -d'=' -f2`
DEPLOY_HOST=`echo $DEPLOY_TO | cut -d':' -f1`

cd ..
rm -rf staff/var/cache/*
#rsync -a --delete --exclude var/sqlite.db staff root@$DEPLOY_TO
rsync -a --delete staff root@$DEPLOY_TO
ssh root@$DEPLOY_HOST "chown -R apache:apache /var/www/html/staff"
cd staff


