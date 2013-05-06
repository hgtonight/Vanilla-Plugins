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
// Define the plugin:
$PluginInfo['PanelBoxToggles'] = array(
	'Name' => 'Panel Box Toggles',
	'Description' => 'Turns the all boxes in the panel into a toggleable box.',
	'Version' => '1.0',
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
	'Author' => "Zachary Doll",
	'AuthorEmail' => 'hgtonight@gmail.com',
	'AuthorUrl' => 'http://github.com/hgtonight/Panel-Box-Toggles',
	'License' => 'GPLv3'
);

class PanelBoxTogglesPlugin extends Gdn_Plugin {
	public function Base_Render_Before($Sender) {
		if(GetValue('Panel',$Sender->Assets) && $Sender->MasterView != 'admin') {
			$Sender->AddJsFile($this->GetResource('js/panelboxtoggles.js', FALSE, FALSE));
			$Sender->AddCSSFile($this->GetResource('design/panelboxtoggles.css', FALSE, FALSE));
		}
	}
}
