<?php
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

function db_install_table_example()
{
    global $wpdb;

    $table_name = 'table_example';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        evenement_id mediumint(9) NOT NULL,
        participant_id varchar(255),
        inscription_id varchar(255),
        civilite varchar(100),
        titre varchar(100),
        prenom varchar(255) NOT NULL,
        nom varchar(255) NOT NULL,
        email varchar(255),
        profession varchar(255),
        specialite varchar(255),
        exercice varchar(255),
        rpps varchar(255),
        codeERP varchar(255),
        mobile varchar(255),
        mode varchar(255),
        cp mediumint(9),
        etablissement varchar(255),
        questions varchar(255),
        ville varchar(255),
        optin boolean DEFAULT 0 NOT NULL,
        reminders boolean DEFAULT 0 NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    dbDelta($sql);
}

add_action('after_switch_theme', 'db_install_table_example');
