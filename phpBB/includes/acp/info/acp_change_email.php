<?php
/**
*
* @package acp
* @version $Id: acp_change_email.php,v 1.00 2008/10/04 Martin Truckenbrodt Exp $
* @copyright (c) 2006 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_change_email_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_change_email',
			'title'		=> 'ACP_CHANGE_EMAIL',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'list'		=> array('title' => 'ACP_CHANGE_EMAIL', 'auth' => 'acl_a_user', 'cat' => array('ACP_CAT_USERS')),
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