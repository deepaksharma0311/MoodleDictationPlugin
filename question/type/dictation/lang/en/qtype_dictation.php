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
 * Strings for component 'qtype_dictation', language 'en'
 *
 * @package    qtype_dictation
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Dictation';
$string['pluginname_help'] = 'Students listen to an audio recording and fill in missing words, or complete text gaps without audio (C-test mode).';
$string['pluginnameadding'] = 'Adding a dictation question';
$string['pluginnameediting'] = 'Editing a dictation question';
$string['pluginnamesummary'] = 'A dictation question type that allows students to fill in gaps while listening to audio, with intelligent scoring and research export capabilities.';

// Form elements
$string['enableaudio'] = 'Enable audio';
$string['enableaudio_help'] = 'Enable audio playback for dictation mode. Disable for C-test mode (text-only gap filling).';
$string['audiofile'] = 'Audio file';
$string['audiofile_help'] = 'Upload an MP3, WAV, or OGG audio file for the dictation exercise.';
$string['audiofile_required'] = 'Audio file is required when audio is enabled.';
$string['transcript'] = 'Transcript';
$string['transcript_help'] = 'Enter the transcript text. Mark words to be filled in by students using square brackets, e.g., "She [went] to the [store]."';
$string['transcriptrequired'] = 'Transcript is required.';
$string['maxplays'] = 'Maximum plays';
$string['maxplays_help'] = 'Set the maximum number of times students can play the audio. Choose unlimited for no restrictions.';

// Play options
$string['unlimited'] = 'Unlimited';
$string['once'] = 'Once';
$string['twice'] = 'Twice';
$string['threetimes'] = '3 times';
$string['fivetimes'] = '5 times';

// Instructions and help
$string['gapinstructions'] = 'Instructions: Mark the words that students should fill in using square brackets. For example: "The [cat] sat on the [mat]." Students will see input boxes where the bracketed words are.';
$string['nogapsfound'] = 'No gaps found in transcript. Please mark at least one word with square brackets [word].';

// Audio player
$string['play'] = 'Play';
$string['playlimitreached'] = 'Play limit reached';
$string['playcount'] = 'Played {$a->current} of {$a->max} times';
$string['audionotsupported'] = 'Your browser does not support audio playback.';

// Feedback and responses
$string['feedback'] = 'Feedback';
$string['gap'] = 'Gap';
$string['correct'] = 'Correct';
$string['incorrect'] = 'Incorrect';
$string['youranswer'] = 'Your answer';
$string['score'] = 'Score';
$string['correctansweris'] = 'The correct answer is:';
$string['pleaseenterananswer'] = 'Please enter an answer.';

// Preview
$string['preview'] = 'Preview';

// Export
$string['exportcsv'] = 'Export CSV';
$string['exportall'] = 'Export all responses';
$string['exportfilename'] = 'dictation_responses_{$a}.csv';
$string['exportheader_student'] = 'Student';
$string['exportheader_attempt'] = 'Attempt';
$string['exportheader_question'] = 'Question';
$string['exportheader_gap'] = 'Gap {$a}';
$string['exportheader_gap_correct'] = 'Gap {$a} Correct';
$string['exportheader_gap_score'] = 'Gap {$a} Score';
$string['exportheader_totalscore'] = 'Total Score';
$string['exportheader_playcount'] = 'Play Count';
$string['exportheader_timecreated'] = 'Time Created';

// Privacy
$string['privacy:metadata:qtype_dictation_attempts'] = 'Information about user attempts on dictation questions.';
$string['privacy:metadata:qtype_dictation_attempts:userid'] = 'The ID of the user who made the attempt.';
$string['privacy:metadata:qtype_dictation_attempts:responses'] = 'The responses given by the user.';
$string['privacy:metadata:qtype_dictation_attempts:scores'] = 'The scores achieved for each gap.';
$string['privacy:metadata:qtype_dictation_attempts:totalscore'] = 'The total score for the attempt.';
$string['privacy:metadata:qtype_dictation_attempts:playcount'] = 'The number of times the user played the audio.';
$string['privacy:metadata:qtype_dictation_attempts:timecreated'] = 'The time when the attempt was created.';
