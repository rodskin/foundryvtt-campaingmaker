<?php
	ini_set('default_charset', "UTF8");
	setlocale(LC_ALL, 'en_US.UTF8');
	$min_version = '0.5.3';
	
    if (isset($_POST) && !empty($_POST)) {
        //print_r($_POST);
		$options = $_POST;
		$options['min_version'] = $min_version;
        $uniq_id = uniqid('', true);
        //echo $uniq_id;
        // module creation
		$tmp_folder = '/tmp/' . $uniq_id;
        mkdir($tmp_folder);
		
        $module_slug = strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array(' ', '-', ''), remove_accents($_POST['campaign_name'])));
		//echo $module_slug;
		$options['module_slug'] = $module_slug;
		$module_path = $tmp_folder . '/' . $module_slug;
		mkdir($module_path);
		$module_packs_path = $module_path . '/packs';
		mkdir($module_packs_path);
		// creation des chemins pour les ressources
		$module_ressources_path = $module_path . '/ressources';
		mkdir($module_ressources_path);
		$module_scenes_path = $module_ressources_path . '/scenes';
		mkdir($module_scenes_path);
		$module_tokens_path = $module_ressources_path . '/tokens';
		mkdir($module_tokens_path);
		$module_portraits_path = $module_ressources_path . '/portraits';
		mkdir($module_portraits_path);
		$module_pictures_path = $module_ressources_path . '/pictures';
		mkdir($module_pictures_path);
		$module_icons_path = $module_ressources_path . '/icons';
		mkdir($module_icons_path);
		$module_sounds_path = $module_ressources_path . '/sounds';
		mkdir($module_sounds_path);
		$module_musics_path = $module_ressources_path . '/musics';
		mkdir($module_musics_path);
		$module_file = create_module_file($options);
		
		file_put_contents($module_path . '/module.json', $module_file);
		
		$zip_name = '/tmp/' . $uniq_id . '.zip';
		zip($tmp_folder . '/', $zip_name);
		
		//echo filesize('/tmp/' . $uniq_id . '.zip');
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . basename($zip_name) . '"');
		header('Content-Length: ' . filesize($zip_name));

		flush();
		@readfile($zip_name);
		rrmdir($tmp_folder);
		unlink($zip_name);
		
    }
?>
 <!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>FoundryVTT Campaign module maker</title>
    </head>

    <body>
        <h1>Welcome to the Foundry Virtual Tabletop Campaign maker</h1>
		<h3>Using version <strong><?php echo $min_version; ?></strong></h3>
		<div>
			This is a plugin creator to save or export your campaigns / characters etc
			you will find in you module a skeleton of folders for your ressources:
			
			<pre style="background-color:#F6F7F8; padding: 10px;">
+ YOUR_MODULE/
	+ packs/
	+ ressources/
	   + icons/
	   + musics/
	   + pictures/
	   + portraits/
	   + scenes/
	   + sounds/
	   + tokens/
			</pre>
		</div>
        <div>
            <form action="" id="campaign_maker_form" method="post">
                <input type="text" name="creator_name" placeholder="Insert your name *" required="required" style="width: 300px;" /><br />
                <input type="text" name="creator_url" placeholder="Insert your site url *" required="required" style="width: 300px;" /><br />
                <input type="text" name="campaign_name" placeholder="Insert your campaign name *" required="required" style="width: 300px;" /><br />
				<textarea name="campaign_description" placeholder="campaign description *" required="required" style="width: 300px;"></textarea>
                <br />
                <input type="submit" value="create and download module" />
            </form>
        </div>
		<br />
		<div><a href="http://ko-fi.com/rodskin" target="_blank">you can buy me a coffee :)</a></div><br />
		<div><a href="https://foundryvtt.com/" target="_blank">Foundry Virtual Tabletop Official Site</a></div>
    </body>
