<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\MVC\Factory;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryAwareInterface;
use Joomla\CMS\Form\FormFactoryAwareTrait;
use Joomla\Database\DatabaseDriver;

/**
 * Factory to create MVC factories.
 *
 * @since  4.0.0
 */
class NewMVCFactoryFactory implements MVCFactoryFactoryInterface, FormFactoryAwareInterface
{
	use FormFactoryAwareTrait;

	/**
	 * The namespace.
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	private $namespace;

	/**
	 * The namespace.
	 *
	 * @var    DatabaseDriver
	 * @since  4.0.0
	 */
	private $db;

	/**
	 * The constructor.
	 *
	 * @param   string          $namespace  The extension namespace
	 * @param   DatabaseDriver  $db         Database driver
	 *
	 * @since   4.0.0
	 */
	public function __construct(string $namespace, DatabaseDriver $db)
	{
		$this->namespace = $namespace;
		$this->db        = $db;
	}

	/**
	 * Method to create a factory object.
	 *
	 * @param   CMSApplicationInterface  $application  The application.
	 *
	 * @return  \Joomla\CMS\MVC\Factory\MVCFactoryInterface
	 *
	 * @since   4.0.0
	 * @throws  \Exception
	 */
	public function createFactory(CMSApplicationInterface $application): MVCFactoryInterface
	{
		if (!$this->namespace)
		{
			return new LegacyFactory;
		}

		if ($application->isClient('api'))
		{
			return new ApiMVCFactory($this->namespace, $application);
		}
		else
		{
			$factory = new NewMVCFactory($this->namespace, $application, $this->db);
		}

		try
		{
			$factory->setFormFactory($this->getFormFactory());
		}
		catch (\UnexpectedValueException $e)
		{
		}

		return $factory;
	}
}
