<?php if (!defined('APPLICATION')) exit();
/*	Copyright 2012 Zachary Doll
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
class LatestPostListModule extends Gdn_Module {
	protected $_LatestPosts;
	protected $_Link = 'discussions';

	public function __construct(&$Sender = '') {
		parent::__construct($Sender);
	}

	// This actually sets the data since it doesn't return anything
	public function SetData($Limit = 10) {
		$DiscussionModel = new DiscussionModel();
		$this->_LatestPosts = $DiscussionModel->Get(0, $Limit);
	}
	public function SetLink($Link) {
		$this->_Link = $Link;
	}

	// Put it in the panel (sidebar)
	public function AssetTarget() {
		return 'Panel';
	}

	// Actual output string
	public function ToString() {
		$Posts = $this->_LatestPosts->Result();
		$String = '';
		if(!empty($Posts)) {
			ob_start();
			?>
			<div id="LatestPostList" class="Box">
				<h4><?php echo '<a href="'.Url('/', TRUE).$this->_Link.'">'.T("Latest Posts").'</a>'; ?></h4>
				<ul class="PanelInfo">
				<?php
				if ($this->_LatestPosts->NumRows() > 0) {
					foreach($Posts as $Post) {
					?>
					<li<?php if ($Post->CountUnreadComments > 0) { echo ' class="New"';}?>>
						<?php echo Anchor(Gdn_Format::Text($Post->Name), 'discussion/'.$Post->DiscussionID.'/'.Gdn_Format::Url($Post->Name), 'PostTitle' ); ?>
						<div class="Condensed">
							<?php 
							// If there is a comment, let's use that, otherwise use the original poster
							if ($Post->LastName) {
								echo Anchor(Gdn_Format::Text($Post->LastName), 'profile/'.$Post->UpdateUserID.'/'.Gdn_Format::Url($Post->LastName), 'PostAuthor' );
							}
							else {
								echo Anchor(Gdn_Format::Text($Post->FirstName), 'profile/'.$Post->InsertUserID.'/'.Gdn_Format::Url($Post->FirstName), 'PostAuthor' );
							}
							?>
							<span class="PostDate">on <?php echo Gdn_Format::Date($Post->DateLastComment); ?></span>
						</div>
					</li>
					<?php
					}
				}
				?>
				</ul>
			</div>
			<?php
			$String = ob_get_contents();
			@ob_end_clean();
			return $String;
		}
		return $String;
	}
}