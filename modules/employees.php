<?php
class ModuleEmployees extends Module
{
	static public function execute($class_name = null, $default_action=null, $default_view=null)
	{
		$class_name;$default_action;$default_view;
		$function = __FUNCTION__;
		parent::$function("employees");
	}
}

class ModuleEmployeesView extends Base
{
	public function index()
	{
		return $this->employees();
	}
	
	public function leave_history()
	{
		$emp_id = Request::get("emp_id", "get", "int"); 
		$helper = new ModuleEmployeesHelper();
		$history = $helper->emp_leave_history($emp_id);
		$history_list = $history->get_data();
		$data_present = !empty($history_list);
		$emp = $helper->emp_details($emp_id);
?>
  <h2>Leave History</h2>
  <p>
  	Past taken leave by <span class="employee_name"><?php echo $emp->first_name . " " . $emp->last_name ?></span>.
  </p>
  <div class="quick_links">
   	<a class="icon icon_employees" href="<?php echo generate_url(array(), array("module"=>"employees", "view"=>"employees")) ?>">View employees</a>
   	<a class="icon icon_add" href="<?php echo generate_url(array(), array("module"=>"leave", "view"=>"edit", "emp_id"=>$emp_id)) ?>">Add leave</a>
  </div>
  <table class="leave_history hover_hilite_row zebra">
    <thead>
      <tr>
        <th class="noborder">&nbsp;</th>
        <th class="noborder">&nbsp;</th>
        <th class="minimalwidth leave_type">Type</th>
        <th class="minimalwidth date">Start Date</th>
        <th class="minimalwidth date">End Date</th>
        <th class="minimalwidth">Duration</th>
        <th class="col_describ">Reason</th>
      </tr>
      </thead>
      
<?php if($data_present) { ?>

	<?php 
		$even = true;
		foreach ($history_list as $leave) {
			$even = !$even; 
	?>
		<tbody>
		
		<tr class="<?php echo $even ? "even row_norm" : "row_alt" ?>">
			<td>
				<a class="icon icon_delete" href="<?php echo generate_url(array(), array("module"=>"leave", "view"=>"unpublish", "leave_id"=>$leave->leave_id)) ?>"><?php echo Text::translate("Delete")?></a>
			</td>
			<td>
				<a class="icon icon_edit" href="<?php echo generate_url(array(), array("module"=>"leave", "view"=>"edit", "leave_id"=>$leave->leave_id)) ?>"><?php echo Text::translate("Edit")?></a>
			</td>
			<td><?php echo $leave->type_name ?></td>
			<td><?php echo $leave->start_date ?></td>
			<td><?php echo $leave->end_date ?></td>
			<td class="number"><?php echo $leave->duration ?></td>
			<td><?php echo $leave->reason ?></td>
		</tr>
    <?php } ?>
      
      </tbody>
<?php } ?>
    </table>

<?php if(!$data_present) { ?>
	<p><?php echo Text::translate("No past history of leave taken.") ?></p>
<?php } ?>

<?php
	}
	
