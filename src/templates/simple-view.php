<div id="form_success" style="background-color: green; color: white;"></div>
<div id="form_failed" style="background-color: red; color: white;"></div>

<form id="dummy-contact-form" method="post" action="">

    <?php wp_nonce_field('wp_rest'); ?>

    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>


    <label for="phone">Phone:</label>
    <input type="tel" id="phone" name="phone" required>

    <label for="message">Message:</label>
    <textarea id="message" name="message" required></textarea>

    <button type="submit" title="Submit" name="submit_dummy_contact_form" value="Send">Submit
</form>

<script>
    jQuery(document).ready(function($) {
        $('#dummy-contact-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            console.log(form.serialize());
            // alert('Form submission is currently disabled in this demo.');

            // var formData = {
            //     name: $('#name').val(),
            //     email: $('#email').val(),
            //     phone: $('#phone').val(),
            //     message: $('#message').val()
            // };

            $.ajax({
                method: 'POST',
                url: "<?php echo esc_url(get_rest_url(null, 'dummy-contact-form/v1/submit')); ?>",
                data: form.serialize(),
                success: function(response) {
                    // alert('Message sent successfully!');
                    // form[0].reset();
                    form.hide();
                    $("#form_success").html('Message sent successfully!').show().delay(5000).fadeOut();
                },
                error: function(response) {
                    alert('There was an error sending your message.');
                }

            });
        });
    });
</script>