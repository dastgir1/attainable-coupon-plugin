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
 * TODO describe file settings
 *
 * @package    auth_coupon
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if ($ADMIN->fulltree) {
    // Introductory explanation.
    $settings->add(new admin_setting_heading(
        'auth_coupon/pluginname',
        '',
        new lang_string('auth_coupondescription', 'auth_coupon')
    ));


    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('coupon');
    display_auth_lock_options(
        $settings,
        $authplugin->authtype,
        $authplugin->userfields,
        get_string('auth_fieldlocks_help', 'auth'),
        false,
        false
    );
}

$ADMIN->add(
    'root',
    new admin_externalpage(
        'couponmanage',
        new lang_string('couponmanage', 'auth_coupon'),
        new moodle_url('/auth/coupon/coupon_manage.php')
    )
);
