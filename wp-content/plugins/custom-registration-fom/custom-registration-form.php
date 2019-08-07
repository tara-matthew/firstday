<?php
/*
  Plugin Name: Custom Registration
  Plugin URI: http://code.tutsplus.com
  Description: Updates user rating based on number of posts.
  Version: 1.0
  Author: Agbonghama Collins
  Author URI: http://tech4sky.com
 */



function student_registration_form_fields() {
    ob_start(); ?>

    <form action="<?=$_SERVER['REQUEST_URI']?>" id="student-registration" method="post">
        <fieldset>
            <div>
                <label for="first-name">Nome<strong>*</strong></label>
                <input type="text" name="first-name">
             </div>

            <div>
                <label for="last-name">Cognome<strong>*</strong></label>
                <input type="text" name="last-name">
            </div>

            <div>
                <label for="school">Scuola<strong>*</strong></label>
                <input type="text" name="school">
            </div>

            <div>
                <label for="course">Corso studi<strong>*</strong></label>
                <input type="text" name="course">
            </div>

            <div>
                <label for="graduation-year">Anno conseguimento titolo<strong></strong></label>
                <input type="text" name="graduation-year">
            </div>

            <div>
                <label for="email">Email<strong>*</strong></label>
                <input type="text" name="email">
            </div>

            <div>
                <label for="password">Password<strong>*</strong></label>
                <input type="text" name="password">
            </div>

            <div>
                <label for="confirm-password">Conferma password<strong>*</strong></label>
                <input type="text" name="confirm-password">
            </div>

            <div>
                 <input type="submit" name="submit" value="Registrati"/>
            </div>
        </fieldset>
    </form>

    <?php return ob_get_clean();
}

function show_student_registration_form() {
    $output = student_registration_form_fields();

    return $output;
}

add_shortcode('student-registration-form', 'show_student_registration_form');
add_action('init','add_new_student_user');

function add_new_student_user() {

    if (isset($_POST['first-name'])) {
        if (wp_verify_nonce($_POST['register-nonce'], 'register-nonce')) {
            $user_login             = $_POST['first-name'] . '' . $_POST['last-name'];
            $user_email             = $_POST['email'];
            $user_first             = $_POST['first-name'];
            $user_last              = $_POST['last-name'];
            $user_pass              = $_POST['password'];
            $pass_confirm           = $_POST['confirm-password'];
            $school                 = $_POST['school'];
            $course                 = $_POST['course'];
            $graduation_year        = $_POST['graduation-year'];

            //error handling here

            $new_user_ID = wp_insert_user(array(
                'user_login'        => $user_login,
                'user_email'        => $user_email,
                'user_pass'         => $user_pass,
                'first_name'    	=> $user_first,
                'last_name'		    => $user_last,
                'user_registered'	=> date('Y-m-d H:i:s'),
                'role'				=> 'subscriber',
            ));

            $metas = array(
                'type'              => 'student',
                'school'            => $school,
                'course'            => $course,
                'graduation_year'   => $graduation_year
            );

            foreach ($metas as $key => $value) {
                update_user_meta($new_user_ID, $key, $value);
            }

            //echo $new_user_ID; die();


        } else {
            echo 'failed'; die();
        }
    }

}




/*function complete_registration() {
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
        //echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';
    }
}

// Register a new shortcode: [cr_custom_registration]
add_shortcode( 'cr_custom_registration', 'custom_registration_shortcode' );

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

// The callback function that will replace [book]
function custom_registration_shortcode() {
    ob_start();
    custom_registration_function();
    return ob_get_clean();
}*/