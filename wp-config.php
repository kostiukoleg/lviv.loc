<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'lviv');

/** Имя пользователя MySQL */
define('DB_USER', 'olegkostiuk');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'Kostiuk_6173');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '$N%??%uhj./79OY7l|;0Ucv4mY:zT@zg6kP8So<jb0OJuQ/#tX:yZQ*>MqtOx9<$');
define('SECURE_AUTH_KEY',  '..4yx@2H;cLCt*#&=SAFd5&BvrX>EbGjKtbaL@h=&ylV.;0j0bXzu8ugSzS~e>3u');
define('LOGGED_IN_KEY',    '<gu=V8DS%YJm?l=5E.T.BDJQ,^v]yJB*j=G#5Bn(^H 1625l}h*T`PlUDcnP>x&s');
define('NONCE_KEY',        '[TS/U*[*]NE&U@KHKS0`mQQUlD}x5=Gi~R{P{ej_b2F<I.Cd$ RoKy@2YW@NS/Q>');
define('AUTH_SALT',        ')_^LM/8xXnpr6EX`o)P)wW3{<^Tu7:&p/Y56p8r2x0.?:f|Eqm}_$>8HS3w[qbVc');
define('SECURE_AUTH_SALT', 'Ms=T(@l}Fb4SxGsN);Cu]v8Tb,d)B(tm:zov>76>GQPqNAYTMv6+x*ITx5#yzcea');
define('LOGGED_IN_SALT',   'CruEH;5h_.Q.ZsoCc1tV?)`c7uv3o:+X3n67XF;=O(u4<Qo}7x)wv4nvMWjoe{Dt');
define('NONCE_SALT',       'b<zN{Ar;-ns#u[@36!~5eCEOoMLeC6f3bEf0oKX0qbW442p*Ib*_+IY9Y}5Q$O[R');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
