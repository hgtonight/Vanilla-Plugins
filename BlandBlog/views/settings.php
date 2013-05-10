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
 */?>
<h1> <?php echo $this->Data("Title");?> </h1>
<?php
	echo $this->Form->Open();
	echo $this->Form->Errors();
?>
<ul>
	<li>
		<h3><?php echo T("Blog Appearance"); ?></h3>
		<ul class='CheckBoxList'>
			<li>
				<?php
					echo $this->Form->Input("Plugins.BlandBlog.PostsPerPage", "input", array(
						"size" => "3",
						"maxlength" => "3",
						"style" => "text-align: center;"
					)); 
					echo $this->Form->Label("Blog posts per page ( Default: 10 )", "Plugins.BlandBlog.PostsPerPage", array(
						"class" => "CheckBoxLabel",
						"style" => "margin-left: 10px;", 
					)); 
				?>
			</li>
		</ul>
	</li>
	<li>
		<h3><?php echo T("Blog Categories"); ?></h3>
		<?php 
			echo $this->Form->CheckBoxList("Plugins.BlandBlog.CategoryIDs", $this->CategoryData, NULL, array(
				"TextField" => "Name", 
				"ValueField" => "CategoryID",
			));
		?>
	</li>
	
</ul>
<br>

<?php 
   echo $this->Form->Close("Save");

