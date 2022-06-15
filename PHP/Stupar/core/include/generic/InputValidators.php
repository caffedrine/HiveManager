<?php
/** @noinspection PhpUnused */

function IsValidEmail($email, $validate_host = false)
{
    $result = false;
    if (!empty($email) && is_string($email) && (strlen($email) > 5 && strlen($email) < 96))
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $result = true;
        }

        if ($result === true)
        {
            if ($validate_host === true)
            {
                //Get host name from email and check if it is valid
                $email_host = array_slice(explode("@", $email), -1)[0];

                // Check if valid IP (v4 or v6). If it is we can't do a DNS lookup
                if (!filter_var($email_host, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,]))
                {
                    //Add a dot to the end of the host name to make a fully qualified domain name
                    // and get last array element because an escaped @ is allowed in the local part (RFC 5322)
                    // Then convert to ascii (http://us.php.net/manual/en/function.idn-to-ascii.php)
                    $email_host = idn_to_ascii($email_host . '.');

                    //Check for MX pointers in DNS (if there are no MX pointers the domain cannot receive emails)
                    if (!checkdnsrr($email_host, "MX"))
                    {
                        $result = false;
                    }
                }
            }
        }
    }
    return $result;
}

function IsValidName($name)
{
    $result = false;
    if (IsNumericOrString($name, 2, 50))
    {
        if (preg_match("/^[a-zA-Z\-' .)(]+$/", (string)$name))
        {
            $result = true;
        }
    }

    return $result;
}

function IsValidSpecialName($name)
{
    $result = false;
    if (IsNumericOrString($name, 1, 50))
    {
        if (preg_match("/^[a-zA-Z\-' .)(]+$/", (string)$name))
        {
            $result = true;
        }
    }

    return $result;
}

