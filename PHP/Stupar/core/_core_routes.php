<?php

if( !defined('ROUTER_INCLUDED') ) die("Down for maintenance: 0x754876234");

abstract class CORE_ROUTES
{
    public const HOME = "/home";
    public const TICKETS = "/tickets";
    public const CONTACT = "/contact";
    public const ABOUT = "/about";
    public const KB = "/kb";

    public const CONSOLE = RELATIVE_PATH_CONSOLE;
    public const CONSOLE_HOME = self::CONSOLE;
    public const USER_LOGIN = self::CONSOLE . "/account/login";
    public const USER_REGISTER = self::CONSOLE . "/account/register";
    public const USER_PWRESET = self::CONSOLE . "/account/pwreset";
    public const USER_ACTIVATE = self::CONSOLE . "/account/activate";
    public const USER_LOGOUT = self::CONSOLE . "/account/logout";
    public const CONSOLE_EMAILS_HISTORY = self::CONSOLE . "/emails-history";
    public const USER_ACCOUNT = self::CONSOLE . "/account";
    public const USER_ACCOUNT_SETTINGS = self::CONSOLE . "/account/settings";
    public const USER_SERVICES = self::CONSOLE . "/my-services";
    public const CONSOLE_PAYMENTS = self::CONSOLE . "/payments";
    public const CONSOLE_INVOICES = self::CONSOLE . "/invoices";
    public const USER_PAY =  self::CONSOLE . "/pay";
    public const USER_ORDER_SERVICES =  self::CONSOLE . "/order";

    public const ADMIN = RELATIVE_PATH_ADMIN;
    public const ADMIN_HOME = self::ADMIN;
    public const ADMIN_LOGIN = self::ADMIN . "/account/login";
    public const ADMIN_PWRESET = self::ADMIN . "/account/pwreset";
    public const ADMIN_ACTIVATE = self::ADMIN . "/account/activate";
    public const ADMIN_LOGOUT = self::ADMIN . "/account/logout";
    public const ADMIN_SETTINGS = self::ADMIN . "/account/settings";
    public const ADMIN_MAILS_HISTORY = self::ADMIN . "/account/mails-history";
    public const ADMIN_LOGS = self::ADMIN . "/logs";
    public const ADMIN_SESSIONS = self::ADMIN . "/sessions";
    public const ADMIN_TOKENS = self::ADMIN . "/tokens";
    public const ADMIN_STATS = self::ADMIN . "/stats";
    public const ADMIN_TOOLS_EMAIL_CAMPAIGNS = self::ADMIN . "/tools/email-campaigns";
    public const ADMIN_TOOLS_LINKS_TRACKING = self::ADMIN . "/tools/links-tracking";
    public const ADMIN_TOOLS_BLACKLISTING_WAF = self::ADMIN . "/tools/blacklisting-waf";
    public const ADMIN_MANAGE_STAFF = self::ADMIN . "/manage/admins";
    public const ADMIN_MANAGE_CRONJOBS = self::ADMIN . "/manage/cronjobs";
    public const ADMIN_MANAGE_CONFIG = self::ADMIN . "/manage/config";

    public const ADMIN_TOOLS_PAYPAL_LOOKUP = self::ADMIN . "/tools/paypal-lookup";
    public const ADMIN_MANAGE_PAYMENTS = self::ADMIN . "/payments";
    public const ADMIN_MANAGE_INVOICES = self::ADMIN . "/invoices";
    public const ADMIN_MANAGE_AUTOMATIC_PAYMENTS = self::ADMIN . "/automatic-payments";
    public const ADMIN_MANAGE_ORDERS = self::ADMIN . "/orders";

    public const ADMIN_MANAGE_TICKETS = self::ADMIN . self::TICKETS;
    public const ADMIN_MANAGE_USERS = self::ADMIN . "/users";

    public const USER_API = "/api/user";
    public const CRONS = "/crons";
}

if( !isset($ROUTER) )
    return;

#    _______  _______ _____ ____  _   _
#   | ____\ \/ /_   _| ____|  _ \| \ | |
#   |  _|  \  /  | | |  _| | |_) |  \| |
#   | |___ /  \  | | | |___|  _ <| |\  |
#   |_____/_/\_\ |_| |_____|_| \_\_| \_|
#
/** @route / */
$ROUTER->get("/", static function() { exit(require DOC_ROOT . "/home.php"); });
/**  @route /home */
$ROUTER->get(CORE_ROUTES::HOME, static function() { exit(require DOC_ROOT . "/home.php"); });


