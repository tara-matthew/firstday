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
    ob_start();
    display_student_error_messages()

    ?>
    <div style="text-align:right;">
        <a style="color:red;" href="<?= get_page_link(get_page_by_title('Student Login')->ID); ?>">Già registrato?</a>
    </div>

    <form action="<?=$_SERVER['REQUEST_URI']?>" id="student-registration" method="post">
        <fieldset>
            <div>
                <label for="first-name">Nome<strong>*</strong></label>
                <input required
                       type="text"
                       name="student-first-name"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
             </div>

            <div>
                <label for="last-name">Cognome<strong>*</strong></label>
                <input type="text"
                       name="last-name"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
            </div>

            <div>
                <label for="school">Scuola<strong>*</strong></label>
                <input required
                       type="text"
                       name="school"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
            </div>

            <div>
                <label for="course">Corso studi<strong>*</strong></label>
                <input required
                       type="text"
                       name="course"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
            </div>

            <div>
                <label for="graduation-year">Anno conseguimento titolo<strong></strong></label>
                <select name="graduation-year">
                    <?php
                    for($i=2016; $i<=2022; $i++) { ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php }
                    ?>


                </select>
            </div>

            <div>
                <label for="email">Email<strong>*</strong></label>
                <input required
                       type="text"
                       name="email"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
            </div>

            <div>
                <label for="password">Password<strong>*</strong></label>
                <input required
                       type="text"
                       name="password"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
            </div>

            <div style="margin-bottom:20px;">
                <label for="confirm-password">Conferma password<strong>*</strong></label>
                <input required
                       type="text"
                       name="confirm-password"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
            </div>

            <div style="margin-bottom:20px;">
                <input required type="checkbox"> <a class="privacy-policy" href="#">Accetto l’informativa privacy</a>
            </div>

            <div>
                <input type="hidden" name="register-nonce" value="<?= wp_create_nonce('register-nonce'); ?>"/>
                <input type="submit" name="submit" value="Registrati"/>
            </div>
        </fieldset>
    </form>

    <div class="privacy-policy-popup" style="height:200px; border:1px solid black; display:none";>
        <p>L’informativa Privacy</p>
    </div>

    <?php return ob_get_clean();
}

function show_student_registration_form() {
    $output = student_registration_form_fields();

    return $output;
}

add_shortcode('student-registration-form', 'show_student_registration_form');
add_action('init','add_new_student_user');
//add_action('wp_enqueue_scripts', plugins_url('/functions.js', __FILE__), array('jquery'));

function my_script() {
    wp_register_script('my_script', plugins_url('functions.js', __FILE__), array('jquery'),'1.1', true);
    wp_enqueue_script('my_script');
}

add_action( 'wp_enqueue_scripts', 'my_script' );

function add_new_student_user() {

    if (isset($_POST['student-first-name'])) {
        if (wp_verify_nonce($_POST['register-nonce'], 'register-nonce')) {
            $user_login             = $_POST['student-first-name'] . '' . $_POST['last-name'];
            $user_email             = $_POST['email'];
            $user_first             = $_POST['student-first-name'];
            $user_last              = $_POST['last-name'];
            $user_pass              = $_POST['password'];
            $pass_confirm           = $_POST['confirm-password'];
            $school                 = $_POST['school'];
            $course                 = $_POST['course'];
            $graduation_year        = $_POST['graduation-year'];

            //error handling here

            if(email_exists($user_email)) {
                //echo 'exists'; die();
                //Email address already registered
                log_student_errors()->add('email_used', __('Email already registered'));
            }

            if($user_pass != $pass_confirm) {
                // passwords do not match
                log_student_errors()->add('password_mismatch', __('Passwords do not match'));
            }

            $errors = log_student_errors()->get_error_messages();

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

function log_student_errors() {
    static $wp_error;

    return (isset($wp_error) ? $wp_error :  ($wp_error = new WP_Error(null, null, null)));
}

function display_student_error_messages() {
    if($codes = log_student_errors()->get_error_codes()) {
        echo '<div class="student-errors">';
        // Loop error codes and display errors
        foreach($codes as $code){
            $message = log_student_errors()->get_error_message($code);
            echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
        }
        echo '</div>';
    }
}

?>