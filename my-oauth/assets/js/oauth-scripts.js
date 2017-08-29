jQuery(document).ready(function($){
    $('a.oauth-auth.request').on('click', function(e){
        console.log('request');

        e.preventDefault();

        var data = {
            action: 'request',
            noonce: 'oauth_auth',
            url: $(this).data('url')
        }

        var button = $(this);

        $('.loader-wrap').show();
        button.text('Updating...');

        var request = $.post(oauth.ajaxurl, data, function( response ) {
            console.log('requested access');
            console.log( response );
            location = 'http://icl1.co.uk/wp-admin/admin.php?page=oauth';
        });

         // Callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
            // Log a message to the console
            console.log("Hooray, it worked!");
            $('.loader-wrap').hide();
            button.text('Access Requested');         
        });

        // Callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown){
            // Log the error to the console
            console.error(
                "The following error occurred: "+
                textStatus, errorThrown
            );
        });

        
    });
    $('a.oauth-auth.access').on('click', function(e){
        console.log('access');

        e.preventDefault();

        var data = {
            action: 'access',
            noonce: 'oauth_auth',
            url: $(this).data('url')
        }

        var button = $(this);

        $('.loader-wrap').show();
        button.text('Updating...');

        var access = $.post(oauth.ajaxurl, data, function( response ) {
            console.log('accessed access');
            console.log( response.data.response );
            console.log( response.data.base_string );
            location = 'http://icl1.co.uk/wp-admin/admin.php?page=oauth';
            
        });

         // Callback handler that will be called on success
        access.done(function (response, textStatus, jqXHR){
            // Log a message to the console
            console.log("Hooray, it worked!");
            // console.log(response.response);
            $('.loader-wrap').hide();
            button.text('Access accessed');         
        });

        // Callback handler that will be called on failure
        access.fail(function (jqXHR, textStatus, errorThrown){
            // Log the error to the console
            console.error(
                "The following error occurred: "+
                textStatus, errorThrown
            );
        });

        
    });
    $('a.oauth-auth.request_add').on('click', function(e){
        console.log('request_add');

        e.preventDefault();

        var data = {
            action: 'request_add',
            noonce: 'oauth_auth',
            url: $(this).data('url')
        }

        var button = $(this);

        $('.loader-wrap').show();
        button.text('Updating...');

        var request_add = $.post(oauth.ajaxurl, data, function( response ) {
            console.log('request_added request_add');
            // location = 'http://icl1.co.uk/wp-admin/admin.php?page=oauth';
        });

         // Callback handler that will be called on success
        request_add.done(function (response, textStatus, jqXHR){
            // Log a message to the console
            console.log("Hooray, it worked!");
            // console.log(response.response);
            $('.loader-wrap').hide();
            button.text('request_add request_added');         
        });

        // Callback handler that will be called on failure
        request_add.fail(function (jqXHR, textStatus, errorThrown){
            // Log the error to the console
            console.error(
                "The following error occurred: "+
                textStatus, errorThrown
            );
        });

        
    });
    $('a.oauth-auth.get_product').on('click', function(e){
        console.log('get_product');

        e.preventDefault();

        var data = {
            action: 'products',
            noonce: 'oauth_auth',
            url: $(this).data('url')
        }

        var button = $(this);

        $('.loader-wrap').show();
        button.text('Updating...');

        var get_product = $.post(oauth.ajaxurl, data, function( response ) {
            // console.log('get_producted get_product');
            console.log( response );
            // location = 'http://icl1.co.uk/wp-admin/admin.php?page=oauth';
        });

         // Callback handler that will be called on success
        get_product.done(function (response, textStatus, jqXHR){
            // Log a message to the console
            console.log("Hooray, it worked!");
            // console.log(response.response);
            $('.loader-wrap').hide();
            button.text('got products');         
        });

        // Callback handler that will be called on failure
        get_product.fail(function (jqXHR, textStatus, errorThrown){
            // Log the error to the console
            console.error(
                "The following error occurred: "+
                textStatus, errorThrown
            );
        });
    });
});