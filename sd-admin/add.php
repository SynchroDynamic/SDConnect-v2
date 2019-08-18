<!doctype html>
<html lang="en">
    <?php
    include_once dirname(__DIR__) . '/sd-admin/inc/functions/Functions.php';
    include_once dirname(__DIR__) . '/sd-admin/inc/functions/javascriptFunctions.php';
    include_once dirname(__DIR__) . '/sd-admin/model/Transaction.php';

    function buildJQueryTableName($tableCount, $tabNCol) {
        $tempColSet = "";
        for ($j = 0; $j < $tableCount; $j++) {

            $tableName = $tabNCol[$j][0];

            if ($j > 0) {
                $tempColSet .= ",'$tableName': [";
            } else {
                $tempColSet .= "'$tableName': [";
            }
            $columnCount = count($tabNCol[$j]);
            for ($z = 1; $z < $columnCount; $z++) {
                if ($z > 1) {
                    $tempColSet .= ",";
                }
                $colName = $tabNCol[$j][$z];
                $tempColSet .= "'$colName'";
            }
            $tempColSet .= "]";
        }
        return $tempColSet;
    }

    function validateDBData($tValue) {

        if (isset($tValue)) {
            $tableCount = count($tValue);
            if (!isset($tableCount)) {
                $tableCount = 0;
            }
        }
        return $tableCount;
    }

    $count = 1;
    $countOut = 1;
    $transactions;
    $tValues;
    readfile(dirname(__FILE__) . "/inc/head.html");
    echo "<body onload='getPageName()'>";
    readfile(dirname(__FILE__) . "/inc/layout.html");
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $gate = $_GET['gate'];
        $tid = isset($_GET['tid']) ? $_GET['tid'] : -1;
    } else {
        die('404');
    }

    if ($tid >= 0) {

        //echo $tid;
        //now if it has the tid in the url, load what is available.
        $transactions = \admin\Functions::getTransactionData($tid);
        $tValues = \admin\Functions::getTablesAndCols();

        $tableCount = count($tValues);

        $arrayString = "<script>tableCols = {";

        $arrayString .= \admin\javascriptFunctions::buildJQueryTableName($tableCount, $tValues);

        $arrayString .= "};</script>";
        echo $arrayString;
    }

    if (isset($_POST['outSubmit'])) {
        $out = $_POST['countOut'];
        $countOut = $out === 0 ? 1 : $out;
        //echo "OUT COUNT :: $countOut";
        //get the data
        $dataArray = array();
        $dataCount = 0;
        for ($i = 0; $i < $countOut; $i++) {
            $multipleColumns = $_POST['colNames' . $i];
            $colCount = 0;
            $columnString = "";
            if (is_array($multipleColumns)) {
                foreach ($multipleColumns as $selectedOption) {
                    if ($colCount > 0) {
                        $columnString .= ",";
                    }
                    $columnString .= $selectedOption;
                    $colCount++;
                }
            } else {
                $columnString .= $multipleColumns;
            }
            $temp = array(
                $_POST['tableNames' . $i],
                $columnString,
                $tid
            );
            $dataArray[$dataCount++] = $temp;
        }

//        add wheres first. this way you can delete old wheres

        $preOutgoingIds = \admin\Functions::getOutgoingIds($tid); //lets do this before so that we can delete the old wheres

        $mess = \admin\Functions::deleteOldWhereStatements($preOutgoingIds);
//      echo "Where Sets Deleted? " . implode(", ", $mess->errorInfo());
        $ParametersAdded = \admin\Functions::addOutgoingTablesAndColumns($dataArray, $tid);

        $outgoingIds = \admin\Functions::getOutgoingIds($tid); //lets do this before so that we can delete the old wheres
        $whereArray = array();
        $wACount = 0;
        for ($i = 0; $i < $countOut; $i++) {
            $countWheres = $_POST['whereCount' . $i] + 1;

            $outId = -1;
            $tablName = $_POST['tableNames' . $i];

            foreach ($outgoingIds as $d) {
                $temName = $d['name'];
                //echo "$tablName && $temName";
                if ($tablName == $temName) {
                    $outId = $d['id'];
                    break;
                }
            }
            for ($j = 0; $j < $countWheres; $j++) {
                $where = array(
                    $outId,
                    $_POST['whereInp' . $i . $j],
                    $_POST['whereOpInp' . $i . $j],
                    $_POST['whereValInp' . $i . $j]
                );
                $whereArray[$wACount++] = $where;
            }
        }
        $stmt = \admin\Functions::addOutgoingWhereStatements($whereArray, $outgoingIds);

        //echo $stmt;
        //echo '<meta http-equiv="refresh" content="0;url=http://localhost:8081/SynchroDynamicRESTAPICreator/sd-admin/add.php?id=' . $id . '&gate=' . $gate . '&tid=' . $tid . '">';
        //ship to db
        $transactions = \admin\Functions::getTransactionData($tid);
        $tValues = \admin\Functions::getTablesAndCols();

        $tableCount = count($tValues);

        $arrayString = "<script>tableCols = {";

        $arrayString .= buildJQueryTableName($tableCount, $tValues);

        $arrayString .= "};</script>";
        echo $arrayString;
    } else if (isset($_POST['inSubmit'])) {
        //echo 'incoming parameter set submitted';
        $count = $_POST['count'];
        //echo $count;
        $dataArray = array();
        $dataCount = 0;
        for ($i = 0; $i < $count; $i++) {
            $temp = array(
                $_POST["pName$i"],
                $_POST["inputState$i"],
                $tid
            );
            $dataArray[$dataCount++] = $temp;
            //echo "{ " . implode(", ", $temp) . " }";
        }

        $ParametersAdded = \admin\Functions::addIncoming($dataArray);
        //echo $count . " parameters Sent : " . $ParametersAdded . " parameters saved";
        $transactions = \admin\Functions::getTransactionData($tid);
        $tValues = \admin\Functions::getTablesAndCols();

        $tableCount = count($tValues);

        $arrayString = "<script>tableCols = {";

        $arrayString .= buildJQueryTableName($tableCount, $tValues);

        $arrayString .= "};</script>";
        echo $arrayString;
    } else if (isset($_POST['generalSubmit'])) {
        echo 'General Transaction Settings Submitted!';
        $dataArray = array(
            $_POST['tName'],
            $_POST['tType'],
            $_POST['hasParams'],
            $_POST['method'],
            $id
        );

        $transactionid = \admin\Functions::addGeneral($id, $dataArray);
        include_once dirname(__DIR__) . '/inc/sd-config1.php';
        echo '<meta http-equiv="refresh" content="0;url='. \SDC::URL . \SDC::SUBFOLDER .'sd-admin/transactions.php?id=' . $id . '&gate=' . $gate . '">';
        //echo "<script>alert('$transactionid');</script>";
        //echo "<script>location.reload(true);</script>";
        //echo "added " . $transactionAdded;
        $transactions = \admin\Functions::getTransactionData($tid);
        $tValues = \admin\Functions::getTablesAndCols();

        $tableCount = count($tValues);

        $arrayString = "<script>tableCols = {";

        $arrayString .= buildJQueryTableName($tableCount, $tValues);

        $arrayString .= "};</script>";
    }

    $tablesAndCols = \admin\Functions::getTablesAndCols(); //Returns and array like: {[tablename, col1, col2, col3, ... , coln], [tablename, ... , coln]}
    ?>
    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id=""></div>
    </div>
    <p class="h4 mb-4" id="titleTrans">Add Transaction To: <?php echo $gate; ?></p>

    <button class="tablink" onclick="openPage('General', this, 'red')" style='background-color: red;'>General</button>
    <button class="tablink" onclick="openPage('Incoming', this, 'red')">Incoming</button>
    <button class="tablink" onclick="openPage('Outgoing', this, 'red')" id="defaultOpen">Outgoing</button>
    <button class="tablink" onclick="openPage('Functional', this, 'red')">Function</button>


    <div id="General" class="tabcontent" style='display:block;'>
        <?php
        readfile(dirname(__DIR__) . '/sd-admin/inc/addForms/generalForm.html');
        ?>
        <button class="btn btn-info btn-block my-4" type="submit" name="generalSubmit">Update General Settings</button>
    </form>
