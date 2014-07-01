<?php
add_action('wp_ajax_sandbox_edit_verify', 'sandbox_edit_verify_ajax');

function sandbox_edit_verify_ajax(){
    global $sandbox_errors;
    
    $name = $_REQUEST['name'];
    $shortname = $_REQUEST['shortname'];
    $description = $_REQUEST['description'];
    $action = $_REQUEST['edit_action'];
    try {
        Sandbox::verify_parameters($action, $name, $shortname);
    } catch (Sandbox_Exception $sandbox_exception) {
        $sandbox_exception->sandbox_error->print_error();
    }
    die();
}
?>