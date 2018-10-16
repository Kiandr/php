<?php
require_once "Db.php"
?>

<?php
session_start();
$message = '';
echo "here";
if (isset($_POST['name']) && isset($_POST['password'])) {
    
    $myDb = new Db();
    //$myDb->GetAlLFromUser();
    echo $myDb->Login($_POST['name'], $_POST['password']);
    //echo $myDb;

    // $db = mysqli_connect('localhost', 'root', '', 'php');
    // $sql = sprintf("SELECT * FROM users WHERE name='%s'",
    //     mysqli_real_escape_string($db, $_POST['name'])
    // );
    // $result = mysqli_query($db, $sql);
    // $row = mysqli_fetch_assoc($result);
    // if ($row) {
    //     $hash = $row['password'];
    //     $isAdmin = $row['isAdmin'];

    //     if (password_verify($_POST['password'], $hash)) {
    //         $message = 'Login successful.';

    //         $_SESSION['user'] = $row['name'];
    //         $_SESSION['isAdmin'] = $isAdmin;

    //     } else {
    //         $message = 'Login failed.';
    //     }
    // } else {
    //     $message = 'Login failed.';
    // }
    // mysqli_close($db);
}


?>


<header>
</header>
<body>
<form method="post" action="">
<br>
UserName: <input name="name" type="text"></input>
<br>
PassWord <input name="password" type="password"></input>
</br>
<input type = "submit" value="Login">
</form>
</body>