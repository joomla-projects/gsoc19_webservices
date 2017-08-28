<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Api\Serializer;

use Tobscure\JsonApi\AbstractSerializer;

defined('_JEXEC') or die;

/**
 * Temporary serializer
 *
 * @since  __DEPLOY_VERSION__
 */
class ItemSerializer extends AbstractSerializer
{
	protected $type = 'items';

	/**
	 * Get the attributes array.
	 *
   * @param   mixed  $post    The model
	 * @param   array  $fields  The fields can be array or null
	 *
	 * @since  __DEPLOY_VERSION__
	 * @return array
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
