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
 * TODO describe file coupon_form
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/adminlib.php');
require_login();
$id = optional_param('id', 0, PARAM_INT);
$returnurl = new moodle_url('coupon_manage.php');
$url = new moodle_url('/auth/coupon/coupon_form.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading(get_string('pluginname', 'auth_coupon'));
$mform = new \auth_coupon\coupon_form(null, [
    'id' => $id
]);
// Form processing and displaying is done here.
if ($mform->is_cancelled()) {
    // Redirect the page.
    redirect($returnurl);
} else if ($fromform = $mform->get_data()) {

    // Get data from form and save in a database table
    $coupon                 = new stdClass();
    $coupon->code    = $fromform->code;
    $coupon->usage_count    = $fromform->allowusage;
    $coupon->creation_date  = time();
    $coupon->start_date  = $fromform->start_date;
    $coupon->expiry_date  = $fromform->expiry_date;
    $coupon->creatorid      = $USER->id;
    $coupon->companyid      = $fromform->company;
    $coupon->notes    = $fromform->notes;

    if ($id == 0) {
        if ($DB->insert_record('auth_coupon', $coupon)) {
            notice('Record inserted successfully', $returnurl);
        }
    } else {
        $coupon->id = $id;
        if ($DB->update_record('auth_coupon', $coupon)) {
            notice('Record updated successfully', $returnurl);
        }
    }
} else {
    if ($id == 0) {
        $mform->set_data([]);
    } else {
        // Retrieve existing data from the database based on the coupon ID
        $existingData = $DB->get_record('auth_coupon', ['id' => $id]);


        $data               = new stdClass();
        $data->couponcode   = $existingData->code;
        $data->allowusage   = $existingData->usage_count;
        $data->company      = $existingData->companyid;
        $data->start_date      = $existingData->start_date;
        $data->expiry_date      =  $existingData->expiry_date;
        $data->notes      = $existingData->notes;
        // Set default values for the form elements
        $mform->set_data($data);
    }
}
echo $OUTPUT->header();
// Display the form.
$mform->display();
echo $OUTPUT->footer();
