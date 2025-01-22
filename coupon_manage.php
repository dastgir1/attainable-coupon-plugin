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
 * TODO describe file coupon_manage
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
$PAGE->requires->js('/auth/coupon/js/jquery-3.7.1.min.js');

require_login();
$url = new moodle_url('/auth/coupon/coupon_manage.php', []);
$PAGE->requires->js('/auth/coupon/js/script.js');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($SITE->fullname);
$PAGE->requires->css('/auth/coupon/styles.css');
// PARAMS.
$page       = optional_param('page', 0, PARAM_INT);
$perpage    = optional_param('perpage', 5, PARAM_INT);
echo $OUTPUT->header();
$totalcount = $DB->count_records_sql('SELECT COUNT(*) FROM {auth_coupon} WHERE delete_date=0 AND start_date < expiry_date');

$start = $page * $perpage;
if ($start > $totalcount) {
    $page = 0;
    $start = 0;
}
$coupons = $DB->get_records_sql(
    "SELECT ac.*, co.name
    FROM {auth_coupon} ac
    JOIN {company} co ON ac.companyid = co.id
    WHERE ac.delete_date=0 AND start_date < expiry_date
    LIMIT $start, $perpage
"
);


$strings =  [
    'couponcode'    => get_string('couponcode', 'auth_coupon'),
    'notes'         => get_string('notes', 'auth_coupon'),
    'company'       => get_string('company', 'auth_coupon'),
    'available'     => get_string('available', 'auth_coupon'),
    'consumed'      => get_string('consumed', 'auth_coupon'),
    'expiry'        => get_string('expirydate', 'auth_coupon'),
    'startdate'     => get_string('startdate', 'auth_coupon'),
    'created'       => get_string('created', 'auth_coupon'),
    'creator'       => get_string('creator', 'auth_coupon'),
    'addcoupon'     => get_string('addcoupon', 'auth_coupon'),
    'actions'       => get_string('actions', 'auth_coupon'),
    'selected'      => get_string('selected', 'auth_coupon'),
    'expirecoupon'  => get_string('expirecoupon', 'auth_coupon'),
    'deletecoupon'  => get_string('deletecoupon', 'auth_coupon'),
    'couponmanage'  => get_string('couponmanage', 'auth_coupon'),
];

$crecords = [];
foreach ($coupons as $coupon) {
    $coupon->consumed = $DB->count_records_select('auth_coupon_usages', "couponid = $coupon->id");

    $user = $DB->get_record('user', array('id' => $coupon->creatorid));
    $coupon->creator = $user->firstname . ' ' . $user->lastname;
    $expirydate = date('Y/m/d', $coupon->expiry_date);
    $coupon->expiry_date = $expirydate;
    $startdate = date('Y/m/d', $coupon->start_date);
    $coupon->start_date = $startdate;
    $creationdate = date('Y/m/d', $coupon->creation_date);
    $coupon->creation_date = $creationdate;
    $crecords[] = $coupon;
}
$companiesrecords = $DB->get_records('company', [], '', 'id, name');
$companies = [];
foreach ($companiesrecords as $company) {
    $companies[] = [
        'id'    => $company->id,
        'name'  => $company->name,
    ];
}

echo $OUTPUT->render_from_template('auth_coupon/coupon_manage', [
    'crecords'   => array_values($crecords),
    'companies' => array_values($companies),
    'strings'   => $strings,
]);
echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $url);
echo $OUTPUT->footer();