	public function employees()
	{
?>
<h2>Employees</h2>
<div class="quick_links">
	<a class="icon icon_add" href="index.php?module=employees&view=edit">Add employee</a>
</div>
<?php
	$helper = new ModuleEmployeesHelper();
	$emp_list = $helper->emp_list();
	$msg = '';
?>

<table class="employees zebra hover_hilite_row">
    <thead>
      <tr>
        <th class="noborder minimalwidth">&nbsp;</th>
        <th class="noborder minimalwidth">&nbsp;</th>
        <th class="noborder minimalwidth">&nbsp;</th>
        <th class="">First Name</th>
        <th class="">Last Name</th>
        <th class="minimalwidth">General Leave</th>
        <th class="minimalwidth">Sick</th>
        <th class="minimalwidth">Other</th>
        <th class="minimalwidth">Total</th>
        <th class="noborder minimalwidth">&nbsp;</th>
        <th class="noborder minimalwidth">&nbsp;</th>
      </tr>
      </thead>
      
		<?php 
			if($emp_list->count() == 0) { 
				$msg = '<p>No employees found</p>'; 
			} else {
		?>
      
      <tbody>
      	<?php
	      		$emp_list->rewind();
				$emp_entry = $emp_list->current();
				$infinite = 0;
				$even_row = true;
				while($emp_entry !== false) {
					$even_row = !$even_row;
//					if($infinite > 100) break;
//					$infinite++;
					$total_days_taken = $emp_entry->type_1 + $emp_entry->type_2 + $emp_entry->type_3;
      	?>
      <tr class="<?php echo $even_row ? "even row_norm" : "row_alt" ?>">
         <td><a class="icon icon_edit" href="<?php echo generate_url(array("module", "view"), array("module"=>"employees", "view"=>"edit", "emp_id"=>$emp_entry->emp_id)) ?>">Edit</a></td>
         <td><a class="icon icon_delete" href="<?php echo generate_url(array("module", "view"), array("module"=>"employees", "view"=>"delete", "emp_id"=>$emp_entry->emp_id)) ?>">Delete</a></td>
         <td><?php echo $emp_entry->emp_id ?></td>
        <td><?php echo $emp_entry->first_name ?></td>
        <td><?php echo $emp_entry->last_name ?></td>
        <td class="number"><?php echo $emp_entry->type_3 == 0 ? "" : $emp_entry->type_3 ?></td>
        <td class="number"><?php echo $emp_entry->type_1 == 0 ? "" : $emp_entry->type_1 ?></td>
        <td class="number"><?php echo $emp_entry->type_2 == 0 ? "" : $emp_entry->type_2 ?></td>
        <td class="number"><?php echo ($total_days_taken) ?></td>
        <td><a class="icon icon_add" href="<?php echo generate_url(array("module", "view"), array("module"=>"leave", "view"=>"edit", "emp_id"=>$emp_entry->emp_id)) ?>">Insert leave data</a></td>
        <td><a class="icon icon_history" href="<?php echo generate_url(array(), array("module"=>"employees", "view"=>"leave_history", "emp_id"=>$emp_entry->emp_id)) ?>">View history</a></td>
      </tr>
      
		<?php
					$emp_entry = $emp_list->next();
				}
		?>
      
      </tbody>
		<?php
			}
		?>
    </table>
    <?php echo $msg ?>
    <div id="emp_list_paging" class="paging nodisplay"><a href="#" title="First">&lt;&lt;</a> <a href="#" title="Previous page">&lt;</a> <a href="#">5</a> <a href="#">6</a> <a href="#">7</a> <a href="#">8</a> <a href="#">9</a> <a href="#" class="active">10</a> <a href="#">11</a> <a href="#">12</a> <a href="#" title="Page 13">13</a> <a href="#" title="Next page">&gt;</a> <a href="#" title="Last page">&gt;&gt;</a></div>
<?php 
	}
	
	public function delete()
	{
		$emp = new MySQLTable("employee");
		$emp_id = Request::get("emp_id", "get");
		$emp->select_first("emp_id=" . $emp_id);
		
		
?>
		<h2>Delete employee</h2>
		<p>Do you reall want to delete <?php echo $emp->get("first_name") . " " . $emp->get("last_name") ?> from the system?</p>
		<p>
			<a class="icon icon_delete" href="<?php echo generate_url(array("module", "view", "action"), array("module"=>"employees", "emp_id"=>$emp->get("emp_id"), "action"=>"unpublish")) ?>">Yes, delete the employee</a><br />
			<a class="icon icon_back" href="<?php echo generate_url(array("module", "view", "action"), array("module"=>"employees")) ?>">No, return to employee listing</a>
		</p>
<?php 
	}
	
