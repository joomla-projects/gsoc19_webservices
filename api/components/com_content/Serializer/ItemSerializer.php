<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Api\Serializer;

defined('_JEXEC') or die;

use Tobscure\JsonApi\AbstractSerializer;

/**
 * Temporary serializer
 *
 * @since  __DEPLOY_VERSION__
 */
class ItemSerializer extends AbstractSerializer
{
	/**
	 * The resource type
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $type = 'items';

	/**
	 * Get the attributes array.
	 *
	 * @param   mixed  $post    The model
	 * @param   array  $fields  The fields can be array or null
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getAttributes($post, array $fields = null)
	{
		return array(
			'typeAlias' => $post->typeAlias,
			'id'  => $post->id,
			'asset_id'  => $post->asset_id,
			'title' => $post->title,
			'introtext'  => $post->introtext,
			'fulltext' => $post->fulltext,
			'state'  => $post->state,
			'catid' => $post->catid,
			'created'  => $post->created,
		);
	}
}
