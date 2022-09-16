<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define('DB_SERVER', 'localhost');
define('DB_USER', '3fs_db');
define('DB_PASS', 'odu59*D4');
define('DB_DATABASE', '3fs_db');
define("TBL_PREFIX", "vny_");
define("SALT", "2HkfPfcPU9Uer2y");

define('IMAGE_PATH',$_SERVER['DOCUMENT_ROOT'] . '/3fscp/');
define('SITE_URL',"https://ainra.co/3fscp/");
define('DISPLAY_PATH',"https://ainra.co/3fscp/");

define("TBL_ANNOUNCEMENT", TBL_PREFIX . "announcement");
define("TBL_BIG_PRESENT", TBL_PREFIX . "big_present");
define("TBL_BIG_PRESENT_LOG", TBL_PREFIX . "big_present_log");
define("TBL_BIG_PRESENT_PACKAGE", TBL_PREFIX . "big_present_package");
define("TBL_CART", TBL_PREFIX . "cart");
define("TBL_CART_REMIND", TBL_PREFIX . "cart_remind");
define("TBL_CATEGORY", TBL_PREFIX . "category");
define("TBL_CB_POINT", TBL_PREFIX . "cb_point");
define("TBL_COLOR_THEME", TBL_PREFIX . "color_theme");
define("TBL_COMPANY", TBL_PREFIX . "company");
define("TBL_COURSE", TBL_PREFIX . "course");
define("TBL_COURSE_ATTACHMENT", TBL_PREFIX . "course_attachment");
define("TBL_COURSE_DETAILS", TBL_PREFIX . "course_details");
define("TBL_COMPANY_CONTENT", TBL_PREFIX . "company_content");
define("TBL_COMPANY_SECTION", TBL_PREFIX . "company_section");
define("TBL_COMPANY_TOPUP", TBL_PREFIX . "company_topup");
define("TBL_CURRENCY", TBL_PREFIX . "currency");
define("TBL_DELIVERY_FEE", TBL_PREFIX . "delivery_fee");
define("TBL_DRB_REPORT", TBL_PREFIX . "drb_report");
define("TBL_FREE_PACKAGE", TBL_PREFIX . "free_package");
define("TBL_FREE_VOUCHER", TBL_PREFIX . "free_voucher");
define("TBL_GALLERY", TBL_PREFIX . "gallery");
define("TBL_GALLERY_ATTACHMENT", TBL_PREFIX . "gallery_attachment");
define("TBL_GLOBAL_PRICE", TBL_PREFIX . "global_price");
define("TBL_LIMIT_PACKAGE", TBL_PREFIX . "limit_package");
define("TBL_MMS_BONUS", TBL_PREFIX . "mms_bonus");
define("TBL_MMS_REPORT", TBL_PREFIX . "mms_report");
define("TBL_MONTHLY_BONUS", TBL_PREFIX . "monthly_bonus");
define("TBL_MONTHLY_BONUS_REPORT", TBL_PREFIX . "monthly_bonus_report");
define("TBL_OAUTH_TOKEN", TBL_PREFIX . "oauth_token");
define("TBL_ORDER", TBL_PREFIX . "order");
define("TBL_ORDER_DETAIL", TBL_PREFIX . "order_detail");
define("TBL_OTP_LOGS", TBL_PREFIX . "otp_logs");
define("TBL_PACKAGE", TBL_PREFIX . "package");
define("TBL_PACKAGE_VOUCHER", TBL_PREFIX . "package_voucher");
define("TBL_POINT", TBL_PREFIX . "point");
define("TBL_PRODUCT", TBL_PREFIX . "product");
define("TBL_PRODUCT_PRICE", TBL_PREFIX . "product_price");
define("TBL_PROMOTION", TBL_PREFIX . "promotion");
define("TBL_PROMOTION_LOG", TBL_PREFIX . "promotion_log");
define("TBL_PURCHASE_PACKAGE", TBL_PREFIX . "purchase_package");
define("TBL_PV", TBL_PREFIX . "pv");
define("TBL_QUARTERLY_BONUS", TBL_PREFIX . "quarterly_bonus");
define("TBL_QUARTERLY_BONUS_REPORT", TBL_PREFIX . "quarterly_bonus_report");
define("TBL_RB_VOUCHER", TBL_PREFIX . "rb_voucher");
define("TBL_RB_WALLET", TBL_PREFIX . "rb_wallet");
define("TBL_READ_ANNOUNCEMENT", TBL_PREFIX . "read_announcement");
define("TBL_RESET_PASSWORD_LOG", TBL_PREFIX . "reset_password_log");
define("TBL_RESTOCK_VOUCHER", TBL_PREFIX . "restock_voucher");
define("TBL_SHIPMENT_VOUCHER", TBL_PREFIX . "shipment_voucher");
define("TBL_SLIDER", TBL_PREFIX . "slider");
define("TBL_SMART_PARTNER", TBL_PREFIX . "smart_partner");
define("TBL_SMART_PARTNER_PASS_UP", TBL_PREFIX . "smart_partner_pass_up");
define("TBL_SPECIAL_PACKAGE", TBL_PREFIX . "special_package");
define("TBL_STOCK", TBL_PREFIX . "stock");
define("TBL_TICKET", TBL_PREFIX . "ticket");
define("TBL_TICKET_REPLY", TBL_PREFIX . "ticket_reply");
define("TBL_TRANSACTION_LOG", TBL_PREFIX . "transaction_log");
define("TBL_TRANSFER", TBL_PREFIX . "transfer");
define("TBL_UPGRADE", TBL_PREFIX . "upgrade");
define("TBL_USER", TBL_PREFIX . "user");
define("TBL_USER_ADDRESS", TBL_PREFIX . "user_address");
define("TBL_USER_BIG_PRESENT_FREE", TBL_PREFIX . "user_big_present_free");
define("TBL_USER_VOUCHER", TBL_PREFIX . "user_voucher");
define("TBL_VOUCHER_LOG", TBL_PREFIX . "voucher_log");
define("TBL_WALLET", TBL_PREFIX . "wallet");
define("TBL_WITHDRAW", TBL_PREFIX . "withdraw");
define("TBL_YUE_JI_LOG", TBL_PREFIX . "yue_ji_log");