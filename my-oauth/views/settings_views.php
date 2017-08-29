<?php

/**
 * Oauth Settings Admin Options Page
 */

do_settings_sections('oauth_options_info');

?>
<div class="wrap">

    <?php
        $active = "site-list";

       if(isset($_GET["tab"]))
        {
            if($_GET["tab"] == "site-list")
            {
                $active = "site-list";
            } else
            {
                $active = "product-list";
            }
        }
    ?>

    <h2 class="nav-tab-wrapper">
        <a href="?page=oauth&tab=site-list" class="nav-tab <?php echo ($active == 'site-list') ? 'nav-tab-active' : ''; ?> ">
            <?php _e( 'Site Details', 'base' ); ?>
        </a>
        <a href="?page=oauth&tab=product-list" class="nav-tab <?php echo ($active == 'product-list') ? 'nav-tab-active' : ''; ?> ">
            <?php _e( 'Manage Products', 'base' ); ?>
        </a>
    </h2>

    <form action="options.php" method="post">
        <div class="tab-body">
        <?php 
            settings_fields( 'oauth_display_options' );
            do_settings_sections('oauth_display_options'); 
        ?>
            <table class="form">
                <tr class="tr-row">
                    <td><?php submit_button( 'Save Site Info' ); ?></td>
                </tr>
            </table>
        </div>

        <div class="loader-wrap">
            <div class="loader">
                <!--<div class="square square1"></div> -->
                <div class="square square1"></div>
                <div class="square square2"></div>
                <div class="square square3"></div>
            </div>
        </div>
    </form>

</div>