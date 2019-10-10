#!/bin/bash

if [ ! -f .env.local ]; then
  exit 0;
fi 

DEPLOY_TO=`grep DEPLOY_HOST .env.local | cut -d'=' -f2`
DEPLOY_HOST=`echo $DEPLOY_TO | cut -d':' -f1`

cd ..
rm -rf staff/var/cache/*
rsync -a --delete --exclude var/data.db --exclude public/local staff root@$DEPLOY_TO
#rsync -a --delete staff root@$DEPLOY_TO
ssh root@$DEPLOY_HOST "echo 'APP_ENV=prod' >> /var/www/html/staff/.env.local"
ssh root@$DEPLOY_HOST "chown -R apache:apache /var/www/html/staff"

scp root@${DEPLOY_TO}/staff/var/data.db staff/var
scp -r root@${DEPLOY_TO}/staff/public/local staff/public

cd staff


