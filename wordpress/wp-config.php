<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'pharlcom_wpsite');

/** MySQL database username */
define('DB_USER', 'pharlcom_wpadmin');

/** MySQL database password */
define('DB_PASSWORD', 'lb19ej74');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'Y8WcA>5M:[`FNvL]U_,|Z+QTyhra;dJyt$5:P)m#Qaf9$wA??C0R=wOZO`Qs*-Y}');
define('SECURE_AUTH_KEY',  '(h#dzq?}3=Q;TD5&]e:na#3g;ZHtbu?Gxu@>@E.R-kR+|^{<Wo@D+YyP^5rP*S.d');
define('LOGGED_IN_KEY',    'bhbXygB2x}R1c5:}-I!xqU+9W$uZO5kd/%}+y^d=/dsha7, jo-y<bWzu7elN+fM');
define('NONCE_KEY',        '&{x.!2YU6,quCNPbK1/Q6.E?~*8&iJ.^|:%o/VO,-. .(MC3oG%5ePf/v$+LU[YA');
define('AUTH_SALT',        '?#_=icONmoU^OH_S{(/=,|~X@6{wO)hDf&Cu8R~;DQx.s*3E<]23>C]-gI{z./x&');
define('SECURE_AUTH_SALT', ')N|U|Jq s|h(Q|6$~|aAaGk nT!lM=u4Ld nZ ^uKX e}f}cX#j{=40s.fK)C+8)');
define('LOGGED_IN_SALT',   '!UaPbF|c|=%(G{*;Rmzc9ESG+qJdgN[H||v1X9aD/iDEZNFXaYx+N`Y<|?ekgec)');
define('NONCE_SALT',       'x<An1O0O9zK/n&9@F!~,6vPXm?>V4Pu4!${c:vI;tvn6oZQa Rt:s;E*c}A.[6.7');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
