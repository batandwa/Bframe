<?php
class ModuleLeave extends Module
{
	static public function execute($class_name = null, $default_action=null, $default_view=null)
	{
		$function = __FUNCTION__;
		parent::$function("leave");
	}
}

class ModuleLeaveView extends Base
{
	public function index()
	{
	}
	
	public function edit()
	{
		$emp_id = Request::get("emp_id", "get");
		$emp_id = is_null($emp_id) ? Request::get("emp_id", "post") : $emp_id;
		$leave_id = Request::get("leave_id", "get");
		$leave_id = is_null($leave_id) ? Request::get("leave_id", "get") : $leave_id;
		$helper = new ModuleLeaveHelper();
		$leave_types = $helper->leave_types_list();
//		$leave_types_list = $leave_types->get_elements();
		$leave_types_list = $leave_types;
		$leave = new MySQLTable("leave");
		$leave->select_first("leave_id=" . $leave_id);
		
		$start_date = Request::get("start_date", "post");
		$end_date = Request::get("end_date", "post");
		$duration = Request::get("duration", "post");
		$type_id = Request::get("type_id", "post");
		$reason = Request::get("reason", "post");
		
		$start_date = $leave->get("start_date");
		$end_date = $leave->get("end_date");
		$duration = $leave->get("duration");
		$type_id = $leave->get("type_id");
		$reason = $leave->get("reason");

		fb($start_date);
		fb($start_date);
		fb($end_date);
		fb($duration);
		fb($type_id);
		fb($reason);
?>
	<h2><img border="0" align="middle" src="images/icons/icon_cms2.jpg"><?php echo is_null($leave_id) ? "New Leave" : "Edit Leave" ?></h2>
	<div class="form-container">
	
		<div class="errors nodisplay">
			<p><em>Oops... the following errors were encountered:</em></p>
			<ul>
				<li>Start date cannot be empty</li>
				<li>End date cannot be empty</li>
			</ul>
			<p>Data has <strong>not</strong> been saved.</p>
		</div>
	
		<form method="post" action="<?php echo generate_url(array(), array("module"=>"leave", "action"=>"save", "view"=>"edit")) ?>" id="leave_edit">
		
		<p class="legend"><strong>Note:</strong> Required = <em>*</em></p>
		
		<fieldset>
			<legend>Leave Details</legend>
			<div class="row_norm">
				<label class="date" for="start_date">Start Date <em>*</em></label>
				<input type="text" class="" value="<?php echo $start_date ?>" name="start_date" id="start_date" />
				<a href="#" rel="start_date" onclick="displayDatePicker(this.rel, false, 'ymd', '-'); return false;">
					<img class="date_picker" value="select" src="images/btn_calendar.gif" />
				</a>
				<input type="image" class="date_picker nodisplay" value="select" src="images/btn_calendar.gif" />
			</div>
			
			<div class="row_alt">
				<label class="date" for="end_date">End Date <em>*</em></label>
				<input type="text" class="" value="<?php echo $end_date ?>" name="end_date" id="end_date" />
				<a href="#" rel="end_date" onclick="displayDatePicker(this.rel, false, 'ymd', '-'); return false;" >
					<img class="date_picker" value="select" src="images/btn_calendar.gif" />
				</a>
				<input type="image" class="date_picker nodisplay" value="select" src="images/btn_calendar.gif" onclick="displayDatePicker('start_date', false, 'ymd', '.'); return false;" />
			</div>
			
			<div class="row_norm">
				<label class="date" for="duration">Duration <em>*</em></label>
				<input type="text" class="" value="<?php echo $duration ?>" name="duration" id="duration" />
			</div>
			
			<div class="row_alt">
				<label class="date" for="type_id">Leave Type <em>*</em></label>
				<?php echo generate_html_select("type_id", $leave_types_list, $type_id) ?>
			</div>
			
			
			<div class="row_norm">
				<label for="reason">Reason <em>*</em></label>
				<textarea rows="5" cols="40" name="reason" id="reason"><?php echo $reason ?></textarea>
			</div>
	
		</fieldset>
		
		<div class="buttonrow ">
			<input type="hidden" name="emp_id" value="<?php echo $emp_id ?>">
			<?php if(!is_null($leave_id)) { ?>
				<input type="hidden" name="leave_id" value="<?php echo $leave_id ?>">
			<?php } ?>
			<input type="hidden" name="form_sent" value="1">
			<input type="hidden" name="form_sent" value="1">
			<input type="submit" class="button toolbar" value="Save">
			<input type="button" class="button toolbar" value="Discard" onclick="goBack(1)">
		</div>

	</form>
	
	</div>
<?php
	}
	
