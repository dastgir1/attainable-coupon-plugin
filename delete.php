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
 * TODO describe file delete
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/auth/coupon/delete.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);
// PARAMS.
$del_id = required_param('del_id', PARAM_INT);

if (!empty($del_id)) {
    $data = new stdClass();
    $data->id =  $del_id;
    $data->delete_code = 1; // Set delete_code to 1
    $data->delete_date = time(); // Set delete_date to current time
    $delrecord = $DB->update_record('auth_coupon', $data);

    // Update the record in the auth_coupon table
    if ($delrecord) {
        echo 1;
    } else {
        echo 0;
    }
}
echo $OUTPUT->header();
echo $OUTPUT->footer();
