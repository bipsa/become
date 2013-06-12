<?php

	include('JavaScriptPacker.php');
	include('Dom.php');
	include('Configuration.php');
	include('Internationalize.php');
	include('Browser.php');
	
	/**
	* @author Sebastian Romero
	* @date Febuary 5th 2010
	* @updated Optional cache was added, bundle javascripts this new inprovements will 
	* increase the performace of load and server process.
	*/
	class Page {

		public $page = "";
		public $domDocument;
		public $language = "";
		
		/**
		* @author Sebastian Romero
		* @constructor 
		* @param [String] template required
		* @cache [Boolean] Optional
		* @filename [String] Optional This parameter sets a optional name for the cached file, if this value is not set the default template will be use 
		**/
		public function Page( $template, $cache = false, $filename = null) {
			$this->language = Internationalize::getLocale(Configuration::getParameter("LOCALE_FOLDER_PATH"));
			$templateFile = Configuration::getParameter("TEMPLATE_FOLDER_PATH");
			$fname = (isset($filename))?$filename:$template;
			if($cache && $this->isFileCatched($fname)){
				$document = $page = file_get_contents(Configuration::getParameter("TEMPLATE_FOLDER_PATH") . "../cache/" . $fname, "r");
			} else {
				$page = file_get_contents($templateFile . $template, "r");
				$this->domDocument = str_get_html($page);
				$document = self::parsePage($this->domDocument);
				if($cache){
					if(!isset($filename))
						$this->setPageOnCache($template, $document);
					else
						$this->setPageOnCache($filename, $document);
				}
			}
			echo $document;
		}
		
		
		/**
		 * @author Sebastian Romero
		 * this function get the javascript and bundle it
		 */
		public function bundleFiles($files, $fileName = "bundle.js"){
			$files = str_get_html($files);
			$scripts = "";
			$contentScripts = '';
			if($files){
				foreach($files->find("script") as $script) {
					if($script->getAttribute("src") != ""){
						$scripts .= file_get_contents($script->getAttribute("src"), "r");
					} else if ($script->innertext != ""){
						$scripts .= $script->innertext;
					}
				}
			}
			$myPacker = new JavaScriptPacker($scripts);
			$contentScripts = $myPacker->pack();
			$name = $fileName;
			$template = "<script type=\"text/javascript\" src=\"/cache/" . $name . "\"></script>";
			$this->setPageOnCache($name, $contentScripts);
			return $template;
		}
		
		
		/**
		 * @author Sebastian Romero
		 * @param [filename] Requiered parameter with the file name
		 * @param {content} Optional parameter with the file content
		 * This function adds the content to the cache.
		 */
		private function setPageOnCache($filename, $content = "" ){
			$cachefolder = Configuration::getParameter("TEMPLATE_FOLDER_PATH") . "../cache/";
			if (!file_exists($cachefolder)) {
				mkdir($cachefolder, 0777);
		    }
			if(!file_exists($cachefolder . $filename) || Configuration::getParameter("ISDEV") == true){
				$cachefile = fopen($cachefolder . $filename, "w+");
				if($cachefile){
					fwrite($cachefile, $content);
				}
				fclose($cachefile);
			}
		}
		
		
		/**
		 * @autor Sebastian Romero
		 * Checks if the files was catched.
		 */
		private function isFileCatched($filename){
			$cachefile = Configuration::getParameter("TEMPLATE_FOLDER_PATH") . "../cache/" . $filename;
			return file_exists($cachefile);
		}
		
		
		/**
		* Parses the HTML loaded and eliminates the special tags on the template
		* @autor Sebastian Romero
		* A third party used for this method
		* @how : $page = new Page(); echo $page->checkControls("terminal <become:_/> sigue siendo <become:_ />");
		*
		*/
		public function parsePage($html){
			foreach($html->find(Configuration::getParameter("TAG_CLASS")) as $become) {
				if($become->getAttribute("scope") == ""){
					if($become->getAttribute("method") != ""){
						$template = $become->innertext;
						if($template != "")
							@ $become->outertext = call_user_func(Array($this, $become->getAttribute("method")), $template);
						else 
							@ $become->outertext = call_user_func(Array($this, $become->getAttribute("method")));
					}
				} else {
					$template = $become->innertext;
					if($template != "")
						@ $become->outertext = call_user_func(Array($become->getAttribute("scope"), $become->getAttribute("method")), $template);
					else 
						@ $become->outertext = call_user_func(Array($become->getAttribute("scope"), $become->getAttribute("method")));
				}
			}
			return $html;
		}
		
		
		/**
		* loads a user control on the page
		**/
		public function loadControl($path){
			//echo $paht;
			$control = Configuration::getParameter("TEMPLATE_FOLDER_PATH") . $path;
			$userControl = file_get_contents($control, "r");
			$domDocument = str_get_html($userControl);
			$userControl = self::parsePage($domDocument);
			return $userControl; 
		}
		
		/**
		 * 
		 */
		public function isIpad(){
			return (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
		}
		
		
		/**
		 * 
		 */
		public function isIphone(){
			return (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPhone');
		}
		
		
		/**
		 * This method evaluates the user client
		 */
		public function isNewBrowser(){
			$browser = new Browser();
			$value = false;
			
			switch($browser->getBrowser()){
				case Browser::BROWSER_FIREFOX:
					if(floor($browser->getVersion()) >= 5){
						$value = true;
					}
				break;
				case Browser::BROWSER_SAFARI:
					if(($browser->getPlatform() === Browser::PLATFORM_APPLE) || ($browser->getPlatform() === Browser::PLATFORM_WINDOWS)){
						if($browser->getVersion() >= 5){
							$value = true;
						}
					}
				break;
				case Browser::BROWSER_IE:
					if($browser->getVersion() >= 9){
						$value = true;
					}
				break;
				case Browser::BROWSER_CHROME:
					if($browser->getVersion() >= 9){
						$value = true;
					}
				break;
				default :
					if(($browser->getPlatform() === Browser::PLATFORM_IPAD) ||
							($browser->getPlatform() === Browser::PLATFORM_IPHONE) ||
								($browser->getPlatform() === Browser::PLATFORM_IPOD)){
						$value = true;
					}
				break;
			}
			return $value;
		}
	}
?>