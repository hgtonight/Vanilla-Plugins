<?php if (!defined('APPLICATION')) exit();
// blog category view
$Session = Gdn::Session();
if (!function_exists('BookmarkButton')) {
	include($this->FetchViewLocation('helper_functions', 'discussions', 'vanilla'));
}

function FormatBody($Object) {
   Gdn::Controller()->FireEvent('BeforeBlogBody'); 
   $Object->FormatBody = Gdn_Format::To($Object->Body, $Object->Format);
   Gdn::Controller()->FireEvent('AfterBlogFormat');
   
   return $Object->FormatBody;
}

$Alt = ' Alt';
foreach ($this->DiscussionData->Result() as $Discussion) {
	$Alt = $Alt == ' Alt' ? '' : ' Alt';
	$CssClass = CssClass($Discussion);
	$DiscussionUrl = $Discussion->Url;

	if ($Session->UserID) {
		$DiscussionUrl .= '#latest';
	}
	$this->EventArguments['DiscussionUrl'] = &$DiscussionUrl;
	$this->EventArguments['Discussion'] = &$Discussion;
	$this->EventArguments['CssClass'] = &$CssClass;

	$First = UserBuilder($Discussion, 'First');
	$this->EventArguments['FirstUser'] = &$First;

	$this->FireEvent('BeforeBlogName');

	$DiscussionName = $Discussion->Name;
	if ($DiscussionName == '') {
		$DiscussionName = T('Blank Blog Post');
	}
	$this->EventArguments['BlogName'] = &$DiscussionName;

	static $FirstDiscussion = TRUE;
	if (!$FirstDiscussion) {
		$this->FireEvent('BetweenBlog');
	}
	else {
		$FirstDiscussion = FALSE;
	}

	$Discussion->CountPages = ceil($Discussion->CountComments / $this->CountCommentsPerPage);
	?>
	<div id="Blog_<?php echo $Discussion->DiscussionID; ?>" class="<?php echo $CssClass; ?>">
		<?php
		if (!property_exists($this, 'CanEditDiscussions')) {
			$this->CanEditDiscussions = GetValue('PermsDiscussionsEdit', CategoryModel::Categories($Discussion->CategoryID)) && C('Vanilla.AdminCheckboxes.Use');
		}
		?>
		<span class="Options">
		<?php
			echo OptionsList($Discussion);
			echo BookmarkButton($Discussion);
		?>
		</span>
		<h1><?php
			echo AdminCheck($Discussion, array('', ' ')).Anchor($DiscussionName, $DiscussionUrl);
			$this->FireEvent('AfterBlogTitle'); 
		?></h1>
		<div>
			<div class="posted-on">
				<span>Posted on </span>
				<a href="single page link" rel="bookmark">
					<time class="entry-date" datetime="2012-05-30T10:04:26+00:00" pubdate=""><?php echo Gdn_Format::Date($Discussion->FirstDate, 'html');?></time>
				</a>
				<span class="author">
					<span class="sep"> by </span>
					<span class="author">
						<?php echo UserAnchor($First); ?>
					</span>
				</span>
			</div>
		</div>
		<div class="content">
			<?php
			echo FormatBody($Discussion);
			$this->FireEvent('AfterBlogContent');
			?>
		</div>
		<div class="blog-meta">
			<span class="comments-link">
				<?php
				echo '<a href="'.$DiscussionUrl.'">';
				printf(PluralTranslate($Discussion->CountComments, '%s comment html', '%s comments html', '%s comment', '%s comments'),	BigPlural($Discussion->CountComments, '%s comment'));
				echo '</a>';
				echo NewComments($Discussion);
				?>
			</span>
		</div>
	</div>
	<?php
}

echo '<div class="PageControls Bottom">';
PagerModule::Write();
//$PagerOptionsecho Gdn_Theme::Module('NewDiscussionModule', $this->Data('_NewDiscussionProperties', array('CssClass' => 'Button Action Primary')));
echo '</div>';