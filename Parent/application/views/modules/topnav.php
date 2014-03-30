<?php
/**
 * Created by PhpStorm.
 * User: camilord
 * Date: 3/30/14
 * Time: 5:47 AM
 */

?>

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><?php echo $this->config->item('company_name'); ?></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/?t=<?php echo time(); ?>">Dashboard</a></li>
                <li><a href="/server">Servers</a></li>
                <li><a href="/settings">Settings</a></li>
                <li><a href="/home/logout?t=<?php echo time(); ?>">Sign Out</a></li>
            </ul>
            <!-- form class="navbar-form navbar-right">
                <input type="text" class="form-control" placeholder="Search...">
            </form -->
        </div>
    </div>
</div>