<?php
    setcookie('cameFromForm', 'yes', time() + 3600);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>PHP Predefined Variables</title>
		<link href="assets/styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
		<div id="HeaderWrapper">
            <div id="Header">
                <a href="index.php"><img src="assets/hwl.jpg" border="0" alt=""></a>
                <h2>
                    Join Our Literature <br>Mailing List
                </h2>
            </div>        
        </div>
        <div id="Body">
            <form method="post" action="final.php?page2" >
                <div>
                    <label>Favorite Author:</label>
                    <select name="authorSelect">
                        <option value="Henry Wadsworth Longfellow">Henry Wadsworth Longfellow</option>
                        <option value="Charles Dickens">Charles Dickens</option>
                        <option value="Jane Austin">Jane Austin</option>
                    </select>
                </div>		
                <div>
                    <label>Genre:</label>
                    <select name="genreMultiSelect" multiple>
                        <option value="Fiction">Fiction</option>
                        <option value="Autobiography">Autobiography</option>
                        <option value="Biography">Biography</option>
                        <option value="Poetry">Poetry</option>
                        <option value="Science">Science</option>
                    </select>
                </div>		
                <div class="multiple">
                    <label>Favorite Century:</label>
                    17th Century <input type="checkbox" name="centuryCheckbox" value="17th">
                    18th Century <input type="checkbox" name="centuryCheckbox" value="18th"> 
                    19th Century <input type="checkbox" name="centuryCheckbox" value="19th"> 
                </div>
                <div>
                    <label>Comments:</label>
                    <textarea name="commentsTextArea"></textarea>
                </div>
                <div>
                    <label>E-mail Address:</label>
                    <input type="text" name="emailText" />
                </div>
                <div  class="multiple">
                    <label>Receive Newsletter:</label>
                    Yes <input type="radio" name="newsletterRadio" value="yes">
                    No <input type="radio" name="newsletterRadio" value="no">
                </div>
                <div>
                    <label>Add File:</label>
                    <input type="file" name="newFile" />
                </div>
                <div>
                    <label>Password:</label>
                    <input type="password" name="psswdPassword" />
                </div>
                <div class="multiple">
                    <label>&nbsp;</label>
                    <input type="submit" name="submit" value="Join Mailing List">
                </div>
            </form>
        </div>
	</body>
</html>