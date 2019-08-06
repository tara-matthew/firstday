<?php
/*
  Plugin Name: Custom Registration
  Plugin URI: http://code.tutsplus.com
  Description: Updates user rating based on number of posts.
  Version: 1.0
  Author: Agbonghama Collins
  Author URI: http://tech4sky.com
 */

function registration_form($firstName, $lastName, $school, $course, $graduationYear, $email, $password, $confirmPassword) {
    echo '
    <style>
    div {
        margin-bottom:2px;
    }
     
    input{
        margin-bottom:4px;
    }
    </style>
    ';

    echo '
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
    <div>
    <label for="first-name">Nome<strong>*</strong></label>
    <input type="text" name="first-name" value="' . ( isset( $_POST['first-name'] ) ? $firstName : null ) . '">
    </div>
    
    <div>
    <label for="last-name">Cognome<strong>*</strong></label>
    <input type="text" name="last-name" value="' . ( isset( $_POST['last-name'] ) ? $lastName : null ) . '">
    </div>
    
    <div>
    <label for="school">Scuola<strong>*</strong></label>
    <input type="text" name="school" value="' . ( isset( $_POST['school'] ) ? $school : null ) . '">
    </div>
    
    <div>
    <label for="course">Corso studi<strong>*</strong></label>
    <input type="text" name="course" value="' . ( isset( $_POST['course'] ) ? $course : null ) . '">
    </div>
    
    <div>
    <label for="graduation-year">Anno conseguimento titolo<strong></strong></label>
    <input type="text" name="graduation-year" value="' . ( isset( $_POST['graduation-year'] ) ? $graduationYear : null ) . '">
    </div>
    
        
    <div>
    <label for="email">Email<strong>*</strong></label>
    <input type="text" name="email" value="' . ( isset( $_POST['email'] ) ? $email : null ) . '">
    </div>
    
    <div>
    <label for="password">Password<strong>*</strong></label>
    <input type="text" name="password" value="' . ( isset( $_POST['password'] ) ? $password : null ) . '">
    </div>
    
    <div>
    <label for="confirm-password">Conferma password<strong>*</strong></label>
    <input type="text" name="confirm-password" value="' . ( isset( $_POST['confirm-password'] ) ? $confirmPassword : null ) . '">
    </div>
    
        
    <input type="submit" name="submit" value="Registrati"/>
    </form>
    ';
}


function registration_validation($firstName, $lastName, $school, $course, $graduationYear, $email, $password, $confirmPassword) {

    global $reg_errors;
    $reg_errors = new WP_Error();

    $mandatoryFields = [
        $firstName,
        $lastName,
        $school,
        $course,
        $email,
        $password
    ];

    foreach ($mandatoryFields as $mandatory) {
        if (empty($mandatory)) {
            $reg_errors->add('field', 'Required form field is missing');
        }
    }

    if ( is_wp_error( $reg_errors ) ) {

        foreach ( $reg_errors->get_error_messages() as $error ) {

            echo '<div>';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';
            echo '</div>';

        }

    }
}


function custom_registration_function() {
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
            $_POST['first-name'],
            $_POST['last-name'],
            $_POST['school'],
            $_POST['course'],
            $_POST['graduation-year'],
            $_POST['email'],
            $_POST['password'],
            $_POST['confirm-password']
        );

        // sanitize user form input
        global $firstName, $lastName, $school, $course, $graduationYear, $email, $password, $confirmPassword;
        $firstName = sanitize_user( $_POST['first-name'] );
        $lastName = sanitize_user( $_POST['last-name'] );
        $school = sanitize_text_field( $_POST['school'] );
        $course = sanitize_text_field( $_POST['password'] );
        $graduationYear = sanitize_text_field( $_POST['graduation-year']);
        $email = sanitize_email( $_POST['email'] );
        $password = esc_attr( $_POST['password'] );
        $confirmPassword = esc_attr( $_POST['confirm-password'] );

        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
            $firstName,
            $lastName,
            $school,
            $course,
            $graduationYear,
            $email,
            $password,
            $confirmPassword
        );
    }

    registration_form(
        $firstName,
        $lastName,
        $school,
        $course,
        $graduationYear,
        $email,
        $password,
        $confirmPassword

    );
}

function complete_registration() {
    global $reg_errors, $firstName, $lastName, $school, $course, $graduationYear, $email, $password, $confirmPassword;

    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
            'user_login'    => $firstName . '' . $lastName,
            'user_email'    =>   $email,
            'user_pass'     =>   $password,
            'first_name'    =>   $firstName,
            'last_name'     =>   $lastName,
        );
        $userID = wp_insert_user( $userdata );

        $metas = array(
          'type' => 'student',
          'school' => $school,
          'course' => $course,
          'graduation_year' => $graduationYear
        );


        // Put an update condition in here too, in case the user exists?
        foreach ($metas as $key => $value) {
            update_user_meta($userID, $key, $value);
        }
        //$userMeta = add_user_meta($user->ID, 'type', 'student');
        //var_dump($user); die();
        echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';
    }
}

// Register a new shortcode: [cr_custom_registration]
add_shortcode( 'cr_custom_registration', 'custom_registration_shortcode' );

// The callback function that will replace [book]
function custom_registration_shortcode() {
    ob_start();
    custom_registration_function();
    return ob_get_clean();
}