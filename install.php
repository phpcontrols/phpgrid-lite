<?php
global $valid;

// written after a successful install; delete it to run the installer again
const INSTALL_LOCK = 'install.lock';
const DB_TYPES = array('mysqli');

$locked = file_exists(INSTALL_LOCK);

$valid['config'] = true;
$valid['connection'] = true;
$valid['dbready'] = true;
$valid['dbready_msg'] = '';

if(is_writeable('.')){

	$valid['confwritable'] = true;

}

if (!empty($_POST) && !$locked){

	do_install();
	
	$ready = true;

	foreach($valid as $key=>$value) {

		if($value === false){
		
			$ready = false;
		
		}
	}

	if ($ready){

		header("location: examples/index.php");

		die;

	}

}	

function h($value)
{
	return is_scalar($value) ? htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') : '';
}

// conf.sample.php puts the values inside single-quoted PHP strings
function php_quote($value)
{
	return addcslashes($value, "'\\");
}

// The posted settings, or null when one is not a valid host, database name or type.
// The database name goes into CREATE DATABASE/USE statements, so it is limited to safe characters.
function install_input(array $post)
{
	$input = array();
	foreach (array('dbtype', 'dbhost', 'dbuser', 'dbpass', 'dbname') as $key){
		$input[$key] = isset($post[$key]) && is_string($post[$key]) ? $post[$key] : '';
	}

	if (!in_array($input['dbtype'], DB_TYPES, true)
		|| !preg_match('/^[\w.\-:\[\]\/]+$/', $input['dbhost'])
		|| !preg_match('/^[\w$\-]+$/', $input['dbname'])){
		return null;
	}

	return $input;
}

