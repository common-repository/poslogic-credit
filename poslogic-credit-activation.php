<?php
class Poslogic_Credit_Activation{
    CONST TABLE_POSTFIX = "poslogic_credit_categories";
    CONST VERSION_OPTION_NAME = "poslogic_credit_db_version";
    CONST VERSION = "1.0.1";

    private $wpdb = null;
    private $table_name = "";

    private static $instance = null;

    public static function on_activation(){
        if (!current_user_can('activate_plugins'))
            return;
        $instance = self::getInstance();
        $instance->activate();
    }

    public static function on_deactivation(){
        if (!current_user_can('activate_plugins'))
            return;
        $plugin = isset($_REQUEST['plugin']) ? sanitize_text_field($_REQUEST['plugin']) : '';
        check_admin_referer("deactivate-plugin_".$plugin);
    }

    public static function on_uninstall(){
        if (!current_user_can('activate_plugins'))
            return;

        $instance = self::getInstance();
        $instance->uninstall();
    }

    public static function getInstance(){
        is_null(self::$instance) && self::$instance = new self;
        return self::$instance;
    }

    private function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . self::TABLE_POSTFIX;
    }

    private function activate(){
        if(!$this->check_version()){
            $this->update_db();
        }
    }

    private function uninstall(){
        $sql = "DROP TABLE IF EXISTS ". $this->table_name." ";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        if ( !is_multisite() ) {
            delete_option( self::VERSION_OPTION_NAME );
        } else {

            $blog_ids = $this->wpdb->get_col( "SELECT blog_id FROM $this->wpdb->blogs" );
            $original_blog_id = get_current_blog_id();

            foreach ( $blog_ids as $blog_id )   {
                switch_to_blog( $blog_id );
                delete_site_option( self::VERSION_OPTION_NAME );
            }
            switch_to_blog( $original_blog_id );
        }        delete_option( self::VERSION_OPTION_NAME);

    }

    private function check_version(){
        $installed_version = get_option(self::VERSION_OPTION_NAME );
        return self::VERSION === $installed_version;
    }

    private function update_db(){
        $installed_version = get_option(self::VERSION_OPTION_NAME );
        if(!$installed_version){
            $this->create_db();
        }
        update_option( self::VERSION_OPTION_NAME, self::VERSION );
    }

    private function create_db(){
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE ". $this->table_name." (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          wp_product_category_id bigint(20) NOT NULL,
          poslogic_product_category_id mediumint(9) NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

    }



};