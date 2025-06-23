-- Complete database fix for dictation plugin
-- Run these commands in your Moodle database

-- Add missing audiofile column if it doesn't exist
ALTER TABLE mdl_qtype_dictation_options 
ADD COLUMN IF NOT EXISTS audiofile TEXT NULL 
COMMENT 'Audio file information for dictation mode';

-- Add missing displaymode column if it doesn't exist  
ALTER TABLE mdl_qtype_dictation_options 
ADD COLUMN IF NOT EXISTS displaymode VARCHAR(20) NOT NULL DEFAULT 'standard' 
COMMENT 'Display mode for gaps: standard, length, letters, partial';

-- Update existing records to have default displaymode
UPDATE mdl_qtype_dictation_options 
SET displaymode = 'standard' 
WHERE displaymode IS NULL OR displaymode = '';

-- Verify table structure
SHOW COLUMNS FROM mdl_qtype_dictation_options;