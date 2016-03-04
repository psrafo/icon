<?php
	$origIcons = scandir($iconSourcePath);
	
	foreach($origIcons AS $iconName)
	{
		if($iconName !== "." && $iconName !== "..")
		{
			$iconNameWithoutExt = str_replace(".svg", "", $iconName);
			$iconName = $iconNameWithoutExt.".svg";
			
			$iconPath = "icons/".$iconName;
			$origPath = $iconSourcePath."/".$iconName;
			
			if(is_file($origPath) && !is_file($iconPath))
			{
				$defColors = array();
				
				$defColorPath = "colors/".$iconNameWithoutExt.".defColors";
				
				$iconXML = file_get_contents($origPath);
				
				$dom = new DOMDocument;
				@$dom->loadXML($iconXML);
				
				$allTags = $dom->getElementsByTagName("*");
				$i = 0;
				foreach($allTags AS $tag)
				{
					if(trim($tag->getAttribute("fill")) !== "")
					{
						$i++;
						
						$defColors[$i] = $tag->getAttribute("fill");
						$tag->setAttribute("fill", "%".$i);
					}
				}
				
				foreach($defColors AS $defColorInd => $defColorValue)
				{
					$defColors[$defColorInd] = "\"".$defColorValue."\"";
				}
				
				touch($defColorPath);
				file_put_contents($defColorPath, implode(",", $defColors));
				
				touch($iconPath);
				file_put_contents($iconPath, $dom->saveXML());
			}
		}
	}
?>