#!/bin/bash

if [ "$(whoami)" != "apache" ]; then
        echo "Script must be run as user apache"
        exit -1
fi

THEDATE=`date +"%Y%m%d"`
DEST=var/log/import/${THEDATE}
mkdir ${DEST}
cp var/local/data.db ${DEST}

bin/console doctrine:database:drop --force
bin/console doctrine:database:create
bin/console --no-interaction doctrine:migrations:migrate

sed 's/TESISTRIKE\\/TESISTRIKE/g' var/local/csv/newaccount-20200121.csv > var/local/csv/newaccount-20200121-sed.csv
bin/console import:newaccountv1 var/local/csv/newaccount-20200121-sed.csv

doImport () {
  cp /.reserved/r/public/webprojects/ttui/data/personale-$1.csv ${DEST}
  chmod 644 ${DEST}/personale-$1.csv
  bin/console import:personale ${DEST}/personale-$1.csv $1 $1
}

doImport 2016
doImport 2017
doImport 2018
doImport 2019
doImport 2020
