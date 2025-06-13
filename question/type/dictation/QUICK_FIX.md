# Quick Database Fix for "Unknown column 'audiofile'" Error

## Immediate Solution

Run this SQL command in your Moodle database (via phpMyAdmin or command line):

```sql
ALTER TABLE mdl_qtype_dictation_options 
ADD COLUMN audiofile TEXT NULL 
COMMENT 'Audio file information for dictation mode' 
AFTER enableaudio;
```

## Alternative: Use the updated files

If you cannot run SQL commands, replace these files with the updated versions:

1. `questiontype.php` - Removed audiofile from extra_question_fields
2. `version.php` - Updated version number to trigger upgrade
3. `db/upgrade.php` - Added automatic database update
4. `db/install.xml` - Includes audiofile field for new installations

## After applying the fix:

1. Go to Site administration > Notifications
2. Click "Upgrade Moodle database now" if prompted
3. Clear all caches (Site administration > Development > Purge all caches)
4. Test creating a new dictation question

The plugin will then work correctly with both audio dictation and C-test modes.