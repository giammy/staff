#!/bin/bash

THEDATE=`date +"%Y%m%d"`
DEST=var/log/import/${THEDATE}
mkdir ${DEST}
cp var/local/data.db ${DEST}

bin/console doctrine:database:drop --force
bin/console doctrine:database:create
bin/console --no-interaction doctrine:migrations:migrate

#sed 's/TESISTRIKE\\/TESISTRIKE/g' var/local/csv/newaccount-20191010.csv > var/local/csv/newaccount-20191010-sed.csv
#bin/console import:newaccountv1 var/local/csv/newaccount-20191010-sed.csv
#bin/console import:personale var/local/csv/personale-2016.csv 2000 2016 
#bin/console import:personale var/local/csv/personale-2017.csv 2017 2017
#bin/console import:personale var/local/csv/personale-2018.csv 2018 2018
#bin/console import:personale var/local/csv/personale-2019.csv 2019 2019

doImport () {
  cp /.reserved/r/public/webprojects/ttui/data/personale-$1.csv ${DEST}
  bin/console import:personale ${DEST}/personale-$1.csv $1 $1
}

doImport 2016
doImport 2017
doImport 2018
doImport 2019
doImport 2020
