<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shCore.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushAppleScript.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushAS3.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushBash.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushColdFusion.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushCpp.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushCSharp.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushCss.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushDelphi.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushDiff.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushErlang.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushGroovy.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushJavaFX.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushJava.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushJScript.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushPerl.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushPhp.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushPlain.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushPowerShell.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushPython.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushRuby.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushSass.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushScala.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushSql.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushVb.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushXml.js"></script>
<script type="text/javascript" src="modules/faq/jcfilter.min.js"></script>
<script type="text/javascript" src="modules/faq/faq.js"></script>
<link type="text/css" rel="stylesheet" href="modules/faq/syntaxhighlighter/styles/shCoreDefault.css"/>
<?php
function exec_ogp_module()
{
	echo '<h2>F.A.Q.</h2>';
	echo '<div class="maincategory"><img class="headerimage" src="modules/faq/faq.png">Categories<div style="float:right" >'.
		 '<input class=search name=search id=search type=text placeholder="Search"/></div><br></div>';

	require 'modules/faq/rss_php.php';
	$url = 'https://opengamepanel.org/faq/rss.php';
	$local_copy = 'modules/faq/ogpfaq.rss'; ## Relative path
	$save_as = realpath('modules' . DIRECTORY_SEPARATOR . 'faq') . DIRECTORY_SEPARATOR . 'ogpfaq.rss'; 
	## Full path (adding the filename to realpath would fail if the file does not exists yet)
	$online = false;
	$local = false;
	$updated = false;
	$s = (isset($_SERVER['HTTPS'])) ? "s" : "";
	$p = (isset($_SERVER['SERVER_PORT']) and $_SERVER['SERVER_PORT'] != "80") ? ":".$_SERVER['SERVER_PORT'] : "";
	$localServerURL = $_SERVER['SERVER_NAME'];
	if($localServerURL == "_"){
		$localServerURL = "localhost";
	}
	$local_url = 'http'.$s.'://'.$localServerURL.$p.$_SERVER['SCRIPT_NAME'];
	$local_url = str_replace('home.php', $local_copy, $local_url);
	if(file_exists($save_as))
	{
		$rss = new rss_php;
		$rss->load($local_url);
		$items = $rss->getItems(); #returns all rss items
		$local = true;
	}
	echo "<script>console.log('Last Update : ".date("r", filemtime($save_as))."\\nCurrent Time: ".date('r',time())."\\nNext Update : ".date('r', strtotime("+1 day", filemtime($save_as)))."');</script>";
	if( ($local AND ( strtotime("+1 day", filemtime($save_as)) <= strtotime("now") )) OR !$local) # Check the file is older than 1 day to avoid spamming the server
	{
		stream_context_set_default(array('ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false
			)
		));
		
		$headers = get_headers( $url, 1 );
		touch( $save_as ); # Connection done, so we reset the file modification time even if the server is down (avoid server spamming)
		echo "<script>console.log('Trying to connect to ".$url."');</script>";
		if( $headers[0] == 'HTTP/1.1 200 OK')
		{
			$online = true;
			$rss_online = new rss_php;
			$rss_online->load($url); # SERVER USAGE WARNING : using 32kb of server bandwidth each time each person loads this function 
			$items_online = $rss_online->getItems();
			echo "<script>console.log('Connected successfully to ".$url.", checking...');</script>";
			if(($local and $items_online != $items ) OR !$local)
			{			
				$contents = file_get_contents($url);
				if(preg_match('#^<\?xml version="1.0" encoding="utf-8" \?>#', $contents, $match))
				{
					if( file_put_contents($save_as, $contents) === false )
					{
						print_failure("Imposible write " . $save_as);
						return;
					}
					touch( $save_as );
					echo "<script>console.log('Entries updated successfully.');</script>";
					$updated = true;
				}
				else
				{
					print_failure("Imposible fetch " . $url);
					return;
				}
			}
			else
			{
				echo "<script>console.log('No changes found...');</script>";
			}
		}
	}

	
	if($updated)
	{
		$rss = new rss_php;
		$rss->load($local_url);
		$items = $rss->getItems(); #returns all rss items
	}
	
	if(!file_exists($save_as))
	{
		print_failure('Unable to load entries.');
		return; # Stop loading page
	}
		
	
	$entries = array();
	foreach($items as $index => $item)
	{
		$category = $item['category'][0];
		$entries[$category][$index]['title'] = $item['title'][0];
		$entries[$category][$index]['content'] = $item['content:encoded'][0];
	}
	$categories = "";
	$accordion_entries = "<div id=\"accordion\">\n";
	foreach($entries as $category_name => $category_entries)
	{
		$categories .= "<li class='faqblock'><a class='faqcategory' href=\"#$category_name\">$category_name</a></li>";
		$accordion_entries .= "<div class=\"category\" id=\"$category_name\"><img class='headerimage' src='modules/faq/faqlower.png'>$category_name</div>";
		foreach($category_entries as $index => $item)
		{
			$accordion_entries .=  "\t<div class=\"accordion-toggle\">".
									"$item[title]</div>\n".
									"\t<div class=\"accordion-content\">\n\t\t<div class=\"faqanswer\">$item[content]</div>\n\t</div>\n";
		}
	}
	$categories .= "</ul>";
	$accordion_entries .= "</div>";
	
	echo $categories.$accordion_entries;
	
	echo "<div class='footer' >".
			"<div style='display:block;float:left' >".
				"<b class='imagetext'>Powered by:</b><br>".
				"<a href='http://docs.s9y.org/index.html' target='_blank' ><img class='footerimg' style='height:50px;' src='http://docs.s9y.org/img/logos/s9y.png'></a>".
			"</div>".
			"<div class='credits' style='display:block;float:right' >".
				"<b>Credits:</b><br>".
				"<div class='credittext'>Original Idea | Chief Content Maintainer : <b>omano</b> at opengamepanel.org<br>".
				"Front End Developer | <b>james30263</b> at opengamepanel.org<br>".
				"Back End Developer | <b>DieFeM</b> at opengamepanel.org<br>".
				"Beta Tester | Content Maintainer : <b>rocco</b> at opengamepanel.org</div>".
			"</div>".
		 "</div>";
}
?>
