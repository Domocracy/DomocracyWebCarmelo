<?php header("Content-Type: text/html; charset=utf-8");
	session_start(); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>jorp project manager</title>
<link rel="stylesheet" type="text/css" href="style.css"/>
<link rel="Icon" href="images/icon.ico" type="image/x-icon"/>
<script type="text/javascript" src="jquery-latest.js"></script>
<script type="text/javascript" src="jquery.validate.js"></script>

<script type="text/javascript">
<!--

function popupProject(project_id) {
	var answer = confirm("Are you sure you want to delete that project?")
	if (answer)
		window.location="index.php?x=deleteProject&y="+project_id;
}

function popupTask(task_id) {
	var answer = confirm("Are you sure you want to delete that task?")
	if (answer)
		window.location="index.php?x=deleteTask&y="+task_id;
}

// -->
</script> 

</head>



<body>

<div id="wrapper">

<div id="errors">
<?php
	include("database.php");
	include("functions.php");
?>
</div>

<div id="bannerBar">
<div id="logo">
</div>
</div>

<div id="topBar">
<div id="topBarLinks">
<ul>
<li><a href="index.php">Main</a></li>

<?php 
	global $logged_in;

	if (isset($_SESSION['username'])) {
		echo "<li><a href=\"logout.php\">Logout</a></li>";
	}
?>
</ul>
</div>
</div>

