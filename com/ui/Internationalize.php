<?php
	
	/**
	* @autor Sebastian Romero 
	* @date Jan 7 2010
	* All common methods for internationalize web in PHP
	* TODO: Change the way how php handle internationalization
	*
	**/
	class Internationalize {
		
		/**
		*
		*Loads the language configuration file
		*Returns an Array of Objects
		**/
	 	public static function loadLanguageFile($file){
			$filecontents = file_get_contents($file, "r");
			$domConfiguration = str_get_html($filecontents);
			$arr_configuration = self::parseLanguages($domConfiguration);
			return $arr_configuration;
		}
		
		
		/**
		*
		**/
		public static function getLocale($localePath, $defaultLanguage = "es_ES"){
			$language = $defaultLanguage;
			if(isset($_GET["language"]))
				$language = $_GET["language"];
			$registered_locale = setlocale(LC_ALL, $language);
			bindtextdomain("messages", $localePath);
			bind_textdomain_codeset("messages", 'UTF-8'); 
			textdomain("messages");
			gettext("Prueba");
			return $language;
		}
		
		
		/**
		*
		*Parses the languages on the xml file
		**/
		private function parseLanguages($langCodes){
			$arr_locale = Array();
			$counter = 0;
			foreach($langCodes->find('language') as $langs) {
				$arr_locale[$counter]["value"] = $langs->getAttribute("value");
				$arr_locale[$counter]["name"] = $langs->innertext;
				$arr_locale[$counter]["isDefault"] = ($langs->getAttribute("default") === "true")?"true":"false";
				$counter++;
			}
			return $arr_locale;
		}	
	}
	
?>