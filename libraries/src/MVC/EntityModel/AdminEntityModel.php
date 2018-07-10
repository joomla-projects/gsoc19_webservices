<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\MVC\EntityModel;

defined('JPATH_PLATFORM') or die;

use Exception;
use Joomla\CMS\Event\Table\AbstractEvent;
use Joomla\CMS\MVC\EntityModel\FormEntityModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\Database\DatabaseQuery;
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

		// Check in all items.
		foreach ($pks as $pk)
		{
			if ($this->entity->load($pk))
			{
				if ($this->entity->checked_out > 0)
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
	 * @param   integer $pk The ID of the primary key.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   1.6
	 * @throws \Exception
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
	 * @throws \Exception
	 */
	public function delete(&$pks)
	{
		$pks = (array) $pks;

		// Include the plugins for the delete events.
		\JPluginHelper::importPlugin($this->events_map['delete']);

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			// TODO if we want to keep constraint loading, we can optimize this loads to only query the primary key.
			if ($this->entity->load($pk))
			{
				if ($this->canDelete($this->entity))
				{
					$context = $this->option . '.' . $this->name;

					// Trigger the before delete event.
					$result = \JFactory::getApplication()->triggerEvent($this->event_before_delete, array($context, $this->entity));

					if (in_array(false, $result, true))
					{
						// TODO better exception
						throw new \Exception("no idea what exception this may be");
					}

					// TODO associations

					if (!$this->entity->delete())
					{
						// TODO better exception
						throw new \Exception("failed to delete");
					}

					// Trigger the after event.
					\JFactory::getApplication()->triggerEvent($this->event_after_delete, array($context, $this->entity));
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);

					\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');
				}
			}
			else
			{
				// TODO better exception
				throw new \Exception("failed to load entity for delete");
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
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
	 * @throws \Exception
	 */
	public function publish(&$pks, $value = 1)
	{
		$user = \JFactory::getUser();
		$pks = (array) $pks;

		// Include the plugins for the change of state event.
		\JPluginHelper::importPlugin($this->events_map['change_state']);

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			if ($this->entity->load($pk, true))
			{
				if (!$this->canEditState($this->entity))
				{
					// Prune items that you can't change.
					unset($pks[$i]);

					\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');

					return false;
				}

				// If the table is checked out by another user, drop it and report to the user trying to change its state.
				if ($this->entity->hasField('checked_out') && $this->entity->checked_out && ($this->entity->checked_out != $user->id))
				{
					\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'), \JLog::WARNING, 'jerror');

					// Prune items that you can't change.
					unset($pks[$i]);

					return false;
				}
			}
		}

		// Attempt to change the state of the records.
		$userId = (int) $user->get('id');
		$state  = (int) $value;

		// Pre-processing by observers
		$event = AbstractEvent::create(
			'onTableBeforePublish',
			[
				'subject'	=> $this,
				'pks'		=> $pks,
				'state'		=> $state,
				'userId'	=> $userId,
			]
		);
		$this->getDispatcher()->dispatch('onTableBeforePublish', $event);

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			$pk = array();

			// TODO we do not support composed primary keys.
			if ($this->$key)
			{
				$pk[$key] = $this->$key;
			}
			// We don't have a full primary key - return false
			else
			{
				throw new Exception(\JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
			}

			$pks = array($pk);
		}

		$publishedField = $this->entity->getColumnAlias('published');
		$checkedOutField = $this->entity->getColumnAlias('checked_out');

		$db = $this->entity->getDb();

		foreach ($pks as $pk)
		{
			// Update the publishing state for rows with the given primary keys.
			$query = $db->getQuery(true)
				->update($this->entity->getTableName())
				->set($db->quoteName($publishedField) . ' = ' . (int) $state);

			// If publishing, set published date/time if not previously set
			if ($state && $this->entity->hasField('publish_up') && (int) $this->entity->publish_up == 0)
			{
				$nowDate = $db->quote(\JFactory::getDate()->toSql());
				$query->set($db->quoteName($this->entity->getColumnAlias('publish_up')) . ' = ' . $nowDate);
			}

			// Determine if there is checkin support for the table.
			if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time'))
			{
				$query->where('(' . $db->quoteName($checkedOutField) . ' = 0 OR ' . $db->quoteName($checkedOutField) . ' = ' . (int) $userId . ')');
				$checkin = true;
			}
			else
			{
				$checkin = false;
			}

			// Build the WHERE clause for the primary keys.
			$this->appendPrimaryKeys($query, $pk);

			$db->setQuery($query);

			$db->execute();

			// If checkin is supported and all rows were adjusted, check them in. TODO I don't get this.
			if ($checkin && (count($pks) == $db->getAffectedRows()))
			{
				$this->checkin($pk);
			}

			// If the Table instance value is in the list of primary keys that were set, set the instance.
			$ours = true;

			// TODO we do not suport composed primary keys yet
			if ($this->$key != $pk[$key])
			{
				$ours = false;
			}

			if ($ours)
			{
				$this->$publishedField = $state;
			}
		}

		// Pre-processing by observers
		$event = AbstractEvent::create(
			'onTableAfterPublish',
			[
				'subject'	=> $this,
				'pks'		=> $pks,
				'state'		=> $state,
				'userId'	=> $userId,
			]
		);
		$this->getDispatcher()->dispatch('onTableAfterPublish', $event);

		$context = $this->option . '.' . $this->name;

		// Trigger the change state event.
		$result = \JFactory::getApplication()->triggerEvent($this->event_change_state, array($context, $pks, $value));

		if (in_array(false, $result, true))
		{
			// TODO better exception
			throw new \Exception("no idea what exception this may be");
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to append the primary keys for this table to a query.
	 *
	 * @param   DatabaseQuery  $query  A query object to append.
	 * @param   mixed          $pk     Optional primary key parameter.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function appendPrimaryKeys($query, $pk = null)
	{
		if (is_null($pk))
		{
			foreach ($this->_tbl_keys as $k)
			{
				$query->where($this->_db->quoteName($k) . ' = ' . $this->_db->quote($this->$k));
			}
		}
		else
		{
			if (is_string($pk))
			{
				$pk = array($this->_tbl_key => $pk);
			}

			$pk = (object) $pk;

			foreach ($this->_tbl_keys as $k)
			{
				$query->where($this->_db->quoteName($k) . ' = ' . $this->_db->quote($pk->$k));
			}
		}
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
