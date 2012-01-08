<?php

	/**
	*@autor Sebastian Romero
	*@use 	$bitmap = new Bitmap("cocacola.png");
	*@note Uses GD
	*
	**/
	class Bitmap {
		
		private $imagePath;
		private $imageData;
		
		
		/**
		* Constructor 
		* @param String Image Path Optional
		**/
		public function __construct($image = ""){
			if($image !== "") {
				$this->imagePath = $image;
				$this->imageData = $this->getImageData();
			}
		}
		
		
		
		/**
		* gets the path of the image
		**/
		public function path(){
			return 	$this->imagePath;
		}
		
		
		/**
		* @return String Retruns the Extension of the file
		**/
		public function extension(){
			return substr($this->imagePath, strrpos($this->imagePath, '.') + 1);
		}
		
		
		/**
		* @return Number Retruns the Width of the Image
		**/
		public function width(){
			return (int) $this->getImageInformation();
		}
		
		
		/**
		* @return Number Retruns the Height of the Image
		**/
		public function height(){
			return (int) $this->getImageInformation("height");
		}
		
		
		/**
		 * Creates the Thumbnail with the given bitmap
		 * @return 
		 */
		public function createThumb($scale){
			$thumb = imagecreatetruecolor($this->width() * $scale, $this->height() * $scale);
			imagecopyresized($thumb, $this->imageData, 0, 0, 0, 0, $this->width() * $scale, $this->height() * $scale, $this->width(), $this->height());
			return $thumb;
		}
		
		
		/**
		 * Creates the Thumbnail with the given bitmap by a given size
		 * @return [Boolean]
		 */
		public function createThumbBySize($new_w, $new_h, $target_img){
			
		    $ratio = max($new_w/$this->width(), $new_h/$this->height());
		    $h = $new_h / $ratio;
		    $x = ($this->width() - $new_w / $ratio) / 2;
		    $w = $new_w / $ratio;
			
			
			
			$dst_img=imagecreatetruecolor($new_w, $new_h);
			$newThumb = imagecopyresampled($dst_img, $this->imageData, 0, 0, $x, 0, $new_w, $new_h, $w, $h);
			imagejpeg($dst_img, $target_img);
			return $newThumb;
		}
		
		
		/**
		 * 
		 */
		public function getImageFormat($imageData, $format = "jpg"){
			if($format === "jpg"){
				imagejpeg($imageData,null, 100);
			}
			return $imageData;
		}
		
		
		/**
		 * 
		 */
		public static function getImageBuffer($imageData, $format){
			ob_start();
			$this->getImageFormat($imageData, $format);
			$bytes = ob_get_contents();
			ob_clean();
			return $bytes;
		}
		
		
		/**
		* Gets the Image Information according a Path
		* @return ImageData 
		**/
		private function getImageData(){
			$_imageData = ( $this->extension($this->imagePath) === "png")?@imagecreatefrompng($this->imagePath):@imagecreatefromjpeg($this->imagePath);
			return $_imageData; 
		}
		
		
		/**
		* @param String Optional
		**/
		private function getImageInformation($propertie = "width"){
			 $size = getimagesize($this->imagePath);
			 $value;
			 switch($propertie){
				case "height" :
					$value = $size[1];
				break;
				default :
					$value = $size[0];
				break;
			}
			return $value;
		}
	
	}
?>