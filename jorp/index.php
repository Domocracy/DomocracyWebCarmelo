<?php
	include('header.php');
	include("login.php");

	if( isset( $_POST["x"] ) || isset( $_GET["x"] ))
	{
		$page = isset($_GET["x"]) ? $_GET["x"] : $_POST["x"];
	}
	if( isset( $_POST["y"] ) || isset( $_GET["y"] ))
	{
		$y = isset($_GET["y"]) ? $_GET["y"] : $_POST["y"];
	}

	if($logged_in) {
		if ($page=="editProject")
			include('edit_project.php');
		else if ($page=="editTask")
			include('edit_task.php');
		else if ($page=="createProject")
			include('create_project.php');
		else if ($page=="createTask")
			include('create_task.php');
		else if ($page=="addClient")
			include('add_client.php');
		else if ($page=="editUser")
			include('edit_user.php');
		else if ($page=="listClients")
			include('client_list.php');
		else if ($page=="listUsers")
			include('user_list.php');
		else if ($page=="addUser")
			include('register.php');
		else if ($page=="tools")
			include('tools.php');
		else if ($page=='deleteProject') {
			if (getUserLevel()=="1") {
				deleteProject($y);
			}
			else {
				echo "You do not have permission to perform this function. <META http-equiv=\"refresh\" content=\"1;URL=index.php\">";
			}
		}
		else if ($page=='deleteTask') {
			if (getUserLevel()=="1" || taskAuth($y)) {
				deleteTask($y);
			}
			else {
				echo "You do not have permission to perform this function. <META http-equiv=\"refresh\" content=\"1;URL=index.php\">";
			}
		}
		else displayLogin();
	}
	else {
		displayLogin(); 
	}

include('footer.php');
?>

</body>
</html>
