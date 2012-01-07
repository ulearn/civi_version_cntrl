<?php
	include_once("config.inc.php");
	define("AMFPHP_BASE", $cfg['AmfphpPath'] . 'amf-core/');
	include_once(AMFPHP_BASE . "util/Compat.php");
	error_reporting(E_ALL ^ E_NOTICE);

	if( !is_dir( $cfg['ServicesPath'] )) 
	{
		print( "FATAL ERROR - Invalid ServicesPath<BR><BR>See right pane." );
		exit;
	}   
	include_once($cfg['AmfphpPath'] . "/amf-core/util/MethodTable.php");

	ob_start();
	chdir($cfg['ServicesPath']);
	@include_once($cfg['ServicesPath'] . $_GET['class'] . ".php");
	ob_end_clean();
	$methodTable = MethodTable::create($cfg['ServicesPath'] . $_GET['class'] . ".php");
	
	$className = substr($_GET['class'], strrpos('/', $_GET['class']));
	
	if($methodTable === false)
	{
		exit();
	}
	else
	{
		if($_GET['action'] == 'save')
		{
			$loc = $cfg['ServicesPath'] . $_GET['class'] . '.methodTable.php';
			$bytes = file_put_contents($loc, "<" . "?php \n" . MethodTable::showCode($methodTable) . "\n?" . ">");
			
			if($bytes == 0)
			{
				$feedback = "<p class='error'>Error writing file, are permissions set correctly?</p>";
			}
			else
			{
				$feedback = "<p class='feedback'>Write file succesful</p>";
			}
		}
	}
	
?>
<html>
	<head>
		<title>MethodTable</title>
		<link rel="stylesheet" type="text/css" href="images/service-browser.css" />
	</head>
	<body>
		<h1>MethodTable for <?php echo $_GET['class']  . '.php'; ?></h1>
		<?php echo $feedback; ?>        
		<p>This code was generated by the MethodTable class by looping through the class methods and reading the signature and JavaDoc of each method. Read more about it in the <a href="http://www.amfphp.org/docs/creatingclasses.html" target="_blank">amfphp docs</a>.<br/><br/>When deploying your application on a webserver it's better to have a hard-coded methodTable for faster performance. You can copy/paste this code in the constructor of your service class, or, provided you have 777 permissions in /services, click the 'save' link,  and write <code>include("<?php echo substr($className, strrpos('/' . $className, '/')); ?>.methodTable.php");</code> in the constructor.</p>

		<div style='text-align:right'><a href='<?php echo $PHP_SELF . '?class=' . $_GET['class'] . '&action=save'; ?>'>Save to <?php echo $className; ?>.methodTable.php</a></div>
		<textarea class="codex" name="methodTable"><?php echo MethodTable::showCode($methodTable); ?></textarea>
	</body>
</html>