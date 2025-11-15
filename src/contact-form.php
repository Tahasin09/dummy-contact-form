<?php

add_shortcode('dummy_contact_form', 'create_contact_form');
add_action('rest_api_init', 'register_rest_api');

function create_contact_form()
{
    include MY_PLUGIN_PATH . 'src/templates/simple-view.php';
}

function register_rest_api()
{
    register_rest_route('dummy-contact-form/v1', '/submit', array(
        'methods' => 'POST',
        'callback' => 'handle_form_submission',

    ));
}

function handle_form_submission($data)
{
    $params = $data->get_params();
    // var_dump($params);

    if (!wp_verify_nonce($params['_wpnonce'], 'wp_rest')) {
        return new WP_REST_Response('Invalid nonce!!', 403);
    }

    unset($params['_wp_http_referer']);
    unset($params['_wpnonce']);


    //send the email message
    $headers = [];
    $admin_email = get_bloginfo('admin_email');
    $admin_name = get_bloginfo('name');

    $headers[] = 'From: ' . $admin_name . ' <' . $admin_email . '>';
    $headers[] = 'Reply-To: ' . $params['name'] . '<' . sanitize_email($params['email']) . '>';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    $subject = 'New Contact Form Submission from ' . $params['name'];

    $message = '';
    $message  = "<h1>Message has been sent from {$params['name']}</h1><br>";
    foreach ($params as $label => $value) {
        $message  .= '<strong>' . ucfirst($label) . '</strong>' . ": {$value}<br>";
    }

    $postArr = [
        'post_title' => sanitize_text_field($params['name']),
        'post_type' => 'submission',
    ];
    wp_insert_post($postArr);

    wp_mail($admin_email, $subject, $message, $headers);
    return new WP_REST_Response('Message was sent', 200);
}
