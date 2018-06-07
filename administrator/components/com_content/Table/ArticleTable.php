<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Administrator\Table;

use Joomla\CMS\Table\TableInterface;

defined('JPATH_PLATFORM') or die;

/**
 * Article entity
 *
 * @since  __DEPLOY_VERSION__
 */
class ArticleTable extends \Joomla\Entity\Model implements TableInterface
{
	use EntityTableTrait;

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 * @since  __DEPLOY_VERSION__
	 */
	protected $casts = array(
		'attribs'  => 'array',
		'metadata' => 'array',
	);

	/**
	 * The table name.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $table = '#__content';

	/**
	 * The attributes that should be mutated to dates. Already aliased!
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected $dates = array(
		'checked_out_time',
		'publish_up',
		'publish_down',
		'created',
		'modified'
	);

	/**
	 * Array with alias for "special" columns such as ordering, hits etc etc
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected $columnAlias = array(
		'published' => 'state',
		'createdAt' => 'created',
		'updatedAt' => 'modified'
	);

	public function bind($src, $ignore = array())
	{
		// Search for the {readmore} tag and split the text up accordingly.
		if (isset($src['articletext']))
		{
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tagPos = preg_match($pattern, $src['articletext']);

			if ($tagPos == 0)
			{
				$src['introtext'] = $src['articletext'];
				$src['fulltext'] = '';
			}
			else
			{
				list ($src['introtext'], $src['fulltext']) = preg_split($pattern, $src['articletext'], 2);
			}

			unset($src['articletext']);
		}

		if (is_string($ignore))
		{
			$ignore = explode(' ', $ignore);
		}

		// Bind the source value, excluding the ignored fields.
		foreach ($this->getAttributes() as $k => $v)
		{
			// Only process fields not in the ignore array.
			if (!in_array($k, $ignore))
			{
				if (isset($src[$k]))
				{
					$this->setAttribute($k, $src[$k]);
				}
			}
		}

		return true;
	}
}
