<?php
class ModuleUser extends Module
{
	static public function execute($class_name = null, $default_action=null, $default_view=null)
	{
		$function = __FUNCTION__; 
		parent::$function("user");
	}
}

class ModuleUserView extends Base
{
	public function login()
	{
		$username = Request::get("username", "post", null);
		$password = Request::get("password", "post", null);
		
		$login = new Form();
		$login->setMethod("post");
		$login->setAction(generate_url(array("action"), array("action" => "login")));
		$login->setId("loginform");

		$fld_username = new FormInputText();
		$fld_username->setLabel("Username");
		$fld_username->setName("username");
		$fld_username->setValue($username);

		$fld_password = new FormInputPassword();
		$fld_password->setLabel("Password");
		$fld_password->setName("password");
		$fld_password->setValue($password);

		$formSent = new FormInputHidden();
		$formSent->setName("formSent");
		$formSent->setValue("1");

		$submit = new FormInputSubmit();
		$submit->setValue("Login");

		$login->addControls($fld_username, $fld_password, $submit, $formSent);
		
//		echo $login;
?>

<form method="post" action="<?php echo generate_url(array("action"), array("action" => "login")) ?>" id="loginform" name="login_frm">
<table cellspacing="0" cellpadding="0">
	<tbody>
		<tr class="title_row">
			<td width="19"><img border="0" src="images/bar_left.gif"></td>
			<td width="100%" class="box_title">&nbsp;&nbsp;Login</td>
			<td width="6"><img border="0" src="images/bar_right.gif"></td>
		</tr>
		<tr>
			<td colspan="3" class="box">
			<table class="login_controls">
				<tbody>
					<tr>
						<td class="text1 label">Username:</td>
						<td><input type="text" name="loginform[username]" /></td>
					</tr>
					<tr>
						<td class="text1 label">Password:</td>
						<td class="minimalwidth"><input type="password" name="loginform[password]" /></td>
					</tr>
					<tr class="button_row">
						<td align="center" colspan="2"><input type="submit" value="Submit" class="button1"></td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" value="1" name="loginform[formSent]" />
<input type="hidden" value="login" name="action" />
</form>

<?php 
	}
}

class ModuleUserAction extends Base
{
	public function login()
	{
		$login_form_request = new FormRequest("loginform", "post" ,true);
		$username = $login_form_request->get("username");
		$password = $login_form_request->get("password");
		$password = encrypt($password);
		
		$username = mysql_real_escape_string(trim($username));
		$session =& Session::instance();
		
		$user = new MySQLTable("admin");
		$user->select_first("username='" . $username . "' AND password='" . $password . "'");
		
		if(isset($user->data))
		{
			$session->set("user_id", $user->get("id"));
			$response =& Response::instance();
			$response->redirect(generate_url(array("module", "view", "action")));
		}
		else
		{
			$notif =& Notification::instance();
			$notif->addNotification(Text::translate("Incorrect user name/password entered."), Notification::NOTIF_ERROR);
		}
	}
	
	public function logout()
	{
		$session =& Session::instance();
		$session->remove("user_id");
		
		$response =& Response::instance();
		$response->redirect(generate_url(array("module", "view", "action"), array("module" => "user", "view" => "login")));
	}
}

class ModuleUserHelper
{
}