	public function edit()
	{
		$helper = new ModuleEmployeesHelper();
		$emp_edit_form = $helper->emp_edit_form();
		$emp_id = Request::get("emp_id", "get", null, "int");
		
		$emp_edit_form_request = new FormRequest("emp_edit_form", "post");
		
		$first_name = null;
		$last_name = null;
		$display_name = null;
		$email = null;
		
		if(!is_null($emp_edit_form_request->get("form_sent")))
		{
			$first_name = $emp_edit_form_request->get("first_name");
			$last_name = $emp_edit_form_request->get("last_name");
			$display_name = $emp_edit_form_request->get("display_name");
			$email = $emp_edit_form_request->get("email");
		}
		else if(!empty($emp_id))
		{
			$emp = new MySQLTable("employee");
			$emp->select_first("emp_id=" . $emp_id);
			$first_name = $emp->get("first_name");
			$last_name = $emp->get("last_name");
			$display_name = $emp->get("display_name");
			$email = $emp->get("email");
				
			assert($emp_id == $emp->get("emp_id"));
		}
?>
		<h2><img border="0" align="middle" src="images/icons/icon_cms2.jpg"><?php echo empty($emp_id) ? "New" : "Edit"?> Employee</h2>
		<div class="form-container">
			
			<form method="post" id="emp_edit_form" action="<?php echo generate_url(array("action"), array("action"=>"save")) ?>">
				
				<p class="legend"><strong>Note:</strong> Required = <em>*</em></p>
				
				<fieldset>
					<legend>Employee Details</legend>
					<div class="row_norm">
						<label for="first_name">First Name <em>*</em></label>
						<input type="text" size="50" value="<?php echo ($first_name) ?>" name="first_name" id="first_name" /></div>
					<div class="row_alt">
						<label for="last_name">Last Name <em>*</em></label>
						<input type="text" size="50" value="<?php echo ($last_name) ?>" name="last_name" id="last_name" />
					</div>
					<div class="row_norm">
						<label for=display_name>Display Name <em>*</em></label>
						<input type="text" size="50" value="<?php echo ($display_name) ?>" name="display_name" id="display_name" />
					</div>
					<div class="row_alt">
						<label for="email">Email Address <em>*</em></label>
						<input type="text" size="50" value="<?php echo ($email) ?>" name="email" id="email" />
					</div>
				</fieldset>
				
				
				<div class="buttonrow">
					<input type="submit" class="button toolbar" value="Save" />
					<input type="button" class="button toolbar" value="Discard" onclick="go('<?php echo generate_url(array(), array("view"=>"employees")) ?>')" />
				</div>
				
				
				<input type="hidden" name="form_sent" value="1" />
				<?php if(!empty($emp_id)) { ?>
					<input type="hidden" name="emp_id" value="<?php echo $emp_id ?>" />
				<?php } ?>
			</form>
			
		</div>
<?php 
	}
}

class ModuleEmployeesAction extends Base
{
	public function save()
	{
		global $debug;
		
		$emp_edit_form = new FormRequest("emp_edit_form", "post");
		$emp = new MySQLTable("employee");
		$db =& DbConnection::instance();
		$notifications =& Notification::instance();
		$helper = new ModuleEmployeesHelper();
		
		$first_name = $emp_edit_form->get("first_name");
		$last_name = $emp_edit_form->get("last_name");
		$display_name = $emp_edit_form->get("display_name");
		$email = $emp_edit_form->get("email");
		
		$first_name = trim($first_name);
		$last_name = trim($last_name);
		$display_name = trim($display_name);
		$email = trim($email);

		$validation = new Validation();
		$validation->attach($first_name, Validation::VALID_TYPE_ALPHA, Text::translate("Please specify a valid first name."));
		$validation->attach($last_name, Validation::VALID_TYPE_ALPHA, Text::translate("Please enter a valid last name."));
		$validation->attach($display_name, Validation::VALID_TYPE_ALPHA, Text::translate("Please enter a valid display name."));
		$validation->attach($email, Validation::VALID_TYPE_EMAIL, Text::translate("Please enter a valid email address."));

		if($emp_edit_form->exists("emp_id"))
		{
			$id = $emp_edit_form->get("emp_id");
			$validation->attach($id, Validation::VALID_TYPE_NUMERIC, Text::translate("Invalid id."));
		}

		$validates = $validation->validate();

		if(!$validates)
		{
			$msgs = $validation->getErrors();
			foreach($msgs as $msg)
			{
				$notifications->addNotification($msg, Notification::NOTIF_ERROR);
			}
		}
		else
		{
			$emp->bind($emp_edit_form->getData(), array("form_sent", "id", "img_header_logo", "MAX_FILE_SIZE", "img_home_banner_reset", "img_header_logo_reset", "img_header_logo_wap_reset"));
			$emp->fix_nulls(array("first_name", "last_name", "display_name", "email"));
			$emp->escape(array("first_name", "last_name", "display_name", "email"));
			$emp->quote_data(array("first_name", "last_name", "display_name", "email"));
				
			$msg_format = "";
			if($emp_edit_form->exists("emp_id"))
			{
				$saved = $emp->update("emp_id=" . $id);
				$err_no = $db->getLastErrorCode();
				$msg_format = "Employee  %1\$s %2\$s updated successfully.";

				$success = $saved ? 1 : 0;
				$helper->save_trail($id, AUDIT_ACT_UPDATE, $success);
			}
			else
			{
				$saved = $emp->insert();
				$err_no = $db->getLastErrorCode();
				$msg_format = "Employee %1\$s %2\$s added successfully.";
				$id = $db->last_insert_id();

				$success = $saved ? 1 : 0;
				$helper->save_trail($id, AUDIT_ACT_ADD, $success);
			}
		
			if($saved)
			{
				$msg = sprintf($msg_format, $first_name, $last_name);
				$notifications->addNotification(Text::translate($msg), Notification::NOTIF_SUCCESS);
		
				$response =& Response::instance();
				$redirect_url = generate_url(array("action", "view"), array(), false, true, true);
				$response->redirect($redirect_url);
			}
			else
			{
				if($err_no == "1062")
				{
					$msg_format = "Error adding employee %s %s. This domain already exists. <br />If this DNS is not in the main list please check the trash and restore it from there.";
					if($debug)
					{
						$msg_format .= '<br />' . $db->getLastError();
					}
					$msg = sprintf($msg_format, $first_name, $last_name);
					$notifications->addNotification(Text::translate($msg), Notification::NOTIF_ERROR);
				}
				else
				{
					$msg_format = "Error adding employee %s %s.";
					if($debug)
					{
						$msg_format .= '<br />' . $db->getLastError();
					}
					$msg = sprintf($msg_format, $first_name, $last_name);
					$notifications->addNotification(Text::translate($msg), Notification::NOTIF_ERROR);
				}
			}
			
		}
		
	}

