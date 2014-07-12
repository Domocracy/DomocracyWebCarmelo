<?

/**
 * Checks whether or not the given username is in the
 * database
 */
function confirmUser($username, $password){
   global $conn;
   /* Add slashes if necessary (for query) */
   if(!get_magic_quotes_gpc()) {
	$username = sanitize($username);
   }

   /* Verify that user is in database */
   $q = "select password from users where username = '$username'";
   $result = mysql_query($q,$conn);
   if(!$result || (mysql_numrows($result) < 1)){
      return 1; //username failure
   }

   $dbarray = mysql_fetch_array($result);
   $dbarray['password']  = stripslashes($dbarray['password']);
   $password = sanitize($password);

   /* Validate that password is correct */
   if($password == $dbarray['password']){
      return 0; //username and password confirmed
   }
   else{
      return 2; //failure
   }
}

function checkLogin(){
 
  if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])){
      $_SESSION['username'] = $_COOKIE['cookname'];
      $_SESSION['password'] = $_COOKIE['cookpass'];
   }

   /* username and password set */
   if(isset($_SESSION['username']) && isset($_SESSION['password'])){
      /* check that username and password are valid */
      if(confirmUser($_SESSION['username'], $_SESSION['password']) != 0){
         /* user not logged in */
         unset($_SESSION['username']);
         unset($_SESSION['password']);
         return false;
      }
      return true;
   }
   /* not logged in */
   else{
      return false;
   }
}

//Log in and show the administrator portal

