<?php
/**
*
* @package Database Optimize & Repair Tool
* @version $Id$
* @copyright (c) 2010 Matt Friedman
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package module_install
*/
class acp_database_or_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_database_or',
			'title'		=> 'ACP_DATABASE_OR',
			'version'	=> '1.0.2',
			'modes'		=> array(
				'view'	=> array('title' => 'ACP_DATABASE_OR', 'auth' => 'acl_a_backup', 'cat' => array('ACP_CAT_DATABASE')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>