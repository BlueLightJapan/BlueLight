#!/bin/sh
rm -rf *

git clone https://github.com/BlueLightJapan/BlueLight/ bl
git submodule update --init
cd bl
mkdir ../artifacts
git clone https://github.com/BlueLightJapan/PocketMine-DevTools

php -dphar.readonly=0 PocketMine-DevTools/src/DevTools/ConsoleScript.php --make ../bl/src --relative ../bl/ --entry src/pocketmine/PocketMine.php --out ../artifacts/BlueLight-PHP7.phar