function IsValidVAT($vat_code)
{
    $result = false;
    if (IsNumericOrString($vat_code, 2, 50))
    {
        if (preg_match("/^[a-zA-Z0-9\-'_ .)(]+$/", (string)$vat_code))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidCompanyName($company)
{
    $result = false;
    if (IsNumericOrString($company, 2, 50))
    {
        if (preg_match("/^[A-Za-z0-9\-'._ (),]+$/", (string)$company))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidAddress($address)
{
    $result = false;
    if (IsNumericOrString($address, 5, 50))
    {
        if (preg_match("/^[A-Za-z0-9\-'._ ,]+$/", $address))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidCity($city)
{
    $result = false;
    if (IsNumericOrString($city, 2, 50))
    {
        if (preg_match("/^[A-Za-z0-9\-'. _]+$/", (string)$city))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidStateRegion($state_region)
{
    $result = false;
    if (IsNumericOrString($state_region, 2, 70))
    {
        if (preg_match("/^[A-Za-z0-9\-'. \"_]+$/", (string)$state_region))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidZipCode($zipcode)
{
    $result = false;
    if (IsNumericOrString($zipcode, 2, 16))
    {
        $result = IsNumericPositive($zipcode);
    }
    return $result;
}

function IsValidCountry($country)
{
    $result = false;
    if (IsNumericOrString($country, 2, 50))
    {
        $result = Country_IsValidLong((string)$country);
    }
    return $result;
}

function IsValidPhoneNumber($phone_number)
{
    $result = false;
    if (IsNumericOrString($phone_number, 8, 50))
    {
        if (preg_match("/^[0-9+\-()]+$/", (string)($phone_number)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidPassword($sha512_password_hash)
{
    $result = false;
    if (IsNumericOrString($sha512_password_hash, 128, 512))
    {
        if (preg_match('/^[A-Za-z0-9]+$/', (string)$sha512_password_hash))
        {
            $result = true;
        }
    }
    return $result;
}

/** @noinspection NotOptimalRegularExpressionsInspection */
function IsValidStrongAlphanumPassword($password)
{
    $result = false;

    if (IsNumericOrString($password, 12, 32))
    {
        $number = preg_match('/[0-9](.*?)[0-9]/', (string)$password);
        $uppercase = preg_match('/[A-Z](.*?)[A-Z]/', (string)$password);
        $lowercase = preg_match('/[a-z](.*?)[a-z]/', (string)$password);

        if( $number && $uppercase && $lowercase )
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidCaptcha($captcha_text)
{
    $result = false;
    if (IsNumericOrString($captcha_text, 3, 128))
    {
        $result = Utils_IsAlphanumericRestricted($captcha_text);
    }
    return $result;
}

function IsValidActivationCode($code)
{
    $result = false;
    if (IsNumericOrString($code, 64, 128))
    {
        if (preg_match('/^[A-Za-z0-9]+$/', (string)($code)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidApiKey($api_key)
{
    $result = false;
    if (IsNumericOrString($api_key, 64, 128))
    {
        if (preg_match('/^[A-Za-z0-9]+$/', (string)($api_key)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidCheckbox($checked)
{
    $result = false;
    if (IsNumericOrString($checked, 1, 5))
    {
        if (((int)($checked) === 1) || ((int)($checked) === 0) || ((string)$checked === "on") || ((string)$checked) === "off")
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidBool($checked)
{
    $result = false;
    if (IsNumericOrString($checked, 1, 5))
    {
        if (((int)($checked) === 1) || ((int)($checked) === 0))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidCsrfToken($token)
{
    $result = false;
    if (IsNumericOrString($token, 16, 1024))
    {
        if (preg_match('/^[A-Za-z0-9\-]+$/', (string)($token)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidPwresetToken($token)
{
    $result = false;
    if (IsNumericOrString($token, 32, 1024))
    {
        if (preg_match('/^[A-Za-z0-9\-]+$/', (string)($token)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidGeneralToken($token)
{
    $result = false;
    if (IsNumericOrString($token, 16, 256))
    {
        if (preg_match('/^[A-Za-z0-9\-]+$/', (string)($token)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidTicketId($ticket_id)
{
    $result = false;
    if (IsNumericOrString($ticket_id, 1, 16))
    {
        $result = IsNumericPositive($ticket_id);
    }
    return $result;
}

function IsValidCampaignId($campaign_id)
{
    $result = false;
    if (IsNumericOrString($campaign_id, 1, 16))
    {
        $result = IsNumericPositive($campaign_id);
    }
    return $result;
}

function IsValidInvoiceId($invoice_id)
{
    $result = false;
    if (IsNumericOrString($invoice_id, 1, 16))
    {
        $result = IsNumericPositive($invoice_id);
    }
    return $result;
}

function IsValidAbuseId($abuse_id)
{
    $result = false;
    if (IsNumericOrString($abuse_id, 1, 16))
    {
        $result = IsNumericPositive($abuse_id);
    }
    return $result;
}

function IsValidEmailId($email_id)
{
    $result = false;
    if (IsNumericOrString($email_id, 1, 16))
    {
        $result = IsNumericPositive($email_id);
    }
    return $result;
}

function IsValidDepartment($department)
{
    $result = false;
    if (IsNumericOrString($department, 2, 50))
    {
        if (preg_match("/^[A-Za-z0-9\-_]+$/", (string)($department)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidAbuseCategory($department)
{
    $result = false;
    if (IsNumericOrString($department, 2, 50))
    {
        if (preg_match("/^[A-Za-z0-9\-_]+$/", (string)($department)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidSubjectString($subject)
{
    $result = false;
    if (IsNumericOrString($subject, 2, 100))
    {
        if (preg_match("/^[A-Za-z0-9\-\[\]._, ?!':@#$%^&*(€)+=;|\/\"]+$/u", (string)($subject)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidTicketMessage($message)
{
    $result = false;
    if (IsNumericOrString($message, 2, 50000))
    {
        if (preg_match("/^[A-Za-z0-9\-.,_ ~`!@#%U+20AC^$&*(){}€<>\[\]:;'\"?=\/\s]+$/u", (string)$message))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidPostTitle($subject)
{
    $result = false;
    if (IsNumericOrString($subject, 2, 80))
    {
        if (preg_match("/^[A-Za-z0-9\-\[\]._, ?!]+$/", (string)($subject)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidAbuseReply($reply)
{
    $result = false;
    if (IsNumericOrString($reply, 2, 50000))
    {
        return true;
        //$result = Utils_IsAlphanumericExtended($reply);
    }
    return $result;
}

function IsValidUserID($user_id)
{
    $result = false;
    if (IsNumericOrString($user_id, 1, 32))
    {
        $result = IsNumericPositive($user_id);
    }
    return $result;
}

function IsValidServiceID($service_id)
{
    $result = false;
    if (IsNumericOrString($service_id, 1, 32))
    {
        $result = IsNumericPositive($service_id);
    }
    return $result;
}

function IsValidResourceID($service_id)
{
    $result = false;
    if (IsNumericOrString($service_id, 1, 32))
    {
        $result = IsNumericPositive($service_id);
    }
    return $result;
}

function Utils_IsAlphanumericAndUnderscore($input, $max_length = 1024)
{
    $result = false;
    if (IsNumericOrString($input, 0, $max_length))
    {
        if (preg_match("/^\w+$/", (string)($input)))
        {
            # Cannot start with underscore
            if( !Utils_StringStartsWith($input, "_") )
            {
                return true;
            }
            $result = false;
        }
    }
    return $result;
}

function Utils_IsAlphanumericRestricted($input, $max_length = 1024)
{
    $result = false;
    if (IsNumericOrString($input, 0, $max_length))
    {
        if (preg_match("/^[A-Za-z0-9\-.,_() ]+$/", (string)($input)))
        {
            $result = true;
        }
    }
    return $result;
}

function Utils_IsAlphanumeric($input, $max_length = 1024)
{
    $result = false;
    if (IsNumericOrString($input, 0, $max_length))
    {
        if (preg_match("/^[A-Za-z0-9]+$/", (string)($input)))
        {
            $result = true;
        }
    }
    return $result;
}

function Utils_IsAlphanumericExtended($input, $max_length = 10000)
{
    $result = false;
    if (IsNumericOrString($input, 0, $max_length))
    {
        if (preg_match('/^[A-Za-z0-9\-.,_ !@#$&*()\[\]:;\'"?+=\/\s]+$/', (string)$input))
        {
            $result = true;
        }
    }
    return $result;
}

function Utils_IsValidHttpGetString($input, $max_size = 10000)
{
    $result = false;
    if (IsNumericOrString($input, 0, $max_size))
    {
        if (preg_match('/^[A-Za-z0-9\-.,_@#$&*()\[\];\'"?+=\/\s]+$/', (string)$input))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidDateTimeStr($date_Time)
{
    if (IsNumericOrString($date_Time, 1, 128))
    {
        $format1 = (date('Y-m-d H:i:s', strtotime($date_Time)) === (string)$date_Time);
        $format2 = (date('Y-m-d H:i', strtotime($date_Time)) === (string)$date_Time);
        $format3 = (date('Y-m-d\TH:i', strtotime($date_Time)) === (string)$date_Time);
        $format4 = (date('Y-m-d\TH:i:s', strtotime($date_Time)) === (string)$date_Time);
        $format5 = (date(Utils_GetDateTimeFormatString(), strtotime($date_Time)) === (string)$date_Time);
        return ($format1 || $format2 || $format3 || $format4 || $format5);
    }
    else
    {
        return false;
    }
}

function IsValidDateStr($date)
{
    if (IsNumericOrString($date, 1, 50))
    {
        $format1 = (date(Utils_GetDateFormatString(), strtotime($date)) === (string)$date);
        return ($format1);
    }
    else
    {
        return false;
    }
}

function IsValidTimeStr($date)
{
    if (IsNumericOrString($date, 1, 50))
    {
        $format1 = (date(Utils_GetTimeFormatString(), strtotime($date)) === (string)$date);
        $format2 = (date('H:i', strtotime($date)) === (string)$date);
        return ($format1 || $format2);
    }
    else
    {
        return false;
    }
}

function IsValidIPAddress($ip_address)
{
    if (!filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
    {
        if (filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
        {
            return true; # Valid IPv6
        }
        else
        {
            return false;
        }
    }
    else
    {
        return true; # Valid IPv4
    }
}

function IsValidIPv4Address($ip_address)
{
    if (!filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
    {
        return false;
    }
    else
    {
        return true; # Valid IPv4
    }
}

function IsPublicIPv4Address($ip_address)
{
    return filter_var(
        $ip_address,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    );
}

function IsValidConfigurationNameOrType($CfgNameOrType)
{
    $result = false;
    if (IsNumericOrString($CfgNameOrType, 2, 50))
    {
        if (preg_match("/^[a-zA-Z0-9\-_]+$/", (string)$CfgNameOrType))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidConfigurationID($config_id)
{
    $result = false;
    if (IsNumericOrString($config_id, 1, 20))
    {
        $result = IsNumericPositive($config_id);
    }
    return $result;
}

function IsValidConfigurationParam($CfgParam)
{
    $result = false;
    if (IsNumericOrString($CfgParam, 0, 1024))
    {
        $result = Utils_IsAlphanumericRestricted($CfgParam);
    }
    return $result;
}

function IsValidAdminID($admin_id)
{
    $result = false;
    if (IsNumericOrString($admin_id, 1, 20))
    {
        $result = IsNumericPositive($admin_id);
    }
    return $result;
}

function IsValidBanOrCloseAccountReason($reason)
{
    $result = false;
    if (IsNumericOrString($reason, 2, 80))
    {
        if (preg_match("/^[A-Za-z0-9\-._ ?!]+$/", (string)($reason)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidReasonDescription($reason)
{
    $result = false;
    if (IsNumericOrString($reason, 2, 512))
    {
        if (preg_match("/^[A-Za-z0-9\-._ ?!\[\]]+$/", (string)($reason)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidAdminNote($note)
{
    $result = false;
    if (IsNumericOrString($note, 2, 5000))
    {
        $result = true;
    }
    return $result;
}

function IsValidPhpFileName($file_name)
{
    if (IsNumericOrString($file_name, 1, 256))
    {
        if (preg_match('/^[a-zA-Z0-9-_]+\.php$/', (string)$file_name))
        {
            return true;
        }
    }
    return false;
}

function IsValidCronLogMessage($cron_msg)
{
    $result = false;
    if (IsNumericOrString($cron_msg, 1, 2000))
    {
        $result = true;
    }

    return $result;
}

function IsValidCronDescription($cron_desc)
{
    $result = false;
    if (IsNumericOrString($cron_desc, 1, 2000))
    {
        $result = true;
    }

    return $result;
}

function IsValidOfferId($offer_id)
{
    $result = false;
    if (IsNumericOrString($offer_id, 1, 32))
    {
        if (preg_match('/^[A-Za-z0-9\-]+$/', (string)($offer_id)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidDedicatedServerOsId($os_id)
{
    $result = false;
    if (IsNumericOrString($os_id, 2, 16))
    {
        if (preg_match('/^[A-Za-z0-9]+$/', (string)($os_id)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidHtmlCheckbox($html_checkbox)
{
    $result = false;
    if (IsNumericOrString($html_checkbox, 1, 5))
    {
        if (preg_match('/^[A-Za-z0-9]+$/', (string)($html_checkbox)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidServerOsType($os_type)
{
    $result = false;
    if (IsNumericOrString($os_type, 2, 32))
    {
        if (preg_match('/^[A-Za-z0-9_-]+$/', (string)($os_type)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsValidPaymentProcessorName($payment_processor)
{
    $result = false;
    if (IsNumericOrString($payment_processor, 2, 32))
    {
        if (preg_match("/^[a-zA-Z0-9\-_]+$/", (string)$payment_processor))
        {
            $result = true;
        }
    }

    return $result;
}

function IsValidSubmenuName($submenu_name)
{
    $result = false;
    if (IsNumericOrString($submenu_name, 1, 64))
    {
        if (preg_match("/^[A-Za-z0-9\-._ ]+$/", (string)($submenu_name)))
        {
            $result = true;
        }
    }
    return $result;
}

function IsNumericPositive($number)
{
    return is_numeric($number) && ((int)$number >= 0);
}

function IsValidDomainName($domain_name)
{
    $result = false;
    if (IsNumericOrString($domain_name, 1, 1024))
    {
        return filter_var($domain_name, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);
    }
    return $result;
}

function IsValidHostname($hostname)
{
    if( Utils_IsAlphanumeric($hostname, 256) )
    {
        return !(is_numeric($hostname[0]));
    }
    return false;
}

function IsNumericOrString($input, int $min_length, int $max_length)
{
    if ((!(is_string($input))) && (!is_numeric($input)))
    {
        return false;
    }

    $length = strlen((string)($input));
    return !(($length > $max_length) || ($length < $min_length));
}

function IsValidBase64Encoded($s){
    // Check if there are valid base64 characters
    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;

    // Decode the string in strict mode and check the results
    $decoded = base64_decode($s, true);
    if(false === $decoded) return false;

    // Encode the string again
    if(base64_encode($decoded) != $s) return false;

    return true;
}