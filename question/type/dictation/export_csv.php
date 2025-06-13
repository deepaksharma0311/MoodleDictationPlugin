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
 * CSV export script for dictation questions.
 *
 * @package    qtype_dictation
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../../config.php');
require_once($CFG->dirroot . '/question/type/dictation/classes/output/export_csv.php');

// Get parameters
$questionid = required_param('questionid', PARAM_INT);
$contextid = required_param('contextid', PARAM_INT);

// Verify context and permissions
$context = context::instance_by_id($contextid);
require_login();
require_capability('moodle/question:editall', $context);

// Verify question exists and is a dictation question
$question = $DB->get_record('question', array('id' => $questionid, 'qtype' => 'dictation'), '*', MUST_EXIST);

// Export the data
\qtype_dictation\output\export_csv::export_responses($questionid, $contextid);
