<?php
/**
*
* @package ucp
* @version $Id: ucp_change_email.php,v 1.004 2009/12/29 Martin Truckenbrodt Exp $
* @copyright (c) 2005 phpBB Group
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
* ucp_email
* Force e-mail address change
* @package ucp
*/
class ucp_change_email
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $phpbb_root_path, $phpEx;
		global $db, $user, $auth, $template;

		$submit			= (isset($_POST['submit'])) ? true : false;
		$error = $data = array();

		$data = array(
			'email'				=> strtolower(request_var('email', '')),
			'email_confirm'		=> strtolower(request_var('email_confirm', '')),
		);

		add_form_key('ucp_change_email');

		if ($submit)
		{
			if (!check_form_key('ucp_change_email'))
			{
				trigger_error('FORM_INVALID');
			}

			$error = validate_data($data, array(
				'email'				=> array(
					array('string', false, 6, 60),
					array('email')),
				'email_confirm'		=> array('string', false, 6, 60),
			));
			if (sizeof($error))
			{
				trigger_error((sizeof($error)) ? implode('<br />', $error) : '');
			}

			if (!$user->data['user_id'] || $user->data['user_id'] == ANONYMOUS)
			{
				trigger_error('NO_EMAIL_USER');
			}

			if ($user->data['user_type'] == USER_IGNORE)
			{
				trigger_error('NO_USER');
			}

			if ($user->data['user_type'] == USER_INACTIVE && $user->data['user_inactive_reason'] == INACTIVE_MANUAL)
			{
				trigger_error('ACCOUNT_DEACTIVATED');
			}

			if ($data['email'] != $data['email_confirm'])
			{
				trigger_error('NEW_EMAIL_ERROR');
			}

			if ($auth->acl_get('u_chgemail'))
			{
				add_log('user', $user->data['user_id'], 'LOG_USER_UPDATE_EMAIL', $user->data['username'], $user->data['user_email'], $data['email']);
			}

			// Determine coppa status on group (REGISTERED(_COPPA))
			$sql = 'SELECT group_name, group_type
				FROM ' . GROUPS_TABLE . '
				WHERE group_id = ' . $user->data['group_id'];
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if (!$row)
			{
				trigger_error('NO_GROUP');
			}

			$coppa = ($row['group_name'] == 'REGISTERED_COPPA' && $row['group_type'] == GROUP_SPECIAL) ? true : false;

			if(!class_exists('messenger'))
			{
				include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
			}

			$server_url = generate_board_url();
			$user_actkey = gen_rand_string(10);
			$key_len = 54 - (strlen($server_url));
			$key_len = ($key_len > 6) ? $key_len : 6;
			$user_actkey = substr($user_actkey, 0, $key_len);

			$messenger = new messenger(false);

			if ($config['require_activation'] == USER_ACTIVATION_SELF || $config['require_activation'] == USER_ACTIVATION_USER_ADMIN || $coppa)
			{
				$messenger->template(($coppa) ? 'coppa_resend_inactive' : 'user_resend_inactive', $user->data['user_lang']);
				$messenger->to($data['email'], $user->data['username']);

				$messenger->headers('X-AntiAbuse: Board servername - ' . $config['server_name']);
				$messenger->headers('X-AntiAbuse: User_id - ' . $user->data['user_id']);
				$messenger->headers('X-AntiAbuse: Username - ' . $user->data['username']);
				$messenger->headers('X-AntiAbuse: User IP - ' . $user->ip);

				$messenger->assign_vars(array(
					'WELCOME_MSG'	=> htmlspecialchars_decode(sprintf($user->lang['WELCOME_SUBJECT'], $config['sitename'])),
					'USERNAME'		=> htmlspecialchars_decode($user->data['username']),
					'U_ACTIVATE'	=> $server_url . "/ucp.$phpEx?mode=activate&u={$user->data['user_id']}&k={$user_actkey}")
				);

				if ($coppa)
				{
					$messenger->assign_vars(array(
						'FAX_INFO'		=> $config['coppa_fax'],
						'MAIL_INFO'		=> $config['coppa_mail'],
						'EMAIL_ADDRESS'	=> $data['email'])
					);
				}

				$messenger->send(NOTIFY_EMAIL);
			}

			if ($config['require_activation'] == USER_ACTIVATION_ADMIN)
			{
				if ($config['email_activate_ext'])
				{
					include($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);

					$cp = new custom_profile();

					$cp_data = $cp_error = array();

					$user->profile_fields = array();
					$register = true;
					$user_profile_data = array();
					$user_profile_data = $cp->generate_profile_fields_email($user->data['user_id']);
				}

				// Grab an array of user_id's with a_user permissions ... these users can activate a user
				$admin_ary = $auth->acl_get_list(false, 'a_user', false);

				$sql = 'SELECT user_id, username, user_email, user_lang, user_jabber, user_notify_type
					FROM ' . USERS_TABLE . '
					WHERE ' . $db->sql_in_set('user_id', $admin_ary[0]['a_user']);
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					$messenger->template('admin_activate', $row['user_lang']);
					$messenger->to($row['user_email'], $row['username']);
					$messenger->im($row['user_jabber'], $row['username']);

					if ($config['email_activate_ext'])
					{
						$user->setup('profile');
						$user_profile = "\n\n" . $lang['USER_CUSTOM_PROFILE_FIELDS'] . ":\n" . $user_profile_data['pf'];
						if ($user->data['user_inactive_reason'] != INACTIVE_REGISTER)
						{
							$user_profile .= "\n\n" . $lang['PROFILE_INFO'] . ":\n" . $user_profile_data['details'];
						}
					}

					$messenger->assign_vars(array(
						'USERNAME'			=> htmlspecialchars_decode($user->data['username']),
						'U_USER_DETAILS'	=> $server_url . "/memberlist.$phpEx?mode=viewprofile&u={$user->data['user_id']}",
						'U_ACTIVATE'		=> $server_url . "/ucp.$phpEx?mode=activate&u={$user->data['user_id']}&k={$user_actkey}",
						'U_VERIFY' 			=> $server_url . "/adm/index.$phpEx?i=users&mode=verify&u={$user->data['user_id']}",
						'USER_PROFILE_DATA'	=> $user_profile)
					);

					$messenger->send($row['user_notify_type']);
				}
				$db->sql_freeresult($result);
				$message = $user->lang['ACTIVATION_EMAIL_SENT_ADMIN'] . '<br /><br />' . sprintf($user->lang['RETURN_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>');
			}
			else
			{
				$message = $user->lang['ACTIVATION_EMAIL_SENT'] . '<br /><br />' . sprintf($user->lang['RETURN_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>');
			}

			user_active_flip('deactivate', $user->data['user_id'], INACTIVE_CHANGE_EMAIL);

			// Because we want the profile to be reactivated we set user_newpasswd to empty (else the reactivation will fail)
			$sql_ary = array();
			$sql_ary['user_actkey'] = $user_actkey;
			$sql_ary['user_email'] = $data['email'];
			$sql_ary['user_email_hash'] = (crc32($data['email']) . strlen($data['email']));
			$sql_ary['user_newpasswd'] = '';

			if (sizeof($sql_ary))
			{
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE user_id = ' . $user->data['user_id'];
				$db->sql_query($sql);
			}

			$message = ($config['require_activation'] == USER_ACTIVATION_ADMIN) ? $user->lang['ACTIVATION_EMAIL_SENT_ADMIN'] : $user->lang['ACTIVATION_EMAIL_SENT'];
			$message .= '<br /><br />' . sprintf($user->lang['RETURN_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>');

			meta_refresh(5, append_sid($phpbb_root_path . 'index.' . $phpEx));

			$user->session_kill();

			trigger_error($message);

		}

		if ($user->data['user_id'] == ANONYMOUS)
		{
			trigger_error('NO_EMAIL_USER');
		}

		$this->tpl_name = 'ucp_change_email';
		$this->page_title = 'UCP_CHANGE_EMAIL';

		$template->assign_vars(array(
			'USER_EMAIL'	=> $user->data['user_email'],
		));
	}
}

?>