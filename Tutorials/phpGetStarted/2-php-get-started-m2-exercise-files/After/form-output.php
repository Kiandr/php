<!DOCTYPE html>
<html>
<head>
    <title>PHP</title>
</head>
<body>
<?php
  if (isset($_POST['submit'])) {
      // process form
      printf('User name: %s
      <br>Password: %s
      <br>Gender: %s
      <br>Color: %s
      <br>Language(s): %s
      <br>Comments: %s
      <br>T&amp;C: %s',
          htmlspecialchars($_POST['name']),
          htmlspecialchars($_POST['password']),
          htmlspecialchars($_POST['gender']),
          htmlspecialchars($_POST['color']),
          htmlspecialchars(implode(' ', $_POST['languages'])),
          htmlspecialchars($_POST['comments']),
          htmlspecialchars($_POST['tc']));
  }
?>
<form method="post" action="">
    User name: <input type="text" name="name"><br>
    Password: <input type="password" name="password"><br>
    Gender:
        <input type="radio" name="gender" value="f">female
        <input type="radio" name="gender" value="m">male<br>
    Favorite color:
        <select name="color">
            <option value="#f00">red</option>
            <option value="#0f0">green</option>
            <option value="#00f">blue</option>
        </select><br>
    Languages spoken:
        <select name="languages[]" multiple size="3">
            <option value="en">English</option>
            <option value="fr">French</option>
            <option value="it">Italian</option>
        </select><br>
    Comments: <textarea name="comments"></textarea><br>
    <input type="checkbox" name="tc" value="ok">I accept the T&C<br>
    <input type="submit" name="submit" value="Submit">
</form>
</body>
</html>