<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'webbalo');

/** MySQL database username */
define('DB_USER', 'webbalo');

/** MySQL database password */
define('DB_PASSWORD', 'P!Yi88!SE9');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '0ig7tkymukspnojiv6701jtkcupwkum0a7xgipp5vwiyxvl7pypunjjfablkxuyn');
define('SECURE_AUTH_KEY',  '0lczekugh8okqfhqigqiaqgivoudf3ybjol4f4zar2hezvxqmewgkzvnkuidpqt2');
define('LOGGED_IN_KEY',    'cujrizsveshn4biib0rwzbhnfeqhkqfge6mtodo7al7rrlfqivdrv8uh3c0d311x');
define('NONCE_KEY',        'yc9rfkxqbculibfzogirjnerq0c4fl8ecivglvhcbwfdnx4vsyjyhnt5gkda9o32');
define('AUTH_SALT',        'oq1ugdmacfsbcxg7sorh7eri3jv1ufiqzyasud3b67iazaxp62jscxvv5uirelma');
define('SECURE_AUTH_SALT', 'o9bwr2h2b1pwtydasgrrvpstakiasqh3puz5a4q5mzdwzav10zrhulnh2ftlisjs');
define('LOGGED_IN_SALT',   'rwufgtap3ov1mszvb6xhq3exvvnyofmxkctss9gc9gh7ylkqv2uoydj7tseppixz');
define('NONCE_SALT',       '99suyxpsuzfofk6dzdquwkumqpfn4uhtixtxjvlf1oqwctdqedwehe5gylvxavyj');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp2fa_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
