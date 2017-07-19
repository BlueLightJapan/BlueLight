#!/bin/sh
rm -rf *

git clone https://github.com/BlueLightJapan/BlueLight/ bl
git submodule update --init
cd bl
mkdir plugins
mkdir ../artifacts
wget -O PHP7.tar.gz https://dl.bintray.com/pocketmine/PocketMine/PHP_7.0.6_x86-64_Linux.tar.gz --no-check-certificate
tar -xf PHP7.tar.gz
#wget -O plugins/DevTools.phar https://github.com/PocketMine/DevTools/releases/download/v1.11.0/DevTools_v1.11.0.phar

./bin/php7/bin/php ci.php
mv plugins/DevTools/*  ../artifacts/BlueLight-PHP7.phar
