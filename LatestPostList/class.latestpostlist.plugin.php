<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['LatestPostList'] = array(
   'Description' => 'Provides a list of links to the latest posts in the panel.',
   'Version' => '0.1',
   'RequiredApplications' => array('Vanilla' => '2.0.10'),
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => FALSE,
   'SettingsUrl' => '/plugin/latestpostlist',
   'SettingsPermission' => 'Garden.AdminUser.Only',
   'Author' => "Zachary Doll",
   'AuthorEmail' => 'hgtonight@gmail.com',
   'AuthorUrl' => 'http://github.com/hgtonight/Latest-Post-List',
   'License' => 'GPLv3'
);

class LatestPostList extends Gdn_Plugin {

	// runs once every page load
	public function __construct() {
	  
	}
	
	// Create a method called "LatestPostList" on the PluginController
   public function PluginController_LatestPostList_Create($Sender) {
		$Sender->Title('Latest Post List Plugin');
		$Sender->AddSideMenu('plugin/latestpostlist');

		// get sub-pages forms ready
		$Sender->Form = new Gdn_Form();

		// needed for a "fake" controller
		$this->Dispatch($Sender, $Sender->RequestArgs);
	}
   
	//This is a common hook that fires for all controllers on the Render method
	public function Base_Render_Before($Sender) {
		// Get the config and controller name for comparison
		$Pages = C('Plugin.LatestPostList.Pages', 'all');
		$Controller = $Sender->ControllerName;
		
		// Enumerate what preference relates to which controller
		$ShowOnController = array();		
		switch($Pages) {
			case 'announcements':
				$ShowOnController = array(
				'profilecontroller',
				'activitycontroller'
				);
				break;
			case 'discussions':
				$ShowOnController = array(
				'discussioncontroller',
				'discussionscontroller',
				'categoriescontroller'
				);
				break;
			case 'all':
			default:
				$ShowOnController = array(
				'discussioncontroller',
				'categoriescontroller',
				'discussionscontroller',
				'profilecontroller',
				'activitycontroller'
				);
				break;				
		}
		// leave if we aren't in an approved controller
		if (!InArrayI($Controller, $ShowOnController)) return; 

		// bring in the module
		$Count = C('Plugin.LatestPostList.Count', 5);
		include_once(PATH_PLUGINS.DS.'LatestPostList'.DS.'class.latestpostlist.module.php');
		$LatestPostListModule = new LatestPostListModule($Sender);
		$LatestPostListModule->GetData($Count);
		$Sender->AddModule($LatestPostListModule);

		// Only add the JS file and definition if needed
		$Frequency = C('Plugin.LatestPostList.Frequency', 30);
		if($Frequency > 0) {
			// JS to update the list through ajax
			$Sender->AddJsFile($this->GetResource('js/latestpostlist.js', FALSE, FALSE));
			// put the frequency someplace the js can access it
			$Sender->AddDefinition('LatestPostListFrequency', $Frequency);
		}
	}
	
	public function Controller_module($Sender) {
		// This just spits out the html of the module. Used for the ajax refresh
		$Count = C('Plugin.LatestPostList.Count', 5);
		include_once(PATH_PLUGINS.DS.'LatestPostList'.DS.'class.latestpostlist.module.php');
		$LatestPostListModule = new LatestPostListModule($Sender);
		$LatestPostListModule->GetData($Count);
		echo $LatestPostListModule->ToString();
	}
   
	// index is a good place for an admin page (plugin/latestpostlist)
	public function Controller_Index($Sender) {
		// Admins only
		$Sender->Permission('Vanilla.Settings.Manage');
		$Sender->SetData('PluginDescription',$this->GetPluginKey('Description'));
		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array(
			'Plugin.LatestPostList.Pages'	=> 'all',
			'Plugin.LatestPostList.Frequency'	=> 120,
			'Plugin.LatestPostList.Count'	=> 5
		));

		// Set the model on the form.
		$Sender->Form->SetModel($ConfigurationModel);

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Apply the config settings to the form.
			$Sender->Form->SetData($ConfigurationModel->Data);
		}
		else {
			$ConfigurationModel->Validation->ApplyRule('Plugin.LatestPostList.Pages', 'Required');

			$ConfigurationModel->Validation->ApplyRule('Plugin.LatestPostList.Frequency', 'Required');
			$ConfigurationModel->Validation->ApplyRule('Plugin.LatestPostList.Frequency', 'Integer');

			$ConfigurationModel->Validation->ApplyRule('Plugin.LatestPostList.Count', 'Required');
			$ConfigurationModel->Validation->ApplyRule('Plugin.LatestPostList.Count', 'Integer');

			$Saved = $Sender->Form->Save();
			if ($Saved) {
				$Sender->InformMessage('<span class="InformSprite Sliders"></span>'.T("Your changes have been saved."),'HasSprite');
			}
		}
		
		// Render the settings view
		$Sender->Render($this->GetView('latestpostlist.php'));
	}
   
	//Add a link to the dashboard menu
	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu = &$Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Add-ons', 'Latest Post List', 'plugin/latestpostlist', 'Garden.AdminUser.Only');
	}
   
	// fired on install (once)
	public function Setup() {
		// Set up the plugin's default values
		SaveToConfig('Plugin.LatestPostList.Frequency', 120);
		SaveToConfig('Plugin.LatestPostList.Count', 5);
		SaveToConfig('Plugin.LatestPostList.Pages', "all");
	}

	// fired on disable (removal)
	public function OnDisable() {
		RemoveFromConfig('Plugin.LatestPostList.Frequency');
		RemoveFromConfig('Plugin.LatestPostList.Count');
		RemoveFromConfig('Plugin.LatestPostList.Pages');
	}
   
}
