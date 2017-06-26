<?php

/**
 * @package    Joomla.API
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Application;

defined('_JEXEC') or die;

use Joomla\Application\Web\WebClient;
use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Joomla\Router\Router;

/**
 * Joomla! API Application class
 *
 * @since  4.0
 */
final class ApiApplication extends CMSApplication
{
	/**
	 * The API router.
	 *
	 * @var    Router
	 * @since  4.0
	 */
	protected $router;

	/**
	 * Class constructor.
	 *
	 * @param   \JInput   $input       An optional argument to provide dependency injection for the application's input
	 *                                 object.  If the argument is a JInput object that object will become the
	 *                                 application's input object, otherwise a default input object is created.
	 * @param   Registry  $config      An optional argument to provide dependency injection for the application's config
	 *                                 object.  If the argument is a Registry object that object will become the
	 *                                 application's config object, otherwise a default config object is created.
	 * @param   WebClient $client      An optional argument to provide dependency injection for the application's client
	 *                                 object.  If the argument is a WebClient object that object will become the
	 *                                 application's client object, otherwise a default client object is created.
	 * @param   Container $container   Dependency injection container.
	 *
	 * @since   3.2
	 */

	public function __construct(\JInput $input = null, Registry $config = null, WebClient $client = null, Container $container = null)
	{
		// Register the application name
		$this->name = 'japi';

		// Register the client ID 
		$this->clientId = 3;

		// Set format to JSON (uses JDocumentJson)
		$this->input->set('format', $input->get('format', 'json'));

		// Execute the parent constructor
		parent::__construct($input, $config, $client, $container);

		// Set the root in the URI based on the application name
		\JUri::root(null, str_ireplace('/' . $this->getName(), '', \JUri::base(true)));

		// Setup the router
		// TODO: Router class not ready
		// $this->router = new ApiRouter();
	}


	/**
	 * Method to run the application routines.
	 *
	 * Most likely you will want to instantiate a controller and execute it, or perform some sort of task directly.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function doExecute()
	{
		// Initialise the application
		$this->initialiseApp();

	}

	/**
	 * Rendering is the process of pushing the document buffers into the template
	 * placeholders, retrieving data from the document and pushing it into
	 * the application response buffer.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 *
	 * @note    Rendering should be overridden to get rid of the theme files.
	 */
	protected function render()
	{
		// Render the document
		$this->setBody($this->document->render($this->allowCache()));
	}

	/**
	 * Method to send the application response to the client.  All headers will be sent prior to the main application output data.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function respond()
	{
		// Send the content-type header.
		if (!$this->getResponse()->hasHeader('Content-Type'))
		{
			$this->setHeader('Content-Type', $this->mimeType . '; charset=' . $this->charSet);
		}

		// If the response is set to uncachable, we need to set some appropriate headers so browsers don't cache the response.
		if (!$this->allowCache())
		{
			// Expires in the past.
			$this->setHeader('Expires', 'Wed, 17 Aug 2005 00:00:00 GMT', true);

			// Always modified.
			$this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
			$this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);

			// HTTP 1.0
			$this->setHeader('Pragma', 'no-cache');
		}
		else
		{
			// Expires.
			if (!$this->getResponse()->hasHeader('Expires'))
			{
				$this->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 900) . ' GMT');
			}

			// Last modified.
			if (!$this->getResponse()->hasHeader('Last-Modified') && $this->modifiedDate instanceof \DateTime)
			{
				$this->modifiedDate->setTimezone(new \DateTimeZone('UTC'));
				$this->setHeader('Last-Modified', $this->modifiedDate->format('D, d M Y H:i:s') . ' GMT');
			}
		}

		$this->sendHeaders();

		echo json_encode($this->getBody());
	}

	/**
	 * Gets the name of the current template.
	 *
	 * @param   boolean $params True to return the template parameters
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getTemplate($params = false)
	{
		// The API application should not need to use a template
		return 'system';
	}


}

