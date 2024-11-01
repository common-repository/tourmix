<?php

/**
 * Handles the tourmix_parcel_labels table at the database
 *
 * @since      1.1.0
 * @package    Tourmix
 * @subpackage Tourmix/includes
 * @author     Tourmix <info@tourmix.delivery>
 */

class TourmixParcelLabelsTableHandler {
    private $table_name = 'tourmix_parcel_labels';
    private $lastLinkDownloaded = false;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . $this->table_name;
        $this->lastLinkDownloaded = false;
    }

    /**
     * The scheme of the tourmix_orders table
     */
    public function createTable () {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id              INT NOT NULL AUTO_INCREMENT,
            permalink       TEXT NOT NULL,
            orders          TEXT NOT NULL DEFAULT '',
            downloaded      BOOLEAN NOT NULL DEFAULT FALSE,
            PRIMARY KEY     (id)
            ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Save the link in the table
     */
    public function saveLink ( $parcelLabelPDFUrl, $accessKeys ) {
        global $wpdb; 
        
        $wpdb->insert ( 
            $this->table_name, 
            array(
                'permalink' => $parcelLabelPDFUrl,
                'orders' => implode( ', ', $accessKeys )
            )
        );
    }

    /**
     * Delete the table from the wordPress database
     */
    public function deleteTable () {
        global $wpdb;
        $wpdb->query( "DROP TABLE IF EXISTS " . $this->table_name );
    }

    /**
     * loads the links form the table
     */
    public function loadLinksFromTable ($limit = null) {
        global $wpdb;

        $sqlLimit = ($limit == null ? "" : "LIMIT $limit");

        $query = $wpdb->prepare(
            "SELECT id, permalink, downloaded, orders 
            FROM $this->table_name
            ORDER BY id DESC
            $sqlLimit"
        );

        $results = $wpdb->get_results( $query, OBJECT );
        return $results;
    }

    public function getLinksArray () {
        return $this->loadLinksFromTable();
    }

    /**
     * Returns the last link.
     */
    public function getLastLink () {
        $array = $this->loadLinksFromTable(1);

        if($array == null || count($array) == 0)
            return null;

        $this->lastLinkDownloaded = $array[0]->downloaded;

        return $array[0]->permalink;
    }

    /**
     * Returns that the lat link is downloaded or not
     */
    public function isLastLinkDownloaded () {
        return $this->lastLinkDownloaded;
    }

    /**
     * Sets the last link state to downloaded
     */
    public function setLastLinkDownloaded () {
        global $wpdb;

        $sql = $wpdb->prepare(
            "UPDATE $this->table_name 
            SET downloaded = '1' 
            WHERE id = " . $this->loadLinksFromTable(1)[0]->id
        );

        $wpdb->query($sql);
    }

    /**
     * Returns all the links and from the link get the name.
     * We can use this function 
     */
    public function getLinksAndNamesArray () {
        $links = $this->loadLinksFromTable();

        $linksNamesArray = [];

        foreach($links as $link) {
            array_push($linksNamesArray,
                (object) [
                    'url'   => $link->permalink,
                    'name'  => $this->getNameFormPermalink($link->permalink) . " - " . $link->orders
                ]
            );
        }

        return json_encode($linksNamesArray);
    }

    /**
     * This function formats the given permalink to a human readable format.
     */
    private function getNameFormPermalink ($permalink) {

        // Extract the date-time string using regex
        preg_match( '/(\d{4}_\d{2}_\d{2}_\d{2}_\d{2}_\d{2})label\.pdf$/', $permalink, $matches );

        // Check if a match is found
        if ( isset( $matches[1] ) ) {
            $dateTimeString = $matches[1];
            $dateTime = DateTime::createFromFormat( 'Y_m_d_H_i_s', $dateTimeString );

            if ( $dateTime ) {
                return $dateTime->format('Y-m-d H:i:s');
            }
        }

        return $permalink;
    }
}