<?php
/*
  Plugin Name: Custom Profile Page
 */

function displayProfile()
{
    echo 'this is your custom profile';
}

add_shortcode('custom_profile', 'displayProfile');