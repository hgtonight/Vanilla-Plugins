<?php if (!defined('APPLICATION')) exit();
class LatestPostListModule extends Gdn_Module {
	protected $_LatestPosts;

	public function __construct(&$Sender = '') {
		parent::__construct($Sender);
	}

	// This actually sets the data since it doesn't return anything
	public function GetData($Limit = 10) {
		$SQL = Gdn::SQL();
				
		// Join the user table twice, once for first post and then for latest comments
		$SQL->Select('d.InsertUserID, d.DiscussionID, d.Name PostName, d.DateLastComment, d.LastCommentUserID, u.UserID CommenterID, u.Name CommenterName, p.UserID PosterID, p.Name PosterName')
			->From('discussion d')
			->Join('user u', 'u.UserID = d.LastCommentUserID', 'left')
			->Join('user p', 'p.UserID = d.InsertUserID', 'left')
			->OrderBy('d.DateLastComment', 'desc')
			->Limit($Limit, 0);

		$this->_LatestPosts = $SQL->Get();
	}

	// Put it in the panel (sidebar)
	public function AssetTarget() {
		return 'Panel';
	}

	// Actual output string
	public function ToString() {
		$String = '';
		ob_start();
		?>
			<div id="LatestPostList" class="Box">
				<h4><?php echo T("Latest Posts"); ?></h4>
				<ul class="PanelInfo">
				<?php
				if ($this->_LatestPosts->NumRows() > 0) {
					foreach($this->_LatestPosts->Result() as $Post) {
				?>
					<li>
		 				<?php echo Anchor(Gdn_Format::Text($Post->PostName), 'discussion/'.$Post->DiscussionID.'/'.Gdn_Format::Url($Post->PostName), 'PostTitle' ); ?>
						<?php 
						// If there is a comment, let's use that, otherwise use the original poster
						if ($Post->CommenterName) {
							echo Anchor(Gdn_Format::Text($Post->CommenterName), 'profile/'.$Post->LastCommentUserID.'/'.Gdn_Format::Url($Post->CommenterName), 'PostAuthor' );
						}
						else {
							echo Anchor(Gdn_Format::Text($Post->PosterName), 'profile/'.$Post->InsertUserID.'/'.Gdn_Format::Url($Post->PosterName), 'PostAuthor' );
						}
							?> <span class="PostDate">on 
		 				<?php echo Gdn_Format::Date($Post->DateLastComment); ?>
						</span>
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
}