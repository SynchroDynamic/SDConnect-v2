<!doctype html>
<html lang="en">

    <?php
    $count = 1;
    readfile(dirname(__FILE__) . "/inc/head.html");
    echo "<body onload='getPageName()'>";
    readfile(dirname(__FILE__) . "/inc/layout.html");
    if (isset($_POST['addGate'])) {
        $count = $_POST['count'];
        echo "COUNT: " . $count;
        $gate = $_POST['gName'];

        $parameters = "";
        for ($i = 0; $i < $count; $i++) {
            $pName = $_POST['pName' . $i];
            $pType = "";

            switch ($_POST['inputState' . $i]) {
                case "String":
                    $pType = "VARCHAR(" . $_POST['length' . $i] . ")";
                    break;
                case "Integer":
                    $pType = "INT";
                    break;
                default:
                    $pType = $_POST['inputState' . $i];
            }


            $parameters .= $pName . " " . $pType;
            if ($i < $count - 1) {
                $parameters .= ",";
            }
        }

        include_once dirname(__FILE__, 2) . "/SERVERS/SHARED/ServerDatabase.php";

        $conn = new \ServerDatabase();
        $conn->getConnection();
        $status = $conn->createTable($gate, $parameters);
        $date = date("Y-m-d H:i:s");
        $columns = array("id", "gateName", "status", "changed");
        $values = array("NULL", $gate, 1, $date);
        $conn->insert("gates", $columns, $values);
        $conn->closeConnection();
        //echo $status->errorInfo();
        //Add directory for gate transactions
        mkdir(dirname(__FILE__, 2) . "/SERVERS/" . $gate);
        $DAO = fopen(dirname(__FILE__, 2) . "/SERVERS/" . $gate . "/DAO.php", "w");
        fclose($DAO);
        $DataService = fopen(dirname(__FILE__, 2) . "/SERVERS/" . $gate . "/DataService.php", "w");
        fclose($DataService);
        $GateController = fopen(dirname(__FILE__, 2) . "/SERVERS/" . $gate . "/GateController.php", "w");
        fclose($GateController);

        echo '<meta http-equiv="refresh" content="0;url=http://localhost:8081/SynchroDynamicRESTAPICreator/sd-admin/">';
    }
    ?>



    <form class="text-center border border-light p-5" method="POST" action="">

        <p class="h4 mb-4">Add Gate</p>

        <div class="form-group row">
            <label for="gName" class="col col-form-label">Gate Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="gName" name="gName" placeholder="name of gate">
            </div>
        </div>       
        <p class="h4 mb-4">Stored Parameters</p>
        <button type="button" class="btn btn-secondary" id="add" ><span data-feather='plus-circle'></span> Add New Parameter</button>
        <div class="form-group row">

            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Parameter Name</th>
                            <th>Parameter Type</th>
                            <th>Length</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>                               
                                <input type="text" class="form-control" id="pName0" name="pName0" placeholder="">                              
                            </td>
                            <td>
                                <select id="inputState0" name="inputState0" class="form-control">                                    
                                    <option selected>Integer</option>
                                    <option>Double</option>
                                    <option>String</option>
                                    <option>DateTime</option>
                                    <option>Boolean</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" id="length0" name="length0" placeholder=""> 
                            </td>
                            <td><button type="button" class="btn btn-danger" id="delete0"><span data-feather='delete'></span></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   

        <input type="hidden" id="count" name="count" value="<?php echo $count; ?>"> 
        <!-- Sign in button -->
        <button class="btn btn-info btn-block my-4" type="submit" name="addGate">Add Gate</button>
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
                + '</td><td><select id="inputState' + count + '" name="inputState' + count + '" class="form-control"><option selected>Integer</option>'
                + '<option>Double</option><option>String</option><option>DateTime</option><option>Boolean</option>'
                + '</select></td><td><input type="text" class="form-control" id="length' + count + '" name="length' + count + '" placeholder="">'
                + "</td><td><button type='button' class='btn btn-danger' id='delete" + count + "'><span data-feather='delete'></span</button></td></tr>";
        $('tbody').append(row);

        feather.replace();
    }


</script>
</body>
</html>
