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
 * Upgrade steps for auth_coupon
 *
 * Documentation: {@link https://moodledev.io/docs/guides/upgrade}
 *
 * @package    auth_coupon
 * @category   upgrade
 * @copyright  2025 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute the plugin upgrade steps from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_auth_coupon_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024021202) { // Replace 2024030404 with your new version number

        // Define the new table structure
        $table = new xmldb_table('auth_coupon');

        $field = new xmldb_field('start_date', XMLDB_TYPE_INTEGER, '10', null, null, null, '1');


        // Create the table if it doesn't exist
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Incremental upgrade step
        upgrade_plugin_savepoint(true, 2024021202, 'auth', 'coupon');
    }
    if ($oldversion < 2024021204) { // Replace 2024030404 with your new version number

        // Define the new table structure
        $table = new xmldb_table('auth_coupon');

        $field = new xmldb_field('delete_date', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');


        // Create the table if it doesn't exist
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Incremental upgrade step
        upgrade_plugin_savepoint(true, 2024021204, 'auth', 'coupon');
    }
    if ($oldversion < 2024021206) { // Replace 2024030404 with your new version number

        // Define the new table structure
        $table = new xmldb_table('auth_coupon');

        $field = new xmldb_field('delete_code', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');


        // Create the table if it doesn't exist
        if (!$dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
        }

        // Incremental upgrade step
        upgrade_plugin_savepoint(true, 2024021206, 'auth', 'coupon');
    }

    return true;
}
