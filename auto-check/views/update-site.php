<?php 
/**
 * Site Manager
 */

do_settings_sections('autocheck_options_info');
?>
<div class="wrap">
    <form action="options.php" method="post">
        <table class="form">
            <tr class="tr-row">
                <th class="theading">URL</th>
            </tr>
            <tr class="tr-row">
                <td>
                    <div class="input text url">
                        <input type="text" name="autocheck_display_options[site_url]" data-url="<?php echo ( !empty($url) ) ? $url : ''; ?>" value="<?php echo ( !empty($url) ) ? $url : ''; ?>" />
                    </div>
                </td>
            </tr>
        </table>

        <table class="form">
            <tr class="tr-row">
                <td>
                    <a href="<?php echo esc_url( $this->get_url( 'action=updates' ) ); ?>" class="autocheck custombtn get_updates">
                        <?php echo esc_html_x( 'Check for Updates', 'application', 'rest_autocheck1' ); ?>
                    </a>
                </td>
                <td>
                    <a href="<?php echo esc_url( $this->get_url( 'action=updates' ) ); ?>" class="autocheck custombtn secondary single_update">
                        <?php echo esc_html_x( 'Update Selected', 'application', 'rest_autocheck1' ); ?>
                    </a>
                </td>
                <!-- <td>
                    <a href="<?php echo esc_url( $this->get_url( 'action=updates' ) ); ?>" class="autocheck custombtn secondary bulk_update">
                        <?php echo esc_html_x( 'Update All', 'application', 'rest_autocheck1' ); ?>
                    </a>
                </td> -->
            </tr>
        </table>

        <table class="form plugin-list">
            
        </table>

        <div class="loader-wrap">
            <div class="loader">
                <div class="square square1"></div>
                <div class="square square2"></div>
                <div class="square square3"></div>
            </div>
        </div>
    </form>
</div>