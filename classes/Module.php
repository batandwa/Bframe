<?php
abstract class Module extends Base //implements ModuleInt 
{
	static function execute($class_name=null, $default_action=null, $default_view="index")
	{
        $out = null;
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
			$view_class_name = "Module" . ucwords($class_name) ."View";
			$user_view = new $view_class_name();
			
			$registry = Registry::instance();
			$registry->set("view", $user_view);
			
			$out = $user_view->$view();
		}

        return $out;
	}
}

