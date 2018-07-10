<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Entity;

use Joomla\Entity\Model;
use Joomla\Entity\Relations\Relation;

defined('JPATH_PLATFORM') or die;

/**
 * Entity Model for an Article.
 *
 * @since  __DEPLOY_VERSION__
 */
class Content extends Model
{
	use EntityTableTrait;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = '#__content';

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'attribs'  => 'array',
		'metadata' => 'array',
		'images'   => 'array',
		'urls'     => 'array'
	];

	/**
	 * The attributes that should be mutated to dates. Already aliased!
	 *
	 * @var array
	 */
	protected $dates = [
		'created',
		'modified',
		'checked_out_time',
		'publish_up',
		'publish_down'
	];

	/**
	 * Array with alias for "special" columns such as ordering, hits etc etc
	 *
	 * @var    array
	 */
	protected $columnAlias = [
		'createdAt' => 'created',
		'updatedAt' => 'modified'
	];

	/**
	 * Get the category for the current article.
	 * @return Relation
	 */
	public function category()
	{
		return $this->belongsTo('Joomla\CMS\Entity\Category', 'catid');
	}

	/**
	 * Get the featured for the current user.
	 * @return Relation
	 */
	public function featured()
	{
		return $this->hasOne('Joomla\CMS\Entity\Category\Featured');
	}

	/**
	 * Mutation for articletext
	 * @return mixed|string
	 */
	public function getArticletextAttribute()
	{
		return trim($this->fulltext) != '' ? $this->introtext . '<hr id="system-readmore" />' . $this->fulltext : $this->introtext;
	}

	/**
	 * Mutation for articletext
	 *
	 * @param   string  $value  intotext + fulltext
	 * @return void
	 */
	public function setArticletextAttribute($value)
	{
		$value = explode('<hr id="system-readmore" />', $value, 2);

		$this->introtext = $value[0];
		$this->fulltext = (array_key_exists(1, $value)) ? $value[1] : '';
	}
}
