<?php
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
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',          '-Z#s<Dxu5(5Ef<m`(te?hAs&gf8rR2UoWJp8>S$oW-:H%>g!9A*&t|n!D%jW=>],' );
define( 'SECURE_AUTH_KEY',   '<5=vP&Os3RgK+@Z8^ZcNnMLl77R):S.>c<}d.Wfg !phU7{Id|3fL7t,D]r%t%Xv' );
define( 'LOGGED_IN_KEY',     '7^2AAKU~QT#P%_rcGm+4&Lz!Dk7PiqQ7bS6{D84}{4-q{ou{!i2Z[70G9Pmi&^ h' );
define( 'NONCE_KEY',         '>>x<z&Sm}k=M&&p+A[1]d@sQFe 7.5!C8H75abbOLwTY9=sJ1{H vSW^A? >&Hf ' );
define( 'AUTH_SALT',         'PF+%cR:0yX v--wci@&,KPU#pHmf?q%+>p$gRV5*U^8Z6;r7LpqgIb&(NV}Gu30x' );
define( 'SECURE_AUTH_SALT',  'C}UV`wh*I-RAndgsgLCT;r5/A9pB]X&aqjy3L;TT&b brY<:#E`I~UT[zj9pe/$*' );
define( 'LOGGED_IN_SALT',    '.7_:Nh}Nu9n#Om!- bSOb>9C1)2p=FFe%#,T* WJWRtm|i&+}a?f);Mpzsb^SkMn' );
define( 'NONCE_SALT',        '-ApZP=z8fz;QgT/ALVuNajvkcY2_wK)cMw {6E,Csx2R1*+ntE6pXK0k}Jq2@my0' );
define( 'WP_CACHE_KEY_SALT', 'G=W<81*KOl?y%k0krLFIP~)Aw05kW=Q7yo_E$I#A;U1%x}aRw^SvN]w+H7|H3.{b' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
