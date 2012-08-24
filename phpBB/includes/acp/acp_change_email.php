<?php
/**
*
* @package acp
* @version $Id: acp_change_email.php,v 1.003 2010/11/21  Martin Truckenbrodt Exp $
* @copyright (c) 2006 phpBB Group
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
class acp_change_email
{
	var $u_action;
	var $p_master;

	function acp_change_email(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $table_prefix;

		include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

		$user->add_lang('acp/users');

		$submit = (isset($_POST['submit'])) ? true : false;

		$form_key = 'acp_change_email';
		add_form_key($form_key);

		if ($submit && !check_form_key($form_key))
		{
			trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
		}

		if ($submit)
		{
			$email_array = preg_split("/[\s,]+/", request_var('email', ''));

			$user_ids = array();

			$sql = 'SELECT user_id, username
				FROM ' . USERS_TABLE . '
				WHERE user_type = ' . USER_NORMAL . '
					AND ' . $db->sql_in_set('user_email', $email_array);
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$user_ids[] = $row['user_id'];

				add_log('admin', 'LOG_USER_CHANGE_EMAIL', $row['username']);
				add_log('user', $row['user_id'], 'LOG_USER_CHANGE_EMAIL_USER');
			}
			$db->sql_freeresult($result);

			if ($user_ids)
			{
				$sql = 'UPDATE ' . USERS_TABLE . "
					SET user_inactive_reason = " . INACTIVE_CHANGE_EMAIL . "
					WHERE " . $db->sql_in_set('user_id', $user_ids);
				$db->sql_query($sql);

				trigger_error($user->lang['FORCE_CHANGE_EMAIL_SUCCESS'] . adm_back_link($this->u_action));
			}
			else
			{
				trigger_error($user->lang['FORCE_CHANGE_EMAIL_SUCCESS_NONE'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}


		$email_count = 0;

		while ($email_count < $config['topics_per_page'])
		{
			$email_count = $email_count + 1;
		}

		$template->assign_vars(array(
			'U_ACTION'		=> $this->u_action,
		));

		$this->tpl_name = 'acp_change_email';
		$this->page_title = 'ACP_CHANGE_EMAIL';
	}
}

?>