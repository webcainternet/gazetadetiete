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
define('DB_NAME', 'gazetadetiete');

/** MySQL database username */
define('DB_USER', 'gazetadetiete');

/** MySQL database password */
define('DB_PASSWORD', 'd8jy26dj');

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
define('AUTH_KEY',         'epS$D682^wdd1,Y::G-YZ:D?-8MDxx76nB[WB$gZ@H}VYOvUb(wK|,EqA#WQ8#7(');
define('SECURE_AUTH_KEY',  '$Lr!ZSJhS%n~cDV%RoAE/A16v||<^r*wFH`Hb qJ5EZ+`V5<LYF1FmO~J2O?C2DY');
define('LOGGED_IN_KEY',    'JfV=E&w yt&Ri>oeAXs$JMLm0]q!}ir<[yE/UG{Ln~^[Sr=5}|^v<#*,eD@1xYbR');
define('NONCE_KEY',        'NY|13X#T%AM4jA=Tz[>l6xiT,-y|D- QyGQa*o!|XI,-LkFT_qIFkthd^~e8u?oX');
define('AUTH_SALT',        '-}Nz.<qUl,oeXS@n&]%oH+#Wq g0:eJ1)kcGKw5MbH9HO)Bp}}]nL+R;mb[V5-N^');
define('SECURE_AUTH_SALT', 'vphz^`Z{;><2Zhn[FG4Xvm.E&kl_~W`gAcRaA+~He3gS<(gG!vP[)WW-IPl xS#>');
define('LOGGED_IN_SALT',   'JE1P?a8y|7:^oR+-3MrD1}gzc#6XT]CdZ^F+y<w3UWNTrFWIzV^M_yqKPwQqd .G');
define('NONCE_SALT',       'w?!v[qr:@Jc$`^Ch_[NpZHg >$w{l-_ao~9f|W4A+]ux==WdR^DJ=v$/-xf((J*S');

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
