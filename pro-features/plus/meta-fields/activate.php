<?php

    defined('ABSPATH') or die("Jog on!");

    define( 'WE_LS_MYSQL_META_FIELDS', 'WS_LS_META_FIELDS' );
    define( 'WE_LS_MYSQL_META_UNITS', 'WS_LS_META_UNITS' );
    define( 'WE_LS_MYSQL_META_ENTRY', 'WS_LS_META_ENTRY' );


    /**
     * Create the relevant database tables required to support meta fields
     */
    function ws_ls_activate_meta_create_mysql_tables() {

        // Only run this when the plugin version has changed
        if( false === update_option('ws-ls-meta-version-number', WE_LS_DB_VERSION )) {
           return;
        }

        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $table_name = $wpdb->prefix . WE_LS_MYSQL_META_FIELDS;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                field_key varchar(40) NOT NULL,
                field_name varchar(40) NOT NULL,
                abv varchar(5) NOT NULL,
                suffix varchar(5) NOT NULL,
                display_on_chart BIT DEFAULT 0,
                mandatory int DEFAULT 1,
                enabled int DEFAULT 1,
                system BIT DEFAULT 0,
                field_type int NOT NULL,
                sort int DEFAULT 100,
                UNIQUE KEY id (id)
            ) $charset_collate;";

        dbDelta( $sql );

        $table_name = $wpdb->prefix . WE_LS_MYSQL_META_ENTRY;

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                entry_id int NOT NULL,
                meta_field_id int NOT NULL,
                value varchar(800) NOT NULL,
                UNIQUE KEY id (id)              
            ) $charset_collate;";

        dbDelta( $sql );

		// If no meta fields exist, then add some examples
		if ( true === empty( ws_ls_meta_fields( true, true ) ) ) {
            ws_ls_meta_fields_load_examples();
		}

    }
    add_action( 'admin_init', 'ws_ls_activate_meta_create_mysql_tables' );

    /**
     * Simple function to load some example
     */
    function ws_ls_meta_fields_load_examples() {

        // Number
        ws_ls_meta_fields_add([
            'field_name' => __('Cups of water drunk today?', WE_LS_SLUG),
            'abv' => __('Water', WE_LS_SLUG),
            'field_type' => 0,
            'suffix' => __('Cups', WE_LS_SLUG),
            'mandatory' => 2,
            'enabled' => 1,
            'sort' => 100
        ]);

        // Text
        ws_ls_meta_fields_add([
            'field_name' => __('How did you feel today?', WE_LS_SLUG),
            'abv' => __('Feel', WE_LS_SLUG),
            'field_type' => 1,
            'suffix' => '',
            'mandatory' => 2,
            'enabled' => 1,
            'sort' => 120
        ]);

        // Yes / No
        ws_ls_meta_fields_add([
            'field_name' => __('Did you stick to your diet?', WE_LS_SLUG),
            'abv' => __('Diet', WE_LS_SLUG),
            'field_type' => 2,
            'suffix' => '',
            'mandatory' => 1,
            'enabled' => 1,
            'sort' => 130
        ]);

        ws_ls_cache_user_delete( 'meta-fields' );

    }