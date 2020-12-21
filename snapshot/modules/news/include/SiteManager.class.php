<?php
// News Lister All Rights Reserved
// A software product of NetArt Media, All Rights Reserved
// Find out more about our products and services on:
// http://www.netartmedia.net
// Released under the MIT license
class SiteManager
{
	public $page="results";
	public $data_file = "modules/news/data/listings.xml";
	
	
	function SiteManager()
	{
		
	}
	
	/// The html code of the website template
	public $TemplateHTML = "";
	
	/// The site paramets
	public $settings = array();
	
	/// Texts and words shown on the website
	public $texts = array();
	
	
	function SetDataFile($data_file)
	{
		$this->data_file= $data_file;
	}
	
	function SetPage($page)
	{
	
		$this->page=$page;
	}
	
	function LoadSettings()
	{
		if (!file_exists('modules/news/data/listings.xml'))
		{
			copy('modules/news/data/listings.xml.orig', 'modules/news/data/listings.xml');
		}
		if (!file_exists('modules/news/config.php'))
		{
			copy('modules/news/config.php.orig', 'modules/news/config.php');
		}
		if(file_exists("modules/news/config.php"))
		{
			$this->settings = parse_ini_file("modules/news/config.php",true);
		}
		else
		{
			die("The configuration file doesn't exist!");
		}
		
		date_default_timezone_set($this->settings["website"]["time_zone"]);
		
	}
	
	function LoadTemplate()
	{

		if(file_exists("modules/news/pages/template.htm"))
		{
			$templateArray=array();
			
			$templateArray["html"] = file_get_contents('modules/news/pages/template.htm');
		
		}
		
		else
		{
			die("Error: The template file template.htm doesn't exist.");
		}
		
	
		
		$this->TemplateHTML = stripslashes($templateArray["html"]);
		
		$pattern = "/{(\w+)}/i";
		preg_match_all($pattern, $this->TemplateHTML, $items_found);
		foreach($items_found[1] as $item_found)
		{
			
			if(isset($this->texts[$item_found]))
			{
				$this->TemplateHTML=str_replace("{".$item_found."}",$this->texts[$item_found],$this->TemplateHTML);
			}
		}
		
	}
	
	
	function Render()
	{
		$HTML = '';
		ob_start();
		
		if(!file_exists("modules/news/pages/".$this->page.".php")) {
			
			// If the given &page value doesn't exist, load either home.php or results.php depending on if the user
			// is browsing the admin area or not - and display the index/default content.
			$page = ($_REQUEST['p'] == 'admin_news') ? 'home' : 'results';
			include("modules/news/pages/$page.php");
		} else {

			include("modules/news/pages/".$this->page.".php");
		}

		$HTML = ob_get_contents();
		$this->TemplateHTML=str_replace("<site content/>",$HTML,$this->TemplateHTML);

		ob_end_clean();
		echo $this->TemplateHTML;
	}
	
	
	function check_word($input)
	{
		if(!preg_match("/^[a-zA-Z0-9_]+$/i", $input)) die("");
	}
	
	
	function ms_i($input)
	{
		if(!is_numeric($input)) die("");
	} 
	
	function write_ini_file($file, array $options)
	{
		$tmp = '<?php exit;?>';
		$tmp.="\n\n";
		foreach($options as $section => $values){
			$tmp .= "[$section]\n";
			foreach($values as $key => $val){
				if(is_array($val)){
					foreach($val as $k =>$v){
						$tmp .= "{$key}[$k] = \"$v\"\n";
					}
				}
				else
					$tmp .= "$key = \"$val\"\n";
			}
			$tmp .= "\n";
		}
		file_put_contents($file, $tmp);
		unset($tmp);
	}
	
	function format_str($strTitle)
	{
		$strSEPage = ""; 
		$strTitle=strtolower(trim($strTitle));
		$arrSigns = array("~", "!","\t", "@","1","2","3","4","5","6","7","8","9","0", "#", "$", "%", "^", "&", "*", "(", ")", "+", "-", ",",".","/", "?", ":","<",">","[","]","{","}","|"); 
		
		$strTitle = str_replace($arrSigns, "", $strTitle); 
		
		$pattern = '/[^\w ]+/';
		$replacement = '';
		$strTitle = preg_replace($pattern, $replacement, $strTitle);

		$arrWords = explode(" ",$strTitle);
		$iWCounter = 1; 
		
		foreach($arrWords as $strWord) 
		{ 
			if($strWord == "") { continue; }  
			
			if($iWCounter == 4) { break; }  
			if($iWCounter != 1) { $strSEPage .= "-"; }
			$strSEPage .= $strWord;  
			
			$iWCounter++; 
		} 
		
		return $strSEPage;
		
	}
	
	function text_words($string, $wordsreturned)
	{
		$string=trim($string);
		$string=str_replace("\n","",$string);
		$string=str_replace("\t"," ",$string);
		
		$string=str_replace("\r","",$string);
		$string=str_replace("  "," ",$string);
		 $retval = $string;    
		$array = explode(" ", $string);
	  
		if (count($array)<=$wordsreturned)
		{
			$retval = $string;
		}
		else
		{
			array_splice($array, $wordsreturned);
			$retval = implode(" ", $array)." ...";
		}
		return $retval;
	}
	
	
}	
?>
