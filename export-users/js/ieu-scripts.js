jQuery(document).ready(function($){

    $('button.ieu-export').on( 'click', function(e){

        e.preventDefault();

        var data = {
            action: 'ieu_export',
            nonce: ieu_export.nonce
        }

        var button = $(this);

        var request = $.post( ieu_export.ajaxurl, data, function( response ) {
            console.log( response.data ); 
            location = response.data;  
        } );

        // Callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
            // Log a message to the console
            console.log("Hooray, it worked!");
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

});