	public function unpublish()
	{
		$leave_id = Request::get("leave_id", "get");
		$leave = new MySQLTable("leave");
		$leave->select_first("leave_id=" . $leave_id);
		
		$emp_id = $leave->get("emp_id");
		$emp = new MySQLTable("employee");
		$emp->select_first("emp_id=" . $emp_id);
?>
		<h2><?php echo Text::translate("Remove Leave") ?></h2>
		<p>
			Do you reall want to delete the leave for <?php echo $emp->get("first_name") . " " . $emp->get("last_name") ?>, 
			starting <?php echo $leave->get("start_date") ?>, ending <?php echo $leave->get("end_date") ?> from the system?</p>
		<p>
			<a class="icon icon_delete" href="<?php echo generate_url(array(), array("module"=>"leave", "emp_id"=>$emp->get("emp_id"), "action"=>"unpublish")) ?>">Yes, remove the leave</a><br />
			<a class="icon icon_back" href="<?php echo generate_url(array(), array("module"=>"employees", "view"=>"leave_history", "emp_id"=>$emp_id)) ?>">No, return to leave listing</a>
		</p>
<?php
	}
}

class ModuleLeaveAction extends Base
{
	public function save()
	{
		$leave_req = new FormRequest("leave_edit", "post");
		$validator = new Validation();
		$notifications =& Notification::instance();
		$leave = new MySQLTable("leave");
		$response =& Response::instance();
		$db =& DbConnection::instance();
		
		$leave_id = $leave_req->get("leave_id");
		$start_date = $leave_req->get("start_date");
		$end_date = $leave_req->get("end_date");
		$duration = $leave_req->get("duration");
		$reason = $leave_req->get("reason");
		$emp_id = $leave_req->get("emp_id");
		$type_id = $leave_req->get("type_id");
		
		$validator->attach($start_date, Validation::VALID_TYPE_DATE, "Invalid start date or date format.");
		$validator->attach($end_date, Validation::VALID_TYPE_DATE, "Invalid end date or date format.");
		$validator->attach($duration, Validation::VALID_TYPE_NUMERIC, "Invalid duration specified.");
		$validator->attach($reason, Validation::VALID_TYPE_REQUIRED, "Please specify a reason.");
		$validator->attach(array("values"=>$type_id,"min"=>1,"max"=>3), Validation::VALID_TYPE_RANGE, "Please select a type.");
		$validator->attach(array("values"=>array($start_date,$end_date),"data_type"=>Validation::VALID_ORDERING_DATE, "direction"=>Validation::VALID_ORDER_ASC), Validation::VALID_TYPE_ORDERING, "The start date has been set to be after the end date.");
		
		if($leave_req->exists("leave_id"))
		{
			$validator->attach($leave_id, Validation::VALID_TYPE_NUMERIC, "Invalid leave ID.");
		}
		
		$valid = $validator->validate();
//		echo '<pre style="background-color:white">'; var_dump($valid); echo '</pre>';
//		die(":: ".__FILE__." (".__LINE__.") Batandwa 19 Aug 2010 1:58:09 PM");
		if(!$valid)
		{
			$msgs = $validator->getErrors();
			foreach($msgs as $msg)
			{
				$notifications->addNotification($msg, Notification::NOTIF_ERROR);
			}
		}
		else
		{
			$leave_data = $leave_req->getData();
			unset($leave_data["form_sent"]);
			$leave->bind($leave_data);
			
			$leave_frm_fields = $leave->get_data();
			$leave_frm_fields = array_keys($leave_frm_fields);
			$leave->fix_nulls($leave_frm_fields);
			
			$leave_frm_fields = $leave->get_data();
			unset($leave_frm_fields["duration"]);
			unset($leave_frm_fields["emp_id"]);
			unset($leave_frm_fields["type_id"]);
			if(isset($leave_frm_fields["leave_id"]))
			{
				unset($leave_frm_fields["leave_id"]);
			}
			$leave_frm_fields = array_keys($leave_frm_fields);
			$leave->escape($leave_frm_fields);
			
			$leave->quote_data($leave_frm_fields);
			
			$saved = false;
			if($leave_req->exists("leave_id"))
			{
				$saved = $leave->update("leave_id=" . $leave_id);
			}
			else 
			{
				$saved = $leave->insert();
				$leave_id = $db->last_insert_id();
			}
			
			if($saved)
			{
				$response->redirect(generate_url(array(), array("module"=>"employees", "view"=>"leave_history", "emp_id"=>$emp_id)));
				$notifications->addNotification("Saved leave for employee from " . $start_date . " to " . $end_date . ".", Notification::NOTIF_SUCCESS);
			}
			else 
			{
				$notifications->addNotification("Could not save leave.", Notification::NOTIF_ERROR);
			}
		}
	}
	
