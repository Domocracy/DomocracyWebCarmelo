<?php

	$user_name = "".$_SESSION['username']."";	
	$users = "SELECT * FROM users WHERE username='$user_name'";
	$user_list = mysql_query($users, $conn) or die('Could not connect: ' . mysql_error());
	$user_array = mysql_fetch_assoc($user_list);

	$user_role = "".$user_array['role']."";
	$id = "".$user_array['id']."";

	echo "<ul class=\"loggedIn\"><li> Logged In As: <b>";
	echo $user_name;
	echo "</b> [<a href=\"index.php?x=editUser&y=$id\">EDIT</a>]</li>";
	echo "</ul><hr width=\"65%\" size=\"1\" color=\"#e0e0e0\"/>";

	if ($user_role=="1") {
		echo "
			<table border=\"0\" width=\"100%\"><tr><td valign=\"top\" width=\"50%\">
			<a href=\"index.php?x=addClient\">Add Client</a><br/>
			<a href=\"index.php?x=addUser\">Add User</a><br/>
			<a href=\"index.php?x=createProject\">Create Project</a><br/>
			<a href=\"index.php?x=createTask\">Create Task</a>
			</td><td valign=\"top\" align=\"right\" width=\"50%\">
			<a href=\"index.php?x=tools\">Tools</a><br/>
			<a href=\"index.php?x=listClients\">Client List</a><br/>
			<a href=\"index.php?x=listUsers\">User List</a>
			</td></tr></table>
		";
	}
	else if ($user_role=="2") {
		echo "
			<table border=\"0\" width=\"100%\"><tr><td valign=\"top\" width=\"50%\">
			<a href=\"index.php?x=createTask\">Create Task</a><br/><br/>
			</td><td valign=\"top\" align=\"right\" width=\"50%\">
			<a href=\"index.php?x=tools\">Tools</a><br/>
			<a href=\"index.php?x=listClients\">Client List</a><br/>
			<a href=\"index.php?x=listUsers\">User List</a>
			</td></tr></table>
		";

	}
	else if ($user_role=="3") {
		echo "
			<table border=\"0\" width=\"100%\"><tr><td valign=\"top\" width=\"50%\">
			<a href=\"index.php?x=tools\">Tools</a><br/>
			<a href=\"index.php?x=listClients\">Client List</a><br/>
			<a href=\"index.php?x=listUsers\">User List</a><br/>
			</td></tr></table>
		";

	};


?>

