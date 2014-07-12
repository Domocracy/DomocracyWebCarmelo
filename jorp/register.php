<?
session_start(); 
include("database.php");

/**
 * Returns true if the username has been taken
 * by another user, false otherwise.
 */
function usernameTaken($username){
   global $conn;
   if(!get_magic_quotes_gpc()){
      $username = sanitize($username);
   }
   $q = "select username from users where username = '$username'";
   $result = mysql_query($q,$conn);
   return (mysql_numrows($result) > 0);
}

/**
 * Inserts the given (username, password) pair
 * into the database. Returns true on success,
 * false otherwise.
 */
function addNewUser($id, $username, $password, $first_name, $last_name, $role, $email_addr, $ph_num){
   global $conn;
   $q = "INSERT INTO users VALUES ('$id', '$username', '$password', '$first_name', '$last_name', '$role', '$email_addr', '$ph_num', '$logged_in')";
   return mysql_query($q,$conn);
}

/**
 * Displays the appropriate message to the user
 * after the registration attempt.
 */
function displayStatus(){
   $uname = sanitize($_SESSION['reguname']);
   if($_SESSION['regresult']){
?>

<p>[<b><? echo $uname; ?></b>] has been added to the database.</p>

<?
   }
   else{
?>

<p>An error has occurred and [<b><? echo $uname; ?></b>] was not added to the database.<br/></p>

<?
   }
   unset($_SESSION['reguname']);
   unset($_SESSION['registered']);
   unset($_SESSION['regresult']);
}

if(isset($_SESSION['registered'])){
/**
 * This is the page that will be displayed after the
 * registration has been attempted.
 */


?>


<div id="contentContainer">
	<div class="leftContainer">
	<div class="header">
		Add New User
	</div>

	<div class="content">
		<? displayStatus(); 


 echo "
		</div> <!--end content-->

		</div> <!--end left container-->

		<div class=\"rightContainer\">

			<div class=\"header\">
				Options
			</div>

			<div class=\"content\">
	";
	
	include("sidebar.php"); 
	
	echo "
			</div>

		</div> <!--end right container-->

		</div>
	";


   return;
}

/**
 * Determines whether or not to show to sign-up form
 * based on whether the form has been submitted, if it
 * has, check the database for consistency and create
 * the new account.
 */
if(isset($_POST['subjoin'])){
   /* Make sure all fields were entered */
   if(!$_POST['user'] || !$_POST['pass'] || !$_POST['first_name'] || !$_POST['last_name'] || !$_POST['ph_num'] || !$_POST['email_addr']){
      die('You didn\'t fill in a required field.');
   }

   /* Spruce up username, check length */
   $_POST['user'] = trim($_POST['user']);
   if(strlen($_POST['user']) > 30){
      die("Sorry, the username is longer than 30 characters, please shorten it.");
   }

   /* Check if username is already in use */
   if(usernameTaken($_POST['user'])){
      $use = $_POST['user'];
      die("Sorry, the username: <strong>$use</strong> is already taken, please pick another one.");
   }

   /* Add the new account to the database */
   $md5pass = md5(sanitize($_POST['pass']));
   $_SESSION['reguname'] = sanitize($_POST['user']);
   $_SESSION['regresult'] = addNewUser(sanitize($_POST['id']), sanitize($_POST['user']), $md5pass, sanitize($_POST['first_name']), sanitize($_POST['last_name']), sanitize($_POST['role']), sanitize($_POST['email_addr']), sanitize($_POST['ph_num']), '0');
   $_SESSION['registered'] = true;
   echo "<meta http-equiv=\"Refresh\" content=\"0;url=$HTTP_SERVER_VARS[PHP_SELF]\">";
   return;
}
else{
/**
 * This is the page with the sign-up form, the names
 * of the input fields are important and should not
 * be changed.
 */
?>


<div id="contentContainer">
	<div class="leftContainer">
	<div class="header">
		Add New User
	</div>

	<div class="content">

<center>
<table border="0" width="300"><tr><td>
<font class="required">*</font> = Required<br/><br/>
<form action="<? echo $HTTP_SERVER_VARS['PHP_SELF']; ?>" method="post">
<input type="hidden" name="id" maxlength="5" >

    <label for="username"><font class="required">*</font> Username: </label>
<input type="text" name="user" maxlength="30"><br/>

   <label for="password"><font class="required">*</font> Password: </label>
<input type="password" name="pass" maxlength="32">

    <label for="first_name"><font class="required">*</font> First name: </label>
<input type="text" name="first_name" maxlength="30"><br/>

    <label for="last_name"><font class="required">*</font> Last name: </label>
<input type="text" name="last_name" maxlength="30"><br/>

    <label for="role">Role:</label>
<?
$sql="SELECT * FROM roles";
$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
echo "<select name=\"role\">";
while ($row=mysql_fetch_assoc($result)) {
	echo "<option value=\"".$row['role']."\">".$row['role_name']."</option>";
}
echo "</select><br/>";
?>

    <label for="email_addr"><font class="required">*</font> E-mail Address: </label>
<input type="text" name="email_addr" maxlength="200"><br/>

    <label for="ph_num"><font class="required">*</font> Phone Number (123-456-7890): </label>
<input type="text" name="ph_num" maxlength="12"><br/>

<br/>
<input type="submit" name="subjoin" value="Create User" class="send">
</form>
</td></tr></table>
</center>

<? echo "
		</div> <!--end content-->

		</div> <!--end left container-->

		<div class=\"rightContainer\">

			<div class=\"header\">
				Options
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
?>
