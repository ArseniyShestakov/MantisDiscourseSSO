# MantisDiscourseSSO

Supported:

* MantisBT 1.2.x

Install dependency:

* Depend on: [Single file SSO client for Discourse](https://github.com/ArseniyShestakov/singlefile-discourse-sso-php)
* To install SSO client copy ```discourse-sso.php``` into your Mantis root directory and edit defines.
* Visit ```https://example.com/discourse-sso.php``` once so script can create database table.
* You might check if it's working properly by checking contents of SSO_DB_TABLE from commandline:
```
mysql -u mantisuser -pPASSWORD mantisdb -e "SELECT * FROM sso_login;"
```

How to install plugin:

* Clone repository into plugins directory

```
git clone https://github.com/ArseniyShestakov/MantisDiscourseSSO.git /path/to/mantis/plugins/MantisDiscourseSSO
```
* Open Mantis in browser and login as administrator.
* Go to Manage -> Manage Plugins
* Find Discourse SSO Plugin in the list and install it.
* Now set following settings in your ```config_inc.php```:
```
        // Enable login through Discourse
        $g_login_method = DISCOURSE_SSO;
	// Disable signup on Mantis
        $g_allow_signup = OFF;
	// Disable password reset by Mantis
        $g_send_reset_password = OFF;
        // Must be changed since default is login_page.php and that page will redirect to Discourse SSO
        $g_logout_redirect_page = 'view_all_bug_page.php';
```
