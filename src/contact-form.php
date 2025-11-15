<?php

use WP_REST_Response;

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

// function handle_form_submission($data)
// {
//     $params = $data->get_params();
//     // var_dump($params);

//     if (!wp_verify_nonce($params['_wpnonce'], 'wp_rest')) {
//         return new WP_REST_Response('Invalid nonce!!', 403);
//     }

//     unset($params['_wp_http_referer']);
//     unset($params['_wpnonce']);


//     //send the email message
//     $headers = [];
//     $adminEmail = get_bloginfo('adminEmail');
//     $admin_name = get_bloginfo('name');

//     $headers[] = 'From: ' . $admin_name . ' <' . $adminEmail . '>';
//     $headers[] = 'Reply-To: ' . $params['name'] . '<' . sanitize_email($params['email']) . '>';
//     $headers[] = 'Content-Type: text/html; charset=UTF-8';

//     $subject = 'New Contact Form Submission from ' . $params['name'];

//     $message = '';
//     $message  = "<h1>Message has been sent from {$params['name']}</h1><br>";
//     foreach ($params as $label => $value) {
//         $message  .= '<strong>' . ucfirst($label) . '</strong>' . ": {$value}<br>";
//     }

//     $postArr = [
//         'post_title' => sanitize_text_field($params['name']),
//         'post_type' => 'submission',
//     ];
//     wp_insert_post($postArr);

//     wp_mail($adminEmail, $subject, $message, $headers);
//     return new WP_REST_Response('Message was sent', 200);
// }
function handle_form_submission($data)
{
    $params = $data->get_params();

    if (!isset($params['_wpnonce']) || !wp_verify_nonce($params['_wpnonce'], 'wp_rest')) {
        return new WP_REST_Response('Invalid nonce', 403);
    }

    // Sanitize inputs
    $name = sanitize_text_field($params['name']);
    $email = sanitize_email($params['email']);
    $message = isset($params['message']) ? sanitize_textarea_field($params['message']) : '';

    if (empty($name) || !is_email($email)) {
        return new WP_REST_Response('Invalid form data', 400);
    }

    // Prepare email headers
    $headers = [];
    $adminEmail = get_bloginfo('admin_email');
    $adminName = get_bloginfo('name');
    $headers[] = 'From: ' . $adminName . ' <' . $adminEmail . '>';
    $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    $subject = 'New Contact Form Submission from ' . $name;

    $emailMessage = "<h1>Message from {$name}</h1><br>";
    $emailMessage .= "<strong>Name:</strong> {$name}<br>";
    $emailMessage .= "<strong>Email:</strong> {$email}<br>";
    $emailMessage .= "<strong>Message:</strong> {$message}<br>";

    // Insert into database
    $postArr = [
        'post_title'   => $name,
        'post_content' => $message,
        'post_type'    => 'submission',
        'post_status'  => 'pending',
    ];

    $postId = wp_insert_post($postArr);
    if (is_wp_error($postId)) {
        return new WP_REST_Response('Error saving submission', 500);
    }

    // Send email
    $mailSent = wp_mail($adminEmail, $subject, $emailMessage, $headers);
    if (!$mailSent) {
        return new WP_REST_Response('Error sending email', 500);
    }

    return new WP_REST_Response('Message was sent', 200);
}
