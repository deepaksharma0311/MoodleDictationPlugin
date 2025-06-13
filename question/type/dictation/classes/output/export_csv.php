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
 * CSV export class for dictation questions.
 *
 * @package    qtype_dictation
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_dictation\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/csvlib.class.php');

/**
 * Handles CSV export of dictation question responses.
 */
class export_csv {

    /**
     * Export dictation responses to CSV format.
     *
     * @param int $questionid The question ID to export data for
     * @param int $contextid The context ID
     * @return void
     */
    public static function export_responses($questionid, $contextid) {
        global $DB, $CFG;

        // Get question details
        $question = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
        $options = $DB->get_record('qtype_dictation_options', array('questionid' => $questionid), '*', MUST_EXIST);
        $gaps = json_decode($options->gaps, true);

        // Get all attempts for this question
        $sql = "SELECT qa.id as attemptid, qa.userid, qa.timemodified, qa.responsesummary,
                       u.firstname, u.lastname, u.email,
                       qas.state, qas.fraction
                FROM {question_attempts} qa
                JOIN {users} u ON u.id = qa.userid
                LEFT JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
                WHERE qa.questionid = ? AND qas.state LIKE '%graded%'
                ORDER BY u.lastname, u.firstname, qa.timemodified";

        $attempts = $DB->get_records_sql($sql, array($questionid));

        if (empty($attempts)) {
            print_error('noattempts', 'qtype_dictation');
        }

        // Prepare CSV data
        $csvdata = array();
        
        // Create header row
        $header = array(
            get_string('exportheader_student', 'qtype_dictation'),
            get_string('exportheader_attempt', 'qtype_dictation'),
            get_string('exportheader_question', 'qtype_dictation')
        );

        // Add gap headers
        for ($i = 0; $i < count($gaps); $i++) {
            $header[] = get_string('exportheader_gap', 'qtype_dictation', $i + 1);
            $header[] = get_string('exportheader_gap_correct', 'qtype_dictation', $i + 1);
            $header[] = get_string('exportheader_gap_score', 'qtype_dictation', $i + 1);
        }

        $header[] = get_string('exportheader_totalscore', 'qtype_dictation');
        $header[] = get_string('exportheader_playcount', 'qtype_dictation');
        $header[] = get_string('exportheader_timecreated', 'qtype_dictation');

        $csvdata[] = $header;

        // Process each attempt
        foreach ($attempts as $attempt) {
            $row = array();
            
            // Student info
            $row[] = $attempt->firstname . ' ' . $attempt->lastname;
            $row[] = $attempt->attemptid;
            $row[] = format_string($question->name);

            // Get step data for responses
            $stepdata = self::get_step_data($attempt->attemptid);
            
            // Process each gap
            for ($i = 0; $i < count($gaps); $i++) {
                $gapkey = 'gap_' . $i;
                $correctword = $gaps[$i];
                $studentword = isset($stepdata[$gapkey]) ? $stepdata[$gapkey] : '';
                
                // Calculate individual gap score
                $gapscore = self::calculate_word_score($correctword, $studentword);
                
                $row[] = $studentword;
                $row[] = $correctword;
                $row[] = round($gapscore, 4);
            }

            // Total score
            $row[] = round($attempt->fraction, 4);
            
            // Play count
            $playcount = isset($stepdata['playcount']) ? $stepdata['playcount'] : 0;
            $row[] = $playcount;
            
            // Time
            $row[] = userdate($attempt->timemodified);

            $csvdata[] = $row;
        }

        // Generate filename
        $filename = get_string('exportfilename', 'qtype_dictation', date('Y-m-d_H-i-s'));
        
        // Output CSV
        $csvexport = new \csv_export_writer();
        $csvexport->set_filename($filename);
        
        foreach ($csvdata as $row) {
            $csvexport->add_data($row);
        }
        
        $csvexport->download_file();
    }

    /**
     * Get step data for a question attempt.
     *
     * @param int $attemptid The attempt ID
     * @return array Array of step data
     */
    private static function get_step_data($attemptid) {
        global $DB;

        $sql = "SELECT qad.name, qad.value
                FROM {question_attempt_step_data} qad
                JOIN {question_attempt_steps} qas ON qas.id = qad.attemptstepid
                WHERE qas.questionattemptid = ?
                ORDER BY qas.sequencenumber DESC";

        $stepdata = array();
        $records = $DB->get_records_sql($sql, array($attemptid));
        
        foreach ($records as $record) {
            if (!isset($stepdata[$record->name])) {
                $stepdata[$record->name] = $record->value;
            }
        }

        return $stepdata;
    }

    /**
     * Calculate word score using normalized Levenshtein distance.
     *
     * @param string $correct The correct word
     * @param string $student The student's input
     * @return float Score between 0 and 1
     */
    private static function calculate_word_score($correct, $student) {
        if (empty($correct) && empty($student)) {
            return 1.0;
        }
        
        if (empty($correct) || empty($student)) {
            return 0.0;
        }
        
        // Normalize case for comparison
        $correct = strtolower(trim($correct));
        $student = strtolower(trim($student));
        
        if ($correct === $student) {
            return 1.0;
        }
        
        // Calculate Levenshtein distance
        $distance = levenshtein($correct, $student);
        $maxlength = max(strlen($correct), strlen($student));
        
        // Normalized score: 1 - (distance / max_length)
        return max(0, 1 - ($distance / $maxlength));
    }
}