</div>

<div id="Incoming" class="tabcontent">
    <?php
    readfile(dirname(__DIR__) . '/sd-admin/inc/addForms/incomingForm.html');
    ?>
    <input type="hidden" id="count" name="count" value="<?php echo $count; ?>"> 
    <button class="btn btn-info btn-block my-4" type="submit" name="inSubmit">Update Incoming</button>
</form>
</div>

<div id="Outgoing" class="tabcontent">
    <?php
    readfile(dirname(__DIR__) . '/sd-admin/inc/addForms/outgoingForm.html');
    ?>
    <input type="hidden" id="countOut" name="countOut" value="<?php echo $countOut; ?>"> 
    </form><!--FORM GENERAL-->
</div>

<div id="Functional" class="tabcontent">
    <h3>Function</h3>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>

<!-- Icons -->
<script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
<script>
        feather.replace();</script>


<script>

    //add to ownfile in future
    $(document).ready(function () {
        setTimeout(1000);
        var inCount = <?php echo $count; ?>;
        var count = inCount === 0 ? 1 : inCount;
        $('#add').on('click', function (e) {
            e.preventDefault();
            addIncoming(count);
            count++;
            $('input#count').val(count);
        });
        var outCount = <?php echo $countOut; ?>;
        var countOut = outCount === 0 ? 1 : outCount;
        var curentCountOut = countOut;
        //var curentCountOut = countOut;
        $('#addOut').on('click', function (e) {
            e.preventDefault();
            addOutgoing(0, curentCountOut);
            curentCountOut++;
            $('#countOut').val(curentCountOut);
        });
    });

