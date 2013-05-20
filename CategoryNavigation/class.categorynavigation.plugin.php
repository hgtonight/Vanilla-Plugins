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
$PluginInfo['CategoryNavigation'] = array(
	'Name' => 'Category Navigation',
	'Description' => 'A Vanilla Forums plugin that provides a navbar of the categories. Highlights current category and all ancestors. A special thanks to digihub for the idea.',
	'Version' => '1.0',
	'RequiredApplications' => array('Vanilla' => '2.0.10'),
	'Author' => "Zachary Doll",
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
);

class CategoryNavigation extends Gdn_Plugin {
	private $_Module;
	// runs once every page load
	public function __construct() {
		parent::__construct();
	}
	
	// Put the navigation on every forum page
	public function Base_Render_Before($Sender) {//CategoriesController_BeforeGetDiscussions_Handler($Sender) {
		$this->_Module = new CategoryNavigationModule($Sender);
		$Sender->AddModule($this->_Module);
		
		// add the JS/Css
		$Sender->AddJsFile($this->GetResource('js/categorynavigation.js', FALSE, FALSE));
		$Sender->AddCSSFile($this->GetResource('design/categorynavigation.css', FALSE, FALSE));
	}
	   
	// fired on install (once)
	public function Setup() {
		// Set up the plugin's default values
	}

	// fired on disable (removal)
	public function OnDisable() {
	}
   
}
