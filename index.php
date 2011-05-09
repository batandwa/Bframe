<?php
// TODO : Move this to a more appropriate place. This was done and commented
//        out in Session::__construct(). 
session_start();

require "includes/globals.php";
include "functions/functions.php";

$valid_modules = array("user", "leave", "employees");

$session =& Session::instance();

$application =& Application::instance();
$application->init();

$user =& MySQLTableUser::instance();

$notifications =& Notification::instance();

$module = Request::get("module", "get", "employees", "string");

if(!in_array($module, $valid_modules))
{
	exit("Invalid module.");
}

if(!is_alpha($module))
{
	exit("Illegal module name.");
}

//If the user is not logged in and this is not the user module (which renders 
//  the login page) redirect them to the login page.
if($module !== "user" && !isset($user->data))
{
//	$url = generate_url(array("module", "view", "action")) . "&module=user";
	$url = "index.php?module=user&view=login";
	$response =& Response::instance();
	$response->redirect($url);
}

$action = Request::get("action", "get");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<link type="text/css" rel="stylesheet" href="css/reset.css" />
	<link type="text/css" rel="stylesheet" href="css/defaults.css" />
	<link type="text/css" rel="stylesheet" href="css/dw.css" />
	<link type="text/css" rel="stylesheet" href="css/form.css" />
	<link type="text/css" rel="stylesheet" href="css/template.css" />
	<link type="text/css" rel="stylesheet" href="css/interface.css" />
	<link type="text/css" rel="stylesheet" href="css/julian_robichaux_date_picker.css" />
	<?php if(!is_null($module)) { ?>
	<link type="text/css" rel="stylesheet" href="css/<?php echo $module ?>.css" />
	<?php } ?>
	
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/julian_robichaux_date_picker.js"></script>
	<script type="text/javascript" src="js/date_pickers.js"></script>
	<script type="text/javascript" src="js/default.js"></script>
	
	<title>Sabila Leave Manager</title>
</head>
<body class="bg_texture <?php echo $module ?>">
	<div id="wrapper">
	
		<div id="header">
<!--			<img src="images/s2w_logo.jpg" alt="Spin 2 Win logo" />-->
			<img src="images/logo.gif" alt="Sabila logo" />
		</div>
		<div id="user" class="blue_bar_repeat text3">
		
		<?php if(isset($user->data)) { ?>
			<?php
//				$loginFormSent = (bool)Request::get("loginFormSent", "post", false);
				$loginEntry = new FormRequest("loginform", "post");

				if(!is_null($loginEntry->get("form_sent")))
				{
//					if(!Application::login($loginEntry->get("user_name"), $loginEntry->get("user_password")))
//					{
//						$notifications->addNotification("Incorrect login details.", Notification::NOTIF_ERROR);
//					}
				}
				else
				{
//					Application::session();
				}

				$data = array();

				$row = array("employees", "employees", "Employees");
				array_push($data, $row);
//				$row = array("serverbrowser", "ServerBrowser");
//				array_push($data, $row);

				$nav = new HTMLList();
				$nav->setData($data);
				$attribs = new Attributes();
				$attribs->set("id", "mainNav");
				$nav->setAttributes($attribs);
				$nav->setFormat('<a href="'.generate_url(array("module", "display", "view", "action", "id"), array("module" => '%1$s', "view" => '%2$s')) . '">%3$s</a>');
//				$nav->setFields(array("Module", "Text"));
//				$nav->setGroupBy("Text");
//				var_dump($nav->calcGroupAggregates(4));die(__FILE__." (".__LINE__.") 15 Sep 2009 00:18:37");
//				echo $nav;

				if(isset($user->data))
				{
			?>
					<p id="user_info">
						Logged in as: <span class="username"><?php echo $user->get("username") ?></span> 
						<a href="<?php echo generate_url(array("action", "module", "view"), array("module" => "user", "action" => "logout")) ?>">logout</a>
					</p>
			<?php
				}
			?>
		<?php } else { ?>
					<p id="user_info">&nbsp;</p>
		<?php }  ?>
		
		</div>
		
		<div id="main" class="box">
<?php
	//Determine the paths of the module files.
	$modulePath = "modules/" . $module . ".php";
	$moduleFunctionsPath = "functions/" . $module . ".php";

	//If the files exist load them.
	if(is_file($moduleFunctionsPath))
	{
		include $moduleFunctionsPath;
	}
	if(is_file($modulePath))
	{
		include $modulePath;
		eval("Module$module::execute();");
	}
	
?>
		</div>
		<div style="clear: both;"><!-- x --></div>
		<div id="footer"></div>
	</div>
	<?php
		echo $notifications;
		$notifications->clear();
	?>
</body>
</html>
