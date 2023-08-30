<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'wordpress' );

/** Database hostname */
define( 'DB_HOST', 'database' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '98GUWgr5@<8bH}+d@hJhUrk&JO+-+r^5~8byp5v[xo9 O!6^J.wjd#y&J+N18=@e');
define('SECURE_AUTH_KEY',  'MBXXGRrGJlc[?i:?hx1k`hKZ^qnq4)g)Ih:A5~z:to(qga$q#q0<YPiK[~TB?f:x');
define('LOGGED_IN_KEY',    'R^38<Ga%|7>dJ/o!vl<2t`>R2C;Nof!amwV4O8}7BIlu=nlLW>*dq)xy+;G7)r1>');
define('NONCE_KEY',        'heU;i+e?H1w}D[3f:)s:$J>H%+EUM/T(p$8qLQ;|(7D3_S0/FtWhD bPjilzbgP.');
define('AUTH_SALT',        'Z`&Pc`/|wjV?V5h|I6Ag`|j]?)19-$+`xP$apr(7SNk=A5(egLWt0xvdHYB~|P+;');
define('SECURE_AUTH_SALT', 'Weu6D g`Q]))B2eM~?LSvo05P<9(.nv4Ue?9zEQYUV0=y3_|bsg#}X3CC.9}AP{X');
define('LOGGED_IN_SALT',   'PRxSA>AL)gbBb3Pwn$S@X~po (]Z<e!A$rZrJno9w-RE9zF>!nxtBVm0$_?%N]jG');
define('NONCE_SALT',       '`l?TA~3|/tey>GN+>>X%R/Weno%{y}#hORceNE0$G-D0x=o#!]ta4X.X2(bk-RFI');
define( 'WP_CACHE_KEY_SALT', '#gy-u(A0]+vna& tvX85). c|kgQxGUG}#&NuedV|SfFw,K(<(%BoDbhtY02jC>y' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */

/*
 * Custom Content Directory
 */
if ( defined( 'WP_CLI' ) ) {
    $_SERVER['HTTP_HOST'] = 'HTTP_HOST';
}
define( 'WP_CONTENT_DIR', __DIR__ . '/wp-content' );
define( 'WP_CONTENT_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/wp-content' );

/*
 * Environment
 */
define( 'WP_ENVIRONMENT_TYPE', 'local' );


/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/wp/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