function do_install()
{
	global $valid;

	$input = install_input($_POST);

	if ($input === null){

		$valid['connection'] = false;
		$valid['connection_msg'] = 'Please check the database settings. The host can contain letters, digits, dots, dashes and a :port, and the database name letters, digits, dashes and underscores.';
		return;

	}

	['dbtype' => $dbtype, 'dbhost' => $dbhost, 'dbuser' => $dbuser, 'dbpass' => $dbpass, 'dbname' => $dbname] = $input;

	// the checks below expect false on failure; PHP 8.1+ throws by default
	if (function_exists('mysqli_report'))
		mysqli_report(MYSQLI_REPORT_OFF);

	if (function_exists("mysqli_connect")){

		if (isset($_POST["createdb"]) && $_POST["createdb"] == 1)
		
			$link = @mysqli_connect($dbhost, $dbuser, $dbpass);
		
		else
		
			$link = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
			
		if (!$link) {

			$valid['connection'] = false;
			$valid['connection_msg'] = "Cannot connect to the database '" . h($dbhost) . "'. Please check database settings.";

		}

	} else {

		$link = mysql_connect($dbhost, $dbuser, $dbpass);
		
		if (!$link) {
		
			$valid['connection'] = false;
			$valid['connection_msg'] = "Cannot connect to the database '" . h($dbhost) . "'. Please check database settings.";
		
		}

		// if db does not need to be created, then select it
		if (empty($_POST["createdb"])){

			$db_selected = mysql_select_db($dbname);
			
			if (!$db_selected) {
			
				$valid['connection'] = false;
				$valid['connection_msg'] = "Cannot connect to the database '" . h($dbhost) . "'. Please check database settings.";
			
			}
		}
	}
	
	if (!$valid['connection'])
		return;
	
	if ($valid['connection'] == true){

		$templine = '';
		
		// Read in entire file
		$lines = file("examples/SampleDB/mysql_db.sql");

		// append create db calls
		if (isset($_POST["createdb"]) && $_POST["createdb"] == 1){

			// Loop through each line
			foreach ($lines as &$line){

				// ignore internal create db if used from installer
				if ((strstr($line,"CREATE DATABASE") !== false || strstr($line,"USE") !== false))
					$line = "";
			}
			
			array_unshift($lines, "CREATE DATABASE `$dbname`;", "USE `$dbname`;");
		}
		// append on USE db call
		else {

			// Loop through each line
			foreach ($lines as &$line){

				// ignore internal create db if used from installer
				if ((strstr($line,"CREATE DATABASE") !== false || strstr($line,"USE") !== false))
					$line = "";
			
			}
			
			array_unshift($lines, "USE `$dbname`;");		
		}
		
		// was reference to last index of $lines
		unset($line);
		
		// Loop through each line
		foreach ($lines as $line){

			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '')

				continue;
			
			// Add this line to the current segment
			$templine .= $line;
		
			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';'){

				// Perform the query
				if (function_exists("mysqli_connect")){

					if (!mysqli_query($link,$templine)){

						$valid['dbready'] = false;
						$valid['dbready_msg'] .= 'Error performing query \'<strong>' . h($templine) . '\': ' . h(mysqli_error($link)) .'</strong>' ;
						// break;
					}

				} else {
					
					if (!mysql_query($templine)){

						$valid['dbready'] = false;
						$valid['dbready_msg'] .= 'Error performing query \'<strong>' . h($templine) . '\': ' . h(mysql_error()) .'</strong>';
						// break;
					
					}
				
				}
				
				// Reset temp variable to empty
				$templine = '';
			}
		}
	}
	
	if (!$valid['dbready'])

		return;	
	
	// create or override config file
	$scriptName = $_SERVER['SCRIPT_NAME'];
	$webRoot = substr($scriptName, 0, strlen($scriptName) - strlen('/install.php'));

	$configContents = file_get_contents("conf.sample.php");
	$configContents = str_replace("{{dbtype}}", php_quote($dbtype), $configContents);
	$configContents = str_replace("{{dbhost}}", php_quote($dbhost), $configContents);
	$configContents = str_replace("{{dbuser}}", php_quote($dbuser), $configContents);
	$configContents = str_replace("{{dbpass}}", php_quote($dbpass), $configContents);
	$configContents = str_replace("{{dbname}}", php_quote($dbname), $configContents);

	$handle = fopen("conf.php", "w+");

	if (!$handle){

		$valid['config'] = false;
		return;

	}

	fwrite($handle, $configContents);
	fclose($handle);

	// without the lock anyone who can open install.php could rewrite conf.php
	$valid['config'] = file_put_contents(INSTALL_LOCK, date('c') ."\n") !== false;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>phpGrid Demos Installation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

	<link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">    
	<link rel="stylesheet" href="node_modules/fontawesome-4.7/css/font-awesome.min.css">    
	
	<style type="text/css">
	  body{
        padding-top: 50px;
	}

    .navbar {
        margin-bottom: 0;
        border-radius: 0;
        background: #fff;
		border: 0;
		background-color: #377BC8;
    }

	.form-control {
		border-radius: 0;
	}

	#btnInstall {
		border-radius: 0;
		height: 50px;
		font-size: 20px;
	}
    .row.content {height: 1000px}

    </style>
  </head>
  <body>

	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>                        
			</button>
			<a class="navbar-brand" href="https://phpgrid.com"><img src="https://phpgrid.com/wp-content/uploads/2015/03/phpgrid-logo-w-slogan.png" style="width:120px"></a>
			</div>
			<div class="collapse navbar-collapse" id="myNavbar">
			<ul class="nav navbar-nav">
				<li class="active"><a href="#">Demo Installation</a></li>
			</ul>
			</div>
		</div>
	</nav>

    <div class="container" style="padding-top:15px">
			
      	<div class="row">

			<h2 class="text-center">Install sample database in your localhost as seen in <a href="https://demo.phpcontrols.com/">Demo Explorer</a></h2>

			  <!-- Form Name -->
			<?php if ($locked): ?>
			<div class="alert alert-info">
				<strong>Already installed.</strong> Open the <a href="examples/index.php">examples</a>, or delete <code>install.lock</code> to run the installer again.
			</div>
			<?php endif; ?>

			<?php if (!empty($_POST) && !$locked): ?>
				<?php if ($valid['connection']): ?>
				<div class="alert alert-success">
					<strong>Checking if connection is valid:</strong> Database connected.
				</div>
				<?php else: ?>
				<div class="alert alert-danger">
					<strong>Checking if connection is valid:</strong> <?php echo $valid['connection_msg']; ?>
				</div>
				<?php endif; ?>
				
				<?php if (!$valid['config']): ?>
				<div class="alert alert-danger">
					<strong>Writing to config file:</strong> <p>The configuration file is not writable.
					<p>Please copy conf.sample.php to conf.php and update the database configuration OR Try <a href="https://phpgrid.com/documentation/installation/">Manual Install</a></p>
				</div>				
				<?php endif; ?>
				
			<?php endif; ?>
			
			<?php if ($valid['confwritable']): ?>
			<div class="alert alert-success">
				<strong><i class="fa fa-check-circle"></i> Checking if config writable:</strong> Your config file (conf.php) is writable.
			</div>
			<?php else: ?>
			<div class="alert alert-error">
				<strong><i class="fa fa-times"></i> Checking if config writable:</strong> <p>The configuration file (conf.php) is not writable.
				<p>Please copy conf.sample.php to conf.php and update the database configuration OR Try <a href="https://phpgrid.com/documentation/installation/">Manual Install</a></p>
			</div>
			<?php endif; ?>
			
			<?php if ($valid['dbready'] === false): ?>
			<div class="alert alert-danger">
				<strong>Error:</strong> <?php echo $valid['dbready_msg']; ?>
			</div>
			<?php endif; ?>
	
		</div><!--/.row -->
	
		<div class="row">

			<div class="col-md-3"></div>
			
			<div class="col-md-6">

				<?php if (!$locked): ?>
				<form class="form-horizontal" method="post">

					<div class="form-group">
						<label class="control-label col-md-4" for="selectbasic">Database Type</label>
						<div class="controls col-md-8">
							<select id="dbtype" name="dbtype" class="form-control">
							<option value="mysqli" <?php echo (isset($_POST['dbtype']) && $_POST['dbtype']=='mysqli') ? "selected" : "" ?>>MySQL</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-md-4" for="db">Database Host</label>
						<div class="controls col-md-8">
							<input id="dbhost" name="dbhost" type="text" placeholder="localhost" class="form-control" required="" value="<?php echo isset($_POST['dbhost']) ? h($_POST['dbhost']) : "localhost" ?>">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-md-4" for="dbuser">Database Username</label>
						<div class="controls col-md-8">
							<input id="dbuser" name="dbuser" type="text" placeholder="" class="form-control" required="" value="<?php echo isset($_POST['dbuser']) ? h($_POST['dbuser']) : "" ?>">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-md-4" for="dbpass">Database Password</label>
						<div class="controls col-md-8">
							<input id="dbpass" name="dbpass" type="password" placeholder="" class="form-control" value="<?php echo isset($_POST['dbpass']) ? h($_POST['dbpass']) : "" ?>">
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-md-4" for="dbpass">Database Name</label>
						<div class="controls col-md-8">
							<input id="dbname" name="dbname" type="text" placeholder="" class="form-control" required="" value="<?php echo isset($_POST['dbname']) ? h($_POST['dbname']) : "" ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="createdb" class="control-label col-md-4">Create Database *</label>
						<div class="controls col-md-8">
							<input name="createdb" id="createdb" value="1" type="checkbox" onclick="if (this.checked) jQuery('#create_tip').show();">
						</div>					
					</div>

					<div class="form-group">
						<label class="control-label col-md-4" for=""></label>
						<div class="controls alert alert-info col-md-8">
							* NOTE: If checked, database user must have CREATE DATABASE privilege. 
							Otherwise You should create database manually before install.
						</div>
					</div>

					<div class="form-group">
						<label class="control-label" for=""></label>
						<div class="controls">
							<button id="btnInstall" name="btnInstall" style="width:100%" class="btn btn-primary">Install</button>
							<br /><br /><br /><br />
							<div class="text-right">
								<a href="https://phpgrid.com/documentation/installation/" target="_new" class="btn btn-secondary">Manual Install</a>
							</div>
						</div>
					</div>

				</form>
				<?php endif; ?>

			</div>
				
			<div class="col-md-3"></div>
			
		</div><!--/.row -->

		<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>		
	  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    </div><!--/.container-->


  </body>
</html>