	public function upload_images($id)
	{
		$notification = Notification::instance();
		$helper = new ModuleEmployeesHelper();

		if(!is_numeric($id))
		{
			trigger_error("Non numeric id passed.");
			return false;
		}

		$emp_edit_form = new FormRequest("dnsedit", "files");

		assert($emp_edit_form->files_posted() !== false); //Indicates error
		if($emp_edit_form->files_posted() == 0)
		{
			return 0;
		}

		$target_dir = WL_RES_DIR . $id . DS . "images" . DS;
		$emp_edit_form->set_all_upload_targets($target_dir);
		$allowed_types = explode(",", WL_IMG_ALLOWED_TYPES);
		foreach($allowed_types as $allowed_type_id => $allowed_type_val)
		{
			$allowed_types[$allowed_type_id] = image_type_to_mime_type($allowed_type_val);
		}
		
		$emp_edit_form->set_upload_types($allowed_types);
		$emp_edit_form->set_upload_filename("img_home_banner", "home_banner");
		$emp_edit_form->set_upload_filename("img_header_logo", "header_logo");
		$emp_edit_form->set_upload_filename("img_header_logo_wap", "header_logo_wap");
		$emp_edit_form->set_auto_add_upload_ext(true);

		$upload_fields = array("img_home_banner", "img_header_logo", "img_header_logo_wap");
		$upload_img_sizes = array(WL_IMG_HOME_BANNER_SIZE, WL_IMG_HEADER_LOGO_SIZE, WL_IMG_HEADER_LOGO_WAP_SIZE);
		$upload_img_name = array(Text::translate("Home Banner Image"), Text::translate("Header Logo Image"), Text::translate("WAP Header Logo Image"));

		foreach($upload_fields as $upl_fld_id => $upl_fld_name)
		{
			$tmp_name = $emp_edit_form->get($upl_fld_name, "tmp_name");
			if(empty($tmp_name))
			{
				continue;
			}
				
			$img_size = getimagesize($tmp_name);
			$size_correct = true;

			if($img_size !== false)
			{
				$img_size_target = explode("x", $upload_img_sizes[$upl_fld_id]);
				assert(count($img_size_target) == 2);

				$msg = "";
				if($img_size_target[0] != $img_size[0])
				{
					$size_correct = false;
				}
				if($size_correct && $img_size_target[1] != $img_size[1])
				{
					$size_correct = false;
				}

				if(!$size_correct)
				{
					$msg = $upload_img_name[$upl_fld_id] . Text::translate(" size incorrect. ");
					$notification->addNotification($msg);
					return false;
				}
			}
				
		}

		$home_banner_error = $emp_edit_form->get("img_home_banner", "error");
		$header_logo_error = $emp_edit_form->get("img_header_logo", "error");
		$header_logo_wap_error = $emp_edit_form->get("img_header_logo_wap", "error");

		if($home_banner_error != 4)
		{
			$helper->backup_images($id, WL_IMG_HOME_BANNER);
		}
		if($header_logo_error != 4)
		{
			$helper->backup_images($id, WL_IMG_HEADER_LOGO);
		}
		if($header_logo_wap_error != 4)
		{
			$helper->backup_images($id, WL_IMG_HEADER_LOGO_WAP);
		}
		
		$uploads = $emp_edit_form->upload_all();
		if($uploads === false)
		{
			$helper->restore_images($id, WL_IMG_HOME_BANNER);
		}
		else
		{
			$helper->delete_images($id, WL_IMG_HOME_BANNER, true);
		}

		return $uploads;
	}

