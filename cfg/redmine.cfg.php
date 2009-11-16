<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * $Id: redmine.cfg.php,v 1.2 2008/05/07 02:59:07 tosikawa Exp $ 
 * 
 * Constants used throughout TestLink are defined within this file
 * they should be changed for your environment
 * 
 * @author Toshiyuki Kawanishi, Hantani (Garyo), TestLink User Community in Japan
 *
 * Thanks to redMine Japanese User Community.
 * We get advice on redMine settings from them. 
 */
// Set the bug tracking system Interface to redMine 0.6.3

/** The DB host to use when connecting to the mantis db */
define('BUG_TRACK_DB_HOST', 'localhost');

/** The name of the database that contains the mantis tables */
define('BUG_TRACK_DB_NAME', 'redmine517');

/** The DB type being used by redMine
 * Check config/database.yml in redMine install directory.
 * values: mysql, mssql, postgres
 */
define('BUG_TRACK_DB_TYPE', 'mysql');

/** The DB password to use for connecting to the redMine db */
define('BUG_TRACK_DB_USER', 'root');
define('BUG_TRACK_DB_PASS', 'root');

/**
 * redMine store information to database with "latain1" char-set by default.
 * If you use another char-set, add "encoding:" entry to config/database.yml.
 *
 * e.g.)
 * ----------------------
 * production:
 *     encoding: utf8
 *     adapter: mysql
 *     database: redmine
 *     host: localhost
 *     username: root
 *     password: xxxxxxxx
 * ----------------------
 */
define('BUG_TRACK_DB_CHARSET', "utf8");
// define('BUG_TRACK_DB_CHARSET',"gb2312");
// define('BUG_TRACK_DB_CHARSET',"UTF-8");


/* link of the web server for redmine */
//define('BUG_TRACK_HREF', "http://localhost/redmine/issues/show/");
define('BUG_TRACK_HREF', "http://localhost:3000/issues/show/");

/** link to the bugtracking system, for entering new bugs */
//define('BUG_TRACK_ENTER_BUG_HREF',"http://localhost/redmine/");
 define('BUG_TRACK_ENTER_BUG_HREF',"http://localhost:3000/");
?>
