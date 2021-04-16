jQuery(document).ready(function ($) {
    'use strict';

    /**
     * Validate the files uploaded.
     */
    $(document).on('change', '#hc-googlesheet-file', function() {
        var this_btn = $(this);
        var fileExtension = ['xlsx'];
        var file_parts = this_btn.get(0).files[0].name.split('.');
        var ext_index = file_parts.length - 1;
        var file_ext = file_parts[ ext_index ];
        if ( -1 === $.inArray( file_ext, fileExtension ) ) {
            $('.hc-file-err').html( 'Only format allowed: ' + fileExtension.join(', ') );
            this_btn.val('');
            return false;
        } else {
            $('.hc-file-err').html( '' );
        }
    });

    /**
     * Copy the shortcode
     */
    $(document).on('click', '.hc-copy-shortcode', function () {
        var chartid = $(this).data('chartid');
        var shortcode = $('#hc-highchart-shortcode-text-' + chartid);
        shortcode.select();
        document.execCommand('copy');
        alert( 'Shortcode copied !!' );
    });

    /**
     * Disable post title edit.
     */
    if( $('.post_type_page').length > 0 ) {
        var cpt = $('.post_type_page').val();
        if( 'highchart-shortcode' === cpt ) {
            $('.row-title').attr( 'href', 'javascript:void(0);' );
        }
    }

});
