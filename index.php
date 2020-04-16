<?php
	require_once('classes/moduleMaker.php');
	ini_set('default_charset', "UTF8");
	setlocale(LC_ALL, 'en_US.UTF8');

	$moduleMaker = new moduleMaker();
	
    if (isset($_POST) && !empty($_POST)) {
		$options = $_POST;
		$moduleMaker->createModule($_POST);
    }
	$default_packs = $moduleMaker->get_default_packs();
	$packs_entities = $moduleMaker->get_packs_entities();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>FoundryVTT Module Maker</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script type="text/javascript"  src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"  data-key="jquery" ></script>
    </head>

    <body>
        <h1>Welcome to the Foundry Virtual Tabletop Module maker</h1>
		<h3>Using version <strong><?php echo $moduleMaker->minVersion; ?></strong></h3>
		<div>
			This is a plugin creator to save or export your characters, items, scenes, journal entries etc...
			You will find in your downloaded module a skeleton of folders for all your ressources:
			
			<pre style="background-color:#F6F7F8; padding: 10px;">
+ YOUR_MODULE/
	+ packs/
	+ lang/
	+ scripts/
	+ templates/
	+ styles/
	+ assets/
	   + icons/
	   + musics/
	   + pictures/
	   + portraits/
	   + scenes/
	   + sounds/
	   + tokens/
	+ module.json
			</pre>
            <form action="" id="module_maker_form" method="post">
				<input type="hidden" id="hid_packs_number" value="<?php echo count($default_packs); ?>" />
				<fieldset>
					<legend>Module Informations:</legend>
					<input type="text" name="module_name" placeholder="Insert your module name *" required="required" style="width: 300px;" /><br />
					<input type="text" name="creator_name" placeholder="Insert your name *" required="required" style="width: 300px;" /><br />
					<input type="text" name="creator_url" placeholder="Insert your site url" style="width: 300px;" /><br />
					<input type="text" name="minimum_core_version" placeholder="Minimum Core Version *" required="required" style="width: 300px;" /><br />
					<input type="text" name="compatible_core_version" placeholder="Compatible Core Version" style="width: 300px;" /><br />
					<textarea name="module_description" placeholder="Module description" style="width: 500px; height: 200px;"></textarea>
				</fieldset>

				<fieldset>
					<legend>Module Folders</legend>
					<?php
						$folders_to_create = $moduleMaker->foldersToCreate;
					?>
					<ul>
						<li>Module root
							<ul>
					<?php
						foreach ($folders_to_create as $folder) {
							$disabled = '';
							if (isset($folder['deletable_on_front']) && $folder['deletable_on_front'] == false) {
								$disabled = ' disabled="disabled"';
							}
							echo '<li>';
							echo '<input type="checkbox" id="folder_' . $folder['name'] . '" name="folders_to_create[]" value="' . $folder['name'] . '" checked="checked"' . $disabled . ' /> ' . $folder['name'];
							if (isset($folder['deletable_on_front']) && $folder['deletable_on_front'] == false) {
									echo '<input type="hidden" id="hid_folder_' . $folder['name'] . '" name="folders_to_create[]" value="' . $folder['name'] . '" />';
								}
							if (isset($folder['children'])) {
								echo '<ul>';
								foreach ($folder['children'] as $child) {
									$disabled = '';
									if (isset($child['deletable_on_front']) && $child['deletable_on_front'] == false) {
										$disabled = ' disabled="disabled"';
									}
									echo '<li>';
									echo '<input type="checkbox" id="folder_' . $folder['name'] . '/' . $child['name'] . '" name="folders_to_create[]" value="' . $folder['name'] . '/' . $child['name'] . '" checked="checked"' . $disabled . ' /> ' . $child['name'];
									if (isset($child['deletable_on_front']) && $child['deletable_on_front'] == false) {
										echo '<input type="hidden" id="hid_folder_' . $folder['name'] . '/' . $child['name'] . '" name="folders_to_create[]" value="' . $folder['name'] . '/' . $child['name'] . '" />';
									}
									echo '</li>';
								}
								echo '</ul>';
							}
							echo '</li>';
						}
					?>
							</ul>
						</li>
					</ul>
				</fieldset>

				<fieldset>
					<legend>Module Packs:</legend>
					<div id="delete_all_packs">Delete all packs: <i id="delete_all" class="fa fa-trash-o" style="color:red;"></i></div>
					<br />
					<div id="packs_wrapper">
						<?php
							foreach ($default_packs as $key => $pack) {
						?>
						<div id="pack_<?php echo $key; ?>" class="pack_block">
							<input type="text" name="packs[<?php echo $key; ?>][label]" class="pack_label" placeholder="Label" value="<?php echo $pack['label']; ?>" />
							<select class="pack_entity" name="packs[<?php echo $key; ?>][entity]">
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
			
			/*var form_el = document.getElementById("module_maker_form");
			form_el.addEventListener("submit", function(evt) {
				evt.preventDefault();
				var packsLabelNumber = 0;
				var packsEntityNumber = 0;
				
				var packsByLabel = document.getElementsByClassName('pack_label');
				Array.prototype.forEach.call(packsByLabel, function(el) {
					if (el.value.trim() != '') {
						packsLabelNumber ++;
					} else {
						el.parentElement.remove();
					}
				});
				
				var packsByEntity = document.getElementsByClassName('pack_entity');
				Array.prototype.forEach.call(packsByEntity, function(el) {
					if (el.value.trim() != '') {
						packsEntityNumber ++;
					} else {
						el.parentElement.remove();
					}
				});
				//return true;
				document.getElementById("module_maker_form").submit();
			});*/
			
			
			document.addEventListener('click', function (event) {
				if (event.target.matches('.fa-trash-o')) {
					event.preventDefault();
					var id = event.target.id.replace('delete_','');
					if (id == 'all') {
						var delete_all = confirm("Are you sure you want to delete all Packs ?");
						if (delete_all == true) {
							var packs_wrapper = document.getElementById('packs_wrapper');
							packs_wrapper.innerHTML = '';
							document.getElementById('hid_packs_number').value = 0;
							document.getElementById('folder_packs').checked = false;
							document.getElementById('hid_folder_packs').value = '';
						}
					} else {
						document.getElementById("pack_" + id).remove();
					}
				}
				if (event.target.matches('#add_pack')) {
					event.preventDefault();
					document.getElementById('folder_packs').checked = true;
					document.getElementById('hid_folder_packs').value = 'packs';
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
