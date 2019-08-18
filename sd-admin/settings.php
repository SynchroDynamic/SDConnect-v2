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
?>

<!doctype html>
<html lang="en">    
    <?php
    readfile(dirname(__FILE__) . "/inc/head.html");
    echo "<body onload='getPageName()'>";
    readfile(dirname(__FILE__) . "/inc/layout.html");
    $message = "";
    if (isset($_POST['install'])) {
        $dbName = $_POST['dbName'];
        $uName = $_POST['uName'];
        $pass = $_POST['pass'];
        $host = $_POST['host'];
        $prefix = $_POST['prefix'];
        $rPath = $_POST['rPath'];
        $rDir = "/" . $_POST['rDir'];
        $salt = $_POST['salt'];

        $message = "name=" . $dbName . " uName=" . $uName . " pass=" . $pass . " host=" . $host . " prefix=" . $prefix;
        echo $message;

        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . $rDir . '/inc/sd-config1.php', 'w');

        $config = "<?php \n\n"
                . "class SDC {\n\n"
                . "//Navigation\n"
                . "const ROOT_PATH = '$rPath';\n"
                . "const SERVER_FOLDER = '/SERVERS/';\n"
                . "const CONTROLLER_PATH = '/Controller/GateController.php';\n"
                . "//Data Connection\n"
                . "const IP = '$host';\n"
                . "const DATABASE_NAME = '$dbName';\n"
                . "const DB_USERNAME = '$uName';\n"
                . "const DB_PASS = '$pass';\n"
                . "//Encryption\n"
                . "const SESS_CIPHER = 'aes-128-cbc';\n"
                . "const KEYWORD = '$salt';\n\n"
                . "}";

        fwrite($fp, $config);
        fclose($fp);


        //include_once $rPath . $rDir . '/inc/sd-config1.php';
        include_once $rPath . $rDir . "/SERVERS/SHARED/ServerDatabase.php";

        $conn = new \ServerDatabase();
        $conn->getConnection();
        $status = $conn->send("select count(*) from information_schema.tables where table_schema = database();");
        $conn->closeConnection();
        echo $status->fetchColumn();
    }
    ?>

    <body>

        <div class="container-fluid">      

            <form class="text-center border border-light p-5" method="POST" action="">

                <p class="h4 mb-4">SynchroDynamic REST API Creator Install</p>
                <div class="form-group row">
                    <label for="dbName" class="col-sm-2 col-form-label">Database Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="dbName" name="dbName" placeholder="sd-admin">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="uName" class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="uName" name="uName" placeholder="root">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="pass" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="pass" name="pass" placeholder="pass">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="host" class="col-sm-2 col-form-label">Database Host IP and Database Port String</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="host" name="host" value="127.0.0.1:3306">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="prefix" class="col-sm-2 col-form-label">Table Prefix</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="prefix" name="prefix" value="sd_">
                    </div>

                </div>

                <div class="form-group row">
                    <label for="rPath" class="col-sm-2 col-form-label">Root Directory</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="rPath" name="rPath" value="<?php echo $_SERVER['DOCUMENT_ROOT'];
    ?>">
                    </div>

                </div>
                <div class="form-group row">
                    <label for="rDir" class="col-sm-2 col-form-label">Root Directory</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="rDir" name="rDir" placeholder="the folder SD is installed(No slashes)">
                    </div>

                </div>
                <div class="form-group row">
                    <label for="salt" class="col-sm-2 col-form-label">Salt Keyword</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="salt" name="salt" placeholder="A unique sequence of characters">
                    </div>

                </div>

                <!-- Sign in button -->
                <button class="btn btn-info btn-block my-4" type="submit" name="install">Install Now</button>
            </form>

            <div class="alert alert-danger" role="alert">
                This file will be maintained until you delete it. You can save a copy off your server, and delete it when you are sure
                that everything is installed correctly. Lastly, Change the file permission of the /inc folder in the root directory.
            </div>
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