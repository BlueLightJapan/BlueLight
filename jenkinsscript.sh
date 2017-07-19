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

git clone https://github.com/BlueLightJapan/PocketMine-DevTools

php -dphar.readonly=0 PocketMine-DevTools/src/DevTools/ConsoleScript.php --make ../bl/src --relative ../bl/ --entry src/pocketmine/PocketMine.php --out ../artifacts/BlueLight-PHP7.phar
