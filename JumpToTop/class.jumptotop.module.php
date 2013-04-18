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
class JumpToTopModule extends Gdn_Module {
	
	public function __construct($Sender = '') {
		parent::__construct($Sender);
	}

	// Required for modules. Tells the controller where to render the module.
	public function AssetTarget() {
		return 'Panel';
	}

	// Required for module to render something
	public function ToString() {
		$String = '';
		ob_start();
		echo Wrap(Anchor(Img('/plugins/JumpToTop/design/jump.png', array('alt' => T('Jump to top of page'))),
				'#top',
				array('title' => T('JumpToTop'))),
			'div',
			array('id' => 'JumpToTop'));
		$String = ob_get_contents();
		@ob_end_clean();
		return $String;
	}
}