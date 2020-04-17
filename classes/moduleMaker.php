<?php
class moduleMaker {
	public $moduleSlug = '';
	public $minVersion = '0.5.5';
	public $compatible_core_version = '';
	public $uniqId;
	public $tmpFolder = '';
	public $modulePath = '';
	
	public $foldersToCreate = array(
		'packs' => array(
			'name' => 'packs',
			'json_entry' => true,
			'deletable_on_front' => false
		),
		'lang' => array(
			'name' => 'lang',
			'json_entry' => true
		),
		'scripts' => array(
			'name' => 'scripts',
			'json_entry' => true
		),
		'templates' => array(
			'name' => 'templates',
			'json_entry' => false
		),
		'styles' => array(
			'name' => 'styles',
			'json_entry' => true
		),
		'esmodules' => array(
			'name' => 'esmodules',
			'json_entry' => true
		),
		'assets' => array(
			'name' => 'assets',
			'json_entry' => false,
			'children' => array(
				'scenes' => array(
					'name' => 'scenes',
					'json_entry' => false
				),
				'tokens' => array(
					'name' => 'tokens',
					'json_entry' => false
				),
				'portraits' => array(
					'name' => 'portraits',
					'json_entry' => false
				),
				'pictures' => array(
					'name' => 'pictures',
					'json_entry' => false
				),
				'icons' => array(
					'name' => 'icons',
					'json_entry' => false
				),
				'sounds' => array(
					'name' => 'sounds',
					'json_entry' => false
				),
				'musics' => array(
					'name' => 'musics',
					'json_entry' => false
				),
			)
		)
	);
	
	public function __construct ()
	{
		ksort($this->foldersToCreate);
	}
	
