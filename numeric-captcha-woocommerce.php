<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
Plugin Name: Numeric Captcha for WooCommerce
Description: Adds a numeric captcha to the WooCommerce login and register forms.
Version: 1.0
Author: Mohammad Ebrahim Aali
Author URI: https://www.linkedin.com/in/mohammad-ebrahim-aali/
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

// Add numeric captcha to WooCommerce login form
add_action('woocommerce_login_form', 'ncw_add_numeric_captcha_to_login_form');

function ncw_add_numeric_captcha_to_login_form() {
    if (is_account_page()) {
        echo '<p class="form-row form-row-wide" id="ncw_numeric_captcha_login_wrapper" style="display: none;">';
        echo '<label for="ncw_numeric_captcha_login">' . esc_html__('Please solve the captcha', 'woocommerce') . ' (e.g., <span id="ncw_captcha_question_login"></span>) <span class="required">*</span></label>';
        echo '<input type="text" class="input-text" name="ncw_numeric_captcha_login" id="ncw_numeric_captcha_login" required />';
        echo '</p>';
    }
}

// Add numeric captcha to WooCommerce register form
add_action('woocommerce_register_form', 'ncw_add_numeric_captcha_to_register_form');

function ncw_add_numeric_captcha_to_register_form() {
    if (is_account_page()) {
        echo '<p class="form-row form-row-wide" id="ncw_numeric_captcha_register_wrapper" style="display: none;">';
        echo '<label for="ncw_numeric_captcha_register">' . esc_html__('Please solve the captcha', 'woocommerce') . ' (e.g., <span id="ncw_captcha_question_register"></span>) <span class="required">*</span></label>';
        echo '<input type="text" class="input-text" name="ncw_numeric_captcha_register" id="ncw_numeric_captcha_register" required />';
        echo '</p>';
    }
}

// Move captcha field after password field
add_action('woocommerce_login_form', 'ncw_move_captcha_after_password_login');
add_action('woocommerce_register_form', 'ncw_move_captcha_after_password_register');

function ncw_move_captcha_after_password_login() {
    echo '<script type="text/javascript">
            jQuery(document).ready(function($) {
                $("#ncw_numeric_captcha_login_wrapper").insertAfter("#password");
                $("#ncw_numeric_captcha_login_wrapper").show();
            });
          </script>';
}

function ncw_move_captcha_after_password_register() {
    echo '<script type="text/javascript">
            jQuery(document).ready(function($) {
                $("#ncw_numeric_captcha_register_wrapper").insertAfter("#reg_password");
                $("#ncw_numeric_captcha_register_wrapper").show();
            });
          </script>';
}

// Dynamic Captcha Question
add_action('wp_footer', 'ncw_dynamic_captcha_question_script');

function ncw_dynamic_captcha_question_script() {
    if (is_account_page()) {
        $captcha_question_login = rand(1, 10) . ' + ' . rand(1, 10);
        $captcha_question_register = rand(1, 10) . ' + ' . rand(1, 10);
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Set dynamic captcha question for login form
                $('#ncw_captcha_question_login').text('<?php echo esc_js($captcha_question_login); ?>');

                // Set dynamic captcha question for register form
                $('#ncw_captcha_question_register').text('<?php echo esc_js($captcha_question_register); ?>');

                // Validate numeric captcha for login and registration
                $('#login, #register').on('submit', function(event) {
                    var $form = $(this).closest('form');
                    var $captchaInput = $form.find('input[name="ncw_numeric_captcha_login"], input[name="ncw_numeric_captcha_register"]');
                    var captchaQuestion = $form.find('#ncw_captcha_question_login, #ncw_captcha_question_register').text();
                    var captchaAnswer = eval(captchaQuestion);

                    if ($captchaInput.val() != captchaAnswer) {
                        event.preventDefault();
                        alert('<?php echo esc_js(__('Incorrect answer to the numeric captcha. Please try again.', 'woocommerce')); ?>');
                    }
                });
            });
        </script>
        <?php
    }
}