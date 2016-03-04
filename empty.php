<?php
	foreach(array("icons", "caches", "colors") AS $empDir)
	{
		$fileList = scandir($empDir);
		foreach($fileList AS $fileName)
		{
			if($fileName !== "." && $fileName !== "..")
			{
				unlink($empDir."/".$fileName);
			}
		}
	}
?>