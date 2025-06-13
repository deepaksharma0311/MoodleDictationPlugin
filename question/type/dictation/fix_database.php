<?php
/**
 * Database fix script for dictation plugin
 * Run this via browser: /question/type/dictation/fix_database.php
 */

require_once('../../../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

echo "<h2>Dictation Plugin Database Fix</h2>";

$dbman = $DB->get_manager();
$table = new xmldb_table('qtype_dictation_options');

// Check if audiofile column exists
if ($dbman->table_exists($table)) {
    $field = new xmldb_field('audiofile', XMLDB_TYPE_TEXT, null, null, null, null, null, 'enableaudio');
    
    if (!$dbman->field_exists($table, $field)) {
        echo "<p>Adding missing 'audiofile' column...</p>";
        $dbman->add_field($table, $field);
        echo "<p style='color: green;'>✓ Successfully added audiofile column</p>";
    } else {
        echo "<p style='color: blue;'>ℹ audiofile column already exists</p>";
    }
    
    // Show table structure
    echo "<h3>Current table structure:</h3>";
    $columns = $DB->get_columns('qtype_dictation_options');
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>{$column->name} ({$column->type})</li>";
    }
    echo "</ul>";
    
} else {
    echo "<p style='color: red;'>✗ Table qtype_dictation_options does not exist</p>";
    echo "<p>Please install the plugin first through Site administration > Notifications</p>";
}

echo "<p><a href='/admin/index.php'>← Back to Site administration</a></p>";
?>