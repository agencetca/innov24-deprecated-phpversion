<?

define('PATH', '../backup/');
define('URL',  '../backup/');

// Maximum execution time in seconds
// 0 - without restrictions
define('TIME_LIMIT', 600);

// Limiting the size of the data for one to get access to the database (in megabytes)
// It is necessary to limit the amount of memory devoured server when dumping a volume of tables
define('LIMIT', 1);

// database if the server does not allow to view a list of databases
// and nothing is displayed after login. List the names, separated by commas
define('DBNAMES', '');

// Encoding connect to MySQL
// Auto - automatic choice (set encoding table), cp1251 - windows-1251, etc.
define('CHARSET', 'auto');

// MySQL connection encoding when restoring
// In case of transfer from the old versions of MySQL (prior to 4.1), which is not specified encoding tables in the dump
// If you add a 'forced->', for instance 'forced-> cp1251', encoding tables in the recovery would be forcibly changed to cp1251
// You can also specify the desired comparison for example 'cp1251_ukrainian_ci' or 'forced-> cp1251_ukrainian_ci'
define('RESTORE_CHARSET', 'utf8');

// Enable saving settings and recent actions
// To turn off the setvalue to 0
define('SC', 1);

// Table Types in which the structure is preserved only, separated by a comma
define('ONLY_CREATE', 'MRG_MyISAM,MERGE,HEAP,MEMORY');

// Global statistic
// To turn off the set value to 0
define('GS', 1);
?>