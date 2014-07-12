<script type="text/javascript">

$(document).ready(function(){
	$("form#email").submit(function() {
 
        var serializedCheckboxes = '';

        $(this).find("input[type=checkbox]").each(function() {
		 if($(this).attr("checked")) {
		        serializedCheckboxes += (serializedCheckboxes != '' ? ', ' : '') + $(this).attr("value");
		 }
        });

        if (serializedCheckboxes != '')
                $(this).find("input[name=allchecks]").attr("value", serializedCheckboxes);

         var sender = $('.sender').attr('value');  
         var subject = $('.subject').attr('value');  
         var message = $('.message').attr('value');  

	 var data = 'recip='+ serializedCheckboxes + '&sender='+ sender + '&subject='+ subject + '&message='+ message;

	        $.ajax({
		        type: "POST",
		        url: "ajax.php",
		        data: data,
		        success: function(){
			        $('div#alert').slideDown("slow");
			        $('div#alert').fadeOut(2000);
		        }
	        });
	return false;
	});
});

</script>

<?php

        echo "<form id=\"email\" enctype=\"multipart/form-data\" action=\"\" method=\"post\">\n";

        echo "<label for=\"client_id\">Recipients: </label>";

        $sql="SELECT * FROM users";
        $result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
	echo "<ul class=\"recipients\">";
        while ($row=mysql_fetch_assoc($result)) {
	        {
			$fname = "".$row['first_name']."";
			$lname = "".$row['last_name']."";
		        echo "<li><div class=\"left\"><input type=\"checkbox\" name=\"address\" value=\"".$row['email_addr']."\"/></div><div class=\"right\">$fname $lname</div></li>";
	        }
        }
	echo "</ul>";

        echo "<label for=\"sender\">Your E-mail Address: </label><input class=\"sender\" type=\"text\" value=\"\" maxlength=\"50\" name=\"sender\"/><br />\n";

        echo "<label for=\"subject\">Subject: </label><input class=\"subject\" type=\"text\" value=\"\" maxlength=\"50\" name=\"subject\"/><br />\n";

        echo "<label for=\"message\">Message: </label><textarea class=\"message\" cols=\"80\" rows=\"2\" wrap=\"ON\" name=\"message\"></textarea><br /><br />\n";
	
	echo "<div id=\"alert\">Your e-mail has been sent.</div>";

        echo "<input type=\"submit\" name=\"submit\" value=\"Send E-mail\" class=\"send\">";

        echo "</form>";

?>
