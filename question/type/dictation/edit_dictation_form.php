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
 * Editing form for dictation questions.
 *
 * @package    qtype_dictation
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Dictation question editing form.
 */
class qtype_dictation_edit_form extends question_edit_form {

    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    protected function definition_inner($mform) {
        global $PAGE;

        // Add CSS and JavaScript
        $PAGE->requires->css('/question/type/dictation/styles.css');
        
        // Audio enable/disable toggle
        $mform->addElement('advcheckbox', 'enableaudio', get_string('enableaudio', 'qtype_dictation'),
            get_string('enableaudio_help', 'qtype_dictation'), array('group' => 1), array(0, 1));
        $mform->addHelpButton('enableaudio', 'enableaudio', 'qtype_dictation');
        $mform->setDefault('enableaudio', 1);

        // Audio file upload
        $mform->addElement('filepicker', 'audiofile', get_string('audiofile', 'qtype_dictation'),
            null, array('accepted_types' => array('.mp3', '.wav', '.ogg')));
        $mform->addHelpButton('audiofile', 'audiofile', 'qtype_dictation');
        $mform->disabledIf('audiofile', 'enableaudio', 'notchecked');

        // Maximum plays
        $playoptions = array(
            0 => get_string('unlimited', 'qtype_dictation'),
            1 => get_string('once', 'qtype_dictation'),
            2 => get_string('twice', 'qtype_dictation'),
            3 => get_string('threetimes', 'qtype_dictation'),
            5 => get_string('fivetimes', 'qtype_dictation')
        );
        $mform->addElement('select', 'maxplays', get_string('maxplays', 'qtype_dictation'), $playoptions);
        $mform->addHelpButton('maxplays', 'maxplays', 'qtype_dictation');
        $mform->setDefault('maxplays', 2);
        $mform->disabledIf('maxplays', 'enableaudio', 'notchecked');

        // Display mode for gaps
        $displayoptions = array(
            'standard' => get_string('displaystandard', 'qtype_dictation'),
            'length' => get_string('displaylength', 'qtype_dictation'),
            'letters' => get_string('displayletters', 'qtype_dictation'),
            'partial' => get_string('displaypartial', 'qtype_dictation')
        );
        $mform->addElement('select', 'displaymode', get_string('displaymode', 'qtype_dictation'), $displayoptions);
        $mform->addHelpButton('displaymode', 'displaymode', 'qtype_dictation');
        $mform->setDefault('displaymode', 'standard');

        // Transcript text area
        $mform->addElement('textarea', 'transcript', get_string('transcript', 'qtype_dictation'),
            array('rows' => 8, 'cols' => 80, 'class' => 'dictation-transcript'));
        $mform->setType('transcript', PARAM_RAW);
        $mform->addRule('transcript', null, 'required', null, 'client');
        $mform->addHelpButton('transcript', 'transcript', 'qtype_dictation');

        // Instructions for gap marking
        $instructions = html_writer::tag('div', get_string('gapinstructions', 'qtype_dictation'),
            array('class' => 'alert alert-info'));
        $mform->addElement('html', $instructions);

        // Preview area
        $previewhtml = html_writer::tag('div', '', array(
            'id' => 'dictation-preview',
            'class' => 'dictation-preview',
            'style' => 'display: none;'
        ));
        $mform->addElement('html', $previewhtml);

        // Preview button
        $mform->addElement('button', 'previewbtn', get_string('preview', 'qtype_dictation'),
            array('id' => 'dictation-preview-btn', 'class' => 'btn btn-secondary'));

        // Add JavaScript for live preview
        $PAGE->requires->js_call_amd('qtype_dictation/edit_form', 'init');
    }

    /**
     * Perform any preprocessing needed on the form data.
     *
     * @param array $toform the data being passed to the form.
     * @return array the modified data.
     */
    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        if (!empty($question->options)) {
            $question->transcript = $question->options->transcript;
            $question->maxplays = $question->options->maxplays;
            $question->enableaudio = $question->options->enableaudio;
            $question->displaymode = isset($question->options->displaymode) ? $question->options->displaymode : 'standard';
        }

        // Prepare audio file
        if (!empty($question->id)) {
            $draftitemid = file_get_submitted_draft_itemid('audiofile');
            file_prepare_draft_area($draftitemid, $this->context->id, 'qtype_dictation', 'audio',
                $question->id, array('subdirs' => false, 'maxfiles' => 1));
            $question->audiofile = $draftitemid;
        }

        return $question;
    }

    /**
     * Validate the form data.
     *
     * @param array $data the form data
     * @param array $files the uploaded files
     * @return array array of errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate transcript has gaps
        if (empty($data['transcript'])) {
            $errors['transcript'] = get_string('transcriptrequired', 'qtype_dictation');
        } else {
            $gapcount = preg_match_all('/\[([^\]]+)\]/', $data['transcript']);
            if ($gapcount == 0) {
                $errors['transcript'] = get_string('nogapsfound', 'qtype_dictation');
            }
        }

        // Validate audio file if audio is enabled
        if (!empty($data['enableaudio'])) {
            $draftitemid = $data['audiofile'];
            $usercontext = context_user::instance($USER->id);
            $fs = get_file_storage();
            $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id');
            
            $hasaudiofile = false;
            foreach ($draftfiles as $file) {
                if (!$file->is_directory()) {
                    $hasaudiofile = true;
                    break;
                }
            }
            
            if (!$hasaudiofile && empty($this->question->id)) {
                $errors['audiofile'] = get_string('audiofile_required', 'qtype_dictation');
            }
        }

        return $errors;
    }

    /**
     * Get the question type name.
     *
     * @return string the question type name.
     */
    public function qtype() {
        return 'dictation';
    }
}
