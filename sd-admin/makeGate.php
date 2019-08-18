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
    if (empty($_SESSION["userId"])) {
        include_once dirname(__DIR__) . '/inc/sd-config1.php';
        // $root .= !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        $root = 'http://' . $_SERVER['HTTP_HOST'];
        echo '<meta http-equiv="refresh" content="0;url=' . $root . '/' . \SDC::SUBFOLDER . 'sd-admin/login/">';
    }
    $count = 1;
    include_once dirname(__DIR__) . '/sd-admin/inc/functions/Functions.php';
    readfile(dirname(__FILE__) . "/inc/head.html");
    echo "<body onload='getPageName()'>";
    readfile(dirname(__FILE__) . "/inc/layout.html");

    if (isset($_POST['addGate'])) {
        $count = (int) $_POST['count'];
        echo "COUNT: " . $count;
        $gate = $_POST['gName'];
        $userDependence = $_POST['userD'];

        $parameters = "";
        $columns = "";
        for ($i = 0; $i < $count; $i++) {
            if ($i > 0) {
                $columns .= ",";
            }
            $pName = $_POST['pName' . $i];
            $pType = "";
            echo $_POST['inputState' . $i];
            switch ($_POST['inputState' . $i]) {
                case "varchar":
                    $pType = "VARCHAR(" . $_POST['length' . $i] . ")";
                    break;
                default:
                    $pType = $_POST['inputState' . $i];
            }


            $parameters .= $pName . " " . $pType;
            $columns .= "'$pName'";
            if ($i < $count - 1) {
                $parameters .= ",";
            }
        }

        include_once dirname(__FILE__, 2) . "/SERVERS/SHARED/ServerDatabase.php";
        include_once dirname(__FILE__, 2) . "/SERVERS/SHARED/DependenceSets/ServerBuilder.php";

        $conn = new \ServerDatabase();
        $conn->getConnection();
        $writeParameters = "";

        //ADD NEW TABLE

        switch ($userDependence) {
            case "0":
                $status = $conn->createTableNotUserDependent($gate, $parameters);
                // print_r($status);
                $writeParameters .= "'id'," . $columns;
                break;
            case "1":
                $status = $conn->createTableUniqueUser($gate, $parameters);
                $writeParameters .= "'id','uid'," . $columns;
                break;
            case "2":
                $status = $conn->createTableMultipleEntryUser($gate, $parameters);
                $writeParameters .= "'id','uid'," . $columns;
                break;
        }

        //ADD TO DB
        $date = date("Y-m-d H:i:s");
        $columns1 = array("id", "gateName", "status", "changed");
        $values = array("NULL", $gate, $userDependence, $date);
        $conn->insert("gates", $columns1, $values);
        $conn->closeConnection();
        //echo $status->errorInfo();
        //
        //
        //Add directory for gate transactions
        mkdir(dirname(__FILE__, 2) . "/SERVERS/" . $gate);
        $DAO = fopen(dirname(__FILE__, 2) . "/SERVERS/" . $gate . "/DAO.php", "w");
        $config = \Processor\ServerBuilder::buildDAO($gate, $columns, $writeParameters);
        fwrite($DAO, $config);
        fclose($DAO);
        $DataService = fopen(dirname(__FILE__, 2) . "/SERVERS/" . $gate . "/DataService.php", "w");
        $config1 = \Processor\ServerBuilder::buildDataService($gate);
        fwrite($DataService, $config1);
        fclose($DataService);
        $GateController = fopen(dirname(__FILE__, 2) . "/SERVERS/" . $gate . "/GateController.php", "w");
        $config2 = \Processor\ServerBuilder::buildGate($gate);
        fwrite($GateController, $config2);
        fclose($GateController);

        //echo '<meta http-equiv="refresh" content="0;url=http://localhost:8081/SynchroDynamicRESTAPICreator/sd-admin/">';
    }
    $arrString = "";
    $typeString = "";
    $tableName = "";
    $savedStatus = "";
    $id = isset($_GET['id']) ? $_GET['id'] : -1;
    if ((int) $id > 0) {
        $name = $_GET['gate'];

        if (isset($_POST['everything'])) {

            deleteDir(dirname(__FILE__, 2) . "/SERVERS/" . $name);
            Functions::deleteGate($id, $name);
        }



        $transactions = \admin\Functions::getTablesAndColsByName($name);
        $gateInfo = \admin\Functions::getGateByName($name);
        //print_r($transactions);
        $tableName = $transactions[0];
        $savedStatus = $gateInfo[0]['status'];
        //echo "SAVED STATUS : " . $savedStatus;
        $parCount = 0;
        $totalCount = count($transactions);
        $arrString = "var pars = [";
        $typeString = "var type = [";
        for ($v = 1; $v < $totalCount; $v++) {
            if ($v > 1) {
                $arrString .= ",";
                $typeString .= ",";
            }
            $f = $transactions[$v]['Field'];
            $t = $transactions[$v]['Type'];
            $arrString .= "'$f'";
            $typeString .= "'$t'";
        }
        $arrString .= "];";
        $typeString .= "];";
    }

    function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
    ?>



    <form class="text-center border border-light p-5" method="POST" action="">

        <p class="h4 mb-4" id="gateTitle">Add Gate</p>

        <div class="form-group row">
            <div class="col">
                <label for="gName" class="col col-form-label">Gate Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="gName" name="gName" placeholder="name of gate">
                </div>
            </div>
            <div class="col">
                <label for="userD" class="col col-form-label">Gate Type</label>
                <div class="col-sm-10">
                    <select class="form-control" id="userD" name="userD">
                        <option value="0">No Dependence</option>
                        <option value="1">Unique User</option>
                        <option value="2">Multiple User Entry</option>
                    </select>
                </div>
            </div>
        </div>       
        <p class="h4 mb-4">Column Names</p>

        <?php
        if (!isset($gateInfo)) {
            echo '<button type="button" class="btn btn-secondary" id="add" ><span data-feather="plus-circle"></span> Add New Parameter</button>';
        } else {
            echo '';
        }
        ?>
        <div class="form-group row">

            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Parameter Name</th>
                            <th>Parameter Type</th>
                            <th>Length</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>                               
                                <input type="text" class="form-control" id="pName0" name="pName0" placeholder="">                              
                            </td>
                            <td>
                                <select id="inputState0" name="inputState0" class="form-control">                                    
                                    <option value="int">Integer</option>
                                    <option value="double">Double</option>
                                    <option value="varchar">String</option>
                                    <option value="datetime">DateTime</option>
                                    <option value="tinyint">Boolean</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" id="length0" name="length0" placeholder=""> 
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   

        <input type="hidden" id="count" name="count" value="<?php echo $count; ?>"> 


        <?php
        if (!isset($gateInfo)) {
            echo '<button class="btn btn-info btn-block my-4" type="submit" name="addGate">Add Gate</button>';
        } else {
            echo '<button class="btn btn-warning" type="submit" name="delGate" data-toggle="tooltip" data-placement="top" title="Delete The Gate, but Maintain you table">Delete Gate</button>';
            echo '<button class="btn btn-danger" type="submit" name="everything" data-toggle="tooltip" data-placement="top" title="Delete gate, table, and any data inside of table">Delete Everything</button>';
        }
        ?>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>




