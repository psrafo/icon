<?php
	$configs = parse_ini_file("config.ini");
	
	$iconSourcePath = $configs["iconSourcePath"];
	$allowCache = $configs["allowCache"];
	
	function getColor($iconNameWithoutExt = "", $colorIndex = 1)
	{
		global $params, $defColors;
		
		if(isset($params[$colorIndex]) && trim($params[$colorIndex]) !== "")
		{
			if(isset($params[$colorIndex][0]) && $params[$colorIndex][0] == "$")
			{
				$params[$colorIndex][0] = "#";
			}
			
			return $params[$colorIndex];
		}
		else
		{
			if(isset($defColors[$colorIndex - 1]))
			{
				return $defColors[$colorIndex - 1];
			}
		}
	}
	
	function renderDefColor($defColorPath = "")
	{
		$defColorDATA = substr(file_get_contents($defColorPath), 1, -1);
		
		$defColors = explode("\",\"", $defColorDATA);
		foreach($defColors AS $defColorInd => $defColorValue)
		{
			$defColors[$defColorInd] = $defColorValue;
		}
		
		return $defColors;
	}
	
	if(isset($_GET["p"]) && trim($_GET["p"]) !== "")
	{
		$paramStr = trim($_GET["p"]);
		
		if($paramStr == "empty")
		{
			require "empty.php";
		}
		else if($paramStr == "parse")
		{
			require "parse.php";
		}
		else
		{
			$params = explode(",", $paramStr);
			
			if($allowCache)
			{
				mkdir("caches", 0777);
			}
			
			$cacheFile = "caches/".md5($paramStr).".svg";
			
			if(is_file($cacheFile) && $allowCache)
			{
				header("Content-Type:image/svg+xml");
				echo file_get_contents($cacheFile);
			}
			else if(isset($params[0]) && strpos($params[0], "..") === FALSE)
			{
				$iconNameWithoutExt = $params[0];
				$iconName = $iconNameWithoutExt.".svg";
				
				$iconPath = "icons/".$iconName;
				
				$defColorPath = "colors/".$iconNameWithoutExt.".defColors";
				
				if(is_file($iconPath))
				{
					$iconXML = file_get_contents($iconPath);
					
					if(is_file($defColorPath))
					{
						$defColors = renderDefColor($defColorPath);
						
						for($i = 1; $i <= count($defColors); $i++)
						{
							if(strpos($iconXML, "%".$i) !== FALSE)
							{
								$color = getColor($iconNameWithoutExt, $i);
								
								$iconXML = str_replace("%".$i, $color, $iconXML);
							}
						}
					}
					
					if($allowCache)
					{
						touch($cacheFile);
						file_put_contents($cacheFile, $iconXML);
					}
					
					header("Content-Type:image/svg+xml");
					echo $iconXML;
				}
			}
		}
	}
?>