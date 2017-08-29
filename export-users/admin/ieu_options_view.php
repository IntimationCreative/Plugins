<?php

/**
 * IEU Admin Options Page 
 */

?>

<div class="wrap">
    <h1>Intimation Export Users</h1>

    <form action="options.php" method="post">
        <?php

        // output for the registered setting "export_users_settings" (option group name)
        settings_fields('export_users_settings'); ?>

        <ul>
        
        <?php // output for setting sections and their fields - (page name)
        do_settings_sections('inti_export_users'); ?>

        </ul>
    
        <form action="" method="post">
            <button type="submit" class="button ieu-export">Export</button>
        </form>

    </form>
</div>