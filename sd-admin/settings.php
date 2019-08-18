<?php

use \Phppot\Member;

session_start();
if (!empty($_SESSION["userId"])) {
    require_once __DIR__ . '/login/class/Member.php';
    $member = new Member();
    $memberResult = $member->getMemberById($_SESSION["userId"]);
    //echo "MEMEBER RESULTS : " . implode("::", $memberResult);
    if (!empty($memberResult[0]["username"])) {
        $displayName = $memberResult[0]["username"];
    } else {
        $displayName = $memberResult[0]["username"];
    }
} else {
    include_once dirname(__DIR__) . '/inc/sd-config1.php';
    echo '<meta http-equiv="refresh" content="0;url=' . \SDC::URL . \SDC::SUBFOLDER . 'sd-admin/login/">';
}

$message = "";
if (isset($_POST['update'])) {
    $url = $_POST['url'];
    $dbName = $_POST['dbName'];
    $uName = $_POST['uName'];
    $pass = $_POST['pass'];
    $host = $_POST['host'];
    $rPath = $_POST['rPath'];
    $sub = $_POST['sub'];
    $salt = $_POST['salt'];

    //$message = "name=" . $dbName . " uName=" . $uName . " pass=" . $pass . " host=" . $host;
    //echo $message;

    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $sub . '/inc/sd-config1.php', 'w');

    $config = '<?php ' . "\n";
    $config .= 'class SDC {' . "\n";
    $config .= '//URL' . "\n";
    $config .= 'const URL = "' . $url . '";' . "\n";
    $config .= '//Navigation' . "\n";
    $config .= 'const ROOT_PATH = "' . $rPath . '";' . "\n";
    $config .= 'const SUBFOLDER = "' . $sub . '";' . "\n";
    $config .= 'const SERVER_FOLDER = "SERVERS/";' . "\n";
    $config .= 'const CONTROLLER_PATH = "/GateController.php";' . "\n";
    $config .= '//Data Connection' . "\n";
    $config .= 'const IP = "' . $host . '";' . "\n";
    $config .= 'const DATABASE_NAME = "' . $dbName . '";' . "\n";
    $config .= 'const DB_USERNAME = "' . $uName . '";' . "\n";
    $config .= 'const DB_PASS = "' . $pass . '";' . "\n";
    $config .= '//Encryption' . "\n";
    $config .= 'const SESS_CIPHER = "aes-128-cbc";' . "\n";
    $config .= 'const KEYWORD = "' . $salt . '";' . "\n";
    $config .= "}";

    fwrite($fp, $config);
    fclose($fp);


    include_once dirname(__FILE__, 2) . '/inc/sd-config1.php';
    include_once $rPath . "/" . $sub . "/SERVERS/SHARED/ServerDatabase.php";

    $conn = new \ServerDatabase();
    $conn->getConnection();
    $status = $conn->send("select count(*) from information_schema.tables where table_schema = database();");
    $conn->closeConnection();
    $cout = $status->rowCount();
    //echo "COUNT " . $cout;
    if ($cout > 0) {
        $_GET['install'] = null;
        //echo '&url=' . $url . "/" . $sub . '">';
        echo '<script>alert("Update Successful!");</script>';
    } else {
        echo '<script>alert("Install Error!");</script>';
    }
}
?>

<!doctype html>
<html lang="en">    
    <?php
    readfile(dirname(__FILE__) . "/inc/head.html");
    echo "<body onload='getPageName()'>";
    readfile(dirname(__FILE__) . "/inc/layout.html");
    include_once dirname(__DIR__) . '/inc/sd-config1.php';

    $url = \SDC::URL;
    $dbName = \SDC::DATABASE_NAME;
    $uName = \SDC::DB_USERNAME;
    $pass = \SDC::DB_PASS;
    $host = \SDC::IP;
    $rPath = \SDC::ROOT_PATH;
    $sub = \SDC::SUBFOLDER;
    $salt = \SDC::KEYWORD;
    ?>

    <body>

        <div class="container-fluid">      

            <form class="text-center border border-light p-5" method="POST" action="">

                <p class="h4 mb-4">SynchroDynamic Database Management System</p>
                <div class="form-group row">
                    <label  for="url" class="col-sm-2 col-form-label">URL</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control"id="url" name="url" value="<?php echo $url; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="dbName" class="col-sm-2 col-form-label">Database Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="dbName" name="dbName" value="<?php echo $dbName; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="uName" class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="uName" name="uName" value="<?php echo $uName; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="pass" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="pass" name="pass" value="<?php echo $pass; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="host" class="col-sm-2 col-form-label">Database Host IP and Database Port String</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="host" name="host" value="<?php echo $host; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="rPath" class="col-sm-2 col-form-label">Root Directory</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="rPath" name="rPath" value="<?php echo $rPath; ?>">
                    </div>

                </div>
                <div class="form-group row">
                    <label for="sub" class="col-sm-2 col-form-label">Sub-Folder</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="sub" name="sub" value="<?php echo $sub; ?>">
                    </div>
                    <input type="hidden" class="form-control" id="salt" name="salt" value="<?php echo $salt; ?>">

                </div>

                <!-- Sign in button -->
                <button class="btn btn-info btn-block my-4" type="submit" name="update">Update Settings</button>
            </form>

        </main>
    </div>
</div>
</div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>

<!-- Icons -->
<script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace()
</script>     
<script>
    $(document).ready(function () {
        $('#logout').on('click', function () {
            location.href = '/sd/sd-admin/login/logout.php';
        });
    });

    $('#inas').append('<a href="#"><?php echo $displayName; ?></a>');

</script>
</body>
</html>