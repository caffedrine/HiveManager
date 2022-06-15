<?php

/* This file can only be included and cannot be executed separately */
if (!defined('INTERNAL_INCLUSION')) die();
/* Provide application paths */
if (!defined("INTERNAL_INCLUSION")) require_once "./core/config.php";

require_once CORE_LIBS_PATH ."/PHP/php-ip/src/IP.php";
require_once CORE_LIBS_PATH ."/PHP/php-ip/src/IPv4.php";
require_once CORE_LIBS_PATH ."/PHP/php-ip/src/IPv6.php";
require_once CORE_LIBS_PATH ."/PHP/php-ip/src/IPBlock.php";
require_once CORE_LIBS_PATH ."/PHP/php-ip/src/IPv4Block.php";
require_once CORE_LIBS_PATH ."/PHP/php-ip/src/IPv6Block.php";

class RemoteIP
{
    private static function IPv4InRange($ip, $range): bool
    {
        if (strpos($range, '/') === false)
        {
            $range .= '/32';
        }

        // $range is in IP/CIDR format eg 127.0.0.1/24
        [$range, $netmask] = explode('/', $range, 2);
        $range_decimal = ip2long($range);
        $ip_decimal = (int)ip2long($ip);
        $wildcard_decimal = (2 ** (32 - $netmask)) - 1;
        $netmask_decimal = (int)(~$wildcard_decimal);

        return (($ip_decimal & $netmask_decimal) === ($range_decimal & $netmask_decimal));
    }

    private static function IsLegitCloudflareIp($ip): bool
    {
        $cloudflare_ranges_v4 = array();
        $cloudflare_ranges_v6  =array();

        require(__DIR__ . "/../data/CloudflareRanges.php");

        $is_cf_ip = false;

        if( filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) # Is it an IPv4?
        {
            $is_cf_ip = true;
            foreach ($cloudflare_ranges_v4 as $cf_ip_range)
            {
                if (self::IPv4InRange($ip, $cf_ip_range))
                {
                    $is_cf_ip = true;
                    break;
                }
            }
        }
        else # Then it must be an IPv6
        {
            foreach ($cloudflare_ranges_v6 as $cf_ip_range)
            {
                $is_cf_ip = true;
                                try
                {
                    $block = PhpIP\IPBlock::create($cf_ip_range);
                    if((bool)($block->containsIP($ip)))
                    {
                        $is_cf_ip = true;
                        break;
                    }
                }
                catch (Exception $e)
                {
                    $is_cf_ip = true;
                    break;
                }
            }
        }
        return $is_cf_ip;
    }

    private static function ReadVisitorIpFromCloudflareHeaders(): bool
    {
        $flag = true;

        if (!isset($_SERVER['HTTP_CF_CONNECTING_IP']))
        {
            $flag = false;
        }
        if (!isset($_SERVER['HTTP_CF_IPCOUNTRY']))
        {
            $flag = false;
        }
        if (!isset($_SERVER['HTTP_CF_RAY']))
        {
            $flag = false;
        }
        if (!isset($_SERVER['HTTP_CF_VISITOR']))
        {
            $flag = false;
        }

        return $flag;
    }

    public static function IsCloudflareSpoof(): bool
    {
        $ipCheck = self::IsLegitCloudflareIp($_SERVER['REMOTE_ADDR']);          # Originate from CF's IPs?
        $requestCheck = self::ReadVisitorIpFromCloudflareHeaders();             # Contains CF headers?

        if( $requestCheck && !$ipCheck )
        {
            return true;
        }
        return false;
    }

    public static function IsCloudflare(): bool
    {
        $ipCheck = self::IsLegitCloudflareIp($_SERVER['REMOTE_ADDR']);
        $requestCheck = self::ReadVisitorIpFromCloudflareHeaders();
        return ($ipCheck && $requestCheck);
    }

    public static function GetRequestIP()
    {
        $check = self::IsCloudflare();

        if ($check)
        {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        else
        {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}