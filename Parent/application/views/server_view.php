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
            <h1 class="page-header">Server: <?php echo $server_details['hostname']; ?></h1>
            <div id="server-details" class="row placeholders">
                <table class="table table-bordered">
                    <tr>
                        <td class="text-left">Hostname:</td><th><?php echo $server_details['hostname']; ?></th>
                        <td class="text-left">Operating System:</td><th><?php echo $server_details['os']; ?></th>
                        <td class="text-left">IP Address:</td><th><?php echo $server_details['ip']; ?></th>
                    </tr>
                    <tr>
                        <td class="text-left">RAM / HDD:</td><th><?php echo $this->porky->bytes2size($server_details['memory']['total']).' / '.$this->porky->bytes2size($server_details['hdd']['total']); ?></th>
                        <td class="text-left">Kernel:</td><th><?php echo $server_details['kernel']; ?></th>
                        <td class="text-left">Architecture:</td><th><?php echo $server_details['architecture']; ?></th>
                    </tr>
                </table>
            </div>
            <h1 class="page-header">Stats</h1>
            <div id="server-stats" class="row placeholders">

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

</script>
</body>
</html>
