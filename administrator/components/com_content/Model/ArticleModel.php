<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Entity\Category;
use Joomla\CMS\Entity\Featured;
use Joomla\CMS\MVC\EntityModel\AdminEntityModel;
use Joomla\Utilities\ArrayHelper;

/**
 * Entity Model for an Article.
 *
 * @since  1.6
 */
class ArticleModel extends AdminEntityModel
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_CONTENT';

	/**
	 * The type alias for this content type (for example, 'com_content.article').
	 *
	 * @var    string
	 * @since  3.2
	 */
	public $typeAlias = 'com_content.article';

	/**
	 * The context used for the associations table
	 *
	 * @var    string
	 * @since  3.4.4
	 */
	protected $associationsContext = 'com_content.item';

	/**
	 * The Model associated entity class. MUST be defined for each model.
	 *
	 * @var string
	 */
	protected $entityClass = 'Joomla\CMS\Entity\Content';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			// We allow published items to be directly deleted in the API
			if ($record->state != -2 && !\JFactory::getApplication()->isClient('api'))
			{
				return false;
			}

			return \JFactory::getUser()->authorise('core.delete', 'com_content.article.' . (int) $record->id);
		}

		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = \JFactory::getUser();

		// Check for existing article.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_content.article.' . (int) $record->id);
		}

		// New article, so check against the category.
		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_content.category.' . (int) $record->catid);
		}

		// Default to component settings if neither article nor category known.
		return parent::canEditState($record);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A \JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_content.article', 'article', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$jinput = \JFactory::getApplication()->input;

		/*
		 * The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		 * The back end uses id so we use that the rest of the time and set it to 0 by default.
		 */
		$id = $jinput->get('a_id', $jinput->get('id', 0));

		// Determine correct permissions to check.
		if ($this->getState('article.id'))
		{
			$id = $this->getState('article.id');

			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');

			// Existing record. Can only edit own articles in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		$user = \JFactory::getUser();

		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_content.article.' . (int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', 'com_content')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		// Prevent messing with article language and category when editing existing article with associations
		$app = \JFactory::getApplication();
		$assoc = \JLanguageAssociations::isEnabled();

		// Check if article is associated
		if ($this->getState('article.id') && $app->isClient('site') && $assoc)
		{
			$associations = \JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $id);

			// Make fields read only
			if (!empty($associations))
			{
				$form->setFieldAttribute('language', 'readonly', 'true');
				$form->setFieldAttribute('catid', 'readonly', 'true');
				$form->setFieldAttribute('language', 'filter', 'unset');
				$form->setFieldAttribute('catid', 'filter', 'unset');
			}
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 * @throws \Exception
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = \JFactory::getApplication();
		$data = $app->getUserState('com_content.edit.article.data', array());

		if (empty($data))
		{
			$data = $this->getItem()->toArray();

			// Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
			if ($this->getState('article.id') == 0)
			{
				$filters = (array) $app->getUserState('com_content.articles.filter');
				$data->state = $app->input->getInt(
					'state',
					((isset($filters['published']) && $filters['published'] !== '') ? $filters['published'] : null)
				);
				$data->catid = $app->input->getInt('catid', (!empty($filters['category_id']) ? $filters['category_id'] : null));
				$data->language = $app->input->getString('language', (!empty($filters['language']) ? $filters['language'] : null));
				$data->access = $app->input->getInt(
					'access', (!empty($filters['access']) ? $filters['access'] : \JFactory::getConfig()->get('access'))
				);
			}
		}

		$this->preprocessData('com_content.article', $data);

		return $data;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  array|boolean  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   3.7.0
	 */
	public function validate($form, $data, $group = null)
	{
		// Don't allow to change the users if not allowed to access com_users.
		if (\JFactory::getApplication()->isClient('administrator') && !\JFactory::getUser()->authorise('core.manage', 'com_users'))
		{
			if (isset($data['created_by']))
			{
				unset($data['created_by']);
			}

			if (isset($data['modified_by']))
			{
				unset($data['modified_by']);
			}
		}

		return parent::validate($form, $data, $group);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array $data      The form data.
	 * @param   array $relations The relations associated with this entity. (Just for inheritance compatibility)
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 * @throws \Exception
	 */
	public function save(array $data, array $relations = [])
	{
		$input  = \JFactory::getApplication()->input;
		$filter = \JFilterInput::getInstance();

		if (isset($data['metadata']) && isset($data['metadata']['author']))
		{
			$data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
		}

		if (isset($data['created_by_alias']))
		{
			$data['created_by_alias'] = $filter->clean($data['created_by_alias'], 'TRIM');
		}

		\JLoader::register('CategoriesHelper', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/categories.php');

		// Cast catid to integer for comparison
		$catid = (int) $data['catid'];

		$category = (new Category($this->entity->getDb()))->find($catid);

		// Check if New Category exists
		if ($catid > 0 && !$category)
		{
			$catid = 0;
		}

		// Save New Category
		if ($catid == 0)
		{
			if (!$this->canCreateCategory())
			{
				// TODO better exception
				throw new \Exception("user cannot create category");
			}

			$category = new Category($this->entity->getDb());

			$category->title = $data['catid'];
			$category->parent_id = 1;
			$category->extension = 'com_content';
			$category->language = $data['language'];
			$category->published = 1;

			$category->persist();
		}

		if (isset($data['urls']) && is_array($data['urls']))
		{
			$check = $input->post->get('jform', array(), 'array');

			foreach ($data['urls'] as $i => $url)
			{
				if ($url != false && ($i == 'urla' || $i == 'urlb' || $i == 'urlc'))
				{
					if (preg_match('~^#[a-zA-Z]{1}[a-zA-Z0-9-_:.]*$~', $check['urls'][$i]) == 1)
					{
						$data['urls'][$i] = $check['urls'][$i];
					}
					else
					{
						$data['urls'][$i] = \JStringPunycode::urlToPunycode($url);
					}
				}
			}

			unset($check);
		}

		// TODO Alter the title for save as copy

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), array('apply', 'save', 'save2new')) && (!isset($data['id']) || (int) $data['id'] == 0))
		{
			if ($data['alias'] == null)
			{
				if (\JFactory::getConfig()->get('unicodeslugs') == 1)
				{
					$data['alias'] = \JFilterOutput::stringURLUnicodeSlug($data['title']);
				}
				else
				{
					$data['alias'] = \JFilterOutput::stringURLSafe($data['title']);
				}

				if ($rows = $this->entity->where(['alias' => $data['alias'], 'catid' => $data['catid']])->get([$category->getPrimaryKey()]))
				{
					$msg = \JText::_('COM_CONTENT_SAVE_WARNING');
				}

				$rows = ($rows) ?: [];

				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title'], $rows);
				$data['alias'] = $alias;

				if (isset($msg))
				{
					\JFactory::getApplication()->enqueueMessage($msg, 'warning');
				}
			}
		}

		$relations = [$category->articles()];

		if (parent::save($data, $relations))
		{
			if (isset($data['featured']))
			{
				$this->featured($this->entity->id, $data['featured']);
			}

			return true;
		}

		return false;
	}

	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param   array   $pks   The ids of the items to toggle.
	 * @param   integer $value The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 * @throws \Exception
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		$pks = ArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			throw new \Exception(\JText::_('COM_CONTENT_NO_ITEM_SELECTED'));
		}

		$db = $this->entity->getDb();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__content'))
			->set('featured = ' . (int) $value)
			->where('id IN (' . implode(',', $pks) . ')');
		$db->setQuery($query);
		$db->execute();

		if ((int) $value == 0)
		{
			// Adjust the mapping table.
			// Clear the existing features settings.
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__content_frontpage'))
				->where('content_id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();
		}
		else
		{
			// First, we find out which of our new featured articles are already featured.
			$query = $db->getQuery(true)
				->select('f.content_id')
				->from('#__content_frontpage AS f')
				->where('content_id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);

			$oldFeatured = $db->loadColumn();

			// We diff the arrays to get a list of the articles that are newly featured
			$newFeatured = array_diff($pks, $oldFeatured);

			// Featuring.
			$tuples = array();

			foreach ($newFeatured as $pk)
			{
				$tuples[] = $pk . ', 0';
			}

			if (count($tuples))
			{
				$columns = array('content_id', 'ordering');
				$query = $db->getQuery(true)
					->insert($db->quoteName('#__content_frontpage'))
					->columns($db->quoteName($columns))
					->values($tuples);
				$db->setQuery($query);
				$db->execute();
			}
		}

		$this->reorderAll(new Featured($this->entity->getDb()));

		$this->cleanCache();

		return true;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object  $model  A model object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($model)
	{
		return array('catid = ' . (int) $model->catid);
	}

	/**
	 * Allows preprocessing of the \JForm object.
	 *
	 * @param   \JForm $form  The form object
	 * @param   array  $data  The data to be merged into the form object
	 * @param   string $group The plugin group to be executed
	 *
	 * @return  void
	 *
	 * @since   3.0
	 * @throws \Exception
	 */
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{
		if ($this->canCreateCategory())
		{
			$form->setFieldAttribute('catid', 'allowAdd', 'true');
		}

		// Association content items
		if (\JLanguageAssociations::isEnabled())
		{
			$languages = \JLanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

			if (count($languages) > 1)
			{
				$addform = new \SimpleXMLElement('<form />');
				$fields = $addform->addChild('fields');
				$fields->addAttribute('name', 'associations');
				$fieldset = $fields->addChild('fieldset');
				$fieldset->addAttribute('name', 'item_associations');

				foreach ($languages as $language)
				{
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $language->lang_code);
					$field->addAttribute('type', 'modal_article');
					$field->addAttribute('language', $language->lang_code);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('select', 'true');
					$field->addAttribute('new', 'true');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}

				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Custom clean the cache of com_content and content modules
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_content');
		parent::cleanCache('mod_articles_archive');
		parent::cleanCache('mod_articles_categories');
		parent::cleanCache('mod_articles_category');
		parent::cleanCache('mod_articles_latest');
		parent::cleanCache('mod_articles_news');
		parent::cleanCache('mod_articles_popular');
	}

	/**
	 * Void hit function for pagebreak when editing content from frontend
	 *
	 * @return  void
	 *
	 * @since   3.6.0
	 */
	public function hit()
	{
		return;
	}

	/**
	 * Is the user allowed to create an on the fly category?
	 *
	 * @return  boolean
	 *
	 * @since   3.6.1
	 */
	private function canCreateCategory()
	{
		return \JFactory::getUser()->authorise('core.create', 'com_content');
	}

	/**
	 * Delete #__content_frontpage items if the deleted articles was featured
	 *
	 * @param   array  $pks  The primary key related to the contents that was deleted.
	 *
	 * @return  boolean
	 *
	 * @since   3.7.0
	 * @throws \Exception
	 */
	public function delete(&$pks)
	{
		$return = parent::delete($pks);

		if ($return)
		{
			// Now check to see if this articles was featured if so delete it from the #__content_frontpage table
			$db = $this->entity->getDb();
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__content_frontpage'))
				->where('content_id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();
		}

		return $return;
	}
}
