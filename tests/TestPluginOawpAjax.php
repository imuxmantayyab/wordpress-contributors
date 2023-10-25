<?php
// use WPDieException as WPAjaxDieContinueException;
class TestPluginOawpAjax extends WP_Ajax_UnitTestCase
{
    public function test_ajax_function_get_contributor_names()
    {
        $user_id = wp_insert_user(array(
            'user_login' => 'john_doe_yes',
            'user_email' => 'john_doe@example.com',
            'user_pass'  => 'password',
            'role'       => 'contributor',
        ));
        
        // Spoof the nonce in the POST superglobal
        $_POST['nonce'] = wp_create_nonce('my-nonce');

        // Set up the inputVal parameter for the AJAX request
        $_POST['inputVal'] = 'doe';

        // Call the AJAX function
        try {
            $this->_handleAjax('ajax_function_get_contributor_names');
        } catch (WPAjaxDieContinueException $e) {
        }

        // Check the response
        $response = json_decode($this->_last_response);
        $this->assertIsString($response);
        $this->assertStringContainsString('<li class="suggested-name">john_doe_yes</li>', $response);

        // Clean up the user
        wp_delete_user($user_id);
    }
}
