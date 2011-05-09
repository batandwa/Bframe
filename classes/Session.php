<?php
class Session {
    private $_session;
    public $maxTime;
    private $db;
    private $table = "wl_sesssions";
    
	/**
	 * @var Request The singleton instance.
	 */
	private static $instance;
	
	/**
	 * Returns the singleton instance of this class.
	 *
	 * @return Request The singleton instance.
	 */
	public static function &instance()
	{
		if((!isset(self::$instance)))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
    
    private function __construct() {
        $this->maxTime['access'] = time();
        $this->maxTime['gc'] = 21600; //21600 = 6 hours

        //it is session handler
        session_set_save_handler(array($this,'_open'),
                array($this,'close'),
                array($this,'read'),
                array($this,'write'),
                array($this,'destroy'),
                array($this,'clean')
                );

        register_shutdown_function('session_write_close');

//        session_start();//SESSION START
    }
    
/*
    private function getDB() {
        $mysql_host = 'your_host';
        $mysql_user = 'user';
        $mysql_password = 'pass';
        $mysql_db_name = 'db_name';


        if (!isset($this->db)) {
            $this->db = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db_name);
            if (mysqli_connect_errno()) {
                printf("Error no connection: <br />%s\n", mysqli_connect_error());
                exit();
            }
        }
        
        

        return $this->db;
    }
*/

    // O_O !!!
    public function open() {
        return true;
    }


    public function close() {
        $this->_clean($this->maxTime['gc']);
    }

    public function read($id)  {     
//        $stmt= $this->getDB()->prepare("SELECT session_variable FROM table_sessions 
//                                            WHERE table_sessions.session_id = ?");
//        $stmt->bind_param('s',$id);
//        $stmt->bind_result($data);
//        $stmt->execute();
//        $ok = $stmt->fetch() ? $data : '';
//        $stmt->close();

		$sess_dat= new MySQLTable($this->table);
		$data = $sess_dat->select_first("session_id=" . $id);
		$data = is_array($data) || is_object($data) ? $data->session_data : "";
        return $data;
    }

    public function write($id, $data) {  
    	$sess_table = new MySQLTable($this->table);
    	$data = array("session_id" => $id, "session_data" => $data, "session_last_access" => $this->maxTime['access']);
    	return $sess_table->insert($data, true);
    	
//        $stmt = $this->getDB()->prepare("REPLACE INTO table_sessions (session_id, session_variable, session_access) VALUES (?, ?, ?)");
//        $stmt->bind_param('ssi', $id, $data, $this->maxTime['access']);
//        $ok = $stmt->execute();
//        $stmt->close();
//        return $ok;     
    }

    public function destroy($id) {
    	$sess_table = new MySQLTable($this->table);
    	return $sess_table->delete("session_id='" . $id . "'");
    	
//    $stmt=$this->getDB()->prepare("DELETE FROM table_sessions WHERE session_id = ?");
//    $stmt->bind_param('s', $id);
//    $ok = $stmt->execute();
//    $stmt->close();
//    return $ok;
    }

    public function clean($max) {
    	$old=($this->maxTime['access'] - $max);
    	$sess_table = new MySQLTable($this->table);
    	return $sess_table->delete("session_last_access='" . $old . "'");
    
//    $old=($this->maxTime['access'] - $max);
//    $stmt = $this->getDB()->prepare("DELETE FROM table_sessions WHERE session_access < ?");
//    $stmt->bind_param('s', $old);
//    $ok = $stmt->execute();
//    $stmt->close();
//    return $ok;
    }
    
    function get($var, $default=null)
    {
    	if(empty($var))
    	{
    		trigger_error('Emty "var" sent.'); 
    	}
    	
    	if(isset($_SESSION[$var]))
    	{
	    	return $_SESSION[$var];
    	}
    	return $default;
    }
    
    function set($var, $value)
    {
    	if(empty($var))
    	{
    		trigger_error('Emty "var" sent.'); 
    	}
    	if(empty($value))
    	{
    		trigger_error('Emty "value" sent.'); 
    	}
    	
    	$_SESSION[$var] = $value;
    }
    
    function remove($var)
    {
    	if(empty($var))
    	{
    		trigger_error('Emty "var" sent.'); 
    	}
    	
    	if(isset($_SESSION[$var]))
    	{
	    	unset($_SESSION[$var]);
    	}
    }
    
    function varset($var)
    {
    	if(empty($var))
    	{
    		trigger_error('Emty "var" sent.'); 
    	}
    	
    	if(isset($_SESSION[$var]))
    	{
	    	return true;
    	}
    	
    	return false;
    }
}
?>
