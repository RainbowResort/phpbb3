<?php
/** 
*
* @package Advanced Double Activation Pack
* @version $Id: umil_auto_adap.php, v 1.000 2009/03/31 Martin Truckenbrodt Exp$
* @copyright (c) 2009 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'INSTALL_ADVANCED_DOUBLE_ACTIVATION_PACK'				=> 'Install Advanced Double Activation Pack',
	'INSTALL_ADVANCED_DOUBLE_ACTIVATION_PACK_CONFIRM'		=> 'Are you ready to install the Advanced Double Activation Pack?',

	'ADVANCED_DOUBLE_ACTIVATION_PACK'						=> 'Advanced Double Activation Pack',
	'ADVANCED_DOUBLE_ACTIVATION_PACK_EXPLAIN'				=> 'Double Activation and some other features for account management for phpBB3',

	'UNINSTALL_ADVANCED_DOUBLE_ACTIVATION_PACK'				=> 'Uninstall Advanced Double Activation Pack',
	'UNINSTALL_ADVANCED_DOUBLE_ACTIVATION_PACK_CONFIRM'		=> 'Are you ready to uninstall the Advanced Double Activation Pack? All settings and data saved by this mod will be removed!',
	'UPDATE_ADVANCED_DOUBLE_ACTIVATION_PACK'				=> 'Update Advanced Double Activation Pack',
	'UPDATE_ADVANCED_DOUBLE_ACTIVATION_PACK_CONFIRM'		=> 'Are you ready to update the Advanced Double Activation Pack?',
));

?>