
/**
 * Created by PhpStorm.
 * User: taram
 * Date: 01/08/2019
 * Time: 11:25
 */

<?php
/*
Plugin Name: Add Textttt To Footer
*/

// Hook the 'wp_footer' action hook, add the function named 'mfp_Add_Text' to it
add_action("wp_footer", "mfp_Add_Text");

// Define 'mfp_Add_Text'
function mfp_Add_Text()
{
    echo "<p style='color: black;'>After the footer is loaded, my text is added!</p>";
}