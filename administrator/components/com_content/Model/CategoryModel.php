<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Administrator\Model;


use Joomla\Entity\Model;
use Joomla\Entity\Relations\Relation;

/**
 * Entity Model for an Category.
 *
 * @since  __DEPLOY_VERSION__
 */
class CategoryModel extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = '#__categories';

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'metadata' => 'array',
		'params' => 'array',
		'rules' => 'array'
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
		'createdAt' => 'created_time',
		'updatedAt' => 'modified_time'
	];

	/**
	 * Get the articles from the current category.
	 * @return Relation
	 */
	public function articles()
	{
		/** @todo I believe the related class does not need to be a Model with all the extra stuff in it,
		 * but, just a plain Entity, therefore, we may consider splitting Models and Entities.
		 * I don't know if loading larger objects has that much impact on performance, but it certainly has some.
		 * Next, this CategoryModel certainly does not belong here, as I believe it is also used in other components.
		 * Why not put it in the library?
		 */
		return $this->hasMany('Joomla\Component\Content\Administrator\Model\ArticleModel', 'catid');
	}

}