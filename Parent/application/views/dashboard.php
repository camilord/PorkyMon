<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/03/14
 * Time: 11:20 AM
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include(APPPATH.'views/modules/head.php');
    ?>
</head>

<body>

<?php
include(APPPATH.'views/modules/topnav.php');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <?php
            include(APPPATH.'views/modules/sidebar.php');
            ?>
        </div>
        <div id="main-container" class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h1 class="page-header">Dashboard</h1>

            <div id="servers-list" class="row placeholders">
                <div class="pull-left" style="margin: 4px 0 50px 20px">
                    <img src="/public/images/ajax-loader.gif" alt="" /> Loading Servers...
                </div>
            </div>

            <h2 class="sub-header">Recent Updates</h2>
            <div id="recent-updates" class="table-responsive">
                <div class="pull-left" style="margin: 4px 0 50px 20px">
                    <img src="/public/images/ajax-loader.gif" alt="" /> Loading Recent Server Updates...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<?php
include(APPPATH.'views/modules/jscripts.php');
?>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            type: "POST",
            url: "/ajax/server/list",
            cache: false
        }).done(function( response_data ) {
            $("div#servers-list").html(response_data);
        });
        $.ajax({
            type: "POST",
            url: "/ajax/updates",
            cache: false
        }).done(function( response_data ) {
            $("div#recent-updates").html(response_data);
        });
    });
</script>
</body>
</html>
