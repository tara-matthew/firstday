<?php
/*
  Plugin Name: Custom Employer Registration
  Plugin URI: http://code.tutsplus.com
  Version: 1.0
  Author: Agbonghama Collins
  Author URI: http://tech4sky.com
 */

function registration_form_fields() {
    ob_start(); ?>

    <form action="<?=$_SERVER['REQUEST_URI']?>" id="employer-registration" method="post">
        <input type="hidden" name="action" value="register_form">
        <fieldset>
            <div>
                <label for="first-name">Nome<strong>*</strong></label>
                <input required
                       type="text"
                       name="first-name"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
            </div>

            <div>
                <label for="last-name">Cognome<strong>*</strong></label>
                <input required
                       type="text"
                       name="last-name"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
            </div>

            <div>
                <label for="role">Ruolo<strong>*</strong></label>
                <input required
                       type="text"
                       name="role"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
            </div>

            <div>
                <label for="company">Nome Azienda<strong>*</strong></label>
                <input required
                       type="text"
                       name="company"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
            </div>

            <div>
                <label for="headquarters">Sede<strong></strong></label>
                <input type="text" name="headquarters">
            </div>

            <div>
                <label for="phone-number">Numero di telefono<strong>*</strong></label>
                <input required
                       type="text"
                       name="phone-number"
                       oninvalid="this.setCustomValidity('Campo obbligatorio')"
                       onchange="this.setCustomValidity('')"/>
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
                <label for=">
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
                <?php do_action('add_new_user') ?>
                <input type="hidden" name="register-nonce" value="<?= wp_create_nonce('register-nonce'); ?>"/>
                <input name="submit" type="submit" value="Register Your Account"/>
            </div>

        </fieldset>
    </form>

    <div class="privacy-policy-popup" style="height:200px; border:1px solid black; display:none";>
        <p>L’informativa Privacy</p>
    </div>

    <?php return ob_get_clean();
}

function show_registration_form() {
    $output = registration_form_fields();

    return $output;
}

add_shortcode('employer-registration-form', 'show_registration_form');
add_action('init','add_new_user');
//var_dump($_POST); die();
//add_action('register_form', 'add_new_user');
//do_action('add_new_user');

function add_new_user() {

    if (isset($_POST['first-name'])) {
        if (wp_verify_nonce($_POST['register-nonce'], 'register-nonce')) {
                $user_login     = $_POST['first-name'] . '' . $_POST['last-name'];
                $user_email     = $_POST['email'];
                $user_first     = $_POST['first-name'];
                $user_last      = $_POST['last-name'];
                $user_pass      = $_POST['password'];
                $pass_confirm   = $_POST['confirm-password'];
                $role           = $_POST['role'];
                $company        = $_POST['company'];
                $headquarters   = $_POST['headquarters'];

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
                    'type'          => 'employer',
                    'role'          => $role,
                    'company'       => $company,
                    'headquarters'  => $headquarters
            );

            foreach ($metas as $key => $value) {
                update_user_meta($new_user_ID, $key, $value);
            }


        } else {
            echo 'failed'; die();
        }
    }

}