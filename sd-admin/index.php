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




include_once dirname(__DIR__) . '/sd-admin/inc/functions/Functions.php';
?>
<!doctype html>
<html lang="en">
    <?php
    readfile(dirname(__FILE__) . "/inc/head.html");
    echo "<body onload='getPageName()'>";
    readfile(dirname(__FILE__) . "/inc/layout.html");
    ?>

    <h2>Gates</h2>
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gate Name</th>
                    <th>Manage</th>
                    <th>Changed</th>
                </tr>
            </thead>
            <tbody>
                <?php
//Move to Functions
                $gates = \admin\Functions::getGates();
                $gateCount = count($gates);
//echo $gateCount;
                $tableRows = "";
                for ($i = 0; $i < $gateCount; $i++) {
                    $tableRows = "<tr>";
                    $tableRows .= "<td>" . $gates[$i]['id'] . "</td>";
                    $tableRows .= "<td>" . $gates[$i]['gateName'] . "</td>";

                    $tableRows .= "<td>";
                    $tableRows .= ""
                            . ""
                            . "<a class='nav-link' style='display: inline-block;' href='makeGate.php?id=" . $gates[$i]['id'] . "&gate=" . $gates[$i]["gateName"] . "' data-toggle='tooltip' data-placement='top' title='Delete Gate'>"
                            . "<span data-feather='edit'></span></a>"
                            /* . "<a class='nav-link' style='display: inline-block;' href='delete.php?id=" . $gates[$i]['id'] . "&gate=" . $gates[$i]["gateName"] . "' data-toggle='tooltip' data-placement='top' title='Delete Gate'>"
                              . "<span data-feather='delete'></span></a> */ . "</td>";
                    //This delete button set, but no functionality added
                    $tableRows .= "<td>" . $gates[$i]['changed'] . "</td></tr>";
                    echo $tableRows;
                }
                ?>
            </tbody>
        </table>
    </div>
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
