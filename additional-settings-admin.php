<?php
   /*
   Plugin Name: Additional settings admin (devswb)
   description: This plugin was developed for a specific task. For example of working with: get_option, register_setting, add_settings_section, add_settings_field
   Version: 1.2
   License: GPL2
   */

   class MySettingsPage
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
           add_action( 'admin_menu', array( $this, 'add_page_count_setting' ) );
           add_action( 'admin_init', array( $this, 'page_init' ) );
       }

       /**
        * Add options page
        */
       public function add_page_count_setting()
       {
           // This page will be under "Settings" - https://developer.wordpress.org/reference/functions/add_options_page/
           add_options_page(
               'Settings Admin', 
               'Additional settings',
               'manage_options', 
               'my-setting-admin', 
               array( $this, 'create_admin_page' )
           );
       }

       /**
        * Options page callback (render fields, content, and other elements)
        */
       public function create_admin_page()
       {
           // Set class property
           $this->options = get_option( 'additional_setting' );
           ?>
           <div class="wrap">
               <h1>Additional settings</h1>
               <form method="post" action="options.php">
               <?php
                   settings_fields( 'additional_setting_group' );
                   do_settings_sections( 'my-setting-admin' );
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
           register_setting( // https://developer.wordpress.org/reference/functions/register_setting/
               'additional_setting_group', // Option group
               'additional_setting', // Option name
               array( $this, 'sanitize' ) // Sanitize
           );

           add_settings_section(
               'setting_section_id', // ID
               'My Settings', // Title
               array( $this, 'print_section_info' ), // Callback
               'my-setting-admin' // Page
           );  

           add_settings_field(
               'id_count', // ID
               'Count (quantity for the list Home page - get_posts):', // Title
               array( $this, 'id_count_callback' ), // Callback
               'my-setting-admin', // Page
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
           if( isset( $input['id_count'] ) )
               $new_input['id_count'] = absint( $input['id_count'] );

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
       public function id_count_callback()
       {
           printf(
               '<input type="text" id="id_count" name="additional_setting[id_count]" value="%s" />',
               isset( $this->options['id_count'] ) ? esc_attr( $this->options['id_count']) : ''
           );
       }
   }

   if( is_admin() )
       $my_settings_page = new MySettingsPage();