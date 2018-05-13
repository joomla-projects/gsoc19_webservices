<?php
/**
 * @package     Joomla.API
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Api\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\Api;

/**
 * The article controller
 *
 * @since  __DEPLOY_VERSION__
 */

class ArticleController extends Api
{
	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable  If true, the view output will be cached
	 * @param   array    $urlparams An array of safe url parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  static  A \JControllerLegacy object to support chaining.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function displayList($cachable = false, $urlparams = array())
	{
		$vName = $this->input->getCmd('view', 'articles');
		$this->input->set('view', $vName);

		return parent::displayList($cachable, $urlparams);
	}

	public function displayItem($cachable = false, $urlparams = array())
	{
		$vName = $this->input->getCmd('view', 'article');
		$this->input->set('view', $vName);

		return parent::displayItem($cachable, $urlparams);
	}
}
