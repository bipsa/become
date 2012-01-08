<?php
	
	
	include('JavaScriptPacker.php');
	
	class Compressor {
		
		public static function compressJavascript($path) {
			$scripts = file_get_contents($path, "r");
			$myPacker = new JavaScriptPacker($scripts);
 			return $myPacker->pack() . ";";
		}
		
		
		public static function compressALLJavascript($path){
			$compressedContent = "";
			if (is_dir($path)) {
				if ($context = opendir($path)) {
					while (($file = readdir($context)) !== false) {
						$info = pathinfo($path. $file);
						if($info['extension'] === "js"){
							$compressedContent .= self::compressJavascript($path. $file);
						}
					}
					closedir($context);
				}
			}
			return $compressedContent;
		}
	}

?>
