<?php
/**
*
* @package acp
* @version $Id: acp_database_or.php 15 11/22/10 9:14 AM VSE $
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
* @package acp
*/
class acp_database_or
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $template;

		$user->add_lang('mods/info_acp_database_or');
		$this->tpl_name = 'acp_database_or';
		$this->page_title = 'ACP_DATABASE_OR';

		$form_name = 'acp_database_or';
		add_form_key($form_name);

		// Check to make sure only MySQL users can proceed
		if ($db->sql_layer != 'mysql4' && $db->sql_layer != 'mysqli')
		{
			trigger_error($user->lang['WARNING_MYSQL'], E_USER_WARNING);
		}

		// Get vars from the form
		$action			= request_var('action', '');
		$submit			= (isset($_POST['submit'])) ? true : false;
		$type			= request_var('type', '');
		$table_ary		= request_var('mark', array(''));
		$disable_board	= !$config['board_disable'] ? request_var('disable_board', 0) : 0;

		if ($submit)
		{
			if (!check_form_key($form_name))
			{
				trigger_error('FORM_INVALID');
			}

			if (!sizeof($table_ary))
			{
				trigger_error($user->lang['TABLE_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Make sure Safe Mode is disabled during this script execution
			if (@ini_get('safe_mode') || @strtolower(ini_get('safe_mode')) == 'on')
			{
				@ini_set('safe_mode', 'Off');
			}

			// Extend or disable script execution timeout (copied this from acp_database.php)
			@set_time_limit(1200);
			@set_time_limit(0);

			$tables = implode(', ', $table_ary);
			unset($table_ary);

			switch ($type)
			{
				case 'optimize':

					$optimize = $this->table_maintenance('OPTIMIZE TABLE', $tables, $disable_board);

					add_log('admin', 'OPTIMIZE_LOG', $tables);

					trigger_error($user->lang['OPTIMIZE_SUCCESS'] . $optimize . adm_back_link($this->u_action));

				break;

				case 'repair':

					$repair = $this->table_maintenance('REPAIR TABLE', $tables, $disable_board);

					add_log('admin', 'REPAIR_LOG', $tables);

					trigger_error($user->lang['REPAIR_SUCCESS'] . $repair . adm_back_link($this->u_action));

				break;

				case 'check':

					$check = $this->table_maintenance('CHECK TABLE', $tables, $disable_board);

					trigger_error($user->lang['CHECK_SUCCESS'] . $check . adm_back_link($this->u_action));

				break;
			}
		}

		// Generate Show Table Data
		$total_data_size = $total_data_free = 0;
		$overhead = '';

		$tables = $db->sql_query('SHOW TABLE STATUS');

		while ($table = $db->sql_fetchrow($tables))
		{
			/* Only MyISAM, InnoDB and Archive storage engines can be used
			 * Check    supports MyISAM, InnoDB and Archive
			 * Optimize supports MyISAM, InnoDB and (as of MySQL 5.0.16) Archive
			 * Repair   supports MyISAM, Archive
			 */
			$table['Engine'] = (!empty($table['Type']) ? $table['Type'] : $table['Engine']);
			if (in_array(strtolower($table['Engine']), array('myisam', 'innodb', 'archive')))
			{
				$data_size = ($table['Data_length'] + $table['Index_length']);
				$total_data_size = $total_data_size + $data_size;
				$total_data_free = $total_data_free + $table['Data_free'];

				$template->assign_block_vars('tables', array(
					'TABLE_NAME'	=> $table['Name'],
					'TABLE_TYPE'	=> $table['Engine'],
					'DATA_SIZE'		=> $this->file_size($data_size),
					'DATA_FREE'		=> $this->file_size($table['Data_free']),
					'HAS_OVERHEAD'	=> $table['Data_free'] ? ' overhead' : '',
				));
			}
		}
		$db->sql_freeresult($tables);

		$template->assign_vars(array(
			'TOTAL_DATA_SIZE' => $this->file_size($total_data_size),
			'TOTAL_DATA_FREE' => $this->file_size($total_data_free),
			'U_ACTION' => $this->u_action,
		));
	}

	/**
	* Perform table SQL query and return any messages
	* @param string $query	should either be OPTIMIZE TABLE, REPAIR TABLE, or CHECK TABLE
	* @param string $tables comma delinieated string of all tables to be processed
	* @param bool $disable_board the users option to disable the board during run time
	*
	* $message returns any errors or status information
	*/
	function table_maintenance($query, $tables, $disable_board = 0)
	{
		global $cache, $db;

		// Disbale the board if user selected this option
		if ($disable_board)
		{
			set_config('board_disable', 1);
		}

		$message = '';
		$result = $db->sql_query($query . ' ' . $db->sql_escape($tables));
		while ($msg = $db->sql_fetchrow($result))
		{
			// Build a message only for optimize/repair errors, or if check table is run
			if ((in_array(strtolower($msg['Msg_type']), array('error', 'info', 'note', 'warning'))) || $query == 'CHECK TABLE')
			{
				$message .= '<br />' . substr($msg['Table'], (strpos($msg['Table'], '.') + 1)) . ' ... ' . $msg['Msg_type'] . ': ' . $msg['Msg_text'];
			}
		}
		$db->sql_freeresult($result);

		// Enable the board again if user selected this option
		if ($disable_board)
		{
			set_config('board_disable', 0);
		}

		// Clear cache to ensure board is re-enabled for all users
		$cache->purge();

		// Let's add an extra linebreak if there are messages, it looks better.
		$message = !empty($message) ? '<br />' . $message : '';

		return $message;
	}

	/**
	* Display filesize in the proper units
	* @param int $size number representing bytes
	*
	* returns $size converted to the correct units as a string
	*/
	function file_size($size)
	{
		$filesizename = array(' B', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
		return $size ? round($size / pow(1024, ($i = floor(log($size) / log(1024)))), 1) . $filesizename[$i] : '0 B';
	}

}
?>