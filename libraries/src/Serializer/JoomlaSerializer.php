<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Serializer;

defined('_JEXEC') or die;

use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\Relationship;

/**
 * Temporary serializer
 *
 * @since  4.0.0
 */
class JoomlaSerializer extends AbstractSerializer
{
	/**
	 * Checks for an associations property to add into the JSON API Response
	 *
	 * @type  bool
	 */
	private $hasAssociations = false;

	/**
	 * Constructor.
	 *
	 * @param   string   $type             The content type to be loaded
	 * @param   boolean  $hasAssociations  Should relationships be included in the response
	 *
	 * @since 4.0.0
	 */
	public function __construct(string $type, bool $hasAssociations)
	{
		$this->type = $type;
		$this->hasAssociations = $hasAssociations;
	}

	/**
	 * Get the attributes array.
	 *
	 * @param   Table|array|\stdClass|CMSobject  $post    The model
	 * @param   array                            $fields  The fields can be array or null
	 *
	 * @return  array
	 *
	 * @since   4.0.0
	 */
	public function getAttributes($post, array $fields = null)
	{
		if (!($post instanceof Table) && !($post instanceof \stdClass) && !(is_array($post))
			&& !($post instanceof CMSObject))
		{
			$message = sprintf(
				'Invalid argument for TableSerializer. Expected array or %s. Got %s',
				Table::class,
				gettype($post)
			);

			throw new \InvalidArgumentException($message);
		}

		// The response from a standard ListModel query
		if ($post instanceof \stdClass)
		{
			$post = (array) $post;
		}

		// The response from a standard AdminModel query
		if ($post instanceof CMSObject)
		{
			$post = $post->getProperties();
		}

		// TODO: Find a way to make this an instance of TableInterface instead of the concrete class
		if ($post instanceof Table)
		{
			$post = $post->getProperties();
		}

		return is_array($fields) ? array_intersect_key($post, array_flip($fields)) : $post;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \LogicException
	 */
	public function getRelationship($model, $name)
	{
		$result = parent::getRelationship($model, $name);

		if ($result !== null)
		{
			return $result;
		}

		// TODO: Should the associations property here be dynamic?
		if ($this->hasAssociations && isset($model->associations))
		{
			foreach ($model->associations as $language => $association)
			{
				if ($name !== $language)
				{
					continue;
				}

				$itemId = explode(':', $association);

				return (new Relationship())
					// TODO: The link here must be dynamic :) Serializer is independent of content
					->addLink('self', Route::link('site', Uri::root(true) . '/api/index.php/v1/content/article/' . $itemId[0]));
			}
		}

		return null;
	}
}
