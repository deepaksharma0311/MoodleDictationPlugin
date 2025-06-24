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
 * JavaScript for dictation question type functionality.
 *
 * @module     qtype_dictation/dictation
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    'use strict';

    var playCount = 0;
    var maxPlays = 0;
    var enableAudio = true;

    /**
     * Initialize the dictation question functionality.
     *
     * @param {Object} params Configuration parameters
     * @param {number} params.questionid The question ID
     * @param {number} params.maxplays Maximum number of plays allowed
     * @param {boolean} params.enableaudio Whether audio is enabled
     */
    function init(params) {
        maxPlays = params.maxplays || 0;
        enableAudio = params.enableaudio;
        
        var questionContainer = $('#q' + params.questionid);
        if (questionContainer.length === 0) {
            // Fallback: look for any dictation question container
            questionContainer = $('.qtype_dictation').first();
        }

        if (questionContainer.length > 0) {
            setupAudioPlayer(questionContainer);
            setupGapFocus(questionContainer);
        }
    }

    /**
     * Set up the audio player functionality.
     *
     * @param {jQuery} container The question container
     */
    function setupAudioPlayer(container) {
        var audioElement = container.find('audio').first();
        var playButton = container.find('.audio-play-btn');
        var counterElement = container.find('.play-counter');
        var playCountInput = container.find('input[name*="playcount"]');

        if (audioElement.length === 0 || !enableAudio) {
            // Hide audio controls if no audio or audio disabled
            container.find('.audio-controls').hide();
            return;
        }

        // Get current play count from hidden input
        if (playCountInput.length > 0 && playCountInput.val()) {
            playCount = parseInt(playCountInput.val()) || 0;
        }

        updateButtonState();
        updateCounter();

        // Play button click handler
        playButton.on('click', function(e) {
            e.preventDefault();
            if (canPlay()) {
                playAudio();
            }
        });

        // Audio ended handler
        audioElement.on('ended', function() {
            playCount++;
            updatePlayCount();
            updateButtonState();
            updateCounter();
        });

        // Audio error handler
        audioElement.on('error', function() {
            console.error('Audio failed to load');
            playButton.prop('disabled', true).text('Audio Error');
        });

        function canPlay() {
            return maxPlays === 0 || playCount < maxPlays;
        }

        function playAudio() {
            if (audioElement[0] && canPlay()) {
                audioElement[0].play().catch(function(error) {
                    console.error('Audio play failed:', error);
                });
            }
        }

        function updateButtonState() {
            if (!canPlay()) {
                playButton.prop('disabled', true);
                playButton.find('.btn-text').text('Play limit reached');
            } else {
                playButton.prop('disabled', false);
                playButton.find('.btn-text').text('Play Audio');
            }
        }

        function updateCounter() {
            if (counterElement.length > 0) {
                if (maxPlays > 0) {
                    counterElement.text(playCount + ' / ' + maxPlays);
                } else {
                    counterElement.text(playCount);
                }
            }
        }

        function updatePlayCount() {
            if (playCountInput.length > 0) {
                playCountInput.val(playCount);
            }
        }
    }

    /**
     * Set up gap input focus functionality.
     *
     * @param {jQuery} container The question container
     */
    function setupGapFocus(container) {
        var gapInputs = container.find('input[type="text"][name*="gap"]');
        
        gapInputs.on('focus', function() {
            $(this).addClass('gap-focused');
        });

        gapInputs.on('blur', function() {
            $(this).removeClass('gap-focused');
        });

        // Auto-advance to next gap on Enter (optional enhancement)
        gapInputs.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var currentIndex = gapInputs.index(this);
                var nextInput = gapInputs.eq(currentIndex + 1);
                if (nextInput.length > 0) {
                    nextInput.focus();
                }
            }
        });
    }

    return {
        init: init
    };
});