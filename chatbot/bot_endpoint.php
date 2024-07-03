<?php
// Función para manejar las solicitudes del chatbot
function handle_chatbot_request() {
    if (!is_user_logged_in()) {
        echo json_encode(array('response' => "Debes estar registrado para interactuar con el chatbot.", 'disabled' => true));
        wp_die();
    }

    $user_input = $_POST['message'];
    $api_url = 'http://localhost:5000/get_response'; // URL del servicio Flask

    //añadir un error tipo chat desactivado o en mantenimiento cuando falle el puerto
    $response = wp_remote_post($api_url, array(
        'method'    => 'POST',
        'body'      => json_encode(array('message' => $user_input)),
        'headers'   => array(
            'Content-Type' => 'application/json',
        ),
    ));

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        echo json_encode(array('response' => "Chat desactivado o en mantenimiento."));
    } else {
        $body = wp_remote_retrieve_body($response);
        echo $body;
    }

    wp_die();
}
add_action('wp_ajax_nopriv_get_chatbot_response', 'handle_chatbot_request');
add_action('wp_ajax_get_chatbot_response', 'handle_chatbot_request');

// Función para mostrar el chatbot
function my_chatbot_display() {
    include(plugin_dir_path(__FILE__) . '../templates/chatbot.html');
}
//add_shortcode('my_chatbot', 'my_chatbot_display');
add_action('wp_footer', 'my_chatbot_display');

?>