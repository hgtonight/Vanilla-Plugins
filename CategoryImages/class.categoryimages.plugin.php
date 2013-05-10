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
$PluginInfo['CategoryImages'] = array(
	'Name' => 'Category Images',
	'Description' => 'Lets you assign each category an icon image.',
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => FALSE,
	'MobileFriendly' => TRUE,
	'HasLocale' => TRUE,
	'Version' => '0.1',
	'Author' => 'Zachary Doll',
	'AuthorEmail' => 'Zachary.Doll@gmail.com',
	'AuthorUrl' => 'http://www.daklutz.com/',
);

class CategoryImagesPlugin extends Gdn_Plugin {

	public function __construct()
	{
		parent::__construct();
	}

	public function DiscussionsController_BeforeDiscussionContent_Handler($Sender) {
		$this->_RenderCategoryIcon($Sender);
	}
	
	public function CategoriesController_BeforeDiscussionContent_Handler($Sender) {
		$this->_RenderCategoryIcon($Sender);
	}
	
	/*public function CategoriesController_BeforeCategoryItem_Handler($Sender) {
		$this->_RenderCategoryIcon($Sender);
	}*/

	public function DiscussionsController_Render_Before($Sender) {
		$this->_AddResources($Sender);
	}
	
	public function CategoriesController_Render_Before($Sender) {
		$this->_AddResources($Sender);
	}
	
	public function Setup() {
		parent::Setup();
	}
	
	private function _RenderCategoryIcon($Sender) {
		$Discussion = $Sender->EventArguments['Discussion'];
		$CatID = $Discussion->CategoryID;
		echo Img('/plugins/CategoryImages/design/images/'.$CatID.'.png', array('class' => 'CategoryImage Category-'.$CatID));
	}
	
	private function _AddResources($Sender) {
		$Sender->AddCSSFile($this->GetResource('design/categoryimages.css', FALSE, FALSE));
	}
}