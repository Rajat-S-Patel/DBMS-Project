<?php
require_once "config.php";

$username = $pass = $name = $semester = $mobile = $branch = $confirm_password = $state = $city = $college = "";
$username_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter email!";
    }else if(!filter_var(trim($_POST['username']),FILTER_VALIDATE_EMAIL)){
        $username_err="Invalid email";
    } 
    else if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter password";
    }elseif(strlen($_POST['password'])<6){
        $password_err="Password length should be greater than 6 characters";
    } 
    elseif (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please enter the confirm password";
    } else {
        $semester = htmlspecialchars($_POST["semester"]);
        $mobile = htmlspecialchars($_POST["mobile"]);
        $branch = htmlspecialchars($_POST["branch"]);
        $name = htmlspecialchars($_POST["name"]);
        $username = htmlspecialchars(strtolower($_POST["username"]));
    
        $pass = htmlspecialchars($_POST["password"]);
        $state = htmlspecialchars($_POST['stt']);
        $city = htmlspecialchars($_POST['stt1']);       // for security htmlspecialchars() is used to prevent html injection.
        $college = htmlspecialchars($_POST['college']);
        $conf_pass =htmlspecialchars($_POST["confirm_password"]);
        $sql = "select password from users where userid= '$username'";
        $s = oci_parse($c, $sql);



        if (!$s) {
            $m = oci_error($c);
            trigger_error('Could not parse statement: ' . $m['message'], E_USER_ERROR);
        }
        $r = oci_execute($s);
        if (!$r) {
            $m = oci_error($s);
            trigger_error('Could not execute statement: ' . $m['message'], E_USER_ERROR);
        }

        $row = oci_fetch_array($s, OCI_ASSOC + OCI_RETURN_NULLS);
        //echo "row = ".array_values($row)[0];
        if ($row == false) {

            if ($conf_pass == $pass) {
                $sql = "insert into users(userid,password,name,semester,branch,phone,date_of_join,city,state,college) values('$username','$pass','$name','$semester','$branch','$mobile',sysdate,'$city','$state','$college')";
                $s = oci_parse($c, $sql);
                if (!$s) {
                    $m = oci_error($c);
                    trigger_error('Could not parse statement: ' . $m['message'], E_USER_ERROR);
                }
                $r = oci_execute($s);
                if (!$r) {
                    $m = oci_error($s);
                    trigger_error('Could not execute statement: ' . $m['message'], E_USER_ERROR);
                } else {
                    header("Location: login.php");
                    oci_cancel($s);
                    oci_close($c);
                }
            } else {
                $confirm_password_err = "password and confirm password mismatch";
            }
        } else if (count($row) >= 1) {
            $username_err = "User already exist";
            //echo  "<script>alert('user already exist')</script>"; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">-->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/cities.js"></script>

    <style type="text/css">
        body {
            font: 14px sans-serif;
        }

        #select_state_city{
            display: flex;
        }
        #select_state_city *{
            margin:10px 10px 10px 0px;
        }
        .wrapper {
            width: 90%;
            padding: 20px;
            margin-right: auto;
            margin-left: auto;
            display: block;
            box-shadow: 0px 4px 4px 4px lightgray;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .card-body {
            width: 80%;
        }

        .form-group {
            width: 70%;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="wrapper">
            <div class="card-header">
                <h2>Sign Up</h2>
                <p>Please fill this form to create an account.</p>

            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="card-body" style="width: 100%;">
                <table style="width: 100%;">
                    <tr>
                        <th>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required="true">

                            </div>



                            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                <label>Email</label>
                                <input type="text" name="username" class="form-control">
                                <span class="help-block"><?php echo $username_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control">
                                <span class="help-block"><?php echo $password_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control">
                                <span class="help-block"><?php echo $confirm_password_err; ?></span>
                            </div>

                        </th>


                        <th>
                            <div class="form-group">
                                <label>Semester</label>
                                <select class="form-control" name="semester">
                                    <option value="1">1st</option>
                                    <option value="2">2nd</option>
                                    <option value="3">3rd</option>
                                    <option value="4">4th</option>
                                    <option value="5">5th</option>
                                    <option value="6">6th</option>
                                    <option value="7">7th</option>
                                    <option value="8">8th</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Phone number</label>
                                <input type="telephone" name="mobile" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>College</label>
                                <input type="text" name="college" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Branch</label>
                                <select class="form-control" name="branch">
                                    <option value="CSE">CSE</option>
                                    <option value="EC">EC</option>
                                    <option value="MECH">MECH</option>
                                    <option value="EE">EE</option>
                                    <option value="IC">IC</option>
                                    <option value="CHEM">CHEM</option>
                                    <option value="CIVIL">CIVIL</option>

                                </select>
                            </div>
                            Select Area
                            <div class="form-group" id="select_state_city">
                                <select onchange="print_city('state', this.selectedIndex);" id="sts" name="stt" class="form-control" required></select>
                                <select id="state" class="form-control" name='stt1'required></select>
                                <script language="javascript">
                                    //stt is name of select value state 
                                    //stt1 is name of select value city
                                    print_state("sts");
                                </script>

                            </div>
                        </th>
                </table>


                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-default" value="Reset">
                </div>



                <p>Already have an account? <a href="login.php">Login here</a>.</p>
            </form>
        </div>
    </div>
</body>

</html>