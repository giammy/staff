#!/bin/bash
bin/console doctrine:database:drop --force
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate

sed 's/TESISTRIKE\\/TESISTRIKE/g' public/local/newaccount-20191010.csv > public/local/newaccount-20191010-sed.csv
bin/console import:newaccountv1 public/local/newaccount-20191010-sed.csv
bin/console import:personale public/local/personale-2016.csv 2000 2016 
bin/console import:personale public/local/personale-2017.csv 2017 2017
bin/console import:personale public/local/personale-2018.csv 2018 2018
bin/console import:personale public/local/personale-2019.csv 2019 2019
