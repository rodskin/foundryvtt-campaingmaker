<?php
	require_once('functions.php');
	ini_set('default_charset', "UTF8");
	setlocale(LC_ALL, 'en_US.UTF8');
	$min_version = '0.5.3';
	$default_min_version = '0.5.3';
	$compatible_core_version = '';
	
    if (isset($_POST) && !empty($_POST)) {
		//echo '<pre>';
        //print_r($_POST);
		//die();
		$options = $_POST;
		$options['min_version'] = $min_version;
        $uniq_id = uniqid('', true);
		
		$module_slug = strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array(' ', '-', ''), remove_accents($_POST['campaign_name'])));
		
        //echo $uniq_id;
        // module creation
		$tmp_folder = '/tmp/' . $uniq_id;
		
		$options['module_slug'] = $module_slug;
		$module_path = $tmp_folder . '/' . $module_slug;
		
		create_folders($tmp_folder, $module_path);
		
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
	$default_packs = get_default_packs();
	$packs_entities = get_packs_entities();
?>
 <!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>FoundryVTT Campaign module maker</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
	+ assets/
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
				<input type="hidden" id="hid_packs_number" value="<?php echo count($default_packs); ?>" />
				<fieldset>
					<legend>Module Informations:</legend>
					<input type="text" name="campaign_name" placeholder="Insert your campaign name *" required="required" style="width: 300px;" /><br />
					<input type="text" name="creator_name" placeholder="Insert your name *" required="required" style="width: 300px;" /><br />
					<input type="text" name="creator_url" placeholder="Insert your site url" style="width: 300px;" /><br />
					<input type="text" name="compatible_core_version" placeholder="Compatible Core Version" style="width: 300px;" /><br />
					<textarea name="campaign_description" placeholder="campaign description" style="width: 500px; height: 200px;"></textarea>
				</fieldset>
				<fieldset>
					<legend>Module Packs:</legend>
					<div id="packs_wrapper">
						<?php
							foreach ($default_packs as $key => $pack) {
						?>
						<div id="pack_<?php echo $key; ?>" class="pack_block">
							<input type="text" name="packs[<?php echo $key; ?>][label]" class="pack_label" placeholder="Label" value="<?php echo $pack['label']; ?>" />
							<select name="packs[<?php echo $key; ?>][entity]">
								<option value=""> -- select an entity --</option>
								<?php
									foreach ($packs_entities as $entity) {
								?>
								<option value="<?php echo $entity; ?>"<?php if ($entity == $pack['entity']) { echo ' selected="selected"'; } ?>><?php echo $entity; ?></option>
								<?php							
									}
								?>
							</select>
							<i id="delete_<?php echo $key; ?>" class="fa fa-trash-o"></i>
						</div>
						<?php
							}
						?>
					</div>
					<hr />
					<button id="add_pack">Add pack</button>
				</fieldset>
                <br />
                <input type="submit" value="create and download module" />
            </form>
        </div>
		<br />
		<div><a href="http://ko-fi.com/rodskin" target="_blank">you can buy me a coffee :)</a></div><br />
		<div><a href="https://foundryvtt.com/" target="_blank">Foundry Virtual Tabletop Official Site</a></div>
		<script type="text/javascript">
			var packs_wrapper_html = packs_wrapper.innerHTML;
			
			document.addEventListener('click', function (event) {
				if (event.target.matches('.fa-trash-o')) {
					event.preventDefault();
					var id = event.target.id.replace('delete_','');
					document.getElementById("pack_" + id).remove();
				}
				if (event.target.matches('#add_pack')) {
					event.preventDefault();
					var next_id = parseInt(document.getElementById('hid_packs_number').value);
					
					var packs_wrapper = document.getElementById('packs_wrapper');
					
					var add_pack = '<div id="pack_' + next_id + '">';
					add_pack += '<input type="text" name="packs[' + next_id + '][label]" class="pack_label" placeholder="Label" value="" /> ';
					add_pack += '<select name="packs[' + next_id + '][entity]">';
					add_pack += '<option value=""> -- select an entity --</option>';
					<?php
						foreach ($packs_entities as $entity) {
					?>
					add_pack += '<option value="<?php echo $entity; ?>"><?php echo $entity; ?></option>';
					<?php							
						}
					?>
					add_pack += '</select>';
					add_pack += '<i id="delete_' + next_id + '" class="fa fa-trash-o"></i>';
					
					packs_wrapper.innerHTML = packs_wrapper.innerHTML + add_pack;
					
					document.getElementById('hid_packs_number').value = next_id + 1;
				}
			}, false);
		</script>
    </body>
</html>
