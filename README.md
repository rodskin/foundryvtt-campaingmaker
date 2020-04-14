# foundryvtt-campaingmaker
https://foundryvtt.rodskin.com/


# Assets folder
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

# default compendiums
Creating a blank module with compendiums
Actual compendiums:
- Equipment
- Items
- Spells
- Weapons
- Players
- NPCs
- Monsters
- Journals
- Playlists
- Scenes

# TODO
- add multiple compendiums
- select compendium typt / name
- With 0.5.4, there's now a "compatibleCoreVersion" parameter instead of "minimumCoreVersion" You should switch to that (and enter 0.5.4, or allow them to enter their own version) otherwise the module will show up in the setup list as "Unknown Compatibility"

- For the items that get added to the "packs" tag (items.db, spells.db, monsters.db, etc.) you could have a set of <input type="checkbox"> elements to choose whether or not that type of pack exists in the module. That way it won't give any errors when you use the module (from missing .db files). Either that, or you could add more textboxes, but for packs. So people can add their own entries, since the .db file can be named differently. You could also use some simple Javascript to have an "Add Pack" button that adds another textbox/entry.

- A couple misspellings that might cause confusion. ressources should be resources and "resources/musics" should be "resources/music"

- Most modules don't use a "resources" directory at all, and instead put the folders inside the root module directory (e.g. "modulename/icons") alongside the module.json. Not sure if Foundry requires things to be like that, but I'd set it up that way just to make it simpler to route file paths. That's your decision, though.

- There are other parameters you could add support for like "languages" and  the "lang" folder. If this is just for compendiums though, those may not be useful.
