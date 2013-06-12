<?php
	
	/**
	* @autor Sebastian Romero 
	* @date Jan 7 2010
	* This class allow you to save your configuration file and uses it 
	* Fell free to change the configuation path if is needed
	*
	**/
	class Configuration {
		
		private static $configurationFile = "application.conf";
		
		
		/**
		*
		*Loads the configuration and retrieves the dom file
		**/
	 	private  function loadConfiguration(){
			$filecontents = file_get_contents(self::$configurationFile, "r");
			$domConfiguration = str_get_html($filecontents);
			return $domConfiguration;
		}
		
		
		/**
		*
		*Parses and gets the parameter the default value is empty string
		*@example Configuration::getParameter("TAG_CLASS");
		**/
		public static function getParameter($parameter){
			$config = self::loadConfiguration();
			$value = "";
			foreach($config->find('param') as $parameters) { 
				if($parameters->getAttribute("name") != ""){
					if($parameters->getAttribute("name") === $parameter){
						$value = $parameters->getAttribute("value");
						break;
					}
				}
			}
			return $value;
		}
	}
	
?>