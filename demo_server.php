<?php
/**
 * Demo server for dictation question plugin
 */

// Simple router
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Serve static files
if (preg_match('/\.(css|js|mp3|wav|ogg)$/', $path)) {
    $file = __DIR__ . $path;
    if (file_exists($file)) {
        $mime = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg'
        ];
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        header('Content-Type: ' . ($mime[$ext] ?? 'application/octet-stream'));
        readfile($file);
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dictation Question Plugin Demo</title>
    <link rel="stylesheet" href="/question/type/dictation/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .demo-header {
            background: #007cba;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .demo-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .demo-section h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #007cba;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            height: 120px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }
        .btn-primary {
            background: #007cba;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .feature-list li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
        }
        .code-example {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            font-family: monospace;
            margin: 10px 0;
        }
        .demo-question {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="demo-header">
        <h1>Moodle Dictation Question Plugin</h1>
        <p>A comprehensive solution for dictation and C-test assessments with intelligent scoring</p>
    </div>

    <div class="demo-section">
        <h3>Plugin Features</h3>
        <ul class="feature-list">
            <li>Audio file upload support (MP3, WAV, OGG)</li>
            <li>C-test mode (text-only gap filling without audio)</li>
            <li>Square bracket notation for marking gaps [word]</li>
            <li>Intelligent scoring using normalized Levenshtein distance</li>
            <li>Configurable audio playback limits</li>
            <li>Immediate detailed feedback for students</li>
            <li>CSV export for research and analysis</li>
            <li>Backup and restore support</li>
            <li>Responsive design with accessibility features</li>
        </ul>
    </div>

    <div class="demo-section">
        <h3>Teacher Interface - Create Question</h3>
        
        <div class="form-group">
            <label>
                <input type="checkbox" checked> Enable Audio (uncheck for C-test mode)
            </label>
        </div>

        <div class="form-group">
            <label for="audiofile">Audio File:</label>
            <input type="file" id="audiofile" accept=".mp3,.wav,.ogg">
            <small>Upload MP3, WAV, or OGG audio file for dictation</small>
        </div>

        <div class="form-group">
            <label for="maxplays">Maximum Plays:</label>
            <select id="maxplays">
                <option value="0">Unlimited</option>
                <option value="1">Once</option>
                <option value="2" selected>Twice</option>
                <option value="3">3 times</option>
                <option value="5">5 times</option>
            </select>
        </div>

        <div class="form-group">
            <label for="displaymode">Gap Display Mode:</label>
            <select id="displaymode">
                <option value="standard">Standard blank (_______)</option>
                <option value="length">Length hints (_ _ _ _)</option>
                <option value="letters">Letter hints (g o _ _)</option>
                <option value="partial" selected>Partial word (C-test style)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="transcript">Transcript:</label>
            <textarea id="transcript" placeholder="Enter transcript with gaps marked using square brackets...">The qu[ick] brown f[ox] jumps ov[er] the la[zy] dog.</textarea>
        </div>

        <div class="alert alert-info">
            <strong>Instructions:</strong> Mark words that students should fill in using square brackets. 
            For example: "The [cat] sat on the [mat]." Students will see input boxes where the bracketed words are.
        </div>

        <button class="btn btn-secondary" onclick="showPreview()">Preview Question</button>
        <button class="btn btn-primary">Save Question</button>
    </div>

    <div class="demo-section">
        <h3>Student Interface - Answer Question</h3>
        <div class="demo-question">
            <p><strong>Question:</strong> Listen to the audio and fill in the missing words.</p>
            
            <div class="dictation-audio-player">
                <audio id="demo-audio" class="dictation-audio" preload="metadata">
                    <source src="demo-audio.mp3" type="audio/mpeg">
                    Your browser does not support audio playback.
                </audio>
                <button type="button" class="btn btn-primary dictation-play-btn" onclick="playAudio()">Play</button>
                <span class="dictation-play-counter">Played 0 of 2 times</span>
            </div>

            <div class="dictation-question-text" id="student-view">
                The qu<input type="text" class="dictation-gap dictation-gap-partial" size="4" placeholder="ick"> brown f<input type="text" class="dictation-gap dictation-gap-partial" size="3" placeholder="ox"> jumps ov<input type="text" class="dictation-gap dictation-gap-partial" size="3" placeholder="er"> the la<input type="text" class="dictation-gap dictation-gap-partial" size="3" placeholder="zy"> dog.
            </div>

            <button class="btn btn-primary">Submit Answer</button>
        </div>
    </div>

    <div class="demo-section">
        <h3>Scoring Algorithm</h3>
        <p>The plugin uses normalized Levenshtein distance for intelligent partial credit scoring:</p>
        
        <div class="code-example">
Word Score = 1 - (Levenshtein distance ÷ max(correct word length, student input length))

Sentence Score = (Sum of word scores × correct word lengths) ÷ total characters in correct sentence
        </div>

        <p>This ensures:</p>
        <ul>
            <li>Longer words contribute proportionally more to the final score</li>
            <li>No cascading errors between words</li>
            <li>Fair partial credit for near-correct answers</li>
            <li>Suitable for language proficiency testing</li>
        </ul>
    </div>

    <div class="demo-section">
        <h3>Example Feedback</h3>
        <div class="dictation-feedback">
            <h4>Feedback</h4>
            <div class="dictation-feedback-details">
                <div class="gap-feedback correct">
                    <strong>Gap 1:</strong>
                    <div class="correct-answer">Correct: cat</div>
                    <div class="student-answer">Your answer: cat</div>
                    <div class="gap-score">Score: 100.0%</div>
                </div>
                <div class="gap-feedback incorrect">
                    <strong>Gap 2:</strong>
                    <div class="correct-answer">Correct: mat</div>
                    <div class="student-answer">Your answer: hat</div>
                    <div class="gap-score">Score: 66.7%</div>
                </div>
                <div class="gap-feedback correct">
                    <strong>Gap 3:</strong>
                    <div class="correct-answer">Correct: birds</div>
                    <div class="student-answer">Your answer: birds</div>
                    <div class="gap-score">Score: 100.0%</div>
                </div>
                <div class="gap-feedback incorrect">
                    <strong>Gap 4:</strong>
                    <div class="correct-answer">Correct: tree</div>
                    <div class="student-answer">Your answer: three</div>
                    <div class="gap-score">Score: 60.0%</div>
                </div>
            </div>
        </div>
        <div class="alert alert-success">
            <strong>Overall Score: 81.6%</strong> - Well done! Most of your answers were correct or very close.
        </div>
    </div>

    <div class="demo-section">
        <h3>Enhanced C-Test Mode Examples</h3>
        <p>Different display modes for text-only gap filling:</p>
        
        <h4>Standard Mode</h4>
        <div class="demo-question">
            <div class="dictation-question-text">
                She under<input type="text" class="dictation-gap-standard" size="6" placeholder="______"> the topic well and can 
                expl<input type="text" class="dictation-gap-standard" size="6" placeholder="______"> it clearly to others.
            </div>
        </div>

        <h4>Length Hints Mode</h4>
        <div class="demo-question">
            <div class="dictation-question-text">
                She under<input type="text" class="dictation-gap-length" size="6" placeholder="_ _ _ _ _ _"> the topic well and can 
                expl<input type="text" class="dictation-gap-length" size="6" placeholder="_ _ _"> it clearly to others.
            </div>
        </div>

        <h4>Letter Hints Mode</h4>
        <div class="demo-question">
            <div class="dictation-question-text">
                She under<input type="text" class="dictation-gap-letters" size="6" placeholder="s _ _ _ _ _"> the topic well and can 
                expl<input type="text" class="dictation-gap-letters" size="6" placeholder="a _ _"> it clearly to others.
            </div>
        </div>

        <h4>Partial Word Mode (C-test Style)</h4>
        <div class="demo-question">
            <div class="dictation-question-text">
                She under<input type="text" class="dictation-gap-partial" size="6" placeholder="stands"> the topic well and can 
                expl<input type="text" class="dictation-gap-partial" size="6" placeholder="ain"> it clearly to others.
            </div>
        </div>
    </div>

    <div class="demo-section">
        <h3>Research Export</h3>
        <p>Teachers can export all student responses in CSV format for research analysis:</p>
        <button class="btn btn-secondary dictation-export-btn">Export CSV Data</button>
        <p><small>Includes individual gap scores, total scores, play counts, and timestamps.</small></p>
    </div>

    <script>
        let playCount = 0;
        const maxPlays = 2;

        function playAudio() {
            const audio = document.getElementById('demo-audio');
            const button = document.querySelector('.dictation-play-btn');
            const counter = document.querySelector('.dictation-play-counter');
            
            if (playCount >= maxPlays) {
                return;
            }
            
            if (audio.paused) {
                audio.play().catch(function() {
                    alert('Demo audio not available, but this shows the interface behavior.');
                });
                button.textContent = 'Pause';
            } else {
                audio.pause();
                button.textContent = 'Play';
            }
        }

        function showPreview() {
            const transcript = document.getElementById('transcript').value;
            const preview = transcript.replace(/\[([^\]]+)\]/g, '<span class="dictation-preview-gap"></span>');
            
            alert('Preview would show: ' + preview.replace(/<[^>]*>/g, '___'));
        }

        // Mock audio events
        document.addEventListener('DOMContentLoaded', function() {
            const audio = document.getElementById('demo-audio');
            const button = document.querySelector('.dictation-play-btn');
            const counter = document.querySelector('.dictation-play-counter');
            
            // Simulate audio end event
            setTimeout(function() {
                if (audio && !audio.paused) {
                    audio.pause();
                    button.textContent = 'Play';
                    playCount++;
                    counter.textContent = `Played ${playCount} of ${maxPlays} times`;
                    
                    if (playCount >= maxPlays) {
                        button.disabled = true;
                        button.classList.add('disabled');
                        button.textContent = 'Play limit reached';
                    }
                }
            }, 3000);
        });

        // Gap input behavior
        document.querySelectorAll('.dictation-gap').forEach(function(input, index) {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const gaps = document.querySelectorAll('.dictation-gap');
                    const nextGap = gaps[index + 1];
                    if (nextGap) {
                        nextGap.focus();
                    }
                }
            });
        });
    </script>
</body>
</html>