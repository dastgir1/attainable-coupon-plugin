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
 * Main class for the auth_coupon authentication plugin
 *
 * Documentation: {@link https://docs.moodle.org/dev/Authentication_plugins}
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/authlib.php');

/**
 * Authentication plugin auth_coupon
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_coupon extends auth_plugin_base
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->authtype = 'coupon';
        $this->config = get_config('auth_coupon');
    }


    public function user_signup_url()
    {
        return new moodle_url('/auth/coupon/signup_form.php');
    }
    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_coupon()
    {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }
    /**
     * Return a form to capture user details for account creation.
     * This is used in /login/signup.php.
     * @return moodle_form A form which edits a record from the user table.
     */
    function signup_form()
    {
        return new \auth_coupon\signup_form(null, null, 'post', '', array('autocomplete' => 'on'));
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     *
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password)
    {
        global $CFG, $DB;
        if ($user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id))) {
            return validate_internal_user_password($user, $password);
        }
        return false;
    }

    /**
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param  object  $user        User table object  (with system magic quotes)
     * @param  string  $newpassword Plaintext password (with system magic quotes)
     * @return boolean result
     *
     */
    function user_update_password($user, $newpassword)
    {
        $user = get_complete_user_data('id', $user->id);
        // This will also update the stored hash to the latest algorithm
        // if the existing hash is using an out-of-date algorithm (or the
        // legacy md5 algorithm).
        return update_internal_user_password($user, $newpassword);
    }
    function can_signup()
    {
        return true;
    }

    function user_signup($user, $notify = true)
    {
        // Standard signup, without custom confirmatinurl.
        return $this->user_signup_with_confirmation($user, $notify);
    }
    /**
     * Sign up a new user ready for confirmation.
     *
     * Password is passed in plaintext.
     * A custom confirmationurl could be used.
     *
     * @param object $user new user object
     * @param string $couponcode The couponcode
     * @param boolean $notify print notice with link and terminate
     * @param string $confirmationurl user confirmation URL
     * @return boolean true if everything well ok and $notify is set to true
     * @throws moodle_exception
     * @since Moodle 3.2
     */
    // Check if we should redirect the user once the user is confirmed.
    public function user_signup_with_confirmation($user, $notify = true, $confirmationurl = null)
    {
        global $CFG, $DB, $SESSION;

        // Get additional user data (e.g., coupon code) from your custom form
        // $couponCode = $user->couponcode;
        // Validate the coupon code

        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');

        $plainpassword = $user->password;
        $user->password = hash_internal_user_password($user->password);
        if (empty($user->calendartype)) {
            $user->calendartype = $CFG->calendartype;
        }

        $user->id = user_create_user($user, false, false);
        user_add_password_history($user->id, $plainpassword);
        profile_save_data($user);
        $result = $DB->get_record('auth_coupon', ['code'  => $user->coupon]);
        $DB->insert_record('auth_coupon_usages', array(
            'userid' => $user->id,
            'couponid' => $result->id
        ));
        // Ensure the user object is retrieved from the database.
        if ($user->id) {
            $toUser = \core_user::get_user($user->id);

            $fromUser = \core_user::get_support_user();
            $site = get_site();
            $subject = "Welcome to {$site->fullname}";
            $messagehtml = "Dear {$user->firstname},<br><br>";
            $messagehtml .= "Welcome to {$site->fullname}! We are excited to have you with us. Your username is <strong>{$user->username}</strong>.and password={$user->password} Click
             <a href='{$CFG->wwwroot}/login/index.php'>here</a> to login.<br><br>";
            $messagehtml .= "Best regards,<br>";
            $messagehtml .= "{$site->fullname} team";

            $messagetext = html_to_text($messagehtml);

            // $mailSent = email_to_user($user, $supportuser, $subject, $messagetext, $messagehtml);
            $mailSent = email_to_user($toUser, $fromUser, $subject, $messagetext, $messagehtml);

            // Check if the email was sent successfully
            $returnurl = new moodle_url('/login/index.php');
            if ($mailSent) {
                // echo get_string('emailsend', 'auth_coupon');
                notice(get_string('emailsend', 'auth_coupon'), $returnurl);
            } else {
                notice(get_string('emailnotsend', 'auth_coupon'), $returnurl);
                // echo get_string('emailnotsend', 'auth_coupon');
            }
        } else {
            notice(get_string('accountnotcreated', 'auth_coupon'), $returnurl);
        }
        // IOMAD.
        // Get coupon record.
        $coupon = $DB->get_record_sql("SELECT * FROM {auth_coupon} WHERE code LIKE ?", array($user->coupon));

        if (!empty($coupon)) {
            $user->companyid = $coupon->companyid;
        }
        if (!empty($user->companyid)) {
            require_once($CFG->dirroot . '/local/iomad/lib/company.php');
            $company = new company($user->companyid);

            // assign the user to the company.
            $company->assign_user_to_company($user->id);

            // Assign them to any department.
            if (!empty($user->departmentid)) {
                $company->assign_user_to_department($user->departmentid, $user->id);
            }

            if ($CFG->local_iomad_signup_autoenrol) {
                $company->autoenrol($user);
            }
        }

        // Save wantsurl against user's profile, so we can return them there upon confirmation.
        if (!empty($SESSION->wantsurl)) {
            set_user_preference('auth_coupon_wantsurl', $SESSION->wantsurl, $user);
        }

        // Trigger event.
        \core\event\user_created::create_from_userid($user->id)->trigger();

        if (! send_confirmation_email($user, $confirmationurl)) {
            throw new \moodle_exception('auth_emailnoemail', 'auth_coupon');
        }

        if ($notify) {
            global $CFG, $PAGE, $OUTPUT;
            $emailconfirm = get_string('emailconfirm');
            $PAGE->navbar->add($emailconfirm);
            $PAGE->set_title($emailconfirm);
            $PAGE->set_heading($PAGE->course->fullname);
            echo $OUTPUT->header();
            notice(get_string('emailconfirmsent', '', $user->email), "$CFG->wwwroot/index.php");
        } else {
            return true;
        }
    }
    /**
     * Returns true if plugin allows confirming of new users.
     *
     * @return bool
     */
    function can_confirm()
    {
        return true;
    }
    /**
     * Confirm the new user as registered.
     *
     * @param string $username
     * @param string $confirmsecret
     */
    function user_confirm($username, $confirmsecret)
    {
        global $DB, $SESSION;
        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->auth != $this->authtype) {
                return AUTH_CONFIRM_ERROR;
            } else if ($user->secret === $confirmsecret && $user->confirmed) {
                return AUTH_CONFIRM_ALREADY;
            } else if ($user->secret === $confirmsecret) {   // They have provided the secret key to get in
                $DB->set_field("user", "confirmed", 1, array("id" => $user->id));

                if ($wantsurl = get_user_preferences('auth_coupon_wantsurl', false, $user)) {
                    // Ensure user gets returned to page they were trying to access before signing up.
                    $SESSION->wantsurl = $wantsurl;
                    unset_user_preference('auth_coupon_wantsurl', $user);
                }

                return AUTH_CONFIRM_OK;
            }
        } else {
            return AUTH_CONFIRM_ERROR;
        }
    }
    function prevent_local_passwords()
    {
        return false;
    }
    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal()
    {
        return true;
    }
    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password()
    {
        return true;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password()
    {
        return true;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return bool
     */
    function can_be_manually_set()
    {
        return true;
    }

    /**
     * Returns whether or not the captcha element is enabled.
     * @return bool
     */
    function is_captcha_enabled()
    {
        return get_config("auth_{$this->authtype}", 'recaptcha');
    }
}
