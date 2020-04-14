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
		$array_folders_to_create = array(
			'packs',
			'lang',
			'scripts',
			'templates',
			'styles',
			'assets',
			'assets/scenes',
			'assets/tokens',
			'assets/portraits',
			'assets/pictures',
			'assets/icons',
			'assets/sounds',
			'assets/musics'
		);
		
		foreach ($array_folders_to_create as $folder) {
			mkdir($module_path . '/' . $folder);
		}
	}
	
	function create_module_file($options) {
		$packs_added = array();
		$module_array = array();
		$module_array['name'] = $options['module_slug'];
		$module_array['title'] = $options['campaign_name'];
		if (isset($options['campaign_description']) && trim($options['campaign_description']) != '') {
			$description = str_replace("\r\n", '<br />', $options['campaign_description']);
			$description = str_replace('"', '\"', $description);
			$module_array['description'] = $description;
		} else {
			$module_array['description'] = '';
		}
		$module_array['author'] = $options['creator_name'];
		$module_array['version'] = $options['0.0.1'];
		$module_array['compatibleCoreVersion'] = (isset($options['compatible_core_version']) && trim($options['compatible_core_version']) != '') ? $options['compatible_core_version'] : '';
		$module_array['minimumCoreVersion'] = $options['minimum_core_version'];
		$module_array['url'] = (isset($options['creator_url']) && trim($options['creator_url']) != '') ? $options['creator_url'] : '';
		$module_array['languages'] = languages_for_file();
		$module_array['scripts'] = scripts_for_file();
		$module_array['styles'] = styles_for_file();
		$module_array['esmodules'] = esmodules_for_file;
		$module_array['packs'] = packs_for_file($options['packs'], $options['module_slug']);
		$module_array['manifest'] = '';
		$module_array['download'] = '';

		return json_encode($module_array);
	}
	
	function languages_for_file () {
		$array_languages = array();
		// @TODO ?
		return $array_languages;
	}
	
	function scripts_for_file () {
		$array_scripts = array();
		// @TODO ?
		return $array_scripts;
	}
	
	function styles_for_file () {
		$array_styles = array();
		// @TODO ?
		return $array_styles;
	}
	
	function esmodules_for_file () {
		$array_esmodules = array();
		// @TODO ?
		return $array_esmodules;
	}
	
	function packs_for_file ($packs, $module_slug) {
		$packs_added = array();
		$array_packs = array();
		if (isset($packs) && !empty($packs)) {
			$available_packs_entities = get_packs_entities();
			foreach ($packs as $key => $pack) {
				$name_clean = urlencode(strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array(' ', '-', ''), remove_accents($pack['label']))));
				if (trim($name_clean) != '' && array_key_exists($name_clean, $packs_added) == false) {
					$array_packs[$key]['name'] = $name_clean;
					$array_packs[$key]['label'] = $pack['label'];
					$array_packs[$key]['path'] = 'packs/' . $name_clean . '.db';
					$array_packs[$key]['entity'] = $pack['entity'];
					$array_packs[$key]['module'] = $module_slug;
					$packs_added[$name_clean] = $name_clean;
				}
			}
		}
		return $array_packs;
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