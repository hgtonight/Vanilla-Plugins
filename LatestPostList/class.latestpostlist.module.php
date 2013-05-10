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
class LatestPostListModule extends Gdn_Module {
	protected $_LatestPosts;
	protected $_Link = 'discussions';

	public function __construct($Sender = '') {
		parent::__construct($Sender);
	}

	// Using the discussion model makes my life easy
	public function SetData($Limit = 5, $Link = 'discussions') {
		$DiscussionModel = new DiscussionModel();
		$this->_LatestPosts = $DiscussionModel->Get(0, $Limit, 'all');
		$this->_Link = $Link;
	}

	// Required for modules. Tells the controller where to render the module.
	public function AssetTarget() {
		return 'Panel';
	}

	// Convenience function used to mark the lists date
	public function GetDate() {
		if($this->_LatestPosts->NumRows() < 1) {
			return 0;
		}
		$Posts = $this->_LatestPosts->Result();
		return $Posts[0]->DateLastComment;
	}

	// Returns an html string ready for rendering
	public function PostList() {
		$Posts = '';
		if($this->_LatestPosts->NumRows() >= 1) {
			foreach($this->_LatestPosts->Result() as $Post) {
				$PostTitle = Anchor(Gdn_Format::Text($Post->Name), 'discussion/'.$Post->DiscussionID.'/'.Gdn_Format::Url($Post->Name), 'PostTitle'); 
				
				// If there is a comment, let's use that, otherwise use the original poster
				if ($Post->LastName) {
					$LastPoster = Anchor(Gdn_Format::Text($Post->LastName), 'profile/'.$Post->UpdateUserID.'/'.Gdn_Format::Url($Post->LastName), 'PostAuthor' );
				}
				else {
					$LastPoster = Anchor(Gdn_Format::Text($Post->FirstName), 'profile/'.$Post->InsertUserID.'/'.Gdn_Format::Url($Post->FirstName), 'PostAuthor' );
				}
				
				$PostData = Wrap(T('on ').Gdn_Format::Date($Post->DateLastComment), 'span', array('class' => 'PostDate'));
				$Posts .= Wrap($PostTitle.Wrap($LastPoster.' '.$PostData, 'div', array( 'class' => 'Condensed') ), 'li', array( 'class' => ($Post->CountUnreadComments > 0) ? 'New' : '') );
			}
		}
		return $Posts;
	}
	
	// Required for module to render something
	public function ToString() {
		$String = '';
		if($this->_LatestPosts->NumRows() >= 1) {
			ob_start();
			?>
			<div id="LatestPostList" class="Box"><?php
				echo Wrap(Anchor(T('Latest Posts'), $this->_Link), 'h4');
				?><ul id="LPLUl" class="PanelInfo">
					<?php echo $this->PostList();
				?></ul>
			</div>
			<?php
			$String = ob_get_contents();
			@ob_end_clean();
			return $String;
		}
		return $String;
	}
}