<?php
/**
 * @package    Joomla.API
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\Router\Router;


/**
 * Joomla! API Router class
 *
 * @since  4.0
 */
class ApiRouter extends Router
{

	/**
	 * Add a route of the specified method to the router. If the pattern already exists it will be overwritten.
	 *
	 * @param   string $method     Request method to match. One of GET, POST, PUT, DELETE, HEAD, OPTIONS, TRACE or PATCH
	 * @param   string $pattern    The route pattern to use for matching.
	 * @param   mixed  $controller The controller to map to the given pattern.
	 * @param   array  $rules      An array of regex rules keyed using the named route variables.
	 *
	 * @return  $this
	 *
	 * @since   4.0
	 */
	public function addRoute($method, $pattern, $controller, array $rules = [])
	{
		list($regex, $vars) = $this->buildRegexAndVarList($pattern, $rules);

		$this->routes[strtoupper($method)][] = [
			'regex'      => $regex,
			'vars'       => $vars,
			'controller' => $controller
		];

		return $this;
	}

}
