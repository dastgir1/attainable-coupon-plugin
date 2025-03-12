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

/**
 * TODO describe file signup
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

require_once(__DIR__ . '/../../user/lib.php');
require_login();

$url = new moodle_url('/auth/coupon/signup_form.php', []);
$PAGE->set_url($url);
$PAGE->set_context(core\context\system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);
$isGuest = isguestuser();

// If the user is already logged in.
if ($USER->id > 0 & !$isGuest) {
    echo notice(get_string('already_registered', 'auth_coupon'), new moodle_url('/my/courses.php'));
}

$mform = new \auth_coupon\signup_form();
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/?redirect=0'));
} else if ($fromform = $mform->get_data()) {
    // Get the coupon code from the form and get the company name from the table
    $coupon_code = $fromform->coupon;
    $record = $DB->get_record('auth_coupon', array('code' => $coupon_code), 'companyname');

    // Create a new user object.
    $user = new stdClass();
    $user->auth = 'coupon';
    $user->confirmed  = 1;
    $user->mnethostid = 1;
    $user->maildisplay = 1;
    $user->firstname = $fromform->firstname;
    $user->lastname = $fromform->lastname;
    $user->email = $fromform->email;
    $user->username = $fromform->email;
    $user->department = $record->companyname;
    $user->password = $fromform->password;
    $user->lastlogin = time();
    $user->currentlogin = time();
    $user->country    = 'ES'; //Or another country
    $user->lang       = 'en'; //Or another country
    $user->timecreated = time();
    $user->mailformat = 1;
    // Create the user.
    $newuserid = user_create_user($user);

    // Ensure the user object is retrieved from the database.
    if ($newuserid) {
        $toUser = \core_user::get_user($newuserid);
        print_object($toUser);
        exit;
        $fromUser = \core_user::get_support_user();
        $site = get_site();
        $subject = "Welcome to {$site->fullname}";
        $messagehtml = "Dear {$user->firstname},<br><br>";
        $messagehtml .= "Welcome to {$site->fullname}! We are excited to have you with us. Your username is <strong>{$user->username}</strong>.and password={$password} Click <a href='{$CFG->wwwroot}/login/index.php'>here</a> to login.<br><br>";
        $messagehtml .= "Best regards,<br>";
        $messagehtml .= "{$site->fullname} team";

        $messagetext = html_to_text($messagehtml);

        // $mailSent = email_to_user($user, $supportuser, $subject, $messagetext, $messagehtml);
        $mailSent = email_to_user($toUser, $fromUser, $subject, $messagetext, $messagehtml, '', '', false);

        // Check if the email was sent successfully
        $returnurl = new moodle_url('/login/index.php');
        if ($mailSent) {
            echo get_string('emailsend', 'auth_coupon');
        } else {
            echo get_string('emailnotsend', 'auth_coupon');
        }


        // Confirmation of Account Creation
        notice(get_string('account_created', 'auth_coupon'), $returnurl);
    } else {
        notice(get_string('accountnotcreated', 'auth_coupon'), $returnurl);
    }
}
echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
