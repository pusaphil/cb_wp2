<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

$vcap_var = json_decode($_ENV['VCAP_SERVICES'],true);
# mysqli = new mysqli("hostname","username","password","database"); 
$mysqli = new mysqli($vcap_var["db-mysql"][0]["credentials"]["hostname"],
        $vcap_var["db-mysql"][0]["credentials"]["username"],
        $vcap_var["db-mysql"][0]["credentials"]["password"],
        $vcap_var["db-mysql"][0]["credentials"]["name"]);
if($mysqli->connect_errno){
        echo "\nFailed to connect to MySQL: (".$mysqli->connect_errno . ") " . $mysqli->connect_error;
}
else

$res = $mysqli->query("SHOW TABLES LIKE \"%log%\"");
if(isset($res->num_rows)) {
        if($res->num_rows==0){
                $templine = '';
                $lines = file('/home/vcap/app/htdocs/data.sql');
                foreach ($lines as $line)
                {
                        if (substr($line, 0, 2) == '--' || $line == '')
                                continue;

                        $templine .= $line;

                        if (substr(trim($line), -1, 1) == ';')
                        {   
                            $out = $mysqli->query($templine);
                            $templine = '';
                        }
                }
        }
}


/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
