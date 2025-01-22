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
 * Class coupon_form
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . '/formslib.php');
class coupon_form extends \moodleform
{
    // Add elements to form.
    public function definition()
    {
        global $DB;
        $coupon = $this->generateCouponCode();
        $mform = $this->_form;

        $mform->addElement('header', 'couponform', get_string('couponform', 'auth_coupon'));

        $id = $this->_customdata['id'];

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);

        // Notes
        $mform->addElement('text', 'notes', get_string('notes', 'auth_coupon'));
        $mform->setType('notes', PARAM_RAW);
        $mform->addHelpButton('notes', 'notes', 'auth_coupon');
        // coupon code.
        $mform->addElement('static', 'couponcode', get_string('couponcode', 'auth_coupon', 'coupon code'), $coupon);
        $mform->addElement('hidden', 'code', $coupon);
        $mform->setType('code', PARAM_TEXT);

        // Company Name
        $companies = $DB->get_records_sql("SELECT id, name FROM {company}");

        $options = array();
        foreach ($companies as $company) {
            $options[$company->id] = $company->name;
        }

        $mform->addElement('select', 'company', get_string('company', 'auth_coupon'), $options);

        // Add coupon code allow usage.
        $mform->addElement('text', 'allowusage', get_string('allowusage', 'auth_coupon'));
        $mform->setType('allowusage', PARAM_NOTAGS);
        $mform->addHelpButton('allowusage', 'allowusage', 'auth_coupon');

        // Start Date.
        $mform->addElement('date_selector', 'start_date', get_string('startdate', 'auth_coupon'));
        // Expiry Date.
        $mform->addElement('date_selector', 'expiry_date', get_string('expirydate', 'auth_coupon'));
        $mform->setDefault('expiry_date', strtotime('+1 year')); // Tap - 44.

        $this->add_action_buttons();
    }

    // function for couponcode generation.
    private function generateCouponCode($length = 8)
    {
        global $DB;
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        do {
            $code = substr(str_shuffle($chars), 0, $length);
        } while ($DB->record_exists('auth_coupon', ['code' => $code]));

        return $code;
    }
}
