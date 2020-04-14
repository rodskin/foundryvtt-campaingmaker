<?php
	ini_set(default_charset, "UTF8");
	setlocale(LC_ALL, 'en_US.UTF8');
	
    if (isset($_POST) && !empty($_POST)) {
        print_r($_POST);
		$options = $_POST;
        $uniq_id = uniqid('', true);
        echo $uniq_id;
        // module creation
		$tmp_folder = 'tmp/' . $uniq_id;
        mkdir($tmp_folder);
		
        $module_slug = strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array(' ', '-', ''), remove_accents($_POST['campaign_name'])));
		echo $module_slug;
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
		$module_file = create_module_file($options);
		
		file_put_contents($module_path . '/module.json', $module_file);
		
		
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
        <div>
            <form action="" id="campaign_maker_form" method="post">
                <input type="text" name="creator_name" placeholder="Insert your name *" required="required" /><br />
                <input type="text" name="creator_url" placeholder="Insert your site url" /><br />
                <input type="text" name="campaign_name" placeholder="Insert your campaign name *" required="required" /><br />
				<textarea name="campaign_description" placeholder="campaign description"></textarea>
                <br />
                <input type="submit" value="create and download module" />
            </form>
        </div> 
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
		$module_file='
		{   
   "name": "' . $options['module_slug'] . '",   
   "title": "' . $options['campaign_name'] . '",   
   "description": "' . $options['campaign_name'] . '",   
   "author": "' . $options['campaign_name'] . '",
   "version": "1.0.0",
   "minimumCoreVersion": "0.5.3",
   "packs": [
    {
      "name": "equipment",
      "label": "My Equipment",
      "path": "packs/equipment.db",
      "entity": "Item",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "item",
      "label": "My Items",
      "path": "packs/items.db",
      "entity": "Item",
      "module": "' . $options['module_slug'] . '"
    },
    {
      "name": "spells",
      "label": "My Spells",
      "path": "packs/spells.db",
      "entity": "Item",
      "module": "' . $options['module_slug'] . '"
    },
    {
      "name": "weapons",
      "label": "My Weapons",
      "path": "packs/weapons.db",
      "entity": "Item",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "players",
      "label": "My Players",
      "path": "packs/players.db",
      "entity": "Actor",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "npc",
      "label": "My NPCs",
      "path": "packs/npc.db",
      "entity": "Actor",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "monsters",
      "label": "My Monsters",
      "path": "packs/monsters.db",
      "entity": "Actor",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "journals",
      "label": "My Journals",
      "path": "packs/journals.db",
      "entity": "Item",
      "module": "' . $options['module_slug'] . '"
    },
	{
      "name": "scenes",
      "label": "My Scenes",
      "path": "packs/scenes.db",
      "entity": "Scene",
      "module": "' . $options['module_slug'] . '"
    }
  ]
 }';
		return $module_file;
	}


?>
