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
define( 'DB_NAME', 'techobio.net_master' );

/** MySQL database username */
define( 'DB_USER', 'techobio.net_master' );

/** MySQL database password */
define( 'DB_PASSWORD', '0Lr;NokHI}IFe(h0' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'BKnuJN]/Ij6:CE}{1Gth6*z%]!OPphc}Hs3d/ZaM cUe|<&KBNZ] (>~.(!~P}LO' );
define( 'SECURE_AUTH_KEY',  '1d]XVj%Rd,jeo;X)VOFHo5|=@,}yZ}GCWgLC?8hW}}59ESBYZj}6<@ w,wh)6f1S' );
define( 'LOGGED_IN_KEY',    '5UMDFG?q+AgCVFB/4jio;?JNAP?iBlEPj(.5r:7)PWuP?]_d~);5.|.C9fBwH$nX' );
define( 'NONCE_KEY',        '+RVl^0~ub-7nnDcWiQalSV^W;.6U<ge.XuIp8n*:]!w?z0N<z!`xe/O|d=V($Z~@' );
define( 'AUTH_SALT',        'c,Dghso[-^WSers)eMHu>S1UW]oIi^TlfSyr{8#Fm:5*Hbub?N|R?J%cJ+L@;o2o' );
define( 'SECURE_AUTH_SALT', '<R>V(DYk^f<~D+%;Q*92-/Yt|PGnXMHxlKJI_-dr}AL,/=J9u@t{0R()e4pLcKvc' );
define( 'LOGGED_IN_SALT',   'ycI$u_cNPD,SUs4iI`4)|t9A` tLLU5x^9bLof Ig I2NulM*b^>{L3b+;P?a@^~' );
define( 'NONCE_SALT',       'Adx*X5n?mfaiWSUDK!P%Eu8EDzA-u.mHt? P?-xbW@ZK#M6v>H ct9PwLY?)C:h/' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
