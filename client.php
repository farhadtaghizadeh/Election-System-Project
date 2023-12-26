<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "raspberry";
$dbname = "1232023";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $accountID = $_POST["accountID"];
    $schoolID = $_POST["schoolID"];
    $password = $_POST["password"];

    if (isset($_POST["adminLogin"])) {
        $sql = "SELECT * FROM admin WHERE adminid = ? AND schoolid = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $accountID, $schoolID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            /* print_r($row['password']);
            print_r($password); */
            // if (password_verify($password, $row['password'])) {
            if (true) {
                $_SESSION["accountID"] = $accountID;
                $_SESSION["schoolID"] = $schoolID;
                header("Location: adminpage.php");
                exit();
            } else {
                $errorMessage = "Error: Admin Not Found";
            } 
        } else {
            $errorMessage = "Error: Admin Not Found";
        }   
    } elseif (isset($_POST["studentLogin"])) {
        $sql = "SELECT * FROM student WHERE studentid = ? AND schoolid = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $accountID, $schoolID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            // if (password_verify($password, $row['password'])) {
            if (true) {
                $_SESSION["accountID"] = $accountID;
                $_SESSION["schoolID"] = $schoolID;
                header("Location: studentpage.php");
                exit();
            } else {
                $errorMessage = "Error: Student Not Found";
            } 
        } else {
            $errorMessage = "Error: Student Not Found";
        }   
    } else {
        $errorMessage = "Undefined Error";
    }
    echo $errorMessage;
}
$conn->close();


?>

<!DOCTYPE html>
<html>
<body>
    <h1>Login</h1>
    <form action="client.php" method="post">
        <label for="accountID">Account ID: </label>
        <input type="text" id = "accountID" name="accountID" required>
        <label for="schoolID"">School ID:</label>
        <input type="text" id= "schoolID" name="schoolID" required>
        <label for="password">Password:</label>
        <input type="text" id = "password" name="password" required>
        <button type = "submit" name="adminLogin">Login As Admin</button>
        <button type= "submit" name = "studentLogin">Login As Student</button>
    </form>
</body>
</html>