	public function unpublish()
	{
		global $debug;
		
		$emp_id = Request::get("emp_id", "get");
		$notifications =& Notification::instance();
		$helper = new ModuleEmployeesHelper();
		$db =& DbConnection::instance();

		$validation = new Validation();
		$validation->attach($emp_id, Validation::VALID_TYPE_NUMERIC, Text::translate("Invalid id."));
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

		$emp = new MySQLTable("employee");
		$emp->set("datarowstate", "4");
		$updated = $emp->update("emp_id=" . $emp_id);

		if($updated)
		{
			$helper->save_trail($emp_id, AUDIT_ACT_UNPUB, 1);
			$msg = "Employee deleted.";
			$notifications->addNotification($msg, Notification::NOTIF_SUCCESS);
		}
		else
		{
			$helper->save_trail($id, AUDIT_ACT_UNPUB, 0);
			$msg = "Error deleting employee.";
			if($debug)
			{
				$msg .= '<br />' . $db->getLastError();
			}
			$notifications->addNotification($msg, Notification::NOTIF_ERROR);
		}
	}

	public function delete()
	{
		$emp_id = Request::get("emp_id", "get");
		$notifications =& Notification::instance();
		$helper = new ModuleEmployeesHelper();

		$validation = new Validation();
		$validation->attach($emp_id, Validation::VALID_TYPE_NUMERIC, Text::translate("Invalid id."));
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

		$emp = new MySQLTable("employee");
		$deleted = $emp->delete("emp_id=" . $emp_id);

		if($deleted)
		{
			$helper->save_trail($emp_id, AUDIT_ACT_DELETE, 1);
			$msg = "Employee deleted.";
			$notifications->addNotification($msg, Notification::NOTIF_SUCCESS);
		}
		else
		{
			$helper->save_trail($emp_id, AUDIT_ACT_DELETE, 0);
			$msg = "Error deleting employee.";
			$notifications->addNotification($msg, Notification::NOTIF_ERROR);
		}
	}

	public function publish()
	{
		global $debug;
		$db =& DbConnection::instance();
		$notifications =& Notification::instance();
		$response =& Response::instance();
		$helper = new ModuleEmployeesHelper();

		$id = Request::get("id", "get");

		$validation = new Validation();
		$validation->attach($id, Validation::VALID_TYPE_NUMERIC, Text::translate("Invalid id."));
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

		$emp = new MySQLTable("employee");
		$emp->set("datarowstate", "1");
		$updated = $emp->update("id=" . $id);

		if($updated)
		{
			$helper->save_trail($id, AUDIT_ACT_PUB, 1);
			$msg = "Employee restored.";
			$notifications->addNotification($msg, Notification::NOTIF_SUCCESS);
			$response->redirect(generate_url(array("view", "action"), array("view" => "dnslist"), false, true, true));
		}
		else
		{
			$helper->save_trail($id, AUDIT_ACT_PUB, 0);
			$msg = "Error restoring employee.";
			if($debug)
			{
				$msg .= '<br />' . $db->getLastError();
			}
			$notifications->addNotification($msg, Notification::NOTIF_ERROR);
		}
	}
}

