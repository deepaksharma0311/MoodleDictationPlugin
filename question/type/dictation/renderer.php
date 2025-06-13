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
 * Dictation question renderer class.
 *
 * @package    qtype_dictation
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for dictation questions.
 */
class qtype_dictation_renderer extends qtype_renderer {

    /**
     * Generate the display of the formulation part of the question.
     *
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $PAGE;
        
        $question = $qa->get_question();
        $currentanswer = $qa->get_last_qt_data();
        
        // Display the question text
        $questiontext = $question->format_questiontext($qa);
        $result = html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        // Add audio player if enabled
        if ($question->enableaudio && !empty($question->audiofile)) {
            $result .= $this->render_audio_player($question, $qa, $currentanswer);
        }

        // Add the question text with input gaps
        $result .= $this->render_question_text_with_gaps($question, $qa, $currentanswer);

        // Add JavaScript for audio control and form handling
        $PAGE->requires->js_call_amd('qtype_dictation/dictation', 'init', array(
            'questionid' => $question->id,
            'maxplays' => $question->maxplays,
            'enableaudio' => $question->enableaudio
        ));

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                $question->get_validation_error($currentanswer),
                array('class' => 'validationerror'));
        }

        return $result;
    }

    /**
     * Render the audio player component.
     *
     * @param qtype_dictation_question $question
     * @param question_attempt $qa
     * @param array $currentanswer
     * @return string HTML for audio player
     */
    private function render_audio_player($question, $qa, $currentanswer) {
        $playcount = isset($currentanswer['playcount']) ? (int)$currentanswer['playcount'] : 0;
        $maxplays = (int)$question->maxplays;
        $disabled = ($maxplays > 0 && $playcount >= $maxplays);

        $html = html_writer::start_tag('div', array('class' => 'dictation-audio-player'));
        
        // Audio element
        $audioattrs = array(
            'id' => 'dictation-audio-' . $question->id,
            'class' => 'dictation-audio',
            'preload' => 'metadata'
        );
        
        if ($disabled) {
            $audioattrs['disabled'] = 'disabled';
        }
        
        $html .= html_writer::start_tag('audio', $audioattrs);
        $html .= html_writer::empty_tag('source', array(
            'src' => $question->audiofile,
            'type' => 'audio/mpeg'
        ));
        $html .= get_string('audionotsupported', 'qtype_dictation');
        $html .= html_writer::end_tag('audio');

        // Play button
        $buttonattrs = array(
            'type' => 'button',
            'id' => 'dictation-play-btn-' . $question->id,
            'class' => 'btn btn-primary dictation-play-btn'
        );
        
        if ($disabled) {
            $buttonattrs['disabled'] = 'disabled';
            $buttonattrs['class'] .= ' disabled';
        }
        
        $buttontext = $disabled ? get_string('playlimitreached', 'qtype_dictation') : get_string('play', 'qtype_dictation');
        $html .= html_writer::tag('button', $buttontext, $buttonattrs);

        // Play counter
        if ($maxplays > 0) {
            $countertext = get_string('playcount', 'qtype_dictation', array(
                'current' => $playcount,
                'max' => $maxplays
            ));
            $html .= html_writer::tag('span', $countertext, array(
                'class' => 'dictation-play-counter',
                'id' => 'dictation-counter-' . $question->id
            ));
        }

        // Hidden field to track play count
        $html .= html_writer::empty_tag('input', array(
            'type' => 'hidden',
            'name' => $qa->get_qt_field_name('playcount'),
            'id' => $qa->get_qt_field_name('playcount'),
            'value' => $playcount
        ));

        $html .= html_writer::end_tag('div');
        
        return $html;
    }

    /**
     * Render the question text with input gaps.
     *
     * @param qtype_dictation_question $question
     * @param question_attempt $qa
     * @param array $currentanswer
     * @return string HTML for question text with gaps
     */
    private function render_question_text_with_gaps($question, $qa, $currentanswer) {
        $text = $question->transcript;
        $gapindex = 0;
        
        // Replace [word] with input boxes
        $text = preg_replace_callback('/\[([^\]]+)\]/', function($matches) use (&$gapindex, $qa, $currentanswer) {
            $fieldname = $qa->get_qt_field_name('gap_' . $gapindex);
            $currentvalue = isset($currentanswer['gap_' . $gapindex]) ? $currentanswer['gap_' . $gapindex] : '';
            
            $inputhtml = html_writer::empty_tag('input', array(
                'type' => 'text',
                'name' => $fieldname,
                'id' => $fieldname,
                'value' => $currentvalue,
                'class' => 'dictation-gap',
                'size' => max(8, strlen($matches[1])),
                'autocomplete' => 'off'
            ));
            $gapindex++;
            return $inputhtml;
        }, $text);
        
        return html_writer::tag('div', $text, array('class' => 'dictation-question-text'));
    }

    /**
     * Generate the specific feedback.
     *
     * @param question_attempt $qa
     * @return string HTML fragment
     */
    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();
        
        if (empty($response) || !$qa->get_state()->is_finished()) {
            return '';
        }

        $feedback = $question->get_gap_feedback($response);
        $html = html_writer::start_tag('div', array('class' => 'dictation-feedback'));
        
        $html .= html_writer::tag('h4', get_string('feedback', 'qtype_dictation'));
        
        $html .= html_writer::start_tag('div', array('class' => 'dictation-feedback-details'));
        
        foreach ($feedback as $gapfeedback) {
            $class = $gapfeedback['iscorrect'] ? 'correct' : 'incorrect';
            
            $html .= html_writer::start_tag('div', array('class' => 'gap-feedback ' . $class));
            
            $html .= html_writer::tag('strong', get_string('gap', 'qtype_dictation') . ' ' . ($gapfeedback['gap'] + 1) . ':');
            
            $html .= html_writer::tag('div', 
                get_string('correct', 'qtype_dictation') . ': ' . $gapfeedback['correct'],
                array('class' => 'correct-answer')
            );
            
            $html .= html_writer::tag('div',
                get_string('youranswer', 'qtype_dictation') . ': ' . $gapfeedback['student'],
                array('class' => 'student-answer')
            );
            
            $html .= html_writer::tag('div',
                get_string('score', 'qtype_dictation') . ': ' . round($gapfeedback['score'] * 100, 1) . '%',
                array('class' => 'gap-score')
            );
            
            $html .= html_writer::end_tag('div');
        }
        
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('div');
        
        return $html;
    }

    /**
     * Generate the correct answer display.
     *
     * @param question_attempt $qa
     * @return string HTML fragment
     */
    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        $correctresponse = $question->get_correct_response();
        
        if (empty($correctresponse)) {
            return '';
        }

        $html = html_writer::start_tag('div', array('class' => 'rightanswer'));
        $html .= html_writer::tag('strong', get_string('correctansweris', 'qtype_dictation'));
        
        $answers = array();
        for ($i = 0; $i < count($question->gaps); $i++) {
            $gapkey = 'gap_' . $i;
            if (isset($correctresponse[$gapkey])) {
                $answers[] = ($i + 1) . ': ' . $correctresponse[$gapkey];
            }
        }
        
        $html .= html_writer::tag('div', implode(', ', $answers));
        $html .= html_writer::end_tag('div');
        
        return $html;
    }
}
