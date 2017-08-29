/**
 * Update the post status on click
 */

jQuery(document).ready(function($){

    $('button.archive').on( 'click', function( e ){

        e.preventDefault();

        var id = $(this).data('id');

        var data = {
            action: 'ps_archive_post',
            post_id: id,
            nonce: post_status_archive.nonce
        };

        var button = $(this);

        $.post( post_status_archive.ajaxurl, data, function ( response ) 
        {
            $(button).html(response.data).addClass('archived');
        });

    });

    // Archive all
    $('button.archive-all').on( 'click', function( e ){

        e.preventDefault();

        var id = $(this).data('id');

        var data = {
            action: 'ps_archive_all_posts',
            post_id: id,
            nonce: post_status_archive_all.nonce
        };

        var button = $(this);

        $.post( post_status_archive_all.ajaxurl, data, function ( response ) 
        {   
            console.log(response.data);
            // $(button).html(response.data).addClass('archived');
        });

    });

});