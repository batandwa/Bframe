<?php
//interface ModuleInt
//{
//	static function execute();
//}

abstract class Module extends Base //implements ModuleInt 
{
	static function execute($class_name=null, $default_action=null, $default_view="index")
	{
		$action = Request::get("action", "get post", $default_action);
		if(!empty($action))
		{
			$action_class_name = "Module".$class_name."Action";
			$user_action = new $action_class_name();
			$user_action->$action();
		}

		$view = Request::get("view", "get post", $default_view);
		if(!empty($view))
		{
			$view_class_name = "Module".$class_name."View";
			$user_view = new $view_class_name();
			
			$registry = Registry::instance();
			$registry->set("view", $user_view);
			
			$user_view->$view();
		}

//		$helper_class_name = "Module".$class_name."Helper";
//		$helper = new $helper_class_name();
//		$helper->$view();
	}
	
//	static function &helper()
//	{
//		$helper_class_name = "Module" . $class_name . "Helper";
//		return new $helper_class_name();
//	}
}

