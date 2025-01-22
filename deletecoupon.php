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
 * TODO describe file deletecoupon
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$url = new moodle_url('/auth/coupon/deletecoupon.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
$page       = optional_param('page', 0, PARAM_INT);
$perpage    = optional_param('perpage', 5, PARAM_INT);
$totalcount = $DB->count_records_sql('SELECT COUNT(*) FROM {auth_coupon} WHERE delete_code=1');
$start = $page * $perpage;
if ($start > $totalcount) {
    $page = 0;
    $start = 0;
}
$deleteCoupons =
    $DB->get_records_sql(
        "SELECT *
    FROM {auth_coupon} 
  
    WHERE delete_code=1 
    LIMIT $start, $perpage
"
    );
foreach ($deleteCoupons as $deleteCoupon) {

    // Process the results
    if (!empty($deleteCoupon)) {
        $deleteCoupon->consumed = $DB->count_records_select('auth_coupon_usages', "couponid = $deleteCoupon->id");

        $dcompany = $DB->get_record_sql("SELECT * FROM {company} WHERE id = $deleteCoupon->companyid");
        $deleteCoupon->companyname = $dcompany->name;
        $expirydate = date('Y/m/d', $deleteCoupon->expiry_date);
        $deleteCoupon->expiry_date = $expirydate;
        $startdate = date('Y/m/d', $deleteCoupon->start_date);
        $deleteCoupon->start_date = $startdate;
        $creationdate = date('Y/m/d', $deleteCoupon->creation_date);
        $deleteCoupon->creation_date = $creationdate;
        $deletedate = date('Y/m/d', $deleteCoupon->delete_date);
        $deleteCoupon->delete_date = $deletedate;

        $delrecord[] = $deleteCoupon;
    } else {
        echo "No delete coupon codes found.";
    }
}

$delrecord = isset($delrecord) && is_array($delrecord) ? $delrecord : [];

echo $OUTPUT->render_from_template('auth_coupon/deletecoupon', ['deletecoupon' => array_values($delrecord)]);
echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $url);
echo $OUTPUT->footer();
