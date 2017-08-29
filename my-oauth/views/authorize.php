<?php 
/**
 * Authorize
 */
?>

<table class="form">
        <tr class="tr-row">
            <th class="theading">URL</th>
        </tr>
        <tr class="tr-row">
            <td>
                <div class="input text">
                    <input type="text" name="oauth_display_options[site_url]" value="<?php echo $options['site_url']; ?>" />
                </div>
            </td>
        </tr>
    </table>           
<table class="form">
    <tr class="tr-row">
        <th class="theading">Consumer Key</th>
        <th class="theading">Consumer Secret</th>
    </tr>
    <tr class="tr-row">
        <td>
            <div class="input text">
                <input type="text" name="oauth_display_options[oauth_consumer_key]" value="<?php echo $options['oauth_consumer_key']; ?>" />
            </div>
        </td>
        <td>
            <div class="input text">
                <input type="text" name="oauth_display_options[oauth_consumer_secret]" value="<?php echo $options['oauth_consumer_secret']; ?>" />
            </div>
        </td>
    </tr>
</table>
<table class="form">
    <tr class="tr-row">
        <th class="theading">Oauth Token</th>
        <th class="theading">Oauth Secret</th>
    </tr>
    <tr class="tr-row">
        <td>
            <div class="input text">
                <input type="text" name="oauth_display_options[oauth_token]" value="<?php echo $options['oauth_token']; ?>" />
            </div>
        </td>
        <td>
            <div class="input text">
                <input type="text" name="oauth_display_options[oauth_secret]" value="<?php echo $options['oauth_secret']; ?>" />
            </div>
        </td>
    </tr>
</table>
<table class="form">
    <tr class="tr-row">
        <th class="theading">Callback URL</th>
        <th class="theading">Oauth Verifier</th>
    </tr>
    <tr class="tr-row">
        <td>
            <div class="input text">
                <input type="text" name="oauth_display_options[callback_url]" value="<?php echo $options['callback_url']; ?>" />
            </div>
        </td>
        <td>
            <div class="input text">
                <input type="text" name="oauth_display_options[oauth_verifier]" value="<?php echo $options['oauth_verifier']; ?>" />
            </div>
        </td>
    </tr>
</table>
<table class="form">
    <tr class="tr-row">
        <td>
            <a href="<?php echo esc_url( $this->get_url( 'action=request' ) ); ?>" class="oauth-auth request">
                <?php echo esc_html_x( 'Request', 'application', 'rest_oauth1' ); ?>
            </a>
        

            <a href="<?php echo esc_url( $this->get_auth_url() ); ?>" class="oauth-auth authorize">
                <?php echo esc_html_x( 'Authorize', 'application', 'rest_oauth1' ); ?>
            </a>
        

            <a href="<?php echo esc_url( $this->get_url( 'action=access' ) ); ?>" class="oauth-auth access">
                <?php echo esc_html_x( 'Access', 'application', 'rest_oauth1' ); ?>
            </a>
        

            <a href="<?php echo esc_url( $this->get_url( 'action=request_add' ) ); ?>" class="oauth-auth request_add">
                <?php echo esc_html_x( 'Test Add Post', 'application', 'rest_oauth1' ); ?>
            </a>
        </td>
    </tr>
</table>