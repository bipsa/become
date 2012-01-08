<?php
	require("com/ui/Page.php");
	final class Index extends Page {
		public function Index (){
			parent::Page("index.html");
		}
		public function addTitle($template){
			return "Become is working...";
		}
	}
	$page = new Index();
?>