	public function unpublish()
	{
		global $debug;
		
		$leave_id = Request::get("leave_id", "get");
		$notifications =& Notification::instance();
		$helper = new ModuleLeaveHelper();
		$db =& DbConnection::instance();
		$response =& Response::instance();

		$validation = new Validation();
		$validation->attach($leave_id, Validation::VALID_TYPE_NUMERIC, Text::translate("Invalid id."));
		$validates = $validation->validate();

		if(!$validates)
		{
			$msgs = $validation->getErrors();
				
			foreach($msgs as $msg)
			{
				$notifications->addNotification($msg, Notification::NOTIF_ERROR);
			}
			return;
		}

		$leave = new MySQLTable("leave");
		$leave->set("datarowstate", "4");
		$updated = $leave->update("leave_id=" . $leave_id);
		$leave->select_first("leave_id=" . $leave_id);

		if($updated)
		{
			$msg = "Leave removed.";
			
			$response->redirect(generate_url(array(), array("module"=>"employees", "view"=>"leave_history", "emp_id"=>$leave->get("emp_id"))));
			$notifications->addNotification($msg, Notification::NOTIF_SUCCESS);
		}
		else
		{
			$msg = "Error removing leave.";
			if($debug)
			{
				$msg .= '<br />' . $db->getLastError();
			}
			$notifications->addNotification($msg, Notification::NOTIF_ERROR);
		}
	}
}

class ModuleLeaveHelper
{
	public function leave_types_list()
	{
		$query = "SELECT t.type_name, t.type_id" .
				"   FROM type t" .
				"  WHERE t.datarowstate = 1";
		$db =& DbConnection::instance();
		$list = $db->query_recordset($query);
//		$list = $db->query_list($query);
		
		$assoc_list = array();
		foreach($list as $item)
		{
			$assoc_list[$item->type_id] = $item->type_name;
		}

		return $assoc_list;
	}
	
	public function emp_leaves($emp_id)
	{
		$query = "SELECT `l`.*" .
				"   FROM `leave` `l`" .
				"  WHERE l.emp_id = " . $emp_id;
		
		if(!is_numeric($emp_id))
		{
			trigger_error("\$emp_id must be an intger.");
		}
	
		$db =& DbConnection::instance();
		$list = $db->query_recordset($query);
		
		return $list;
	}
}
