<?php
    session_start();

    if(!isset($_SESSION["loggedin"])||$_SESSION["loggedin"]!==true){
        header("location:login.php");
        exit;
    }

    require_once "config.php";

    $new_password = $confirm_password = "";
    $new_password_err = $confirm_password_err = "";

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(empty(trim($_POST["new_password"]))){
            $new_password_err="please enter new password";

        }
        else{
            $new_password=trim($_POST["new_password"]);
        
        }
        if(empty(trim($_POST["confirm_password"]))){
            $confirm_password_err = "Please confirm the password.";
        }
        else{
            $confirm_password = trim($_POST["confirm_password"]);
            if(empty($new_password_err) && ($new_password != $confirm_password)){
                $confirm_password_err = "Password did not match.";
            }
        }

        if(!empty($new_password)&&!empty($confirm_password)){
            $name=$_SESSION["username"];
            $sql="update users set password = '$new_password' where username='$name'";
            echo "sql = ".$sql;
            $s = oci_parse($c, $sql);
            if (!$s) {
                $m = oci_error($c);
                trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);
            }
            $r = oci_execute($s);
            if (!$r) {
                $m = oci_error($s);
                trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
            }
            
            if($r&&$s){
                session_destroy();
                header("location:login.php");
                exit();
            }
            else{
                echo "Something went wrong";
            }
            oci_cancel($s);
            oci_close($c);
        }

    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px;box-shadow:0px 4px 4px 4px lightgray;margin-top: 100px;}

    </style>
</head>
<body>
    <center>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="welcome.php">Cancel</a>
            </div>
        </form>
    </div>    
    </center>
</body>
</html>
