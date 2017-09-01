<?php
require_once(config_get('class_path').'MantisPlugin.class.php' );

class MantisDiscourseSSOPlugin extends MantisPlugin
{
	function register()
	{
		$this->name = 'Discourse SSO Plugin';
		$this->description = 'Discourse SSO authentication for MantisBT.';
		$this->version = '1.0';
		$this->requires = array(
			'MantisCore' => '1.2.0',
		);
		$this->author = 'Arseniy Shestakov';
		$this->contact = 'find-email-on-website@rseniyshestakov.com';
		$this->url = 'https://github.com/ArseniyShestakov/MantisDiscourseSSO';
	}
	
	function init()
	{
		require_once(config_get('absolute_path').'discourse-sso.php');
	}

	function install()
	{
		return true;
	}

	function hooks()
	{
		return array(
			'EVENT_CORE_READY' => 'login',
			'EVENT_LAYOUT_PAGE_HEADER' => 'loginPage'
		);
	}

	function login()
	{
		if(auth_is_user_authenticated() && !current_user_is_anonymous())
			return;

		if(DISCOURSE_SSO !== config_get('login_method'))
			return;

		$DISCOURSE_SSO = new DiscourseSSOClient(true);
		$SSO_STATUS = $DISCOURSE_SSO->getAuthentication();
		if(true !== $SSO_STATUS['logged'] || empty($SSO_STATUS['data']['username']))
			return;

		$DISCOURSE_SSO->removeNonce($SSO_STATUS['nonce']);
		print_r($SSO_STATUS);
		$userId = user_get_id_by_name($SSO_STATUS['data']['username']);
		if(false === $userId)
		{
			$userId = auth_auto_create_user($SSO_STATUS['data']['username'], '');
			if(false === $userId)
			{
				trigger_error('Discourse SSO: cant create user!');
			}
			else
			{
				set_user_email($userId, $SSO_STATUS['data']['email']);
				if(!empty($SSO_STATUS['data']['name']))
					user_set_realname($userId, $SSO_STATUS['data']['name']);
			}
		}
		user_increment_login_count($userId);
		user_reset_failed_login_count_to_zero($userId);
		user_reset_lost_password_in_progress_count_to_zero($userId);
		auth_set_cookies($userId, true);
		auth_set_tokens($userId);
		print_header_redirect('view_all_bug_page.php');
	}
	
	function loginPage()
	{
		if('login_page.php' !== basename($_SERVER['PHP_SELF']))
			return;
		
		print_header_redirect('discourse-sso.php');
	}
}
