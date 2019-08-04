<?php ?>

<!doctype html>
<html lang="en">

    <?php
    include_once dirname(__DIR__) . '/sd-admin/inc/functions/Functions.php';

    readfile(dirname(__FILE__) . "/inc/head.html");
    echo "<body onload='getPageName()'>";
    readfile(dirname(__FILE__) . "/inc/layout.html");
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $gate = $_GET['gate'];
    } else {
        die('404');
    }
    $transactions = \admin\Functions::getTransactionsForGate($id);
    $count = count($transactions);
    ?>



    <div class="table-responsive">
        <?php
        if ($count == 0) {
            echo "Nothing Yet";
        } else {
            ?>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Has Incoming Params?</th>
                        <th>Has Outgoing Params?</th>
                    </tr>
                </thead>
                <tbody id='incomingTBody'>

                    <?php
                    for ($i = 0; $i < $count; $i++) {
                        $tranId = $transactions[$i]['id'];
                        $tranName = $transactions[$i]['name'];
                        $tranType = $transactions[$i]['type'];
                        $tranPar = $transactions[$i]['hasParameters'];
                        $tranData = $transactions[$i]['hasData'];


                        echo "<tr id='clickable$i'>";
                        echo "<td>$tranId</td><td>$tranName</td>";
                        echo "<td>$tranType</td><td>$tranPar</td>";
                        echo "<td>$tranData</td>";
                        echo "</tr>";
                    }
                    ?>

                    <tr>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>
        <button type="button" class="btn btn-warning" id="addTrans"><span data-feather='plus-circle'></span> Transaction</button>
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
<?php
for ($x = 0; $x < $count; $x++) {
    echo "$('#clickable$x').on('click', 'td', function (e) {"
    . 'var url = window.location.href;'
    . 'url = url.replace("transactions", "add");'
    . 'window.location.href = url + "&tid=" + $("td:first", $(this).parents("tr")).text();'
    . '});';
}
?>



        $('#pageName').append(' for <?php echo $gate ?>');



        $('#addTrans').on('click', function (e) {
            var url = window.location.href;
            url = url.replace("transactions", "add");

            window.location.href = url;
        });

    });
</script>
</body>
</html>


