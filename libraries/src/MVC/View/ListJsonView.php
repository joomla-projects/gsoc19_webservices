<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\MVC\View;

defined('_JEXEC') or die;

use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\SerializerInterface;

/**
 * Base class for a Joomla Json List View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class ListJsonView extends JsonView
{
	/**
	 * The items object
	 *
	 * @var  SerializerInterface
	 */
	protected $serializer;

	/**
	 * The items object
	 *
	 * @var  array
	 */
	protected $items;

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

		$this->items = $model->getItems();
		$this->state = $model->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		if ($this->serializer === null)
		{
			throw new \RuntimeException('Serializer missing');
		}

		$element = new Collection($this->items, $this->serializer);

		$this->document->setData($element);
		$this->document->addLink('self', \JUri::current());
		$this->document->render();
	}
}
