@echo off
TITLE BlueLight Updater
cd /d %~dp0

if exist bin\php\php.exe (
	set PHPRC=""
	set PHP_BINARY=bin\php\php.exe
) else (
	set PHP_BINARY=php
)

if exist PocketMine-MP.phar (
	set UPDATER=PocketMine-MP.phar
) else (
	if exist src\pocketmine\updater\Updater.php (
		set UPDATER=src\pocketmine\updater\Updater.php
	) else (
		echo "Couldn't find a valid BlueLight Updater"
		pause
		exit 1
	)
)

REM if exist bin\php\php_wxwidgets.dll (
REM 	%PHP_BINARY% %UPDATER% --enable-gui %*
REM ) else (
	if exist bin\mintty.exe (
		start "" bin\mintty.exe -o Columns=130 -o Rows=32 -o AllowBlinking=0 -o FontQuality=3 -o Font="DejaVu Sans Mono" -o FontHeight=10 -o CursorType=0 -o CursorBlinks=1 -h error -t "BlueLight-Updater" -i bin/pocketmine.ico %PHP_BINARY% %UPDATER% --enable-ansi %*
	) else (
		%PHP_BINARY% -c bin\php %UPDATER% %*
	)
REM )