<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>

<!-- Icons -->
<script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();
</script>     
<script>
    $(document).ready(function () {
        var count = 1;
        $('#add').on('click', function (e) {
            e.preventDefault();
            addRow(count);
            count++;
            $('#count').val(count);

        });
    });
    function addRow(count) {
        var row = '<tr><td><input type="text" class="form-control" id="pName' + count + '" name="pName' + count + '" placeholder="">'
                + '</td><td><select id="inputState' + count + '" name="inputState' + count + '" class="form-control"><option value="int">Integer</option>'
                + '<option value="double">Double</option><option value="varchar">String</option><option value="datetime">DateTime</option><option value="tinyint">Boolean</option>'
                + '</select></td><td><input type="text" class="form-control" id="length' + count + '" name="length' + count + '" placeholder="">'
                + "</td></tr>";
        $('tbody').append(row);

        feather.replace();
    }


</script>

<script>
    $(document).ready(function () {

<?php echo $arrString; ?>
<?php echo $typeString; ?>
        $('#gateTitle').text('EDIT GATE');
        $('#gName').val('<?php echo $tableName; ?>');
        $('#userD').val('<?php echo $savedStatus; ?>');
        if (typeof pars != 'undefined') {
            $.each(pars, function (i, val) {
                if (i > 0) {
                    addRow(i);
                }
                var fixedState;
                var inpState;
                var length;
                if (type[i].indexOf("(") >= 0) {
                    fixedState = type[i].split("(");
                    inpState = fixedState[0];
                    length = fixedState[1].replace(")", "");
                } else {
                    inpState = type[i];
                    length = "";
                }
                $('#pName' + i).val(pars[i]);
                $('#inputState' + i).val(inpState);
                //$('#inputState' + i).val("'" + inpState + "'");
                $('#length' + i).val(length);
                if (pars[i] == "id" || pars[i] == "uid") {
                    $('#pName' + i).prop('disabled', true);
                    $('#inputState' + i).prop('disabled', true);
                    $('#length' + i).prop('disabled', true);
                    $('#delete' + i).hide();
                }
            });

        }
    });

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
