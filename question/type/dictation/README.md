# Moodle Dictation Question Plugin

A comprehensive Moodle question type for dictation and C-test assessments with intelligent scoring.

## Features

- **Audio Dictation Mode**: Students listen to audio and fill in missing words
- **C-test Mode**: Text-only gap filling without audio
- **Intelligent Scoring**: Uses normalized Levenshtein distance for partial credit
- **Flexible Gap Marking**: Use square brackets [word] to mark gaps
- **Audio Controls**: Configurable playback limits (once, twice, unlimited, etc.)
- **Detailed Feedback**: Shows per-word scores and overall performance
- **Research Export**: CSV export of all student responses and scores
- **Accessibility**: Keyboard navigation and screen reader support

## Installation

1. Copy the `dictation` folder to your Moodle installation:
   ```
   /path/to/moodle/question/type/dictation/
   ```

2. Log in as administrator and visit the notifications page to complete installation

3. The new question type will appear in the question bank

## Usage

### Creating Questions

1. **Choose Mode**: Enable/disable audio for dictation vs C-test mode
2. **Upload Audio**: Add MP3, WAV, or OGG files (dictation mode only)
3. **Set Play Limits**: Control how many times students can hear audio
4. **Mark Gaps**: Use square brackets to mark words for students to fill:
   ```
   The [cat] sat on the [mat] and watched the [birds].
   ```

### Student Experience

- Listen to audio (if enabled) with play count tracking
- Fill in text boxes where gaps appear
- Receive immediate detailed feedback
- See per-word scores based on similarity

### Teacher Tools

- Export student responses to CSV for analysis
- View detailed scoring breakdowns
- Support for backup and restore operations

## Scoring Algorithm

Word Score = 1 - (Levenshtein distance ÷ max(correct length, student length))

Sentence Score = (Sum of word scores × word lengths) ÷ total correct characters

This ensures:
- Longer words contribute proportionally more
- No cascading errors between words  
- Fair partial credit for near-correct answers
- Suitable for language proficiency testing

## Examples

### Dictation Example
```
Audio: "The cat sat on the mat"
Text: "The [cat] sat on the [mat]."
```

### C-test Example  
```
Text: "She under[stands] the topic and can expl[ain] it clearly."
```

## Requirements

- Moodle 3.10 or higher
- PHP 7.4 or higher
- Modern browser with HTML5 audio support

## License

GPL v3 or later