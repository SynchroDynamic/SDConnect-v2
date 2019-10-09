<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!doctype html>
<html lang="en">    
    <?php
    //echo $_SERVER['DOCUMENT_ROOT'];
    readfile(dirname(__FILE__) . "/inc/head.html");

    $newFileName = '/var/www/your/file.txt';

    if (!is_writable(dirname(__FILE__, 2))) {
        echo dirname(dirname(__FILE__, 2)) . ' must be writable!!!' . "\n";
        echo 'CURRENT OWNER   : ' . posix_getpwuid(fileowner(dirname(dirname(__FILE__, 2))))['name'] . "\n";
        echo 'CHANGE OWNER TO : ' . exec('whoami') . "\n";
        echo 'In a Terminal you will need to change directory permission for this directory. Try:' . "\n";
        echo 'sudo chown -R www-data:www-data ' . dirname(dirname(__FILE__, 2));
    } else {

        echo 'WRITABLE!';
    }
    if (isset($_POST['setadmin'])) {
        include_once dirname(__DIR__) . '/sd-admin/login/class/Member.php';
        $username = $_POST['username'];
        $password = $_POST['password'];
        $reg = new \Phppot\Member();
        $reg->addUser($username, $password);
        echo $_GET['url'] . "/" . $_GET['sub'] . '/login/">';
        echo '<meta http-equiv="refresh" content="0;url=' . $_GET['url'] . "/" . $_GET['sub'] . '/sd-admin/login/">';
    }

    $message = "";
    if (isset($_POST['install'])) {
        $url = $_POST['url'];
        $dbName = $_POST['dbName'];
        $uName = $_POST['uName'];
        $pass = $_POST['pass'];
        $host = $_POST['host'];
        $rPath = $_POST['rPath'];
        $sub = $_POST['sub'];
        $salt = $_POST['salt'];

        $message = "name=" . $dbName . " uName=" . $uName . " pass=" . $pass . " host=" . $host;
        echo $message;
        echo dirname(__FILE__, 3);
        mkdir(dirname(__FILE__, 3) . "/" . $sub . '/inc');
        $fp = fopen(dirname(__FILE__, 3) . "/" . $sub . '/inc/sd-config1.php', 'w');

        $config = '<?php ' . "\n";
        $config .= 'class SDC {' . "\n";
        $config .= '//URL' . "\n";
        $config .= 'const URL = "' . $url . '/";' . "\n";
        $config .= '//Navigation' . "\n";
        $config .= 'const ROOT_PATH = "' . $rPath . '";' . "\n";
        $config .= 'const SUBFOLDER = "' . $sub . '/";' . "\n";
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
        include_once dirname(__FILE__, 2) . "/SERVERS/SHARED/ServerDatabase.php";

        $conn = new \ServerDatabase();
        $conn->getConnection();
        $conn->send(file_get_contents(dirname(__FILE__) . '/schema/schema.sql'));
        echo dirname(__FILE__) . '/schema/schema.sql';
        $status = $conn->send("select count(*) from information_schema.tables where table_schema = database();");
        $conn->closeConnection();
        $cout = $status->rowCount();

        echo "COUNT " . $cout;

        if ($cout > 0) {
            $_POST['install'] = null;
            //echo '&url=' . $url . "/" . $sub . '">';
            echo '<meta http-equiv="refresh" content="0;url=' . $url . "/" . $sub . '/sd-admin/install.php?step=' . 2 . '&url=' . $url . "&sub=" . $sub . '">';
        } else {
            echo 'Install Error!';
        }
    }
    ?>

    <body>

        <div class="container-fluid">      

            <?php
            $step = isset($_GET['step']) ? $_GET['step'] : 0;
            if ($step == "2") {
                ?>
                <form class="text-center border border-light p-5" method="POST" action="">

                    <p class="h4 mb-4">SynchroDynamic Connection - Create Admin Account Part 2</p>
                    <div class="form-group row">
                        <label for="username" class="col-sm-2 col-form-label">Username</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="username" name="username" placeholder="admin">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="uName" name="password" placeholder="pass">
                        </div>
                    </div>
                    <button class="btn btn-info btn-block my-4" type="submit" name="setadmin">Add Admin</button>
                </form>
                <?php
            } else {
                ?>


                <form class="text-center border border-light p-5" method="POST" action="">

                    <p class="h4 mb-4">SynchroDynamic Connection - Installation Part1</p>
                    <div class="form-group row">
                        <label for="url" class="col-sm-2 col-form-label">URL</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control"id="url" name="url" value="<?php echo!empty($_SERVER['HTTPS']) ? 'https' : 'http' . '://' . $_SERVER['HTTP_HOST']; ?>">
                        </div>
                    </div>
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
                        <label for="rPath" class="col-sm-2 col-form-label">Root Directory</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="rPath" name="rPath" value="<?php echo $_SERVER['DOCUMENT_ROOT'];
                ?>">
                        </div>

                    </div>
                    <div class="form-group row">
                        <label for="sub" class="col-sm-2 col-form-label">Sub-Folder</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="sub" name="sub" placeholder="Leave Blank if in ROOT Directory">
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

            <?php } ?>
        </main>
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
</body>
</html>





