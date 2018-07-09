<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Entity;

use Joomla\Database\DatabaseDriver;

defined('JPATH_PLATFORM') or die;

/**
 * Trait to apply to the Joomla Entity system which allows it to implement \Joomla\CMS\Table\TableInterface
 *
 * @since  __DEPLOY_VERSION__
 */
trait EntityTableTrait
{
	/**
	 * Wrapper for getPrimaryKey
	 *
	 * @return mixed
	 */
	public function getKeyName()
	{
		return $this->getPrimaryKey();
	}
}
