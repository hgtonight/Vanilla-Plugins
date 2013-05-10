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
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => FALSE,
	'MobileFriendly' => TRUE,
	'HasLocale' => TRUE,
	'Version' => '0.3',
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
		
		$ImageList = '';
		$ImageCount = 0;
		
		foreach($DiscussionModel->Result() as $Discussion) {
			preg_match('#\<img.+?src="([^"]*).+?\>|\[img\]([^\[]*)\[\/img\]#s', $Discussion->Body, $ImageSrcs);
			if ($ImageSrcs[1]) {
				$ImageList .= Wrap(Anchor(Img($ImageSrcs[1], array('class' => 'RandomImage')), $Discussion->Url),
				'li');
				$ImageCount++;
			}
			else if($ImageSrcs[2]) {
				$ImageList .= Wrap(Anchor(Img($ImageSrcs[2], array('class' => 'RandomImage')), $Discussion->Url),
				'li');
				$ImageCount++;
			}
			if($ImageCount >= C('Plugins.RandomImage.MaxLength', 10) ) {
				echo Wrap($ImageList, 'ul', array('id' => 'RandomImageList'));
				break;
			}
		}
		if($ImageCount < C('Plugins.RandomImage.MaxLength', 10) ) {
			echo Wrap($ImageList, 'ul', array('id' => 'RandomImageList'));
		}
	}
	
	private function _AddResources($Sender) {
		$Sender->AddCSSFile($this->GetResource('design/randomimages.css', FALSE, FALSE));
	}
}