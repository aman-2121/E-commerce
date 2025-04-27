<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="l.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="eyob">
        <h1>register form</h1>
    <form action="aman.php" method="POST">
        <div class="aman">
            <i class="fas fa-user"></i>

            <input type="text" name="name" 
        </div>
        <div class="aman">
            <i class="fas fa-envelope"></i>
        
            <input type="text" name="email" 
        </div>
        <div class="aman">
           
            <input type="text" name="id" placeholder="id">
        </div>
        <div class="aman">

            <input type="password" name="password" placeholder="passworrd">
        </div>
        <div class="aman">
            
            <input type="submit" name="submit" id="register" value="register">
        </div>
    </form>
</div>
</body>
</html>
<?php
$servername="localhost";
$username= "root";
$password= "";
$dbname= "mm";
$conn = new mysqli($servername, $username, $password,$dbname);
if ($conn) {
    echo"connection succes";
  
    $name=$_POST["name"];
    $email=$_POST["email"];
    $id=$_POST["id"];
    $pass=$_POST["password"];
    $stmt=$conn->prepare("INSERT INTO bro(name,email,id,password)values(?,?,?,?)");
    $stmt->bind_param("ssss", $name, $email,$id,$password);
    $stmt->execute();{
        echo "register sucsesfully";
    }   }
    else{
        echo "connection faild";

    }
?>