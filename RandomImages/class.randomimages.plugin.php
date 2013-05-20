<?php if (!defined('APPLICATION')) exit();
/*	Copyright 2013 Zachary Doll
*	This program is free software: you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation, either version 3 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*	GNU General Public License for more details.
*
*	You should have received a copy of the GNU General Public License
*	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
$PluginInfo['RandomImages'] = array(
	'Name' => 'Random Images',
	'Description' => 'Renders a list of random images from the current discussion model.',
	'MobileFriendly' => TRUE,
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
	'SettingsUrl' => '/settings/randomimages',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => 'Zachary Doll',
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com/',
);

class RandomImagesPlugin extends Gdn_Plugin {

	public function __construct()
	{
		parent::__construct();
	}

	public function DiscussionsController_BeforeRenderAsset_Handler($Sender) {
		if($Sender->EventArguments['AssetName'] == 'Content') {
			$Discussions = $Sender->Data['Discussions'];
			$this->_RenderImageList($Discussions);
		}
	}
	
	public function CategoriesController_BeforeRenderAsset_Handler($Sender) {
		if($Sender->EventArguments['AssetName'] == 'Content') {
			$Discussions = $Sender->Data['Discussions'];
			$this->_RenderImageList($Discussions);
		}
	}
	
	public function DiscussionsController_Render_Before($Sender) {
		$this->_AddResources($Sender);
	}
	
	public function CategoriesController_Render_Before($Sender) {
		$this->_AddResources($Sender);
	}
	
	public function Setup() {
		parent::Setup();
	}
	
	private function _RenderImageList($DiscussionModel) {
		if(!method_exists($DiscussionModel, 'Result') ) {
			// Fail gracefully if a discussion model object was passed
			return FALSE;
		}
		
		$Images = array();
		$ImageList = '';
		$ImageCount = 0;
		$ImageMax = C('Plugins.RandomImages.MaxLength', 10);
		foreach($DiscussionModel->Result() as $Discussion) {
			$ImageFound = preg_match_all('/([a-z\-_0-9\/\:\.]*\.(jpg|jpeg|png|gif))/i', $Discussion->Body, $ImageSrcs);
			var_dump($ImageSrcs);
			if ($ImageFound) {
				$i = 0;
				while($i < $ImageFound) {
					$i++;
					array_push($Images, $ImageSrcs[0][$i]);
				}
			}
		}
		var_dump($Images);
		// assemble a list
		// $ImageList .= Wrap(Anchor(Img($ImageSrcs[1], array('class' => 'RandomImage')), $Discussion->Url),
		// 'li');
		// $ImageCount++;
				
		//if($ImageCount < $ImageMax) {
			// echo Wrap($ImageList, 'ul', array('id' => 'RandomImageList'));
		//}
	}
	
	public function SettingsController_RandomImages_Create($Sender) {
		$Sender->Permission('Garden.Settings.Manage');

		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array(
			'Plugins.RandomImages.MaxLength'
			));
		$Sender->Form->SetModel($ConfigurationModel);

		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$Sender->Form->SetData($ConfigurationModel->Data);
		} else {
			$ConfigurationModel->Validation->ApplyRule('Plugins.RandomImages.MaxLength', 'Integer');
        	$Data = $Sender->Form->FormValues();
			if ($Sender->Form->Save() !== FALSE) {
        		$Sender->InformMessage('<span class="InformSprite Sliders"></span>'.T("Your changes have been saved."),'HasSprite');
			}
		}
		
		$Sender->AddSideMenu();
		$Sender->Render($this->GetView('settings.php'));
	}
	
	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu = &$Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Add-ons', 'Random Images', 'settings/randomimages', 'Garden.Settings.Manage');
	}
	
	private function _AddResources($Sender) {
		$Sender->AddCSSFile($this->GetResource('design/randomimages.css', FALSE, FALSE));
	}
}