<?php
    $home    = url('');
    $reindex = url('/reindex');
    $plugin  = url('/plugin');
?>
<div class="navbar navbar-inverse navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse">

            <ul class="nav navbar-nav">
                <!--
                    <li class="active">Home</li>
                -->
                <li><a href="<?php echo $home;    ?>">Search</a></li>
                <li><a href="<?php echo $reindex; ?>">重新索引</a></li>
                <li><a href="<?php echo $plugin;  ?>">外掛程式管理</a></li>
            </ul>

        </div>
    </div>
</div>
