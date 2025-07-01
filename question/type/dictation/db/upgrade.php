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
 * Dictation question type upgrade code.
 *
 * @package    qtype_dictation
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the dictation question type.
 *
 * @param int $oldversion the version we are upgrading from.
 * @return bool
 */
function xmldb_qtype_dictation_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Add any future upgrade steps here
    if ($oldversion < 2024120900) {
        // Initial installation, no upgrade needed
        upgrade_plugin_savepoint(true, 2024120900, 'qtype', 'dictation');
    }

    if ($oldversion < 2024121300) {
        // Add audiofile field to existing installations
        $table = new xmldb_table('qtype_dictation_options');
        $field = new xmldb_field('audiofile', XMLDB_TYPE_TEXT, null, null, null, null, null, 'enableaudio');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024121300, 'qtype', 'dictation');
    }

    if ($oldversion < 2024121700) {
        // Add displaymode field for enhanced C-test functionality
        $table = new xmldb_table('qtype_dictation_options');
        $field = new xmldb_field('displaymode', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'standard', 'audiofile');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024121700, 'qtype', 'dictation');
    }

    if ($oldversion < 2025070100) {
        // Add scoringmethod field for research comparison between traditional and Levenshtein scoring
        $table = new xmldb_table('qtype_dictation_options');
        $field = new xmldb_field('scoringmethod', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'levenshtein', 'displaymode');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025070100, 'qtype', 'dictation');
    }

    return true;
}
