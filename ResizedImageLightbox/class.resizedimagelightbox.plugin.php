<?php if(!defined('APPLICATION')) exit();
/* 	Copyright 2013 Zachary Doll
 * 	This program is free software: you can redistribute it and/or modify
 * 	it under the terms of the GNU General Public License as published by
 * 	the Free Software Foundation, either version 3 of the License, or
 * 	(at your option) any later version.
 *
 * 	This program is distributed in the hope that it will be useful,
 * 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 	GNU General Public License for more details.
 *
 * 	You should have received a copy of the GNU General Public License
 * 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
$PluginInfo['ResizedImageLightbox'] = array(
    'Name' => 'Resized Image Lightbox',
    'Description' => 'A plugin in that provides a lightbox for images resized by Vanilla\'s core global.js.',
    'Version' => '1.0',
    'RequiredApplications' => array('Vanilla' => '2.1'),
    'MobileFriendly' => FALSE,
    'SettingsUrl' => '/dashboard/settings/resizedimagelightbox',
    'Author' => "Zachary Doll",
    'AuthorEmail' => 'hgtonight@daklutz.com',
    'AuthorUrl' => 'http://www.daklutz.com',
    'License' => 'GPLv3'
);

class ResizedImageLightbox extends Gdn_Plugin {

  public function SettingsController_ResizedImageLightbox_Create($Sender) {
    $Sender->AddSideMenu('/dashboard/settings/resizedimagelightbox');
    $Sender->Title('Resized Image Lightbox Settings');
    $Sender->Render($this->GetView('settings.php'));
  }

  public function Base_Render_Before($Sender) {
    $this->_AddResources($Sender);
  }

  private function _AddResources($Sender) {
    $Sender->AddJsFile($this->GetResource('js/resizedimagelightbox.js', FALSE, FALSE));
    $Sender->AddCssFile($this->GetResource('design/resizedimagelightbox.css', FALSE, FALSE));
  }

  public function Setup() {
    return TRUE;
  }

}