function loginAdmin() {

	global $conn;

	echo "
		<div id=\"contentContainer\">
		<div class=\"leftContainer\">
		<div class=\"header\">
			administrator portal
		</div>
		<div class=\"content\">
		<div class=\"sort_by\">SORT BY: Name [ <a href=\"?x=name_asc\">Ascending</a> | <a href=\"?x=name_desc\">Descending</a> ] or Deadline [ <a href=\"?x=deadline_soon\">Ascending</a> | <a href=\"?x=deadline_late\">Descending</a> ] or Urgency [ <a href=\"?x=nonurgent\">Ascending</a> | <a href=\"?x=urgent\">Descending</a> ]</div>
	";

	if( isset( $_POST["x"] ) || isset( $_GET["x"] ))
	{
		$order = sanitize(isset($_GET["x"]) ? $_GET["x"] : $_POST["x"]);
	}

	if ($order=="urgent")
	{
		//define the query
		$sql="SELECT * FROM projects ORDER BY on_time DESC";
	}
	else if ($order=="nonurgent")
	{
		//define the query
		$sql="SELECT * FROM projects ORDER BY on_time ASC";
	}
	else if ($order=="deadline_soon")
	{
		//define the query
		$sql="SELECT * FROM projects ORDER BY deadline ASC";
	}
	else if ($order=="deadline_late")
	{
		//define the query
		$sql="SELECT * FROM projects ORDER BY deadline DESC";
	}
	else if ($order=="name_asc")
	{
		//define the query
		$sql="SELECT * FROM projects ORDER BY project_name ASC";
	}
	else if ($order=="name_desc")
	{
		//define the query
		$sql="SELECT * FROM projects ORDER BY project_name DESC";
	}
	else {
		//define the query
		$sql="SELECT * FROM projects";
	}

	$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
	$num = mysql_numrows($result);

	if ($num > 0) {

	//start while loop
	while ($row=mysql_fetch_assoc($result)) {

		$project_id = "".$row['project_id']."";
		$sql2="SELECT * FROM tasks WHERE project_id='$project_id'";
		$task_list = mysql_query($sql2, $conn) or die('Could not connect: ' . mysql_error());

		$client_id= "".$row['client_id']."";
		$sql3="SELECT * FROM clients WHERE client_id='$client_id'";
		$client_list = mysql_query($sql3, $conn) or die('Could not connect: ' . mysql_error());
		$row3= mysql_fetch_assoc($client_list);

		$complete_time = "".$row['complete_time']."";
		$minutes_plain1 = $complete_time % 60;
		$hours_plain1 = ($complete_time-$minutes_plain1)/60;	
		$minutes1 = sprintf("%02d",$minutes_plain1);
		$hours1 = sprintf("%02d",$hours_plain1);

		$spent_time = "".$row['spent_time']."";
		$minutes_plain2 = $spent_time % 60;
		$hours_plain2 = ($spent_time-$minutes_plain2)/60;
		$minutes2 = sprintf("%02d",$minutes_plain2);
		$hours2 = sprintf("%02d",$hours_plain2);

		$isOnTime = isOnTime($row['project_id']);

		if ($isOnTime=="<span class=\"onTime\">ON SCHEDULE</span>") {
			$status=1;
		}
		else if ($isOnTime=="<span class=\"due\">DUE SOON</span>") {
			$status=2;		
		}
		else if ($isOnTime=="<span class=\"late\">BEHIND SCHEDULE</span>") {
			$status=3;
		}
		else if ($isOnTime=="<span class=\"attn\">NEEDS DEADLINE</span>") {
			$status=4;
		}
		else if ($isOnTime=="<span class=\"complete\">COMPLETE</span>") {
			$status=0;
		}

		$setOnTime = "UPDATE projects SET on_time='$status' WHERE project_id='$project_id'";
		mysql_query($setOnTime);

		echo "

			<script type=\"text/javascript\">
			$(document).ready(function() {
				$('#".$row['project_id']."_bottom').hide();
				$('#".$row['project_id']."_top').click(function() {
					$(\"#".$row['project_id']."_bottom\").slideToggle(\"slow\");
				});
			});
			</script>

			<center>
			<div id=\"".$row['project_id']."_top\" class=\"project_top\">
				<div class=\"left\">".$row['project_name']."</div>
				<div class=\"right\">
				<input type=\"button\" class=\"addTaskIcon\" title=\"Add Task\" ONCLICK=\"window.location.href='index.php?x=createTask&y=".$row['project_id']."'\">
				<input type=\"button\" class=\"editProjectIcon\" title=\"Edit Project\" ONCLICK=\"window.location.href='index.php?x=editProject&y=".$row['project_id']."'\">
				<input type=\"button\" class=\"deleteProjectIcon\" title=\"Delete Project\" ONCLICK=\"popupProject(".$row['project_id'].")\">
				</div>
			</div>
			<div id=\"".$row['project_id']."_bottom\" class=\"project_bottom\">

			<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" class=\"taskTable\">
			<tr>
			<td colspan=\"5\" valign=\"middle\" align=\"left\" class=\"project_desc\">
			<div class=\"desc\">
			<b>Client:</b> <a href=\"mailto:".$row3['email_addr']."\">".$row3['first_name']." ".$row3['last_name']."</a><br/>
			<b>Description:</b> ".$row['project_desc']."
			</div>
			<div class=\"time\">
			Time to completion: <font class=\"hours\">$hours1:$minutes1</font><br/>
			Time spent: <font class=\"hours\">$hours2:$minutes2</font>
			</div>
			<div class=\"date\">
			Deadline: <font class=\"deadline\">".$row['deadline']."</font><br/>
			Status: <font class=\"deadline\">$isOnTime</font><br/>
			</div>
			</td>
			</tr>
			<tr>
			<td valign=\"middle\" align=\"center\" class=\"taskNumHeader\" width=\"75\"><b>Task ID</b></td>
			<td valign=\"middle\" align=\"center\" class=\"devNumHeader\" width=\"100\"><b>Developer</b></td>
			<td valign=\"middle\" align=\"center\" class=\"taskNameHeader\"><b>Task Name</b></td>
			<td valign=\"middle\" align=\"center\" class=\"taskCompHeader\" width=\"85\"><b>Complete</b></td>
			<td valign=\"middle\" align=\"center\" class=\"editTaskHeader\" width=\"200\">&nbsp;</td>
			</tr>
		";

		while ($row2=mysql_fetch_assoc($task_list)) {
			echo "
				<tr>
				<td valign=\"middle\" align=\"center\" class=\"taskNum\">".$row2['task_id']."</td>
			";
				// get dev name for project x
				$dev_id= "".$row2['dev_id']."";
				$dev_results="SELECT * FROM users WHERE id='$dev_id'";
				$dev_list = mysql_query($dev_results, $conn) or die('Could not connect: ' . mysql_error());
				$devs= mysql_fetch_assoc($dev_list);

				if ($row2['complete']==1) {
					$complete_status = "Yes";
				}
				else {
					$complete_status = "No";
				}

				echo "
				<td valign=\"middle\" align=\"center\" class=\"devNum\">".$devs['first_name']."</td>
				<td valign=\"middle\" align=\"center\" class=\"taskName\">".$row2['task_name']."</td>
				<td valign=\"middle\" align=\"center\" class=\"taskComp\">$complete_status</td>
				<td valign=\"middle\" align=\"center\" class=\"editTask\" width=\"200\"><input type=\"button\" class=\"editTaskSmall\" ONCLICK=\"window.location.href='index.php?x=editTask&y=".$row2['task_id']."'\"> 
									<input type=\"button\" class=\"deleteTaskSmall\" ONCLICK=\"popupTask(".$row2['task_id'].")\"></td>
				</tr>
			";
		};

		echo "
			</table>
			</div>
			</center>
		";

	}
	//end while loop

	}

	else { echo "There are no active projects. <a href=\"index.php?x=createProject\">Create one</a>!"; }

	echo "

		</div> <!--end content-->

		</div> <!--end left container-->

		<div class=\"rightContainer\">

			<div class=\"header\">
				options
			</div>

			<div class=\"content\">
	";
	
	include("sidebar.php"); 
	
	echo "
			</div>

		</div> <!--end right container-->

		</div>
	";

}

//Log in and show the manager portal

function loginManager() {

	global $conn;

	// get manager id for project x
	$user_name = sanitize("".$_SESSION['username']."");	
	$user = "SELECT * FROM users WHERE username='$user_name'";
	$user_list = mysql_query($user, $conn) or die('Could not connect: ' . mysql_error());
	$user_array = mysql_fetch_assoc($user_list);
	$user_id = "".$user_array['id']."";

	echo "
		<div id=\"contentContainer\">
		<div class=\"leftContainer\">
		<div class=\"header\">
			manager portal
		</div>
		<div class=\"content\">
		<div class=\"sort_by\">SORT BY: Name [ <a href=\"?x=name_asc\">Ascending</a> | <a href=\"?x=name_desc\">Descending</a> ] or Deadline [ <a href=\"?x=deadline_soon\">Ascending</a> | <a href=\"?x=deadline_late\">Descending</a> ] or Urgency [ <a href=\"?x=nonurgent\">Ascending</a> | <a href=\"?x=urgent\">Descending</a> ]</div>
	";

	if( isset( $_POST["x"] ) || isset( $_GET["x"] ))
	{
		$order = sanitize(isset($_GET["x"]) ? $_GET["x"] : $_POST["x"]);
	}

	if ($order=="urgent")
	{
		//define the query
		$sql="SELECT * FROM projects WHERE id='$user_id' ORDER BY on_time DESC";
	}
	else if ($order=="nonurgent")
	{
		//define the query
		$sql="SELECT * FROM projects WHERE id='$user_id' ORDER BY on_time ASC";
	}
	else if ($order=="deadline_soon")
	{
		//define the query
		$sql="SELECT * FROM projects WHERE id='$user_id' ORDER BY deadline ASC";
	}
	else if ($order=="deadline_late")
	{
		//define the query
		$sql="SELECT * FROM projects WHERE id='$user_id' ORDER BY deadline DESC";
	}
	else if ($order=="name_asc")
	{
		//define the query
		$sql="SELECT * FROM projects ORDER BY project_name ASC";
	}
	else if ($order=="name_desc")
	{
		//define the query
		$sql="SELECT * FROM projects ORDER BY project_name DESC";
	}
	else {
		//define the query
		$sql="SELECT * FROM projects WHERE id='$user_id'";
	}

	$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
	$num = mysql_numrows($result);

	if ($num > 0) {

		while ($row=mysql_fetch_assoc($result)) {

			// get list of tasks for project x
			$project_id = "".$row['project_id']."";
			$sql2="SELECT * FROM tasks WHERE project_id='$project_id'";
			$task_list = mysql_query($sql2, $conn) or die('Could not connect: ' . mysql_error());
		
			// get client name for project x
			$client_id= "".$row['client_id']."";
			$sql3="SELECT * FROM clients WHERE client_id='$client_id'";
			$client_list = mysql_query($sql3, $conn) or die('Could not connect: ' . mysql_error());
			$row3= mysql_fetch_assoc($client_list);
		
			$complete_time = "".$row['complete_time']."";
			$minutes_plain1 = $complete_time % 60;
			$hours_plain1 = ($complete_time-$minutes_plain1)/60;	
			$minutes1 = sprintf("%02d",$minutes_plain1);
			$hours1 = sprintf("%02d",$hours_plain1);

			$spent_time = "".$row['spent_time']."";
			$minutes_plain2 = $spent_time % 60;
			$hours_plain2 = ($spent_time-$minutes_plain2)/60;
			$minutes2 = sprintf("%02d",$minutes_plain2);
			$hours2 = sprintf("%02d",$hours_plain2);

			$isOnTime = isOnTime($row['project_id']);

			if ($isOnTime=="<span class=\"onTime\">ON SCHEDULE</span>") {
				$status=1;
			}
			else if ($isOnTime=="<span class=\"due\">DUE SOON</span>") {
				$status=2;		
			}
			else if ($isOnTime=="<span class=\"late\">BEHIND SCHEDULE</span>") {
				$status=3;
			}
			else if ($isOnTime=="<span class=\"attn\">NEEDS DEADLINE</span>") {
				$status=4;
			}
			else if ($isOnTime=="<span class=\"complete\">COMPLETE</span>") {
				$status=0;
			}

			echo "

				<script type=\"text/javascript\">
				$(document).ready(function() {
					$('#".$row['project_id']."_bottom').hide();
					$('#".$row['project_id']."_top').click(function() {
						$(\"#".$row['project_id']."_bottom\").slideToggle(\"slow\");
					});
				});
				</script>

				<center>
				<div id=\"".$row['project_id']."_top\" class=\"project_top\">
					<div class=\"left\">".$row['project_name']."</div>
					<div class=\"right\">
					<input type=\"button\" class=\"addTaskIcon\" title=\"Add Task\" ONCLICK=\"window.location.href='index.php?x=createTask&y=".$row['project_id']."'\">
					<input type=\"button\" class=\"editProjectIcon\" title=\"Edit Project\" ONCLICK=\"window.location.href='index.php?x=editProject&y=".$row['project_id']."'\">
					</div>
				</div>
				<div id=\"".$row['project_id']."_bottom\" class=\"project_bottom\">

				<table border=\"0\" width=\"95%\" cellspacing=\"0\" cellpadding=\"5\" class=\"taskTable\">
				<tr>
				<td colspan=\"5\" valign=\"middle\" align=\"left\" class=\"project_desc\">

				<div class=\"desc\">
				<b>Client:</b> <a href=\"mailto:".$row3['email_addr']."\">".$row3['first_name']." ".$row3['last_name']."</a><br/>
				<b>Description:</b> ".$row['project_desc']."
				</div>
				<div class=\"time\">
				Time to completion: <font class=\"hours\">$hours1:$minutes1</font><br/>
				Time spent: <font class=\"hours\">$hours2:$minutes2</font>
				</div>
				<div class=\"date\">
				Deadline: <font class=\"deadline\">".$row['deadline']."</font><br/>
				Status: <font class=\"deadline\">$isOnTime</font><br/>
				</div>

				</td>
				</tr>
			";

			echo "
				<tr>
				<td valign=\"middle\" align=\"center\" class=\"taskNumHeader\" width=\"75\"><b>Task ID</b></td>
				<td valign=\"middle\" align=\"center\" class=\"devNumHeader\"><b>Developer</b></td>
				<td valign=\"middle\" align=\"center\" class=\"taskNameHeader\"><b>Task Name</b></td>
				<td valign=\"middle\" align=\"center\" class=\"taskCompHeader\" width=\"85\"><b>Complete</b></td>
				<td valign=\"middle\" align=\"center\" class=\"editTaskHeader\" width=\"200\">&nbsp;</td>
				</tr>
			";

			while ($row2=mysql_fetch_assoc($task_list)) {

				if ($row2['complete']==1) {
					$complete_status = "Yes";
				}
				else {
					$complete_status = "No";
				}

				echo "
					<tr>
					<td valign=\"middle\" align=\"center\" class=\"taskNum\" width=\"75\">".$row2['task_id']."</td>";

					// get dev name for project x
					$dev_id= "".$row2['dev_id']."";
					$dev_results="SELECT * FROM users WHERE id='$dev_id'";
					$dev_list = mysql_query($dev_results, $conn) or die('Could not connect: ' . mysql_error());
					$devs= mysql_fetch_assoc($dev_list);
				echo "
					<td valign=\"middle\" align=\"center\" class=\"devNum\" width=\"100\">".$devs['first_name']."</td>
					<td valign=\"middle\" align=\"center\" class=\"taskName\">".$row2['task_name']."</td>
					<td valign=\"middle\" align=\"center\" class=\"taskComp\">$complete_status</td>
					<td valign=\"middle\" align=\"center\" class=\"editTask\"  width=\"200\" title=\"Edit Task\"><input type=\"button\" class=\"editTaskSmall\" ONCLICK=\"window.location.href='index.php?x=editTask&y=".$row2['task_id']."'\"> 
									<input type=\"button\" class=\"deleteTaskSmall\" width=\"200\" title=\"Delete Task\" ONCLICK=\"popupTask(".$row2['task_id'].")\"></td>
					</tr>
				";
			};
		echo "
			</table>
			</div>
			</center>
		";

		}


	}

	else {
		
		echo "You have no active projects.";
	}

	echo "
		</div> <!--end content-->

		</div> <!--end left container-->

		<div class=\"rightContainer\">

			<div class=\"header\">
				options
			</div>

			<div class=\"content\"> 
	";
	
	include("sidebar.php"); 
	
	echo "
			</div>


		</div> <!--end right container-->

		</div>
	";

}

//Log in and show the developer portal

function loginDev() {

	global $conn;

	//define the query
	$user_name = sanitize("".$_SESSION['username']."");	
	$user = "SELECT * FROM users WHERE username='$user_name'";
	$user_list = mysql_query($user, $conn) or die('Could not connect: ' . mysql_error());
	$user_array = mysql_fetch_assoc($user_list);
	$user_id = "".$user_array['id']."";

	echo "
		<div id=\"contentContainer\">
		<div class=\"leftContainer\">
		<div class=\"header\">
			developer portal
		</div>
		<div class=\"content\">
		<div class=\"sort_by\">SORT BY: Task Name [ <a href=\"?x=tname_asc\">Ascending</a> | <a href=\"?x=tname_desc\">Descending</a> ] or Project [ <a href=\"?x=pname_asc\">Ascending</a> | <a href=\"?x=pname_desc\">Descending</a> ]</div>
	";


	//get tasks for developer y

	if( isset( $_POST["x"] ) || isset( $_GET["x"] ))
	{
		$order = sanitize(isset($_GET["x"]) ? $_GET["x"] : $_POST["x"]);
	}

	if ($order=="tname_asc")
	{
		//define the query
		$sql="SELECT * FROM tasks WHERE dev_id='$user_id' ORDER BY task_name";
	}
	else if ($order=="tname_desc")
	{
		//define the query
		$sql="SELECT * FROM tasks WHERE dev_id='$user_id' ORDER BY task_name DESC";
	}
	else if ($order=="pname_asc")
	{
		//define the query
		$sql="SELECT * FROM tasks WHERE dev_id='$user_id' ORDER BY project_id ASC";
	}
	else if ($order=="pname_desc")
	{
		//define the query
		$sql="SELECT * FROM tasks WHERE dev_id='$user_id' ORDER BY project_id DESC";
	}
	else {
		//define the query
		$sql="SELECT * FROM tasks WHERE dev_id='$user_id'";
	}

	$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
	$num = mysql_numrows($result);

	if ($num > 0) {


		while ($row=mysql_fetch_assoc($result)) {

	// get project name for task x
	$project_id= "".$row['project_id']."";
	$sql2="SELECT * FROM projects WHERE project_id='$project_id'";
	$project_list = mysql_query($sql2, $conn) or die('Could not connect: ' . mysql_error());
	$row2= mysql_fetch_assoc($project_list);

	// get project manager for project z
	$manager_id= "".$row2['id']."";
	$sql3="SELECT * FROM users WHERE id='$manager_id'";
	$manager_list = mysql_query($sql3, $conn) or die('Could not connect: ' . mysql_error());
	$row3= mysql_fetch_assoc($manager_list);

	$complete_time = "".$row['complete_time']."";
	$minutes_plain1 = $complete_time % 60;
	$hours_plain1 = ($complete_time-$minutes_plain1)/60;	
	$minutes1 = sprintf("%02d",$minutes_plain1);
	$hours1 = sprintf("%02d",$hours_plain1);

	$spent_time = "".$row['spent_time']."";
	$minutes_plain2 = $spent_time % 60;
	$hours_plain2 = ($spent_time-$minutes_plain2)/60;
	$minutes2 = sprintf("%02d",$minutes_plain2);
	$hours2 = sprintf("%02d",$hours_plain2);

	$isOnTime = isOnTime($row['project_id']);

			echo "

				<script type=\"text/javascript\">
				$(document).ready(function() {
					$('#".$row['task_id']."_bottom').hide();
					$('#".$row['task_id']."_top').click(function() {
						$(\"#".$row['task_id']."_bottom\").slideToggle(\"slow\");
					});
				});
				</script>

				<center>
				<div id=\"".$row['task_id']."_top\" class=\"project_top\">
					<div class=\"left\">".$row['task_name']."</div>
					<div class=\"right\">
						<input type=\"button\" class=\"editProjectIcon\" title=\"Edit Task\" ONCLICK=\"window.location.href='index.php?x=editTask&y=".$row['task_id']."'\"/> &nbsp;
					</div>
				</div>
				<div id=\"".$row['task_id']."_bottom\" class=\"project_bottom\">

				<table border=\"0\" width=\"95%\" cellspacing=\"0\" cellpadding=\"5\" class=\"taskTable\">
				<tr>
				<td colspan=\"3\" valign=\"middle\" align=\"left\" class=\"task_desc\">

				<div class=\"desc\">
				<b>Project Name:</b> ".$row2['project_name']."<br/>
				<b>Project Manager:</b> <a href=\"mailto:".$row3['email_addr']."\">".$row3['first_name']." ".$row3['last_name']."</a>
				</div>
				<div class=\"time\">
				Time to completion: <font class=\"hours\">$hours1:$minutes1</font><br/>
				Time spent: <font class=\"hours\">$hours2:$minutes2</font>
				</div>
				<div class=\"date\">
				Project Deadline: <font class=\"deadline\">".$row2['deadline']."</font><br/>
				Project Status: <font class=\"deadline\">$isOnTime</font><br/>
				</div>

				</td>
				</tr>

				<tr>
				<td valign=\"middle\" align=\"center\" class=\"ftpAddressHeader\" width=\"33%\"><b>FTP Host Address</b></td>
				<td valign=\"middle\" align=\"center\" class=\"ftpNameHeader\" width=\"33%\"><b>FTP Username</b></td>
				<td valign=\"middle\" align=\"center\" class=\"ftpPassHeader\" width=\"33%\"><b>FTP Password</b></td>
				</tr>
				<tr>
				<td valign=\"middle\" align=\"center\" class=\"ftpAddress\">".$row2['ftp_host']."</td>
				<td valign=\"middle\" align=\"center\" class=\"ftpName\">".$row2['ftp_username']."</td>
				<td valign=\"middle\" align=\"center\" class=\"ftpPass\">".$row2['ftp_pass']."</td>
				</tr>
				<tr>
				<td colspan=\"3\" valign=\"top\" align=\"left\" class=\"taskNotes\"><b>Notes:</b><br/>".$row['notes']."</td>
				</tr>


				</table>
				</div>
				</center>
			";


			};
	



	}

	else { 
		echo "You have no active tasks."; 
	};

	echo "
		</div> <!--end content-->

		</div> <!--end left container-->

		<div class=\"rightContainer\">

			<div class=\"header\">
				options
			</div>

			<div class=\"content\"> 
	";
	
	include("sidebar.php"); 
	
	echo "
			</div>


		</div> <!--end right container-->

		</div>
	";

}

function showDefault() {

	echo "

		<div id=\"contentContainer\">

			<div class=\"leftContainer\">
			<div class=\"headerMain\">
				
			</div>

			<div class=\"contentMain\">
				<h2>Welcome to <font class=\"jorp\">Jorp</font>!</h2>
				<p>This system has been built using XHTML, CSS, PHP, and MySQL and is being coded in gedit. It is still in its beta stage and is part of an open source project. Releases will be uploaded regularly.</p>";

				if (file_exists("install.php")) echo "Click <a href=\"install.php\">here</a> to install Jorp on your website.";	

	echo "			<center><img src=\"images/jorp.png\" alt=\"Jorp\" border=\"0\"/></center>
			</div>

			<div class=\"contentFooter\"></div>	
			</div>

			<div class=\"rightContainer\">
			<div class=\"header\">
				User Login
			</div>

			<div class=\"content\">

				<div class=\"login\">

				<form action=\"\" method=\"post\">
				    <label for=\"user\">Username: </label>
					      <input type=\"text\" name=\"user\" maxlength=\"30\"/><br/>
				    <label for=\"pass\">Password: </label>
					      <input type=\"password\" name=\"pass\" maxlength=\"30\"/><br/><br/>
				    <input type=\"submit\" name=\"sublogin\" value=\"Login\" class=\"send\"/>
				 </form>

				</div>

			</div>

			<div class=\"contentFooter\"></div>
	
			</div>

			</div>
	";
}

function displayLogin() {

	global $logged_in;
	if($logged_in) {

		$status = "1";

		$sql = "UPDATE users 
			SET
				logged_in='$status'
			WHERE username='$user_name'";

		mysql_query($sql);

		if (file_exists("install.php"))
			echo "<span class=\"error\">WARNING: Please delete your install.php file!</span>";

		if (getUserLevel()=="1") {
			loginAdmin();
		}
		else if (getUserLevel()=="2") {
			loginManager();
		}
		else if (getUserLevel()=="3") {
			loginDev();
		};

	}
	   
	else {
		showDefault();
	}
	
}


/**
 * checks if user has submitted his
 * username and password through the login form
 */
if(isset($_POST['sublogin'])){

   /* make sure all fields were filled */
   if(!$_POST['user'] || !$_POST['pass']){
      die("You didn't fill in a required field.");
   }
   /* check length */
   $_POST['user'] = trim($_POST['user']);
   if(strlen($_POST['user']) > 30){
      die("The username cannot be longer than 30 characters.");
   }

   /* checks that username is in database and password is correct */
   $md5pass = md5($_POST['pass']);
   $result = confirmUser($_POST['user'], $md5pass);

   if($result == 1){
?>

<div id="contentContainer">

	<div class="leftContainer">
	<div class="headerMain">
		Error
	</div>

	<div class="contentMain">
		<p>We're sorry. That user does not exist in our database. 
		</p>
	</div>
	
	</div>

	<div class="rightContainer">
	<div class="header">
		User Login
	</div>

	<div class="contentMain">

<div class="login">

<form action="" method="post">
    <label for="user">Username: </label>
              <input type="text" name="user" maxlength="30"/><br/>
    <label for="pass">Password: </label>
              <input type="password" name="pass" maxlength="30"/><br/><br/>
    <input type="submit" name="sublogin" value="Login" class="send"/><br/>
 </form>

</div>

	</div>

	<div class="contentFooter"></div>	
	</div>
	</div>
</div>

<?
	include("footer.php");
	die('');
   }
   else if($result == 2){

?>

<div id="contentContainer">

	<div class="leftContainer">
	<div class="headerMain">
		Error
	</div>

	<div class="contentMain">
		<p>Incorrect password. Please try again. 
		</p>
	</div>

	</div>

	<div class="rightContainer">
	<div class="header">
		User Login
	</div>

	<div class="contentMain">

<div class="login">

<form action="" method="post">
    <label for="user">Username: </label>
              <input type="text" name="user" maxlength="30"/><br/>
    <label for="pass">Password: </label>
              <input type="password" name="pass" maxlength="30"/><br/><br/>
    <input type="submit" name="sublogin" value="Login" class="send"/><br/>
 </form>

</div>

	</div>

	<div class="contentFooter"></div>	
	</div>
</div>

<?
	include("footer.php");
	die('');
   }

   /* username and password correct, register session variables */
   $_POST['user'] = sanitize($_POST['user']);
   $_SESSION['username'] = $_POST['user'];
   $_SESSION['password'] = $md5pass;

   if(isset($_POST['remember'])){
      setcookie("cookname", $_SESSION['username'], time()+60*60*24*100, "/");
      setcookie("cookpass", $_SESSION['password'], time()+60*60*24*100, "/");
   }

   /* self-redirect */
   echo "<meta http-equiv=\"Refresh\" content=\"0;url=$HTTP_SERVER_VARS[PHP_SELF]\">";
   return;
}

/* sets the value of the logged_in variable */
$logged_in = checkLogin();

?>
