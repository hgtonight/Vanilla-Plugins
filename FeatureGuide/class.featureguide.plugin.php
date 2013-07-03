<?php if (!defined("APPLICATION")) exit();
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
$PluginInfo['FeatureGuide'] = array(
	'Name' => 'Feature Guide',
	'Description' => 'A Vanilla Forums plugin that provides an on page guide to the features of your fancy forum software. Depends on jquery.pageguide by sprint.ly (https://github.com/sprintly/jquery.pageguide).',
	'Version' => '1.0',
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
	'Author' => "Zachary Doll",
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
);

class FeatureGuide extends Gdn_Plugin {

	private function AddResources($Sender) {
		$Sender->AddJsFile($this->GetResource('lib/jquery.pageguide.js', FALSE, FALSE));
		$Sender->AddJsFile($this->GetResource('js/featureguide.js', FALSE, FALSE));
		$Sender->AddCSSFile($this->GetResource('design/featureguide.css', FALSE, FALSE));
	}
	
	public function Base_Render_Before($Sender) {
		if($Sender->MasterView != 'admin') {
			//echo '<pre>'; var_dump($Sender); echo '</pre>';
			$this->AddResources($Sender);
		}
	}
	
	// fired on install (once)
	public function Setup() {
		return TRUE;
	}

	// fired on disable (removal)
	public function OnDisable() {
		return TRUE;
	}
}