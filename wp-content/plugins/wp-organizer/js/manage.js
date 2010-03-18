/*
 * wp-organizer manage script
 * $Id$
 */

jQuery(document).ready(function($){
    $('.wp_organizer_checkdelallbtn').click(function(){
        $('#wp_organizer_update_form .delete-cell :checkbox').each(function(i){
            $(this).attr('checked', true);
        });
    });

    $('.wp_organizer_uncheckdelallbtn').click(function(){
        $('#wp_organizer_update_form .delete-cell :checkbox').each(function(i){
            $(this).attr('checked', false);
        });
    });
    
});