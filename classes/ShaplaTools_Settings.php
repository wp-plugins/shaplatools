<?php

class ShaplaTools_Settings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_options_page( 
            __( 'ShaplaTools Options', 'shaplatools' ), 
            __( 'ShaplaTools', 'shaplatools' ), 
            'manage_options', 
            'shaplatools', 
            array( $this, 'shaplatools_options_page')
        );
    }

    /**
     * Options page callback
     */
    public function shaplatools_options_page()
    {
        // Set class property
        $this->options = get_option( 'shaplatools_options' );
        ?>
        <div class="wrap">
            <h2><?php _e('ShaplaTools Settings', 'shaplatools'); ?></h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'shaplatools_options' );   
                do_settings_sections( 'shaplatools' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'shaplatools_options', // Option group
            'shaplatools_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'ShaplaTools Custom Post Section', // Title
            array( $this, 'print_section_info' ), // Callback
            'shaplatools' // Page
        );  

        add_settings_field(
            'show_custom_post', // ID
            'Show or Hide Custom Post', // Title 
            array( $this, 'custom_post_type_callback' ), // Callback
            'shaplatools', // Page
            'setting_section_id' // Section           
        );    
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['show_portfolio'] ) )
            $new_input['show_portfolio'] = sanitize_text_field( $input['show_portfolio'] );

        if( isset( $input['show_testimonial'] ) )
            $new_input['show_testimonial'] = sanitize_text_field( $input['show_testimonial'] );

        if( isset( $input['show_event'] ) )
            $new_input['show_event'] = sanitize_text_field( $input['show_event'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function custom_post_type_callback()
    {
        ?>
        <p>
            <label for="show_portfolio">
                <input type="checkbox" name="shaplatools_options[show_portfolio]" id="show_portfolio" value="1" <?php if ( isset($this->options['show_portfolio']) && '1' == $this->options['show_portfolio'] ) echo 'checked'; ?>>Show Portfolio
            </label>
        </p>
        <p>
            <label for="show_testimonial">
                <input type="checkbox" name="shaplatools_options[show_testimonial]" id="show_testimonial" value="1" <?php if ( isset($this->options['show_testimonial']) && '1' == $this->options['show_testimonial'] ) echo 'checked'; ?>>Show Testimonial
            </label>
        </p>
        <p>
            <label for="show_event">
                <input type="checkbox" name="shaplatools_options[show_event]" id="show_event" value="1" <?php if ( isset($this->options['show_testimonial']) && '1' == $this->options['show_event'] ) echo 'checked'; ?>>Show Event
            </label>
        </p>
        <?php
    }
}

if( is_admin() )
$shaplatools_settings = new ShaplaTools_Settings();