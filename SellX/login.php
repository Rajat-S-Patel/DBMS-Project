<?php

use function PHPSTORM_META\type;

session_start();
    //start session
    //check if user is already logged in
    if(isset($_SESSION["loggedin"])&& $_SESSION["loggedin"]===true){
        header("location:body.php");
        exit;
    }
    require_once "config.php";

    $username=$password="";
    $username_err=$password_err="";

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(empty(trim($_POST["username"]))){
            $username_err="Please enter username";

        }
        else{
            $username=htmlspecialchars(strtolower(trim($_POST["username"])));
        }
        if(empty(trim($_POST["password"]))){
            $password_err="Please enter password";

        }
        else{
            $password=htmlspecialchars(trim($_POST["password"]));
        }

        if(empty($username_err)&&empty($password_err)){
            $sql="select password from users where userid='$username'";
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

            $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);
            
            if(!$row){
                $username_err="No account found";
            }
            else if(count($row)==1){
               $item=array_values($row)[0];
               if($item==$password){
                   session_start();
                   $_SESSION["loggedin"]=true;
                   $_SESSION["username"]=$username;
                    $sqllastvisit="UPDATE USERS SET LAST_VISIT=SYSDATE WHERE USERID='$username'";
                    $s_visit=oci_parse($c,$sqllastvisit);
                    oci_execute($s_visit);
                   header("location:body.php?tab=HOME");
               }
               else{
                   $password_err="Invalid password";
               }
           } 
           

        }
        else{
            echo "Oops! Something went wrong. Please try again later.";
        }
        
        oci_close($c);

    }
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <link rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

        <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ 
            width: 350px;
            padding: 20px; 
            box-shadow: 0px 4px 4px 4px lightgray;
            margin-left: auto;
            margin-right: auto;
            display: block;
            margin-top:100px;
        }
    </style>
    </head>
    <body>
    
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="signup.php">Sign up now</a>.</p>
        </form>
    </div>  
    
</body>
</html>