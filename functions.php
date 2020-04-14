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
	
	function create_folders ($tmp_folder, $module_path) {
		mkdir($tmp_folder);
		//echo $module_slug;
		mkdir($module_path);
		$module_packs_path = $module_path . '/packs';
		mkdir($module_packs_path);
		$module_langs_path = $module_path . '/lang';
		mkdir($module_langs_path);
		$module_ressources_path = $module_path . '/assets';
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
	}
	
	
	function create_module_file($options) {
		$packs_added = array();
		$module_file='{   
	"name": "' . $options['module_slug'] . '",   
	"title": "' . $options['campaign_name'] . '",';
		if (isset($options['campaign_description']) && trim($options['campaign_description']) != '') {
			$description = str_replace("\r\n", '<br />', $options['campaign_description']);
			$description = str_replace('"', '\"', $description);
			$module_file .= '
    "description": "' . $description . '",';
		}
		$module_file .= '
	"author": "' . $options['creator_name'] . '",
	"version": "1.0.0",';
		if (isset($options['compatible_core_version']) && trim($options['compatible_core_version']) != '') {
			$module_file .= '
	"compatibleCoreVersion": "' . $options['compatible_core_version'] . '",';
		} else {
			$module_file .= '
	"compatibleCoreVersion": "' . $options['min_version'] . '",';
		}
		if (isset($options['creator_url']) && trim($options['creator_url']) != '') {
			$module_file .= '
	"url": "' . $options['creator_url'] . '",';
		}
		$module_tmp_pack = '';
		if (isset($options['packs']) && !empty($options['packs'])) {
			$module_tmp_pack .= '
	"packs": [';
			$available_packs_entities = get_packs_entities();
			foreach ($options['packs'] as $pack) {
				if (trim($pack['label']) != '' && trim($pack['entity']) != '' && in_array($pack['entity'], $available_packs_entities)) {
					$name_clean = urlencode(strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array(' ', '-', ''), remove_accents($pack['label']))));
					if (trim($name_clean) != '' && array_key_exists($name_clean, $packs_added) == false) {
						$module_tmp_pack .= '
	{
      "name": "' . $name_clean . '",
      "label": "' . $pack['label'] . '",
      "path": "packs/' . $name_clean . '.db",
      "entity": "' . $pack['entity'] . '",
      "module": "' . $options['module_slug'] . '"
    },';
						$packs_added[$name_clean] = $name_clean;
					}
				}
			}
			$module_tmp_pack = substr($module_tmp_pack, 0, -1);
			$module_file .= $module_tmp_pack;
			$module_file .= '
  ]';
		}
		$module_file .= '
 };';
		return $module_file;
	}
	
	function get_default_packs () {
		$default_packs = array(
			array(
				"label" => "Equipment",
				"entity" => "Item"
			),
			array(
				"label" => "Items",
				"entity" => "Item"
			),
			array(
				"label" => "Spells",
				"entity" => "Item"
			),
			array(
				"label" => "Weapons",
				"entity" => "Item"
			),
			array(
				"label" => "Players",
				"entity" => "Actor"
			),
			array(
				"label" => "NPCs",
				"entity" => "Actor"
			),
			array(
				"label" => "Monsters",
				"entity" => "Actor"
			),
			array(
				"label" => "Journals",
				"entity" => "JournalEntry"
			),
			array(
				"label" => "Macros",
				"entity" => "Macro"
			),
			array(
				"label" => "Playlists",
				"entity" => "Playlist"
			),
			array(
				"label" => "Scenes",
				"entity" => "Scene"
			)
		);
		return $default_packs;
	}
	
	function get_packs_entities () {
		$pack_entities = array('Item', 'Actor', 'JournalEntry', 'Macro', 'Playlist', 'Scene');
		sort($pack_entities);
		return $pack_entities;
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