<?php

class LocalLog
{
	private static $instance = null;
	private $mode = 0;
	private $logger = 'default';

	public static function Init()
	{
		if (!self::$instance instanceof self) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function setMode($mode)
	{

		self::$instance->mode = $mode;
	}

	public static function time()
	{
		list($msec, $sec) = explode(' ', microtime());
		$msectime = (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

		self::$instance->write('info', "Current_time", $msectime);

		return $msectime;
	}

	public static function INFO($title, $msg)
	{
		self::$instance->write('info', $title, $msg);
	}

	public static function SUCCESS($title, $msg)
	{
		self::$instance->write('success', $title, $msg);
	}

	public static function WARN($title, $msg)
	{
		self::$instance->write('warn', $title, $msg);
	}

	public static function ERROR($title, $msg)
	{
		$debugInfo = debug_backtrace();

		$count = 0;
		$stack = "";
		foreach ($debugInfo as $key => $val) {

			if (array_key_exists("file", $val)) {
				$stack .= "  #$count file:" . $val["file"] . "(";
			}
			if (array_key_exists("line", $val)) {
				$stack .= $val["line"] . "): ";
			}
			if (array_key_exists("function", $val)) {
				$stack .= "function:" . $val["function"] . "\n";
			}

			$count++;
		}
		self::$instance->write('error', $title,  $msg . "\n  [stacktrace]\n" . $stack);
	}

	public static function BLANK()
	{
		self::$instance->write('blank', '', '');
	}

	public static function SEPRATOR($title, $msg)
	{
		self::$instance->write('seprator', $title, $msg);
	}

	public static function TITLE($title, $msg)
	{
		self::$instance->write('title', $title, $msg);
	}

	public static function DONE($title, $msg)
	{
		self::$instance->write('done', $title, $msg);
	}


	// 记录其他日志信息
	public static function logSms($type, $title, $msg)
	{

		LocalLog::logMonitor($type, $title, $msg, "sms/sms");
	}

	public static function logEmail($type, $title, $msg)
	{

		LocalLog::logMonitor($type, $title, $msg, "email/email");
	}

	public static function logSql($type, $title, $msg)
	{

		LocalLog::logMonitor($type, $title, $msg, "sql/sql");
	}

	public static function logRequest($type, $title, $msg)
	{

		LocalLog::logMonitor($type, $title, $msg, "request/request");
	}

	public static function logMqtt($type, $title, $msg)
	{

		LocalLog::logMonitor($type, $title, $msg, "mqtt/mqtt");
	}

	public static function logMqtt2($type, $title, $msg)
	{

		LocalLog::logMonitor($type, $title, $msg, "mqtt2/mqtt2");
	}

	public static function logUpdate($type, $title, $msg)
	{

		LocalLog::logMonitor($type, $title, $msg, "update/update");

		$msg_log = LocalLog::getConsoleString($type, $title, $msg);

		echo $msg_log;
	}

	public static function logGateway($type, $title, $msg)
	{

		LocalLog::logMonitor($type, $title, $msg, "gateway/gateway");
	}

	public static function logCheckStatus($type, $title, $msg)
	{

		LocalLog::logMonitor($type, $title, $msg, "checkstatus/checkstatus");
	}

	//==================================== 基础方法
	/**
	 * 
	 * 获取控制台输出文本
	 * 
	 * @param string $type 日志类型
	 * @param string $title 日志标题
	 * @param string $msg 日志信息
	 * 
	 * @return string 输出文本
	 */
	public static function getConsoleString($type, $title, $msg)
	{

		$date_time = date("Y-m-d H:i:s");

		if ($type == "info") {
			$str_echo = "\033[1;40;37m$date_time \033[1;46;37m < $title > \033[1;40;36m $msg\033[0m\n";
		} elseif ($type == "warn") {
			$str_echo = "\033[1;40;37m$date_time \033[1;43;37m < $title > \033[1;40;33m $msg\033[0m\n";
		} elseif ($type == "error") {
			$str_echo = "\033[1;40;37m$date_time \033[1;41;37m < $title > \033[1;40;31m $msg\033[0m\n";
		} elseif ($type == "success") {
			$str_echo = "\033[1;40;37m$date_time \033[1;42;37m < $title > \033[1;40;32m $msg\033[0m\n";
		} elseif ($type == "title") {
			$str_echo = "\r\n\033[1;40;37m$date_time \033[1;46;37m $msg\033[0m\n\r";
		} elseif ($type == "done") {
			$str_echo = "\033[1;40;37m$date_time \033[1;45;37m < $title > \033[1;40;35m $msg\033[0m\n";
		} elseif ($type == "seprator") {
			$str_echo = "\r\n\033[1;40;37m$date_time \033[1;40;36m < $title > $msg\033[0m\n";
		} elseif ($type == "blank") {
			$str_echo = "\n";
		} else {
			$str_echo = "$title $msg\n";
		}

		return $str_echo;
	}

	public static function getLogString($type, $title, $msg)
	{

		$date_time = date("Y-m-d H:i:s");

		if ($type == '' || $type == ' ') {

			$msg_log = "$date_time \n";
		} else if ($type == 'blank') {

			$msg_log = "$date_time [$type] < $title > $msg \n";
		} else {

			$msg_log = "$date_time [$type] < $title > $msg \n";
		}

		return $msg_log;
	}

	public static function logMonitor($type, $title, $msg, $file_name)
	{

		$date_time = date("Y-m-d H:i:s");
		$date = explode(' ', $date_time)[0];
		$date_time = "[$date_time]";

		$msg_log = LocalLog::getLogString($type, $title, $msg);

		$path = APP_ROOT . "Storage/$file_name.$date.log";

		// $arr = explode('/',$path);
		// $arr = array_slice($arr,0,count($arr)-1);
		// $dir = join('/',$arr);
		// dump($dir);

		if (strlen(APP_ROOT) == 0) {
			$path = "$date.log";
		}

		$fp = fopen($path, 'a+');
		if ($fp) {

			fwrite($fp, $msg_log);
			fclose($fp);
		} else {

			$handle = fopen($path, "r"); //读取二进制文件时，需要将第二个参数设置成'rb'
			$contents = fread($handle, filesize($path));
			fclose($handle);

			unlink($path);

			$fp = fopen($path, 'a+');
			if ($fp) {

				fwrite($fp, $contents.$msg_log);
				fclose($fp);
			}
			else {

				echo 'Monitor系统日志文件没有读写权限！';
				die();
			}
		}

		// if (ENV == 'dev') {
		// 	$msg_log = LocalLog::getConsoleString($type, $title, $msg);
		// 	echo $msg_log;
		// }
	}

	protected function write($type, $title, $msg)
	{
		$date_time = date("Y-m-d H:i:s");
		$date = explode(' ', $date_time)[0];
		$date_time = "[$date_time]";

		if (LOG_ON) {
			$msg_log = LocalLog::getLogString($type, $title, $msg);

			$path = APP_ROOT . "Storage/$date.log";

			if (strlen(APP_ROOT) == 0) {
				$path = "$date.log";
			}

			$fp = fopen($path, 'a+');
			if ($fp) {

				fwrite($fp, $msg_log);
				fclose($fp);
			} else {

				$handle = fopen($path, "r"); //读取二进制文件时，需要将第二个参数设置成'rb'
				$contents = fread($handle, filesize($path));
				fclose($handle);
	
				unlink($path);
	
				$fp = fopen($path, 'a+');
				if ($fp) {
	
					fwrite($fp, $contents.$msg_log);
					fclose($fp);
				}
				else {
	
					echo '系统日志文件没有读写权限！';
					die();
				}
			}
		}

		if (CONSOLE_OUT || self::$instance->mode == 1) {
			$msg_log = LocalLog::getConsoleString($type, $title, $msg);

			echo $msg_log;
		}
	}
}
