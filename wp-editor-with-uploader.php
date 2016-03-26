<?php
/*
Plugin Name: WP-edtior-with-uploader
Plugin URI: 
Description: Enables you to use wp_editor with uploader
Version: 0.8
Author: @mrxx
Author URI: https://github.com/mrxx
Text Domain: https://github.com/mrxx/wp-editor-with-uploader
*/

function wp_editor_with_uploader( $content, $editor_id, $settings)
{
    $textarea_name = !empty($settings['textarea_name']) ? $settings['textarea_name'] : 'post_content';
    echo '<textarea id="'.$editor_id.'" name="'.$textarea_name.'">'.$content.'</textarea>';
    echo '<script type="text/javascript" src="'.plugins_url('js/plupload-2.1.8/js/plupload.full.min.js',__FILE__).'"></script>';
    echo '<script type="text/javascript" src="'.plugins_url('js/tinymce/js/tinymce/tinymce.min.js',__FILE__).'"></script>';
    echo "<script>var uploader_url='".admin_url( 'admin-ajax.php?action=upload_7c0' )."';</script>";
    echo '<script type="text/javascript" src="'.plugins_url('js/wp-editor-with-uploader.js',__FILE__).'"></script>';
}

add_action( 'wp_ajax_nopriv_upload_7c0', 'wp_editor_with_uploader_do_upload' );
add_action( 'wp_ajax_upload_7c0', 'wp_editor_with_uploader_do_upload' );

function wp_editor_with_uploader_do_upload() {
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    $file_handler = 'user-image-custom'; 
    $attach_id = media_handle_upload( $file_handler, 0 );
    $image_attributes = wp_get_attachment_image_src( $attach_id ,'medium');
    if(!empty($image_attributes))
        echo $image_attributes[0];
    exit;
}

//Thats just a demo how to use wp_editor_with_uploader ,You can make you own
function front_form_7c0(){
    global $current_user;
    if (!is_user_logged_in()){
        auth_redirect();
    }
    $post_id = -1 ;
    $post = null;
    if(!empty($_POST['post_title']))
    {
        $new_post = array(
            'post_title'           => sanitize_text_field( $_POST['post_title'] ),
            'post_content'         => wp_kses_post($_POST['post_content']),
            'post_status'       => 'pending',
            'post_type'        =>'post',
            'post_modified'    =>date("Y-m-d H:i:s"),
        );

        $post_id = wp_insert_post($new_post, true);
        if(empty($post_id))
            $return_message='create post error';
        else
            $return_message= "create post success : <a href='/?p={$post_id}'>Link</a>";
        echo "<b>{$return_message}</b>";
    }
    echo '<form id="edit_case_form" method="post" enctype="multipart/form-data" action="">';
    echo 'Title:<br><input type="text" name="post_title" value=""><br>';
    echo 'Content:<br>';
    wp_editor_with_uploader("","post_content",array());
    echo '<br><input type="submit" value="Submit">';
    echo '</form>';
    return ob_get_clean();
}
add_shortcode( 'front_form_7c0', 'front_form_7c0');
