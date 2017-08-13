<?php

/*
 *   ____  _            _      _       _     _
 *  |  _ \| |          | |    (_)     | |   | |
 *  | |_) | |_   _  ___| |     _  __ _| |__ | |_
 *  |  _ <| | | | |/ _ \ |    | |/ _` | '_ \| __|
 *  | |_) | | |_| |  __/ |____| | (_| | | | | |_
 *  |____/|_|\__,_|\___|______|_|\__, |_| |_|\__|
 *                                __/ |
 *                               |___/
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/
 
namespace pocketmine\updater{
	
	echo "\x1b]0;BlueLight-Updater\x07";

	echo getLogo() . PHP_EOL;
	echo "\x1b[mLoading branches..." . PHP_EOL;
	
	$branches = json_decode(getURL("https://api.github.com/repos/BlueLightJapan/BlueLight/branches"), true);
	
	echo "\x1b[mBranch Name: ";
	
	$array = [];
	$count = count($branches);
	
	foreach($branches as $b){
		$count--;
		$array[] = $b["name"];
		if($count == 0){
			echo "\x1b[38;5;83m".$b["name"] . PHP_EOL;
		}else{
			echo "\x1b[38;5;83m".$b["name"].", ";
		}
	}
	
	do{
		echo "\x1b[mSelect Branch: \x1b[38;5;87m";
		$branch = trim(fgets(STDIN));
		if($tmp = !in_array($branch, $array)){
			echo "\x1b[38;5;124mThe branch is not found." . PHP_EOL;
		}
	}while($tmp);
	
	if(!file_exists("temp")) mkdir("temp");
	
	echo "\x1b[mDownload BlueLight-".$branch." now..." . PHP_EOL;
	
	$data = @file_get_contents("https://github.com/BlueLightJapan/BlueLight/archive/".$branch.".zip");
	if($data){
		$result = file_put_contents("temp/BlueLight.zip",$data);
		echo "\x1b[38;5;83mDownload Success!" . PHP_EOL;
	}else{
		echo "\x1b[38;5;124mDownload Failed." . PHP_EOL;
		dirrm("temp");
		exit(1);
	}
	
	echo "\x1b[mExtract Zip File..." . PHP_EOL;
	$zip = extractZip("temp/BlueLight.zip");

	if($zip){
		echo "\x1b[38;5;83mUnZip Success!" . PHP_EOL;
	}else{
		echo "\x1b[38;5;124mUnZip Failed" . PHP_EOL;
		dirrm("temp");
		exit(1);
	}
	
	if(file_exists("PocketMine-MP.phar")) unlink("PocketMine-MP.phar");
	if(file_exists("src")) dirrm("src");
	
	dircopy("temp/BlueLight-".$branch."/src","src");
	dirrm("temp");
	echo "\x1b[38;5;83mCompleted BlueLight Update!\x1b[m" . PHP_EOL;
	
	exit(1);


	function getLogo(){
		$logo = "\x1b[38;5;87m				 ____  _            _      _       _     _
				|  _ \| |          | |    (_)     | |   | |
				| |_) | |_   _  ___| |     _  __ _| |__ | |_
				|  _ <| | | | |/ _ \ |    | |/ _` | '_ \| __|
				| |_) | | |_| |  __/ |____| | (_| | | | | |_
				|____/|_|\__,_|\___|______|_|\__, |_| |_|\__|
							      __/ |
							     |___/
				\x1b[m";
		return $logo;
	}
	
	function getURL($url, $timeout = 10){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36"));
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (int) $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, (int) $timeout);
		$result = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);

		return $result;
	}
	
	function extractZip($path){
		$zip = new \ZipArchive();
		$res = $zip->open($path);
		if ($res == true){
			$zip->extractTo("temp");
			$zip->close();
			return true;
		}else{
			return false;
		}
	}
	
	function dirrm($dir) {
		if($handle = opendir($dir)) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if(is_dir($dir."/".$item)) {
						dirrm($dir."/".$item);
					}else{
						unlink($dir."/".$item);
					}
				}
			}
			closedir($handle);
			rmdir($dir);
		}
	}

	function dircopy($dir_name, $new_dir){
		if (!is_dir($new_dir)) {
			mkdir($new_dir, 0777, true);
		}
 
		if (is_dir($dir_name)){
			if ($dh = opendir($dir_name)) {
				while (($file = readdir($dh)) !== false) {
					if ($file == "." || $file == "..") {
						continue;
					}
					if (is_dir($dir_name . "/" . $file)) {
					dircopy($dir_name . "/" . $file, $new_dir . "/" . $file);
					}else{	
						copy($dir_name . "/" . $file, $new_dir . "/" . $file);
					}
				}
				closedir($dh);
			}
		}
	}
}
	 
	 