	public function createModuleSlug ($module_name)
	{
		return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array(' ', '-', ''), $this->remove_accents($module_name)));
	}
	
	public function remove_accents ($str)
    {
        if (!extension_loaded('iconv')) {
            trigger_error('Extension PHP ICONV manquante', E_USER_WARNING);
        }
        return iconv('UTF-8', 'US-ASCII//TRANSLIT', $str);
    }
	
	public function create_folders ($folders_to_create = array())
	{
		mkdir($this->tmpFolder);
		//echo $module_slug;
		mkdir($this->modulePath);
		
		foreach ($folders_to_create as $folder) {
			if (isset($this->foldersToCreate[$folder])) {
				mkdir($this->modulePath . '/' . $folder);
			}
		}
	}
	
	public function create_module_file($options) {
		$module_array = array();
		$module_array['name'] = $this->moduleSlug;
		$module_array['title'] = $options['module_name'];
		$module_array['description'] = $this->beautyDescription($options['module_description']);

		$module_array['author'] = $options['creator_name'];
		$module_array['version'] = '0.0.1';
		$module_array['compatibleCoreVersion'] = (isset($options['compatible_core_version']) && trim($options['compatible_core_version']) != '') ? $options['compatible_core_version'] : '';
		$module_array['minimumCoreVersion'] = $options['minimum_core_version'];
		$module_array['url'] = (isset($options['creator_url']) && trim($options['creator_url']) != '') ? $options['creator_url'] : '';
		
		if (isset($options['folders_to_create']) && !empty($options['folders_to_create'])) {
			foreach ($options['folders_to_create'] as $folder) {
				if (isset($this->foldersToCreate[$folder]) && $this->foldersToCreate[$folder]['json_entry'] == true) {
					$item_for_files = $folder . '_for_file';
					if (isset($options[$folder])) {
						$module_array[$folder] = $this->$item_for_files($options[$folder]);
					}
				}
			}
		}
		$module_array['manifest'] = '';
		$module_array['download'] = '';

		return $this->pretty_json($module_array);
	}
	
	public function pretty_json ($array_to_convert) {
		$tab = '    ';
		//print_r($array_to_convert);
		$return_string = '{' . "\n";
		foreach ($array_to_convert as $key => $value) {
			if (!is_array($value)) {
				$return_string .= $tab . '"' . $key . '": ' . '"' . $value . '",' . "\n";
			} else {
				if (!empty($value)) {
					$return_string .= $tab . '"' . $key . '": [' . "\n";
					foreach ($value as $sub_key => $sub_value) {
						$return_string .= $tab . $tab . '{' . "\n";
						foreach ($sub_value as $end_key => $end_value) {
							$return_string .= $tab . $tab . $tab . '"' . $end_key . '": ' . '"' . $end_value . '",' . "\n";
						}
						$return_string = substr($return_string, 0, -2);
						$return_string .= "\n";
						$return_string .= $tab . $tab . '},' . "\n";
					}
					$return_string = substr($return_string, 0, -2);
					$return_string .= "\n";
					$return_string .= $tab . '],' . "\n";
				}
			}
		}
		$return_string = substr($return_string, 0, -2);
		$return_string .= "\n";
		$return_string .= '}';
		return $return_string;
	}
	
	public function lang_for_file ($lang = array()) {
		// @TODO ?
		return $lang;
	}
	
	public function scripts_for_file ($scripts = array()) {
		// @TODO ?
		return $scripts;
	}
	
	public function styles_for_file ($styles = array()) {
		// @TODO ?
		return $styles;
	}
	
	public function esmodules_for_file ($esmodules = array()) {
		// @TODO ?
		return $esmodules;
	}
	
	public function packs_for_file ($packs = array()) {
		$packs_added = array();
		$array_packs = array();
		if (isset($packs) && !empty($packs)) {
			$available_packs_entities = $this->get_packs_entities();
			foreach ($packs as $key => $pack) {
				$name_clean = $this->beautyPackName($pack['label']);
				if (trim($name_clean) != '' && array_key_exists($name_clean, $packs_added) == false) {
					$array_packs[$key]['name'] = $name_clean;
					$array_packs[$key]['label'] = $pack['label'];
					$array_packs[$key]['path'] = 'packs/' . $name_clean . '.db';
					$array_packs[$key]['entity'] = $pack['entity'];
					$array_packs[$key]['module'] = $this->moduleSlug;
					$packs_added[$name_clean] = $name_clean;
				}
			}
		}
		return $array_packs;
	}
	
	public function get_default_packs () {
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
	
	public function get_packs_entities () {
		$pack_entities = array('Item', 'Actor', 'JournalEntry', 'Macro', 'Playlist', 'Scene');
		sort($pack_entities);
		return $pack_entities;
	}
	
	public function beautyDescription ($description = '')
	{
		if (trim($description) != '') {
			$description = str_replace("\r\n", '<br />', $description);
			$description = str_replace('"', '\"', $description);
		}
		 return $description;
	}
	
	public function beautyPackName($label)
	{
		return urlencode(strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array(' ', '-', ''), $this->remove_accents($label))));
	}
	
	public function zip($source, $destination){
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
	
	public function rrmdir($src) {
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				$full = $src . '/' . $file;
				if ( is_dir($full) ) {
					$this->rrmdir($full);
				}
				else {
					unlink($full);
				}
			}
		}
		closedir($dir);
		rmdir($src);
	}
	
	public function createModule ($options)
	{
		$this->uniqId = uniqid('', true);
		$this->moduleSlug = $this->createModuleSlug($options['module_name']);
        $uniq_id = uniqid('', true);
		
        //echo $uniq_id;
        // module creation
		$this->tmpFolder = '/tmp/' . $uniq_id;
		
		$this->modulePath = $this->tmpFolder . '/' . $this->moduleSlug;
		
		foreach ($options['folders_to_create'] as $folder) {
			if (isset($options[$folder])) {
				$num = 0;
				foreach ($options[$folder] as $value) {
					if (trim($value['label']) != '' && $value['entity'] != '') {
						$num ++;
					}
				}
				if ($num == 0) {
					unset($options['folders_to_create'][$folder]);
				}
			}
		}
		
		$this->create_folders($options['folders_to_create']);
		//die();
		$module_file = $this->create_module_file($options);
		
		file_put_contents($this->modulePath . '/module.json', $module_file);
		
		$zip_name = $this->tmpFolder . '.zip';
		$this->zip($this->tmpFolder . '/', $zip_name);
		
		//echo filesize('/tmp/' . $uniq_id . '.zip');
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . basename($zip_name) . '"');
		header('Content-Length: ' . filesize($zip_name));

		flush();
		@readfile($zip_name);
		$this->rrmdir($this->tmpFolder);
		unlink($zip_name);
	}
}