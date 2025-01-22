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
 * TODO describe file expirecode
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/auth/coupon/expirecode.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();

$currenttime = time(); // Get the current timestamp
$totalcount = $DB->count_records_sql('SELECT COUNT(*) FROM {auth_coupon} WHERE expiry_date < ?', [$currenttime]);



$expiredCoupons = $DB->get_records_sql('SELECT * FROM {auth_coupon} WHERE expiry_date < ? ', [$currenttime]);

// Process the results
if (!empty($expiredCoupons)) {
    foreach ($expiredCoupons as $coupon) {

        $coupon->consumed = $DB->count_records_select('auth_coupon_usages', "couponid = $coupon->id");
        $company = $DB->get_record_sql("SELECT * FROM {company} WHERE id = $coupon->companyid");
        $coupon->companyname = $company->name;
        $expirydate = date('Y/m/d', $coupon->expiry_date);
        $coupon->expiry_date = $expirydate;
        $startdate = date('Y/m/d', $coupon->start_date);
        $coupon->start_date = $startdate;
        $creationdate = date('Y/m/d', $coupon->creation_date);
        $coupon->creation_date = $creationdate;
    }
} else {
    echo "No expired coupon codes found.";
}

echo $OUTPUT->render_from_template('auth_coupon/expirecode', [
    'expcoupon' => array_values($expiredCoupons)
]);

echo $OUTPUT->footer();
