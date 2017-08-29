<?php

/**
 * Product List
 */
?>

<table class="form">
    <tr class="tr-row">
        <td>
            <h3 class="theading">Quick Update</h3>
        </td>
    </tr>
    <tr class="tr-row">
        <th class="theading">ID</th>
        <th class="theading">Name</th>
    </tr>
    <tr class="tr-row">
        <td width="20%">
            <div class="input text">
                <input type="text" name="oauth_display_options[product_list][product_id]" value="<?php echo $options['product_list']['product_id']; ?>" />
            </div>
        </td>
        <td width="80%">
            <div class="input text">
                <input type="text" name="oauth_display_options[product_list][product_name]" value="<?php echo $options['product_list']['product_name']; ?>" />
            </div>
        </td>
    </tr>
</table>
<table class="form">
    <tr class="tr-row">
        <th class="theading">Description</th>
    </tr>
    <tr class="tr-row">
        <td>
            <div class="input textarea">
                <!--<input type="text" name="oauth_display_options[product_list][product_description]" value="<?php echo $options['product_list']['product_description']; ?>" />-->
                <textarea name="oauth_display_options[product_list][product_description]" id="" cols="100" rows="10"><?php echo $options['product_list']['product_description']; ?></textarea>
            </div>
        </td>
    </tr>
</table>
<table class="form">
    <tr class="tr-row">
        <th class="theading">SKU</th>
        <th class="theading">Price</th>
        <th class="theading">Date</th>
    </tr>
    <tr class="tr-row">
        <td>
            <div class="input text">
                <input type="text" name="oauth_display_options[product_list][product_sku]" value="<?php echo $options['product_list']['product_sku']; ?>" />
            </div>
        </td>
        <td>
            <div class="input text">
                <input type="text" name="oauth_display_options[product_list][product_price]" value="<?php echo $options['product_list']['product_price']; ?>" />
            </div>
        </td>
        <td>
            <div class="input text">
                <input type="text" name="oauth_display_options[product_list][product_date]" value="<?php echo $options['product_list']['product_date']; ?>" />
            </div>
        </td>
    </tr>
</table>
<table class="form">
    <tr class="tr-row">
        <td>
            <a href="<?php echo esc_url( $this->get_url( 'action=update_product' ) ); ?>" class="oauth-auth update_product">
                <?php echo esc_html_x( 'Update Product', 'application', 'rest_oauth1' ); ?>
            </a>
        </td>
    </tr>
</table>


<table class="form">
    <tr class="tr-row">
        <td>
            <h3>Choose a product to update</h3>
        </td>
    </tr>
    <tr class="tr-row">
        <td>
            <div class="products">List products here</div>
        </td>
    </tr>
    <tr class="tr-row">
        <td>
            <a href="<?php echo esc_url( $this->get_url( 'action=get_product' ) ); ?>" class="oauth-auth get_product">
                <?php echo esc_html_x( 'Get Product', 'application', 'rest_oauth1' ); ?>
            </a>
        </td>
    </tr>
</table>

