<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\MVC\View;

use Joomla\CMS\Router\Exception\RouteNotFoundException;
use Tobscure\JsonApi\Resource;
use Tobscure\JsonApi\SerializerInterface;

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
	 * The items object
	 *
	 * @var  SerializerInterface
	 */
	protected $serializer;

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

		if ($this->item->id === null)
		{
			throw new RouteNotFoundException('Item does not exist');
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		if ($this->serializer === null)
		{
			throw new \RuntimeException('Serializer missing');
		}

		$element = new Resource($this->item, $this->serializer);

		$this->document->setData($element);
		$this->document->addLink('self', \JUri::current());
		$this->document->render();
	}
}
