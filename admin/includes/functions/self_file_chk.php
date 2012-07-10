<?php
	 // (Hash Checker)

	function do_filechk()
	{

		// Tabelle leeren
    $sql = 'TRUNCATE TABLE self_commerce_filechk_' .$_GET['type'];		
    xtc_db_query($sql);
		// Filecheck starten
		recursive_filechk(DIR_FS_DOCUMENT_ROOT, '', $_GET['type']);
	}



	 //recursive_filechk

	function recursive_filechk($dir, $prefix = '', $extension)
	{

		$directory = @opendir($dir);

		while ($file = @readdir($directory))
		{
			if (!in_array($file, array('.', '..')))
			{
				$is_dir = (@is_dir($dir .'/'. $file)) ? true : false;

				// Create a nice Path for the found Files / Folders
				$temp_path  = '';
				$temp_path  = $dir . '/' . (($is_dir) ? strtoupper($file) : $file);
				$temp_path  = str_replace('//', '/', $temp_path);

				// Remove dots from extension Parameter
				$extension  = str_replace('.', '', $extension);

				// Fill it in our File Array if the found file is matching the extension
				if( preg_match("/^.*?\." . $extension . "$/", $temp_path) && !preg_match('/templates_c\\//m', $temp_path) )
				{
					$filehash = @filesize($temp_path) . '-' . count(@file($temp_path));
					$filehash = md5($filehash);

					      $sql = 'INSERT INTO self_commerce_filechk_' .$_GET['type'] . " (`filepath`, `hash`) VALUES ('$temp_path', '$filehash')";

		            if(!($result = xtc_db_query($sql)))
		            {
                  echo 'fehler';
		            }
		        }

		        // Directory found, so recall this function
		        if ($is_dir)
		        {
		        	recursive_filechk($dir .'/'. $file, $dir .'/', $extension);
		        }
	        }
	    }

	    @closedir($directory);
	}
?>
