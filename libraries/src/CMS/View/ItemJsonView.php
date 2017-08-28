<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\View;

use Joomla\Utilities\ArrayHelper;
use Joomla\Component\Content\Api\Serializer\ItemSerializer;
use Tobscure\JsonApi\Resource;
use Tobscure\JsonApi\AbstractSerializer;

defined('_JEXEC') or die;

/**
 * Base class for a Joomla Json Item View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  __DEPLOY_VERSION__
 */
class ItemJsonView extends JsonView
{
	/**
	 * The item object
	 *
	 * @var  \stdClass
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$this->item = $model->getItem();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		$element = new Resource($this->item, new ItemSerializer);

		$this->document->setData($element);
		$this->document->addLink('self', \JUri::current());
		$this->document->render();
	}
}
