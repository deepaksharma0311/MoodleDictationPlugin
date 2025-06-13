-- SQL script to fix the missing audiofile column
-- Run this in your Moodle database to add the missing field

ALTER TABLE mdl_qtype_dictation_options 
ADD COLUMN audiofile TEXT NULL COMMENT 'Audio file information for dictation mode' 
AFTER enableaudio;

-- Verify the table structure
DESCRIBE mdl_qtype_dictation_options;