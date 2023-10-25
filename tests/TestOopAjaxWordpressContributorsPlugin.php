<?php
use PHPUnit\Framework\TestCase;

class TestOopAjaxWordpressContributorsPlugin extends TestCase
{
    /**
     * @var OopAjaxWordpressContributorsPlugin
     */
    public $plugin;

    protected $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new WP_UnitTest_Factory();
        $this->plugin = new OopAjaxWordpressContributorsPlugin();
    }

    /**
     * Test that the add_contributors_metabox function adds a metabox to the post editor.
     */
    public function test_add_contributors_metabox()
    {
        global $wp_meta_boxes;

        // Call the add_contributors_metabox function
        $this->plugin->add_contributors_metabox();

        // var_dump($wp_meta_boxes['post']['advanced']['default']);

        $this->assertArrayHasKey('contributors_metabox', $wp_meta_boxes['post']['advanced']['default']);
        $this->assertEquals('Contributors', $wp_meta_boxes['post']['advanced']['default']['contributors_metabox']['title']);
        $this->assertEquals(array($this->plugin, 'display_contributors_metabox'), $wp_meta_boxes['post']['advanced']['default']['contributors_metabox']['callback']);
    }

    public function test_display_contributors_metabox()
    {
        // Set up the necessary global variables
        global $post;
        $post = $this->factory->post->create_and_get();

        // Call the method to be tested
        ob_start();
        $this->plugin->display_contributors_metabox();
        $output = ob_get_clean();

        // var_dump($output);
        // exit;
        // Assert that the output contains the expected HTML
        $this->assertStringContainsString('<div class="pt-main-box">', $output);
        $this->assertStringContainsString('<label for="contributor-input">Contributors:</label>', $output);
        $this->assertStringContainsString('<input type="text" id="contributor-input" name="contributor-input">', $output);
        $this->assertStringContainsString('<span class="loader" id="loading"></span>', $output);
        $this->assertStringContainsString('<div id="suggested-names" class="cursorstyle"></div>', $output);
        $this->assertStringContainsString('<div id="selected-names">', $output);
        $this->assertStringContainsString('<ul class="ulstyle">', $output);
        $this->assertStringContainsString('<input type="hidden" id="contributor-names" name="contributor-names" value="">', $output);
    }

    public function test_save_contributor_names()
    {
        $post_id = $this->factory->post->create();

        $_POST['contributor-names'] = 'John Doe, Jane Doe';

        // $plugin = new OopAjaxWordpressContributorsPlugin();

        $this->plugin->save_contributor_names($post_id);

        $this->assertNotEmpty(get_post_meta($post_id, 'contributors', true));
    }

    public function test_display_contributors_in_content()
    {
        // Create a new post.
        $post_id = $this->factory->post->create();

        // Set the post as the global $post object.
        global $post;
        $post = get_post($post_id);

        // Add some contributor names to the post meta.
        $contributor_names = array('Contributor 1', 'Contributor 2');
        update_post_meta($post_id, 'contributors', $contributor_names);

        // Call the method being tested.
        $content = $this->plugin->display_contributors_in_content('');

        // Assert that the content contains the expected contributor names.
        $this->assertStringContainsString('Contributor 1', $content);
        $this->assertStringContainsString('Contributor 2', $content);
    }
}
