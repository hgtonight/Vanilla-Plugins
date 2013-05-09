<?php if (!defined("APPLICATION")) exit();
/*
 *  Copyright (C) 2013 Zachary Doll
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$PluginInfo["TranslateThis"] = array(
	"Name" => "Translate This",
	"Description" => "A translate plugin for Vanilla 2+. Wraps Translate This Button (http://translateth.is/) functionality into an easy to use module.",
	"Version" => "1.0",
	"Author" => "Zachary Doll",
	"AuthorEmail" => "hgtonight@gmail.com",
	"SettingsUrl" => "/dashboard/settings/translatethis",
	"SettingsPermission" => "Garden.Settings.Manage",
	"AuthorUrl" => "http://www.daklutz.com",
	"RequiredApplications" => array("Vanilla" => "2.0.18"),
	'License' => 'GPLv3'
);

/**
 * TranslateThis plugin for Vanilla
 * @author Zachary Doll
 */
class TranslateThis extends Gdn_Plugin {

	/**
	 * Build the setting page.
	 * @param $Sender
	 */
	public function SettingsController_TranslateThis_Create($Sender) {
		$Sender->Permission('Garden.Settings.Manage');

		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array("Plugins.TranslateThis.Languages"));
		$Sender->Form->SetModel($ConfigurationModel);

		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$Sender->Form->SetData($ConfigurationModel->Data);
		} else {
        	$Data = $Sender->Form->FormValues();
        	$ConfigurationModel->Validation->ApplyRule("Plugins.TranslateThis.Languages", "RequiredArray");
        	if ($Sender->Form->Save() !== FALSE)
        		$Sender->StatusMessage = T("Your settings have been saved.");
		}

		$Sender->Title('TranslateThis Settings');
		$Sender->AddSideMenu('/dashboard/settings/translatethis');

		$CategoryModel = new CategoryModel();
		$Sender->SetData("CategoryData", $CategoryModel->GetAll(), TRUE);
		array_shift($Sender->CategoryData->Result());

		$Sender->Render($this->GetView("settings.php"));
	}


	public function Base_Render_Before($Sender) {
		// bring in the module into every controller that isn't in the dashboard view
		if($Sender->MasterView != 'admin') {
			include_once(PATH_PLUGINS.DS.'TranslateThis'.DS.'class.translatethis.module.php');
			$Module = new TranslateThisModule($Sender);
			$Sender->AddModule($Module);

			$Sender->Head->AddScript('http://x.translateth.is/translate-this.js');
			$Sender->AddJsFile($this->GetResource('js/translatethis.js', FALSE, FALSE));
			
			// construct the settings array and pass it as a definition
			$TTSettings = '';
			$Sender->AddDefinition('TranslateThisSettings',$TTSettings);
		}
	}

	public function Setup() {}
}