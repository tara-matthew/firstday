<?php
/*
  Plugin Name: Custom Profile Page
 */

function displayProfile()
{
    $currentUser = wp_get_current_user();
    $userID = $currentUser->ID;

    $profile_heading = get_user_meta($userID, 'first_name', true) . ' ' . get_user_meta($userID, 'last_name', true) .  ' ---- ' . 'My Student Profile';

    ob_start(); ?>

    <style>
        .profile-title {
            text-align: center;
            color: white;

            font-size: 27px;
            font-weight: unset;
            margin-bottom: 50px;
            text-transform: uppercase;
        }

        .profile-info {
            color: white;
            font-size: 20px;
            font-weight: bold;
        }

        .profile-details {
            text-align: center;
            display: inline-block;
            width: 25%;
        }

        .my-profile {
            text-align: center;
            font-family: Abel;
        }

        .profile-key, .profile-val {
            display: inline-block;

        }

        .profile-key {
            width: 50%;
            font-weight: bold;
            color: white;

        }

        .profile-val {
            width: 49%;
            color: white;
        }

        .profile-key span {
            /*font-weight: normal;
            display: inline-block;*/
        }
    </style>

    <div class="my-profile">
        <h2 class="profile-title"><?= $profile_heading ?></h2>
        <div class="profile-details">
            <span class="profile-key">Scuola:</span><span class="profile-val"><?= get_user_meta($userID, 'school', true)?></span>
            <span class="profile-key">Corso studi:</span> <span class="profile-val"><?= get_user_meta($userID, 'course', true)?></span>
            <span class="profile-key">Anno conseguimento titolo: </span><span class="profile-val"><?= get_user_meta($userID, 'graduation_year', true)?></span>
            <span class="profile-key">Email:</span><span class="profile-val"><input style="width:200px;" type="text" class="edit-email" value="<?= $currentUser->user_email?>"></span>
        </div>
    </div>


    <?php return ob_get_clean();
}

function my_jquery_script() {
    wp_register_script('my_jquery_script', plugins_url('functions.js', __FILE__), array('jquery'),'1.1', true);
    wp_enqueue_script('my_jquery_script');
}

add_action( 'wp_enqueue_scripts', 'my_jquery_script' );

add_shortcode('custom_profile', 'displayProfile');