class ModuleEmployeesHelper
{
	public function emp_list()
	{
		// TODO : Recreate this query to fetch all the leave types even those
		//        that are added later.
		$query = "SELECT `e`.`emp_id`," .
				"        SUM(IF(l.type_id = 1, l.duration, 0)) AS `type_1`," .
				"        SUM(IF(l.type_id = 2, l.duration, 0)) AS `type_2`," .
				"        SUM(IF(l.type_id = 3, l.duration, 0)) AS `type_3`," .
				"        `e`.`first_name`," .
				"        `e`.`last_name`" .
				"   FROM    `leave` `l`" .
				"        RIGHT OUTER JOIN" .
				"           employee `e`" .
				"        ON (`l`.`emp_id` = `e`.`emp_id`)" .
				"  WHERE `e`.`datarowstate` = 1" .
				" GROUP BY `e`.`emp_id`";
		$query = "SELECT `e`.`emp_id`," .
				"        SUM(IF(l.type_id = 1, l.duration, 0)) AS `type_1`," .
				"        SUM(IF(l.type_id = 2, l.duration, 0)) AS `type_2`," .
				"        SUM(IF(l.type_id = 3, l.duration, 0)) AS `type_3`," .
				"        `e`.`first_name`," .
				"        `e`.`last_name`" .
				"   FROM    (SELECT *" .
				"              FROM `leave`" .
				"             WHERE datarowstate = 1) l" .
				"        RIGHT OUTER JOIN" .
				"           employee `e`" .
				"        ON (`l`.`emp_id` = `e`.`emp_id`)" .
				"  WHERE `e`.`datarowstate` = 1" .
				" GROUP BY `e`.`emp_id`";

		
		
		$db =& DbConnection::instance();
		$emp_list = $db->query_recordset($query);

		return $emp_list;
	}
	
	public function emp_edit_form()
	{
		$request =& Request::instance();
		
		$first_name = Request::get("first_name", "post");
		
		$emp_edit_form = new Form();
		$emp_edit_form->setMethod("post");
		$emp_edit_form->setAction(generate_url(array("action"), array("action" => "save"), true));
		$emp_edit_form->setId("dnsedit");
		
		$emp_edit_form = new FormInputText();
		$emp_edit_form->setLabel("First Name");
		$emp_edit_form->setName("first_name");
		$emp_edit_form->setId("first_name");
		$emp_edit_form->setValue($first_name);
		
		
		return $emp_edit_form;
	}
	
	/**
	 * Gets the specified employees' leave history
	 *
	 * @param int $emp_id The employee id
	 * @return MySQLRecordSet
	 */
	public function emp_leave_history($emp_id)
	{
		require_once('modules' . DS . 'leave.php');
		$leave_helper = new ModuleLeaveHelper();
		$leave_type_list = $leave_helper->leave_types_list();
		
		// TODO: Modify the query so the sum..if parts in the select are
		//       done dynamically from the $leave_type_list. 
		$where_clause_emp_id = !is_null($emp_id) ? "  WHERE l.emp_id = " . $emp_id . " AND " : "  WHERE ";
		$query = "SELECT l.*," .
				"        SUM(IF(l.type_id = 1, 1, 0)) AS type_1," .
				"        SUM(IF(l.type_id = 2, 1, 0)) AS type_2," .
				"        SUM(IF(l.type_id = 3, 1, 0)) AS type_3," .
				"        t.type_name" .
				"   FROM `leave` l" .
				"        LEFT OUTER JOIN" .
				"           type t" .
				"        ON l.type_id = t.type_id" .
				$where_clause_emp_id .
				"       l.datarowstate = 1 " .
				" GROUP BY l.emp_id, l.leave_id";
				
		$db =& DbConnection::instance();
		$list = $db->query_recordset($query);

		return $list;
	}
	
	/**
	 * Retreives employee details
	 *
	 * @param int $emp_id The employee id
	 * @return mixed
	 */
	public function emp_details($emp_id)
	{
		$where_clause_emp_id = !is_null($emp_id) ? "  WHERE `e`.`emp_id` = " . $emp_id : "";
		$query = "SELECT `e`.*" .
				"   FROM employee `e`" .
				$where_clause_emp_id;

		$db =& DbConnection::instance();
		if(empty($where_clause_emp_id))
		{
			$list = $db->query_recordset($query);
		}
		else 
		{
			$list = $db->query_first($query);
		}

		return $list;
	}

	public function save_trail($client, $action, $success=false)
	{
//		if(!is_int($success) && !is_bool($success))
//		{
//			trigger_error("\$success has to be an integer or bool.");
//		}
//
//		$sess =& Session::instance();
//		$trail = new MySQLTable("trail");
//
//		if(is_bool($success))
//		{
//			$success = $success ? 1 : 0;
//		}
//
//		$data = array("admin_id" => $sess->get("user_id"), "client_id" => $client, "action" => $action, "timestamp" => "NOW()", "successful" => $success);
//		$trail->bind($data);
//		$trail->quote_data(array("action"));
//		
//		return $trail->insert();
	}
}
