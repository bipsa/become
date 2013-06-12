<?php
	/**
	* @author Sebastian Romero 
	* @date Jul 21 2012
	* Implements this Interface for forwarding methods
	*
	**/
	interface ApnsDelegate
	{
		public function apnsError($error, $errorCode);
		public function streamCreated($token, $message);
		public function pushSent($token);
	}

	/**
	* @author Sebastian Romero 
	* @date Jul 21 2012
	* This class allow you to send ios push notification 
	* The Configuration file is required with IOS_PUSH variables defined IOS_PUSH_SSL, 
	* IOS_PUSH_FEEDBACK, IOS_PUSH_CERTIFICATES_PATH, IOS_PUSH_SIGNATURE
	* $apns = new Apns();
	* $apns->delegate = $this;
	* $apns->push("test", "message");
	*
	**/
	class Apns {

		public $delegate;

		/**
		* This method sends a message via apns 
		* @param [String] application key
		* @param [String] Token or UID key
		* @param Optional [String] Message you want to send via apns, use empty message for passes
		**/
		public function push($appKey, $token, $message = ""){
			$signature = Configuration::getParameter("IOS_PUSH_CERTIFICATES_PATH") . $appKey . Configuration::getParameter("IOS_PUSH_SIGNATURE");
			$context = stream_context_create();
			stream_context_set_option($context, "ssl", "local_cert", $signature);
			$fp = stream_socket_client(Configuration::getParameter("IOS_PUSH_SSL"), $error, $errorString, 60, STREAM_CLIENT_CONNECT, $context);
			$message = ($message != "")?$this->formatMessage($message):"";
			if(!$fp){
				$this->delegatesError("Failed to connect to APNS.", 1);
			} else {
				$msg = chr(0).pack("n",32).pack('H*',$token).pack("n",strlen($message)).$message;
				$fwrite = fwrite($fp, $msg);
				if(!$fwrite) {
					$this->delegatesError("Failed writing to stream.", 2);
				} else{
					if($this->delegate){
						call_user_func(Array($this->delegate, "streamCreated"), $token, $message);
					}
				}
			}
			fclose($fp);
			$context = stream_context_create();
			stream_context_set_option($context, "ssl", "local_cert", $signature);
			stream_context_set_option($context, "ssl", "verify_peer", false);
			$fp = stream_socket_client(Configuration::getParameter("IOS_PUSH_FEEDBACK"), $error, $errorString, 60, STREAM_CLIENT_CONNECT, $context);
			if(!$fp){
				while ($devcon = fread($fp, 38)){
					$arr = unpack("H*", $devcon);
					$rawhex = trim(implode("", $arr));
					$token = substr($rawhex, 12, 64);
					if(!empty($token)){
						if($this->delegate){
							call_user_func(Array($this->delegate, "pushSent"), $token);
						}
					}
				}
			}
			fclose($fp);
		}


		/**
		* Delegates to its parent the error forwarding the response
		* @param [String] Message with the error 
		* @param [Number] Code of the error 
		**/
		private function delegatesError ($error, $errorCode){
			if($this->delegate){
				call_user_func(Array($this->delegate, "apnsError"), $error, $errorCode);
			}
		}



		/**
		* Formats the message to sent to Apple Push Notification Service
		* @param [String] Message  
		* @param  optional [String] Sound 
		**/
		private function formatMessage($message, $sound = "default"){
			$payload =  array();
			$payload["aps"] = array("alert" => $message, "badge" => 1, "sound" => $sound);
			$payload = json_encode($payload);
			return $payload;
		}
	}
?>