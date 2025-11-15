<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;


add_action('after_setup_theme', 'load_carbon_fields');
add_action('carbon_fields_register_fields', 'create_options');

function load_carbon_fields()
{
    // require_once __DIR__ . '/../vendor/autoload.php';
    \Carbon_Fields\Carbon_Fields::boot();
}


function create_options()
{
    Container::make('theme_options', __('Contact Form'))->set_icon('dashicons-forms')
        ->add_fields(array(
            Field::make('checkbox', 'contact_form_active', 'Active')
                ->set_option_value('yes'),
            Field::make('text', 'contact_form_emails', 'Emails')
                ->set_attribute('placeholder', 'Enter your email address here..')
                ->set_help_text('Email that the form is submitted to'),
            Field::make('textarea', 'contact_form_message', 'Message')
                ->set_attribute('placeholder', 'Enter your confirmation message here..')
                ->set_help_text('Message sent after the confirmation'),
            Field::make('media_gallery', 'crb_media_gallery')
                ->set_type(array('image', 'video'))
                ->set_help_text('Add images or videos')
        ));
}