</html>
<?php
	/**
     * htmlentities : wrapper pour htmlentities afin de lui faire utiliser le bon encodage par defaut (celui du site, et non ISO-8859-1)
     *
     * @param mixed $string
     * @param mixed $quote_style
     * @param mixed $charset
     * @param mixed $double_encode
     * @access public
     * @return void
     */
    function my_htmlentities ($string, $quote_style = ENT_COMPAT, $charset = null, $double_encode = true)
    {
        if ($charset === null) {
            $charset = mb_internal_encoding();
        }
        // php < 5.2.3 compatibility
        if (!$double_encode) {
            return htmlentities($string, $quote_style, $charset, $double_encode);
        }
        return htmlentities($string, $quote_style, $charset);
    }
	
	/**
     * remove_accents : supprime les accents de la chaine $str (par transliteration en ASCII)
     *
     * @param mixed $str
     * @access public
     * @return void
     */
    function remove_accents ($str)
    {
        if (!extension_loaded('iconv')) {
            trigger_error('Extension PHP ICONV manquante', E_USER_WARNING);
        }
        return iconv('UTF-8', 'US-ASCII//TRANSLIT', $str);
    }
	
	
	function create_module_file($options) {
		$module_file='{   
   "name": "' . $options['module_slug'] . '",   
   "title": "' . $options['campaign_name'] . '",   
   "description": "' . str_replace("\r\n", '<br />', $options['campaign_description']) . '",
   "author": "' . $options['creator_name'] . '",
   "version": "1.0.0",
   "minimumCoreVersion": "' . $options['min_version'] . '",
   "url": "' . $options['creator_url'] . '",
   "packs": [
    {
      "name": "equipment",
      "label": "Equipment",
      "path": "packs/equipment.db",
      "entity": "Item",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "item",
      "label": "Items",
      "path": "packs/items.db",
      "entity": "Item",
      "module": "' . $options['module_slug'] . '"
    },
    {
      "name": "spells",
      "label": "Spells",
      "path": "packs/spells.db",
      "entity": "Item",
      "module": "' . $options['module_slug'] . '"
    },
    {
      "name": "weapons",
      "label": "Weapons",
      "path": "packs/weapons.db",
      "entity": "Item",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "players",
      "label": "Players",
      "path": "packs/players.db",
      "entity": "Actor",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "npc",
      "label": "NPCs",
      "path": "packs/npc.db",
      "entity": "Actor",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "monsters",
      "label": "Monsters",
      "path": "packs/monsters.db",
      "entity": "Actor",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "journals",
      "label": "Journals",
      "path": "packs/journals.db",
      "entity": "JournalEntry",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "macros",
      "label": "Macros",
      "path": "packs/macros.db",
      "entity": "Macro",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "playlists",
      "label": "Playlists",
      "path": "packs/playlists.db",
      "entity": "Playlist",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "scenes",
      "label": "Scenes",
      "path": "packs/scenes.db",
      "entity": "Scene",
      "module": "' . $options['module_slug'] . '"
    }
  ]
 }';
		return $module_file;
	}
	
	function zip($source, $destination){
		if (!extension_loaded('zip') || !file_exists($source)) {
			return false;
		}

		$zip = new ZipArchive();
		if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			return false;
		}

		$source = str_replace('\\', '/', realpath($source));

		if (is_dir($source) === true) {
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

			foreach ($files as $file) {
				$file = str_replace('\\', '/', $file);

				// Ignore "." and ".." folders
				if (in_array(substr($file, strrpos($file, '/')+1), array('.', '..'))) {
					continue;
				}               

				$file = realpath($file);

				if (is_dir($file) === true) {
					$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
				} elseif (is_file($file) === true) {
					$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
				}
			}
		} elseif (is_file($source) === true) {
			$zip->addFromString(basename($source), file_get_contents($source));
		}

		return $zip->close();
	}
	
	function rrmdir($src) {
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				$full = $src . '/' . $file;
				if ( is_dir($full) ) {
					rrmdir($full);
				}
				else {
					unlink($full);
				}
			}
		}
		closedir($dir);
		rmdir($src);
	}



?>
