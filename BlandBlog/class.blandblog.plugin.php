<?php if (!defined("APPLICATION")) exit();
/*
 *  BlandBlog vanilla plugin.
 *  Copyright (C) 2012 hgtonight@gmail.com
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

$PluginInfo["BlandBlog"] = array(
	"Name" => "BlandBlog",
	"Description" => "A blander blog plugin for Vanilla 2+. Based on <a href=\"http://blog.canofsleep.com\">Dan Dumont's</a> absolutely excellent NillaBlog plugin.",
	"Version" => "1.0",
	"Author" => "Zachary Doll",
	"AuthorEmail" => "hgtonight@gmail.com",
	"SettingsUrl" => "/dashboard/settings/blandblog",
	"SettingsPermission" => "Garden.Settings.Manage",
	"AuthorUrl" => "http://www.zacharydoll.com",
	"RequiredApplications" => array("Vanilla" => "2.0.18"),
	'License' => 'GPLv3'
);

/**
 * BlandBlog plugin for Vanilla
 * @author hgtonight@gmail.com
 */
class BlandBlog extends Gdn_Plugin {

	/**
	 * Build the setting page.
	 * @param $Sender
	 */
	public function SettingsController_BlandBlog_Create($Sender) {
		$Sender->Permission('Garden.Settings.Manage');

		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array("Plugins.BlandBlog.CategoryIDs"));
		$ConfigurationModel->SetField("Plugins.BlandBlog.PostsPerPage");
		$Sender->Form->SetModel($ConfigurationModel);

		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$Sender->Form->SetData($ConfigurationModel->Data);
		} else {
        	$Data = $Sender->Form->FormValues();
//        	$ConfigurationModel->Validation->ApplyRule("Plugins.BlandBlog.CategoryIDs", "RequiredArray");  // Not required
			$ConfigurationModel->Validation->ApplyRule("Plugins.BlandBlog.PostsPerPage", "Integer");
        	if ($Sender->Form->Save() !== FALSE)
        		$Sender->StatusMessage = T("Your settings have been saved.");
		}

		$Sender->Title('BlandBlog Settings');
		$Sender->AddSideMenu('/dashboard/settings/nillablog');

		$CategoryModel = new CategoryModel();
		$Sender->SetData("CategoryData", $CategoryModel->GetAll(), TRUE);
		array_shift($Sender->CategoryData->Result());

		$Sender->Render($this->GetView("settings.php"));
	}

	/**
	 * Adjusts the number of posts to display in the blog category.
	 * @param $Sender
	 */
	public function CategoriesController_BeforeGetDiscussions_Handler($Sender) {
		if ( !in_array($Sender->CategoryID, C("Plugins.BlandBlog.CategoryIDs")) )
			return;
		$Sender->EventArguments['PerPage'] = C("Plugins.BlandBlog.PostsPerPage");
	}

	/**
	 * Insert the first comment under the discussion title for the blog category.
	 * This turns the blog category into a list of blog posts.
	 * @param $Sender
	 */
	public function CategoriesController_AfterDiscussionTitle_Handler($Sender) {
		if ( !in_array($Sender->CategoryID, C("Plugins.BlandBlog.CategoryIDs")) )
			return;

		$Discussion = $Sender->EventArguments['Discussion'];

		$Body = $Discussion->Body;
		$end = strrpos($Body, "<hr");
		if ($end)
			$Body = substr($Body, 0, $end);
		$Discussion->FormatBody = Gdn_Format::To($Body, $Discussion->Format);
		?>
			<ul class="MessageList">
				<li>
					<div class="Message">
						<?php echo $Discussion->FormatBody; ?>
					</div>
				</li>
				<?php if ($end) { ?>
					<li>
						<a href="<?php echo Gdn::Request()->Url(ConcatSep("/", "discussion", $Discussion->DiscussionID, Gdn_Format::Url($Discussion->Name)))?>"
						   class="More"><?php echo T("Read more");?></a>
					</li>
				<?php } ?>
			</ul>
		<?php
	}

	/**
	 * Adds the blog subscription link to each post for easier access.
	 * @param $Sender
	 */
	public function CategoriesController_DiscussionMeta_Handler($Sender) {
		if ( !in_array($Sender->CategoryID, C("Plugins.BlandBlog.CategoryIDs")) )
			return;

		$Discussion = $Sender->EventArguments['Discussion'];
		?>
			<span class='RSS'>
				<a href='<?php echo Gdn::Request()->Url(ConcatSep("/", $Sender->SelfUrl, "feed.rss")); ?>'>
					<img src="<?php echo Asset("/applications/dashboard/design/images/rss.gif"); ?>"></img>
					<?php echo T("Subscribe to this blog"); ?>
				</a>
			</span>
		<?php

	}

	/**
	 * Adds the class 'BlandBlog' to every discussion in the blog category list.
	 * Allows for themes to style the blog independently of the plugin.
	 * @param $Sender
	 */
	public function CategoriesController_BeforeDiscussionName_Handler($Sender) {
		if ( !in_array($Sender->CategoryID, C("Plugins.BlandBlog.CategoryIDs")) )
			return;
		$Sender->EventArguments["CssClass"] .= " BlandBlog BlandBlog".$Sender->CategoryID." ";
	}

	/**
	 * Adds the class 'BlandBlog' to every comment (including the first post) in the blog category list.
	 * Allows for themes to style the blog independently of the plugin.
	 * @param $Sender
	 */
	public function DiscussionController_BeforeCommentDisplay_Handler($Sender) {
		if ( !in_array($Sender->CategoryID, C("Plugins.BlandBlog.CategoryIDs")) )
			return;
		$Sender->EventArguments["CssClass"] .= " BlandBlog BlandBlog".$Sender->CategoryID." ";
	}

	/**
	 * Sorts blog posts by creation time rather than last comment.
	 * @param $Sender
	 */
	public function DiscussionModel_BeforeGet_Handler($Sender) {
		$Wheres = $Sender->EventArguments["Wheres"];
		if (!array_key_exists("d.CategoryID", $Wheres) || !in_array($Wheres["d.CategoryID"], C("Plugins.BlandBlog.CategoryIDs")))
			return;

		$Sender->EventArguments["SortField"] = "d.DateInserted";
		$Sender->EventArguments["SortDirection"] = "desc";
	}

	/**
	 * Insert default JS into the discussion list for the blog.
	 * @param $Sender
	 */
	public function CategoriesController_Render_Before($Sender) {
		if ( !in_array($Sender->CategoryID, C("Plugins.BlandBlog.CategoryIDs")) )
			return;

		$Sender->AddJsFile($this->GetResource('nillablog.js', FALSE, FALSE));
	}

	/**
	 * Insert default JS into the comment list for the blog discussion.
	 * @param $Sender
	 */
	public function DiscussionController_Render_Before($Sender) {
		if ( !in_array($Sender->CategoryID, C("Plugins.BlandBlog.CategoryIDs")) )
			return;

		$Sender->AddJsFile($this->GetResource('nillablog.js', FALSE, FALSE));
	}

	/**
	 * Insert a clickable comments link appropriate for the blog.  We'll hide the other comment count with CSS.
	 * @param $Sender
	 */
	public function CategoriesController_BeforeDiscussionMeta_Handler($Sender) {
		if ( !in_array($Sender->CategoryID, C("Plugins.BlandBlog.CategoryIDs")) )
			return;

		$Discussion = $Sender->EventArguments['Discussion'];
		$Count = $Discussion->CountComments - 1;
		$Label = sprintf(Plural($Count, '%s comment', '%s comments'), $Count);
		?>
			<span class="CommentCount BlandBlog BlandBlog<?php echo $Sender->CategoryID;?>>">
				<a href="<?php
					echo Gdn::Request()->Url(ConcatSep("/", "discussion", $Discussion->DiscussionID, Gdn_Format::Url($Discussion->Name).($Count > 0 ? "#Item_2" : "")));
				?>">
					<?php echo $Label; ?>
				</a>
			</span>
		<?php
	}

	public function Setup() {}
}