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
$PluginInfo['blanderblog'] = array(
	'Name' => 'Blander Blog',
	'Description' => 'A blog plugin for vanilla 2.1. Inspired by the excellent NillaBlog plugin by Dan Dumont (ddumont@gmail.com).',
	'Version' => '0.1',
	'Author' => 'Zachary Doll',
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'SettingsUrl' => '/dashboard/settings/blanderblog',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'AuthorUrl' => 'http://www.daklutz.com',
	'RequiredApplications' => array('Vanilla' => '2.1b1')
);

/**
 * BlanderBlog plugin for Vanilla
 * @author hgtonight@daklutz.com
 */
class BlanderBlog extends Gdn_Plugin {
	/**
	 * Build the setting page.
	 * @param $Sender
	 */
	public function SettingsController_BlanderBlog_Create($Sender) {
		$Sender->Permission('Garden.Settings.Manage');

		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array('Plugins.BlanderBlog.CategoryIDs'));
		$ConfigurationModel->SetField('Plugins.BlanderBlog.DisableCSS');
		$ConfigurationModel->SetField('Plugins.BlanderBlog.HideBlogCategory');
		$ConfigurationModel->SetField('Plugins.BlanderBlog.PostsPerPage');
		$Sender->Form->SetModel($ConfigurationModel);

		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$Sender->Form->SetData($ConfigurationModel->Data);
		} else {
        	$Data = $Sender->Form->FormValues();
			$ConfigurationModel->Validation->ApplyRule('Plugins.BlanderBlog.PostsPerPage', 'Integer');
        	if ($Sender->Form->Save() !== FALSE)
        		$Sender->InformMessage('<span class="InformSprite Sliders"></span>'.T("Your changes have been saved."),'HasSprite');
				//$Sender->StatusMessage = T('Your settings have been saved.');
		}

		$Sender->AddSideMenu();
		$Sender->SetData('Title', T('Blander Blog Settings'));

		$CategoryModel = new CategoryModel();
		$Sender->SetData('CategoryData', $CategoryModel->GetAll(), TRUE);
		array_shift($Sender->CategoryData->Result());

		$Sender->Render($this->GetView('settings.php'));
	}

	/**
	 * Adjusts the number of posts to display in the blog category.
	 * @param $Sender
	 */
	public function CategoriesController_BeforeGetDiscussions_Handler($Sender) {
		if (in_array($Sender->CategoryID, C('Plugins.BlanderBlog.CategoryIDs'))) {
			$Sender->EventArguments['PerPage'] = C('Plugins.BlanderBlog.PostsPerPage');
		}
	}

	/**
	 * Adds the class 'Blog' to every discussion in the blog category list.
	 * Allows for themes to style the blog independently of the plugin.
	 * @param $Sender
	 */
	public function CategoriesController_BeforeBlogName_Handler($Sender) {
		if (in_array($Sender->CategoryID, C('Plugins.BlanderBlog.CategoryIDs'))) {
			$Sender->EventArguments['CssClass'] .= ' Blog Blog'.$Sender->CategoryID.' ';
		}
	}

	/**
	 * Adds the class 'Blog' to every comment (including the first post) in the blog category list.
	 * Allows for themes to style the blog independently of the plugin.
	 * @param $Sender
	 */
	public function DiscussionController_BeforeCommentDisplay_Handler($Sender) {
		if (in_array($Sender->CategoryID, C('Plugins.BlanderBlog.CategoryIDs'))) {
			$Sender->EventArguments['CssClass'] .= ' Blog Blog'.$Sender->CategoryID.' ';
		}
	}
	
	/**
	 * Sort blog posts by creation time rather than last comment.
	 * @param $Sender
	 */
	public function DiscussionModel_BeforeGet_Handler($Sender) {
		$Wheres = $Sender->EventArguments['Wheres'];
		if (is_array($Wheres) && array_key_exists('d.CategoryID', $Wheres) && in_array($Wheres['d.CategoryID'][0], C('Plugins.BlanderBlog.CategoryIDs'))) {
			$Sender->EventArguments['SortField'] = 'd.DateInserted';
			$Sender->EventArguments['SortDirection'] = 'desc';
		}
	}
	
	/**
	 * Override the default discussion view with a customizable single post blog view
	 * @param $Sender
	 */
	public function DiscussionController_Render_Before($Sender) {
		if (in_array($Sender->CategoryID, C('Plugins.BlanderBlog.CategoryIDs'))) {
			if(!C('Plugins.BlanderBlog.DisableCSS')) {
				$Sender->AddCssFile($this->GetResource('design/custom.css', FALSE, FALSE));
				$Sender->AddJsFile($this->GetResource('js/blanderblog.js', FALSE, FALSE));
			}
			$Sender->View = $Sender->FetchViewLocation('blog', 'Discussion', 'vanilla', FALSE);
			if(!$Sender->View) {
				$Sender->View = $this->GetView('discussion/blog.php');
			}
		}
	}
	
	/**
	 * Override the default category view with a customizable blog view
	 * @param $Sender
	 */
	public function CategoriesController_Render_Before($Sender) {
		if (in_array($Sender->CategoryID, C('Plugins.BlanderBlog.CategoryIDs'))) {
			if(!C('Plugins.BlanderBlog.DisableCSS')) {
				$Sender->AddCssFile($this->GetResource('design/custom.css', FALSE, FALSE));
				$Sender->AddJsFile($this->GetResource('js/blanderblog.js', FALSE, FALSE));
			}
			$Sender->View = $Sender->FetchViewLocation('blog', 'Categories', 'vanilla', FALSE);
			if(!$Sender->View) {
				$Sender->View = $this->GetView('categories/blog.php');
			}
			
			// remove the modules that are discussion related
			unset($Sender->Assets['Panel']['NewDiscussionModule']);
			unset($Sender->Assets['Panel']['DiscussionFilterModule']);
			unset($Sender->Assets['Panel']['CategoriesModule']);
		}
		
		if (C('Plugins.BlanderBlog.HideBlogCategory')) {
			foreach (C('Plugins.BlanderBlog.CategoryIDs') as $CategoryID) {
				unset($Sender->Data['Categories'][$CategoryID]);
			}
		}	
	}
	
	/**
	 * Remove the blog categories from the categories module if needed
	 * @param $Sender
	 */
	public function CategoriesModule_Render_Before($Sender) {
		if (C('Plugins.BlanderBlog.HideBlogCategory')) {
			foreach (C('Plugins.BlanderBlog.CategoryIDs') as $CategoryID) {
				unset($Sender->Data['Categories'][$CategoryID]);
			}
		}
	}

	
	/**
	 * Add a link to the side menu in the dashboard
	 * @param $Sender
	 */
	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu = &$Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Add-ons', 'Blander Blog', 'settings/blanderblog', 'Garden.AdminUser.Only');
	}
	
	public function Setup() {
		SaveToConfig('Plugins.BlanderBlog.CategoryIDs', 1);
		SaveToConfig('Plugins.BlanderBlog.PostsPerPage', 5);
		SaveToConfig('Plugins.BlanderBlog.HideBlogCategory', TRUE);
		SaveToConfig('Plugins.BlanderBlog.DisableCSS', FALSE);
	}

	public function OnDisable() {
		RemoveFromConfig('Plugins.BlanderBlog.CategoryIDs');
		RemoveFromConfig('Plugins.BlanderBlog.PostsPerPage');
		RemoveFromConfig('Plugins.BlanderBlog.HideBlogCategory');
		RemoveFromConfig('Plugins.BlanderBlog.DisableCSS');
	}
}