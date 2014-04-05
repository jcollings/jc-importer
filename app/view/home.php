<?php
global $jcimporter;
$tab = isset($_GET['tab']) && !empty($_GET['tab']) ? $_GET['tab'] : 'home';
$action = isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : 'index';
?>
<div class="wrap">
	<?php 
	switch($tab){
		case 'templates':
			require 'templates.php';
		break;
		case 'settings':
			require 'settings.php';
		break;
		default:
			$id = isset($_GET['import']) && !empty($_GET['import']) ? $_GET['import'] : 0;
			switch($action){
				case 'logs':
					$jcimporter->importer = new JC_Importer_Core($id);
					require 'imports/logs.php';
					break;
				case 'history':
					$jcimporter->importer = new JC_Importer_Core($id);
					require 'imports/history.php';
					break;
				case 'edit':
					$jcimporter->importer = new JC_Importer_Core($id);
					require 'imports/edit.php';
					break;
				case 'add':
					require 'imports/add.php';
					break;
				default:
					require 'imports.php';
					break;
			}
			
		break;
	}
	?>
</div>