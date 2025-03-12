<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace auth_coupon;

/**
 * Class signup
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/login/signup_form.php');
class signup_form extends \moodleform
{
    public function definition()
    {
        global $CFG;
        $mform = $this->_form;

        $mform->addElement('text', 'firstname', get_string('firstname', 'auth_coupon'), [
            'maxlength' => '50',
            'size' => '25',
        ]);
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('firstname_rule', 'auth_coupon'), 'required', null, 'client');

        // Lastname.
        $mform->addElement('text', 'lastname', get_string('lastname', 'auth_coupon'), [
            'maxlength' => '50',
            'size' => '25',
        ]);
        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('lastname_rule', 'auth_coupon'), 'required', null, 'client');

        // Email/Username.
        $mform->addElement('text', 'email', get_string('email', 'auth_coupon'), [
            'maxlength' => '100',
            'size' => '25',
            'onkeyup' => "this.value=this.value.toLowerCase();"
        ]);
        $mform->setType('email', PARAM_EMAIL);
        $mform->addRule('email', get_string('email_rule', 'auth_coupon'), 'required', null, 'client');
        $mform->setForceLtr('email');
        // Password.
        $mform->addElement('password', 'password', get_string('password', 'auth_coupon'), [
            'maxlength' => 50,
            'size' => 25,
            'autocomplete' => 'new-password'
        ]);
        $mform->setType('password', PARAM_RAW);
        $mform->addRule('password', get_string('password_rule', 'auth_coupon'), 'required', 50, 'client');

        // Confirm Password.
        $mform->addElement('password', 'confirmpassword', get_string('confirm_password', 'auth_coupon'), [
            'maxlength' => 50,
            'size' => 25,
            'autocomplete' => 'new-password'
        ]);
        $mform->setType('confirmpassword', PARAM_RAW);
        $mform->addRule('confirmpassword', get_string('confirm_password_rule', 'auth_coupon'), 'required', 50, 'client');

        if (!empty($CFG->passwordpolicy)) {
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }

        $mform->addElement('text', 'coupon', get_string('coupon', 'auth_coupon'), [
            'maxlength' => '50',
            'size' => '25',
        ]);
        $mform->setType('coupon', PARAM_TEXT);
        $mform->addRule('coupon', get_string('coupon_rule', 'auth_coupon'), 'required', null, 'client');

        // Action buttons.
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('submit', 'auth_coupon'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', '', false);
    }


    // Custom validation function.
    function validation($data, $files)
    {
        $errors = parent::validation($data, $files);
        global $DB;

        // Check if password and confirm password match.
        if ($data['password'] !== $data['confirmpassword']) {
            $errors['confirmpassword'] = get_string('incorrect_confirm_password', 'auth_coupon');
        }

        // Check if the coupon code exists in the database.
        if (!empty($data['coupon'])) {
            $coupon_exists = $DB->record_exists('auth_coupon', array('code' => $data['coupon']));
            if (!$coupon_exists) {
                $errors['coupon'] = get_string('invalid_coupon', 'auth_coupon');
            }
        }



        return $errors;
    }
}
