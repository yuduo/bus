<?php
class Session {
	private static $handler = null;
	private static $ip = null;
	private static $lifetime = null;
	private static $time = null;
	private static function init($handler) {
		self::$handler = $handler;
		self::$ip = ! empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 'unknown';
		self::$lifetime = ini_get('session.gc_maxlifetime');
		self::$time = time();
	}
	static function start(PDO $pdo) {
		self::init($pdo);
		session_set_save_handler(array(
				__CLASS__,
				"open" 
		), array(
				__CLASS__,
				"close" 
		), array(
				__CLASS__,
				"read" 
		), array(
				__CLASS__,
				"write" 
		), array(
				__CLASS__,
				"destroy" 
		), array(
				__CLASS__,
				"gc" 
		));
		
		session_start();
	}
	public static function open($path, $name) {
		return true;
	}
	public static function close() {
		return true;
	}
	public static function read($PHPSESSID) {
		$sql = "select PHPSESSID, update_time, client_ip, data from session where PHPSESSID= ?";
		
		$stmt = self::$handler->prepare($sql);
		
		$stmt->execute(array(
				$PHPSESSID 
		));
		
		if (! $result = $stmt->fetch(PDO::FETCH_ASSOC)){
			return '';
		}
		
		if (self::$ip != $result["client_ip"]){
			self::destroy($PHPSESSID);
			return '';
		}
		
		if (($result["update_time"] + self::$lifetime) < self::$time){
			self::destroy($PHPSESSID);
			return '';
		}
		
		return $result['data'];
	}
	public static function write($PHPSESSID, $data) {
		$sql = "select PHPSESSID, update_time, client_ip, data from session where PHPSESSID= ?";
		
		$stmt = self::$handler->prepare($sql);
		
		$stmt->execute(array(
				$PHPSESSID 
		));
		
		if ($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			if ($result['data'] != $data || self::$time > ($result['update_time'] + 30)){
				$sql = "update session set update_time = ?, data =? where PHPSESSID = ?";
				
				$stm = self::$handler->prepare($sql);
				$stm->execute(array(
						self::$time,
						$data,
						$PHPSESSID 
				));
			}
		}else{
			if (! empty($data)){
				$sql = "insert into session(PHPSESSID, update_time, client_ip, data) values(?,?,?,?)";
				
				$sth = self::$handler->prepare($sql);
				
				$sth->execute(array(
						$PHPSESSID,
						self::$time,
						self::$ip,
						$data 
				));
			}
		}
		
		return true;
	}
	public static function destroy($PHPSESSID) {
		$sql = "delete from session where PHPSESSID = ?";
		
		$stmt = self::$handler->prepare($sql);
		
		$stmt->execute(array(
				$PHPSESSID 
		));
		
		return true;
	}
	private static function gc($lifetime) {
		$sql = "delete from session where update_time < ?";
		
		$stmt = self::$handler->prepare($sql);
		
		$stmt->execute(array(
				self::$time - $lifetime 
		));
		
		return true;
	}
}
/**
 * 初始化
 */
try{
	
	// 定义数据库连接
	$dbhost = 'localhost';
	$dbname = 'aiche';
	$dbuser = 'root';
	$dbpasswd = 'rootbx';
	
	$pdo = new PDO("mysql:host=" . $dbhost . ";dbname=" . $dbname, $dbuser, $dbpasswd);
}catch(PDOException $e){
	echo $e->getMessage();
}

Session::start($pdo);


// Example of usage below the code

class session {

	// To permit the same session var being accessed
	// more than once at same time on different places.
	var $prefix;

	function Session() {
		session_start();
		$this->prefix = $_SERVER['HTTP_HOST'];

		if($this->get('flash')) {
			foreach($_SESSION[$this->prefix]['flash'] as $name=>$vals) {
				++$_SESSION[$this->prefix]['flash'][$name]['counter'];
				if($_SESSION[$this->prefix]['flash'][$name]['counter']>1) {
					unset($_SESSION[$this->prefix]['flash'][$name]);
				}
			}
		}
	}

	function get($session_var) {
		return ((isset($_SESSION[$this->prefix][$session_var])) ? $_SESSION[$this->prefix][$session_var] : false);
	}

	function get_cookie($cookie_name) {
		return ((isset($_COOKIE[$cookie_name])) ? $_COOKIE[$cookie_name] : false);
	}

	function set($session_var,$value) {
		$_SESSION[$this->prefix][$session_var] = $value;
	}

	function set_cookie($cookie_name,$value) {
		setcookie($cookie_name,$value,time()+3600*24,'/');
	}

	function del() {
		$session_vars = func_get_args();
		foreach($session_vars as $session_var) {
			if($this->get($session_var)||is_array($this->get($session_var))) {
				unset($_SESSION[$this->prefix][$session_var]);
			}
		}
	}

	function del_cookie() {
		$cookies = func_get_args();
		foreach($cookies as $cookie) {
			if($this->get_cookie($cookie)) {
				setcookie($cookie,'del',time()-3600,'/');
			}
		}
	}

	function flash($flash_var,$value) {
		$_SESSION[$this->prefix]['flash'][$flash_var] = array('val'=>$value,'counter'=>0);
	}

	function get_flash($flash_var) {
		return ((isset($_SESSION[$this->prefix]['flash'][$flash_var]['val'])) ? $_SESSION[$this->prefix]['flash'][$flash_var]['val'] : false);
	}

}
?>
