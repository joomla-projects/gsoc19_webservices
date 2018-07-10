<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\MVC\EntityModel;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Event\Table\AbstractEvent;
use Joomla\CMS\MVC\EntityModel\FormEntityModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\Entity\Model;
use Joomla\String\StringHelper;

/**
 * Prototype admin model.
 *
 * @since  1.6
 */
abstract class AdminEntityModel extends FormEntityModel
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = null;

	/**
	 * The event to trigger after deleting the data.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $event_after_delete = null;

	/**
	 * The event to trigger after saving the data.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $event_after_save = null;

	/**
	 * The event to trigger before deleting the data.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $event_before_delete = null;

	/**
	 * The event to trigger before saving the data.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $event_before_save = null;

	/**
	 * The event to trigger after changing the published state of the data.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $event_change_state = null;

	/**
	 * The context used for the associations table
	 *
	 * @var     string
	 * @since   3.4.4
	 */
	protected $associationsContext = null;

	/**
	 * The user performing the actions (re-usable in batch methods & saveorder(), initialized via initBatch())
	 *
	 * @var     object
	 * @since   3.8.2
	 */
	protected $user = null;

	/**
	 * Constructor.
	 *
	 * @param   array                 $config       An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   MVCFactoryInterface   $factory      The factory.
	 * @param   FormFactoryInterface  $formFactory  The form factory.
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, FormFactoryInterface $formFactory = null)
	{
		parent::__construct($config, $factory, $formFactory);

		if (isset($config['event_after_delete']))
		{
			$this->event_after_delete = $config['event_after_delete'];
		}
		elseif (empty($this->event_after_delete))
		{
			$this->event_after_delete = 'onContentAfterDelete';
		}

		if (isset($config['event_after_save']))
		{
			$this->event_after_save = $config['event_after_save'];
		}
		elseif (empty($this->event_after_save))
		{
			$this->event_after_save = 'onContentAfterSave';
		}

		if (isset($config['event_before_delete']))
		{
			$this->event_before_delete = $config['event_before_delete'];
		}
		elseif (empty($this->event_before_delete))
		{
			$this->event_before_delete = 'onContentBeforeDelete';
		}

		if (isset($config['event_before_save']))
		{
			$this->event_before_save = $config['event_before_save'];
		}
		elseif (empty($this->event_before_save))
		{
			$this->event_before_save = 'onContentBeforeSave';
		}

		if (isset($config['event_change_state']))
		{
			$this->event_change_state = $config['event_change_state'];
		}
		elseif (empty($this->event_change_state))
		{
			$this->event_change_state = 'onContentChangeState';
		}

		$config['events_map'] = $config['events_map'] ?? array();

		$this->events_map = array_merge(
			array(
				'delete'       => 'content',
				'save'         => 'content',
				'change_state' => 'content',
				'validate'     => 'content',
			), $config['events_map']
		);

		// Guess the \JText message prefix. Defaults to the option.
		if (isset($config['text_prefix']))
		{
			$this->text_prefix = strtoupper($config['text_prefix']);
		}
		elseif (empty($this->text_prefix))
		{
			$this->text_prefix = strtoupper($this->option);
		}
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		return \JFactory::getUser()->authorise('core.delete', $this->option);
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		return \JFactory::getUser()->authorise('core.edit.state', $this->option);
	}

	/**
	 * Method override to check-in a record or an array of record
	 *
	 * @param   mixed  $pks  The ID of the primary key or an array of IDs
	 *
	 * @return  integer|boolean  Boolean false if there is an error, otherwise the count of records checked in.
	 *
	 * @since   1.6
	 * @throws \Exception
	 */
	public function checkin($pks = array())
	{
		$pks = (array) $pks;
		$count = 0;

		if (empty($pks))
		{
			$pks = array((int) $this->getState($this->getName() . '.id'));
		}

		$checkedOutField = $this->entity->getColumnAlias('checked_out');

		// Check in all items.
		foreach ($pks as $pk)
		{
			if ($this->entity->load($pk))
			{
				if ($this->entity->{$checkedOutField} > 0)
				{
					// TODO make checkin not load twice the Model
					if (!parent::checkin($pk))
					{
						return false;
					}

					$count++;
				}
			}
			else
			{
				// TODO better exception
				throw new \Exception("falied to load Entity");
			}
		}

		return $count;
	}

	/**
	 * Method override to check-out a record.
	 *
	 * @param   integer  $pk  The ID of the primary key.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   1.6
	 */
	public function checkout($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		return parent::checkout($pk);
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   1.6
	 */
	public function deleteMultiple(&$pks)
	{
		// TODO
		return false;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer $pk The id of the primary key.
	 *
	 * @return  Model|boolean  Object on success, false on failure.
	 *
	 * @since   1.6
	 * @throws \Exception
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		if ($pk > 0)
		{
			// Attempt to load the row.
			if ($this->entity->load($pk) === false)
			{
				throw new \Exception("error");
			}

			return $this->entity;
		}

		return false;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   Table  $table  A \JTable object.
	 *
	 * @return  array  An array of conditions to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		return array();
	}

	/**
	 * Stock method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws \Exception
	 */
	protected function populateState()
	{
		$key = $this->entity->getPrimaryKey();

		// Get the pk of the record from the request.
		$pk = \JFactory::getApplication()->input->getInt($key);
		$this->setState($this->getName() . '.id', $pk);

		// Load the parameters.
		$value = \JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function publish(&$pks, $value = 1)
	{
		// TODO publish

		return true;
	}

	/**
	 * Method to adjust the ordering of a row.
	 *
	 * Returns NULL if the user did not have edit
	 * privileges for any of the selected primary keys.
	 *
	 * @param   integer  $pks    The ID of the primary key to move.
	 * @param   integer  $delta  Increment, usually +1 or -1
	 *
	 * @return  boolean|null  False on failure or error, true on success, null if the $pk is empty (no items selected).
	 *
	 * @since   1.6
	 */
	public function reorder($pks, $delta = 0)
	{
		// TODO reorder

		return true;
	}


	/**
	 * Method to compact the ordering values of rows in a group of rows defined by an SQL WHERE clause.
	 *
	 * @param   Model   $entity  Entity used for reordering
	 * @param   string  $where   WHERE clause to use for limiting the selection of rows to compact the ordering values.
	 *
	 * @return  mixed  Boolean  True on success.
	 *
	 * @since   11.1
	 * @throws  \UnexpectedValueException
	 */
	public function reorderAll($entity, $where = '')
	{
		// Check if there is an ordering field set
		$orderingField = $entity->getColumnAlias('ordering');

		if (!$entity->hasField($orderingField))
		{
			throw new \UnexpectedValueException(sprintf('%s does not support ordering.', get_class($this)));
		}

		$db = $entity->getDb();

		$quotedOrderingField = $db->quoteName($orderingField);

		$subquery = $db->getQuery(true)
			->from($entity->getTableName())
			->selectRowNumber($quotedOrderingField, 'new_ordering');

		$query = $db->getQuery(true)
			->update($entity->getTableName())
			->set($quotedOrderingField . ' = sq.new_ordering');

		$innerOn = array();

		// Get the primary keys for the selection. TODO we only support one primary key

		$subquery->select($db->quoteName($entity->getPrimaryKey(), "pk"));
		$innerOn[] = $db->quoteName($entity->getPrimaryKey()) . ' = sq.' . $db->quoteName("pk");

		// Setup the extra where and ordering clause data.
		if ($where)
		{
			$subquery->where($where);
			$query->where($where);
		}

		$subquery->where($quotedOrderingField . ' >= 0');
		$query->where($quotedOrderingField . ' >= 0');

		$query->innerJoin('(' . (string) $subquery . ') AS sq ON ' . implode(' AND ', $innerOn));

		// Pre-processing by observers
		$event = AbstractEvent::create(
			'onTableBeforeReorder',
			[
				'subject'	=> $this,
				'query'		=> $query,
				'where'		=> $where,
			]
		);
		$this->getDispatcher()->dispatch('onTableBeforeReorder', $event);

		$db->setQuery($query);
		$db->execute();

		// Post-processing by observers
		$event = AbstractEvent::create(
			'onTableAfterReorder',
			[
				'subject'	=> $this,
				'where'		=> $where,
			]
		);
		$this->getDispatcher()->dispatch('onTableAfterReorder', $event);

		return true;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array $data      The form data.
	 * @param   array $relations The relations associated with this entity.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   1.6
	 * @throws \Exception
	 */
	public function save(array $data, array $relations)
	{
		$context    = $this->option . '.' . $this->name;

		if (array_key_exists('tags', $data) && is_array($data['tags']))
		{
			$this->newTags = $data['tags'];
		}

		$key = $this->entity->getPrimaryKey();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');

		// Include the plugins for the save events.
		\JPluginHelper::importPlugin($this->events_map['save']);

		// Allow an exception to be thrown.

		// Load the row if saving an existing record.
		if ($pk > 0)
		{
			$this->entity->load($pk);
		}

		// Bind the data.
		$this->entity->bind($data);

		// TODO Check the data.
		if (!$this->entity->check())
		{
			throw new \Exception("check failed");
		}

		// Trigger the before save event.
		$result = \JFactory::getApplication()->triggerEvent($this->event_before_save, array($context, $this, !$this->entity->exists, $data));

		if (in_array(false, $result, true))
		{
			// TODO better exception
			throw new \Exception("no idea what exception this may be");
		}

		// Store the data.
		foreach ($relations as $relation)
		{
			if (!$relation->save($this->entity))
			{
				// TODO better exception
				throw new \Exception("persist failed");
			}
		}

		if (!$this->entity->persist())
		{
			// TODO better exception
			throw new \Exception("persist failed");
		}

		// Clean the cache.
		$this->cleanCache();

		// Trigger the after save event.
		\JFactory::getApplication()->triggerEvent($this->event_after_save, array($context, $this, !$this->entity->exists, $data));

		if (isset($this->$key))
		{
			$this->setState($this->getName() . '.id', $this->$key);
		}

		$this->setState($this->getName() . '.new', !$this->exists);

		// TODO associations

		return true;
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param   array    $pks    An array of primary key ids.
	 * @param   integer  $order  +1 or -1
	 *
	 * @return  boolean  Boolean true on success, false on failure
	 *
	 * @since   1.6
	 */
	public function saveorder($pks = array(), $order = null)
	{
		// TODO saveorder

		return true;
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer         $category_id  The id of the category.
	 * @param   string          $alias        The alias.
	 * @param   string          $title        The title.
	 * @param   boolean/array   $rows         False if query needs to be done, array of results otherwise.
	 *
	 * @return	array  Contains the modified title and alias.
	 *
	 * @since	1.7
	 */
	protected function generateNewTitle($category_id, $alias, $title, $rows = false)
	{
		$rows = (is_array($rows)) ?: $this->entity->where(['alias' => $alias, 'catid' => $category_id])->first();

		// Alter the title & alias
		foreach ($rows as $row)
		{
			$title = StringHelper::increment($title);
			$alias = StringHelper::increment($alias, 'dash');
		}

		return array($title, $alias);
	}
}
