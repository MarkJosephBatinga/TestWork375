<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'testwork375' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '7$N0D:?dL%wl{T;H(i5!UR2E#>VP^_eEk2FH9D~;+Gn>$C@rX?~iExDe;:p&A_q%' );
define( 'SECURE_AUTH_KEY',  'Ayk{vP.w0B_1w#YL[!lq4%w4-)8=J`Vgp!I5U@IJ/ jo@G7 > .)FtFkaI8wr^Xx' );
define( 'LOGGED_IN_KEY',    'yH@/ <R6Zc&bSW,wK$Fzxy[Q28H@ygLxTD`L8t F:KWp8~<]e!9@e@[orB+L@vq1' );
define( 'NONCE_KEY',        'Di-yI)<g;`RvXADyU%~[J@llUctCDV_hZd2?a:w/[H0^|YBtK0,>r[vI>]@l3&b[' );
define( 'AUTH_SALT',        'nExsXqz|z;Pw6|day:WN:67Cx-stJoO7=jV2,~Ci[x.!`4SMzWFu,VM-6}1H+bhq' );
define( 'SECURE_AUTH_SALT', 'giORN_CHBBeO!Ci|WaEQ}b_]nE]gS_mK@[au<<,(YPnW<^TClMQp~4h0:`HG`zYo' );
define( 'LOGGED_IN_SALT',   'Dx1U=Q]iGZ#BjB_xlg0+.>@&%Av]OlHwR>40S<RD+<8Ce-+{7B-AF:>?&<[wLv7-' );
define( 'NONCE_SALT',       'ld}yOGM4$8l5ARooC<m7(vCI,su5iMO#@o3juzTtGwWz!UX3!;+$?W}or1&V%hZj' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
