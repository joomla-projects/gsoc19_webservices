<?php
/**
 * @package     Joomla.API
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Api\View\Article;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\ItemJsonView;
use Joomla\Component\Content\Api\Serializer\ItemSerializer;

/**
 * Override class for a Joomla Json Item View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  __DEPLOY_VERSION__
 */
class JsonapiView extends ItemJsonView
{
	public function __construct($config = array())
	{
		$this->serializer = new ItemSerializer;

		parent::__construct($config);
	}
}
