param (
	[switch]$Loop = $false
)

if(Test-Path "bin\php\php.exe"){
	$env:PHPRC = ""
	$binary = "bin\php\php.exe"
}else{
	$binary = "php"
}
if(Test-Path "PocketMine-MP.phar"){
	$file = "PocketMine-MP.phar"
}elseif(Test-Path "src\pocketmine\PocketMine.php"){
	$file = "src\pocketmine\PocketMine.php"
}else{
	echo "I couldn't find PocketMine-MP's src."
	pause
	exit 1
}

function StartServer{
	$command = $binary + " " + $file  + " --enable-ansi"
    chcp 65001
	iex $command
}

$loops = 0

StartServer

while($Loop){
	if($loops -ne 0){
		echo ("Restarted " + $loops + " times")
	}
	$loops++
	echo "To escape the loop, press CTRL+C now. Otherwise, wait 5 seconds for the server to restart."
	echo ""
	StartServer
}
