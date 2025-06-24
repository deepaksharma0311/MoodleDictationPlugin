# Moodle Dictation Question Plugin

## Project Overview
A comprehensive Moodle question plugin for language assessment featuring:
- Audio-based dictation exercises with MP3 upload support
- C-test functionality with intelligent gap marking using square brackets [word]
- Multiple display modes: standard blanks, length hints, letter hints, partial words
- Levenshtein distance scoring algorithm for flexible answer matching
- Configurable audio playback limits with JavaScript controls
- CSV export capabilities for research analysis

## Current Status
**Phase 1 Complete** - Core functionality implemented and tested
- ✓ Complete plugin structure with all PHP files
- ✓ Database schema with upgrade scripts
- ✓ Audio file serving system via pluginfile function
- ✓ JavaScript AMD module for audio controls
- ✓ Four C-test display modes implemented
- ✓ Intelligent scoring with Levenshtein distance

## Recent Changes (June 24, 2025)
- ✓ Created missing lib.php with qtype_dictation_pluginfile function
- ✓ Fixed audio file serving "file not found" errors
- ✓ Added JavaScript AMD module (amd/src/dictation.js) for audio controls
- ✓ Updated renderer to handle audio URL generation properly
- ✓ Created database fix scripts for missing columns

## Project Architecture

### Core Files
- **questiontype.php** - Main question type class with file handling
- **question.php** - Question implementation with Levenshtein scoring
- **renderer.php** - HTML output generation with audio controls
- **edit_dictation_form.php** - Question editing interface
- **lib.php** - File serving function for audio files
- **amd/src/dictation.js** - JavaScript audio player controls

### Database Schema
- **qtype_dictation_options** - Question settings and audio file info
- **qtype_dictation_answers** - Answer options with gap mapping

### Key Features
1. **Audio Integration** - MP3 upload with configurable play limits
2. **Gap Detection** - Automatic parsing of [word] syntax
3. **Smart Scoring** - Levenshtein distance with percentage thresholds
4. **Display Modes** - Four different gap presentation styles
5. **Research Export** - CSV data export for analysis

## User Preferences
- Focus on clean, well-documented code
- Prioritize Moodle best practices and security
- Maintain backward compatibility with existing questions
- Keep plugin lightweight and performant

## Technical Decisions
- Uses Moodle's file API for secure audio serving
- AMD JavaScript modules for modern browser compatibility
- Levenshtein algorithm for intelligent answer matching
- Square bracket notation for intuitive gap marking
- PostgreSQL database with proper upgrade handling

## Known Issues Resolved
- ✓ Missing pluginfile function causing audio load failures
- ✓ Database schema mismatches with audiofile/displaymode columns
- ✓ JavaScript AMD module not found errors
- ✓ Audio URL generation problems in renderer

## Deployment Notes
- Requires Moodle 3.9+ for AMD module support
- Database upgrade scripts handle existing installations
- Audio files stored in Moodle's file system securely
- JavaScript minified for production performance