</script>
<?php
if (isset($transactions)) {
    $tranCount = count($transactions);

    echo "<script>\n";
    echo "$(document).ready(function(){\n";
    echo "var c = '';\n  "
    . "while(c == null){"
    . "var url_string = window.location.href;" .
    "var url = new URL(url_string);" .
    "c = url.searchParams.get('tid');";

    echo "}\n     if (c != null) {";
    echo "$('#pageName').text('EDIT');\n";
    for ($vg = 0; $vg < $tranCount; $vg++) {

        $tempName = $transactions[$vg]->getName();
        $typeVal = $transactions[$vg]->getType();
        $hasParams = $transactions[$vg]->getHasIncoming();
        $method = $transactions[$vg]->getHasOutgoing();
        //General Transaction subPage
        echo "$('#tType').val('$typeVal');\n";
        echo "$('#tName').val('$tempName');\n";
        echo "$('#hasParams').val('$hasParams');\n";
        echo "$('#method').val('$method');\n";
        //echo "alert('$method');\n";
        //Incoming subPage
        $Incoming = $transactions[$vg]->getIncoming();
        $inC = count($Incoming);
        echo "$('#count').val('$inC');\n";
        for ($ig = 0; $ig < $inC; $ig++) {
            if ($ig > 0) {
                echo "addIncoming($ig);\n";
            }
            echo "$('#pName$ig').val('" . $Incoming[$ig]->getName() . "');\n";
            echo "$('#input$ig').val('" . $Incoming[$ig]->getType() . "');\n";
        }
        $Outgoing = $transactions[$vg]->getOutgoing();
        $outC = count($Outgoing);

        if ($outC == 0) {
            echo "setTheTable(0);";
            echo "$('#countOut').val('1');\n";
            echo "newWhereClickEvent(0)";
        } else {
            echo "$('#countOut').val('$outC');\n";
            for ($og = 0; $og < $outC; $og++) {

                if ($og > 0) {
                    echo "addOutgoing(0,$og);\n";
                }

                echo "setTheTable($og);\n";
                $outtyName = $Outgoing[$og]->getTableName();

                echo "$('#tableNames$og').find('option:contains($outtyName)').attr('selected', true);\n";
                echo "selectionColumns($og);\n";
                $colStringArray = explode(",", $Outgoing[$og]->getColumns());
                $colStrArC = count($colStringArray);

                for ($ccg = 0; $ccg < $colStrArC; $ccg++) {
                    $td = $colStringArray[$ccg];
                    //echo "alert('$td');";
                    echo "$('#colNames$og').find('option:contains(" . $colStringArray[$ccg] . ")').attr('selected', true);\n";
                }
                $wheres = $Outgoing[$og]->getWhereSets();
                $wheC = count($wheres);

                echo "$('#whereCount$og').val('$wheC');\n";
                echo "newWhereClickEvent($og);\n";
                //echo "alert('$wheC');";
                for ($wg = 0; $wg < $wheC; $wg++) {
                    if ($wg > 0) {
                        echo "addWhere($wg,'whereTBody$og',$og);\n";
                        echo "setForWhere($wg, $og);\n";
                    }
                    echo "$('#whereInp$og" . $wg . "').find('option:contains(" . $wheres[$wg]->getWhereColumn() . ")').attr('selected', true);\n";
                    echo "$('#whereOpInp$og" . $wg . "').find('option:contains(" . $wheres[$wg]->getWhereOperator() . ")').attr('selected', true);\n";
                    echo "$('#whereValInp$og" . $wg . "').val('" . $wheres[$wg]->getWhereValue() . "');\n";
                }
            }
        }
    }
    echo "}";
    echo "setDataList();";
    echo "});";

    echo "</script>";
}
?>
<script>
    function setDataList() {

        var inputCount = $('input#count').val();
        for (var i = 0; i < inputCount; i += 1) {
            var inputName = $('#pName' + i).val();
            $('#inputVars').append('<option value="' + inputName + '">');
        }
        //

    }

    function updateSet(count) {
        //alert('updateSet');
        var whereCount = $('#whereTBody' + count + ' tbody').find('tr').length;
        //alert(whereCount + " << WHERECOUNT");
        setColumns(whereCount, count);
    }

    function setColumns(count, tCount) {
        $("#colNames" + tCount + " option").each(function () {
            $(this).remove();
        });
        //now lets loop this
        for (var c = 0;
                c < count;
                c += 1) {
            $("#whereInp" + tCount + c + " option").each(function () {
                $(this).remove();
            });
        }
        selectionColumns(tCount);
    }

    function getUrlVars() {
        var vars = {};
        window.location.href.replace(/[?&]+([ ^= &]+) = ([^&]*)/gi, function (m, key, value) {
            vars[key] = value;
        });
        return vars;
    }

    function addWhere(count, tbody, tCount) {
        var row = '<tr>'
                + '<td><select class="form-control" id="whereInp' + tCount + count + '"name="whereInp' + tCount + count + '"></select>'
                + '</td><td><select class="form-control" id="whereOpInp' + tCount + +count + '"name="whereOpInp' + tCount + count + '"><option>=</option>'
                + '<option>&lt;</option><option>&gt;</option><option>&lt;&gt;</option><option>%LIKE</option>'
                + '<option>%LIKE%</option><option>LIKE%</option></select>'
                + '</td><td><input type="text" list="inputVars" class="form-control" id="whereValInp' + tCount + count + '"name="whereValInp' + tCount + count + '" placeholder="">'
                + '</td><td><button type="button" class="btn btn-danger" id="delete' + tCount + count + '"><span data-feather="delete"></span></button></td></tr>';
        $('#' + tbody + ' tbody').append(row);
        //$('#whereCount' + tCount).val(count + 1);
        feather.replace();
    }

    function addOutgoing(whereCnt, count) {
        var row = '<div class="jumbotron" style="background: #fff;">'
                + '<div class="form-group row"><div class="col">'
                + '<label for="tableNames' + count + '" class="col col-form-label">Table</label>'
                + '<select id="tableNames' + count + '" name="tableNames' + count + '" class="form-control" onchange="updateSet(' + count + ')">'
                + '</select></div><div class="col"><label for="colNames' + count + '" class="col col-form-label">Columns</label>'
                + '<select multiple id="colNames' + count + '" name="colNames' + count + '[]" class="form-control">'
                + '</select></div></div><button type="button" class="btn btn-warning" id="addWhere' + count + '" data-a="' + count + '">'
                + '<span data-feather="plus-circle"></span>  WHERE Clause</button><div class="form-group row"><div class="table-responsive">'
                + '<input type="hidden" id="whereCount' + count + '" name="whereCount' + count + '" value="0"><table class="table table-striped table-sm" id="whereTBody' + count + '">'
                + '<thead><tr><th>Where</th><th>Operator</th><th>Where Value</th><th>Delete</th></tr></thead><tbody>'
                + '<tr><td><select class="form-control" id="whereInp' + count + '0" aria-describedby="whereInput" name="whereInp' + count + '0"></select>'
                + '<small id="whereInput" class="form-text text-muted">each Column to Compare separated by ","</small></td><td>'
                + '<select class="form-control" id="whereOpInp' + count + '0" aria-describedby="whereOpInput" name="whereOpInp' + count + '0"><option>=</option>'
                + '<option>&lt;</option><option>&gt;</option><option>&lt;&gt;</option><option>%LIKE</option>'
                + '<option>%LIKE%</option><option>LIKE%</option></select>'
                + '<small id="whereOpInput" class="form-text text-muted">each comparative Operator to Compare separated by "," (ex. =, <\>, <, >)</small>'
                + '</td><td><input type="text" list="inputVars" class="form-control" id="whereValInp' + count + '0" aria-describedby="whereValInput" name="whereValInp' + count + '0" placeholder="">'
                + '<small id="whereValInput" class="form-text text-muted">each input string name: Use ##parametername for incoming Parameters, and static values otherwise</small>'
                + '</td><td><button type="button" class="btn btn-danger" id="delete' + count + '0"><span data-feather="delete"></span></button></td>'
                + '</tr></tbody></table></div></div></div>';
        $('#OutgoingForm').append(row);
        feather.replace();

        setTheTable(count);
        newWhereClickEvent(count);
        //selectonColumns(count);

    }

    function newWhereClickEvent(count) {
        var co = parseInt($('#whereCount' + count).val());
        $('#addWhere' + count).on('click', function (e) {
            $('#whereCount' + count).val(co + 1);
            co = parseInt($('#whereCount' + count).val());
            e.preventDefault();
            var whereButData = 'whereTBody' + count;
            //alert(whereButData + " >> " + co + " >> " + count);
            addWhere(co, whereButData, count);
            setForWhere(co, count);

        });
    }

    function setForWhere(count, tCount) {

        selectionForWhere(tCount, count);
    }

    function selectionColumns(count) {
        var selectedTable = $('#tableNames' + count + ' option:selected').text();
        var whereCount = $('#whereTBody' + count + ' tbody').find('tr').length;
        //alert(whereCount + " WHERE COUNT " + '#whereTBody ' + count + ' tbody');
        $.each(tableCols, function (key, value) {
            //alert('KEY ' + key);
            if (key === selectedTable) {
                //alert("FOUND " + key);
                $.each(value, function (i, val) {

                    $('#colNames' + count).append('<option>' + val + '</option>');
                    for (var c = 0;
                            c < whereCount;
                            c += 1) {
                        //alert('#whereInp' + count + c);
                        $('#whereInp' + count + c).append('<option>' + val + '</option>');
                    }
                });
            }
        });
    }

    function selectionForWhere(count, whereCount) {
        var selectedTable = $('#tableNames' + count + ' option:selected').text();
        //var whereCount = $('#whereTBody' + count + ' tbody').find('tr').length;
        //alert(whereCount + " WHERE COUNT " + '#whereTBody ' + count + ' tbody');
        $.each(tableCols, function (key, value) {
            //alert('KEY ' + key);
            if (key === selectedTable) {
                //alert("FOUND " + key);
                $.each(value, function (i, val) {
                    //alert('#whereInp' + count + whereCount);
                    $('#whereInp' + count + whereCount).append('<option>' + val + '</option>');
                });
            }
        });
    }

    function addIncoming(count) {
        var row = '<tr><td><input type="text" class="form-control" id="pName' + count + '" name="pName' + count + '" placeholder="">'
                + '</td><td><select id="inputState' + count + '" name="inputState' + count + '" class="form-control"><option value="0" selected>Integer</option>'
                + '<option  value="1">Double</option><option value="2">String</option><option value="3">DateTime</option><option value="4">Boolean</option>'
                + '</select></td>'
                + "<td><button type='button' class='btn btn-danger' id='delete" + count + "'><span data-feather='delete'></span</button></td></tr>";
        $('tbody#incomingTBody').append(row);
        feather.replace();
    }

    function setTheTable(count) {

        $("#colNames" + count + " option").each(function () {
            $(this).remove();
        });
        $('#tableNames' + count).append('<option></option>');
        $.each(tableCols, function (key, value) {
            //alert(key + " >> " + value);
            $('#tableNames' + count).append('<option>' + key + '</option>');
        });
        //selectionColumns(count);

    }
</script>
</body>
</html>
