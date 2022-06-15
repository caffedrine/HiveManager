<?php /** @noinspection SummerTimeUnsafeTimeManipulationInspection */
/** @noinspection PhpUnused */

function Utils_GetDateTimeFormatString(): string
{
    return "Y-m-d H:i:s";
}

function Utils_GetDateFormatString(): string
{
    return "Y-m-d";
}

function Utils_GetTimeFormatString(): string
{
    return "H:i:s";
}

function Utils_GetCurrentDateTimeStr()
{
    return date(Utils_GetDateTimeFormatString());
}

function Utils_GetDateFromTimeStr($str)
{
    return date(Utils_GetDateFormatString(), strtotime($str));
}

function Utils_GetDateTimeFromTimeStr($str)
{
    return date(Utils_GetDateTimeFormatString(), strtotime($str));
}

function Utils_TimestampToDateTimeStr($timestamp)
{
    return date(Utils_GetDateTimeFormatString(), $timestamp);
}

function Utils_GetCurrentDateStr()
{
    return date(Utils_GetDateFormatString());
}

function Utils_GetCurrentTimeStr()
{
    return date(Utils_GetTimeFormatString());
}

function Utils_GetUserAgent()
{
    return $_SERVER['HTTP_USER_AGENT'] ?? "not_set";
}

function Utils_GetUserAcceptLanguageRAW()
{
    return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
}

function Utils_GetSecondsSinceEpoch()
{
    return time();
}

function Utils_GetMinutesSinceEpoch()
{
    return (int)(time() / 60);
}

function Utils_GetHoursSinceEpoch()
{
    return (int)(time() / 60 / 60);
}

function Utils_GetDaysSinceEpoch()
{
    return (int)(time() / 60 / 60 / 24);
}

function Utils_DateTimeStrToDaysSinceEpoch(string $date_time)
{
    $result = strtotime($date_time);
    if ($result)
    {
        # Convert seconds to days
        $result = (int)($result / 60 / 60 / 24);
    }
    else
    {
        $result = -1;
    }
    return $result;
}

function Utils_DateTimeStrToMinutesSinceEpoch(string $date_time)
{
    $result = strtotime($date_time);
    if ($result)
    {
        # Convert seconds to minutes
        $result = (int)($result / 60);
    }
    else
    {
        $result = -1;
    }
    return $result;
}

function Utils_DateTimeStrToSecondsSinceEpoch(string $date_time)
{
    return strtotime($date_time);
}

function Utils_DateStrToSecondsSinceEpoch(string $date)
{
    return strtotime($date);
}

function Utils_DateStrToDaysSinceEpoch(string $date)
{
    # Get seconds from date
    $result = strtotime($date);
    if ($result)
    {
        # Convert seconds to days
        $result = (int)($result / 60 / 60 / 24);
    }
    else
    {
        $result = -1;
    }
    return $result;
}

function Utils_StrContains($string, $substring)
{
    return strpos($string, $substring) !== false;
}

function Utils_StrSplit($string, $split_by)
{
    return explode($split_by, $string);
}

function Utils_DateTimeStrToDateStr($date_time_str)
{
    return date(Utils_GetDateFormatString(), Utils_DateTimeStrToSecondsSinceEpoch($date_time_str));
}

function Utils_DateTimeStrToStdDateTimeStr($date_time_str)
{
    return date(Utils_GetDateTimeFormatString(), Utils_DateTimeStrToSecondsSinceEpoch($date_time_str));
}

function Utils_DateStrToDateTimeStr($date_str)
{
    return date(Utils_GetDateTimeFormatString(), Utils_DateStrToSecondsSinceEpoch($date_str));
}

function Utils_GetClassVarsNames($class)
{
    $result = array();
    try
    {
        foreach ((new ReflectionClass($class))->getProperties() as $var)
        {
            $result[] = $var->getName();
        }
    }
    catch (Exception $e)
    {
    }
    return $result;
}

function Utils_GetClassMethodsNames($class)
{
    $result = array();
    try
    {
        foreach ((new ReflectionClass($class))->getMethods() as $var)
        {
            $result[] = $var->getName();
        }
    }
    catch (Exception $e)
    {
    }
    return $result;
}

function Utils_GetAllPublicClassVariablesNames($class_name): array
{
    $result = array();
    try
    {
        foreach( (new ReflectionClass($class_name))->getProperties() as $property )
        {
            $result[] = $property->name;
        }
    }
    catch (Exception $e)
    {
    }
    return $result;
}

function Utils_GetClassConstantsAsKeyVal($class_name): array
{
    $result = array();
    try
    {
        $result = (new ReflectionClass($class_name))->getConstants();
    }
    catch (Exception $e)
    {
    }
    return $result;
}

function Utils_GetAllClassObjects($class_name)
{
    return get_object_vars($class_name);
}

function Utils_GetAllObjectsFromStaticClass($class_name)
{
    $result = array();
    try
    {
        $result = (new ReflectionClass($class_name))->getStaticProperties();
    }
    catch (Exception $e)
    {
    }
    return $result;
}

function Utils_FilterHtmlString($unfiltered)
{
    return htmlentities($unfiltered, ENT_QUOTES | ENT_HTML5);
}

function Utils_TimeStrToHoursSinceEpoch($time_str)
{
    return (int)(Utils_TimeStrToSecondsSinceEpoch($time_str) / 60 / 60);
}

function Utils_TimeStrToMinutesSinceEpoch($time_str)
{
    return (int)(Utils_TimeStrToSecondsSinceEpoch($time_str) / 60);
}

function Utils_TimeStrToSecondsSinceEpoch($time_str)
{
    return strtotime($time_str);
}

function Utils_HoursSinceEpochToStrDateTime($hours_since_epoch)
{
    $seconds_since_epoch = $hours_since_epoch * 60 * 60;
    return date(Utils_GetDateTimeFormatString(), $seconds_since_epoch);
}

function Utils_SecondsSinceEpochToStrDateTime($seconds_since_epoch)
{
    return date(Utils_GetDateTimeFormatString(), $seconds_since_epoch);
}

function Utils_DaysSinceEpochToStrDateTime($days_since_epoch)
{
    $seconds_since_epoch = $days_since_epoch * 24 * 60 * 60;
    return date(Utils_GetDateTimeFormatString(), $seconds_since_epoch);
}

function Utils_DaysSinceEpochToStrDate(int $days_since_epoch): string
{
    $seconds_since_epoch = $days_since_epoch * 24 * 60 * 60;
    return date(Utils_GetDateFormatString(), $seconds_since_epoch);
}

function Utils_HoursSinceEpochToStrTime($hours_since_epoch): string
{
    $seconds_since_epoch = $hours_since_epoch * 60 * 60;
    return date(Utils_GetDateFormatString(), $seconds_since_epoch);
}

function Utils_MinutesSinceEpochToStrDateTime($minutes_since_epoch): string
{
    $seconds_since_epoch = $minutes_since_epoch * 60;
    return date(Utils_GetDateTimeFormatString(), $seconds_since_epoch);
}

function Utils_TimeStrToRFC3339(string $time_Str)
{
    return date('Y-m-d\TH:i:sP', strtotime($time_Str));
}

function Utils_ObjectStr($object): string
{
    return print_r($object, true);
}

function Utils_ObjectsPropertiesDifferences($obj1, $obj2, $readable_response = true): array
{
    $results = array();

    if( !is_object($obj1) || !is_object($obj2) )
    {
        return array("error" => "inputs are not objects");
    }

    if( get_class($obj1) !== get_class($obj2) )
    {
        return array("error" => "objects compared are of different types: " . get_class($obj1) . " vs " . get_class($obj2));
    }

    # Compare objects
    foreach (Utils_GetAllPublicClassVariablesNames(get_class($obj1)) as $var_name)
    {
        if( $obj1->{$var_name} !== $obj2->{$var_name} )
        {
            if( $readable_response )
            {
                $results[] = "$var_name: " . $obj1->{$var_name} . " => " . $obj2->{$var_name};
            }
            else
            {
                $results[$var_name] = array($obj1->{$var_name}, $obj2->{$var_name});
            }
        }
    }

    return $results;
}

function PrintIfNotEmpty(&$var)
{
    $IsEmpty = empty($var);
    if (!$IsEmpty)
    {
        echo $var;
    }
    return $IsEmpty;
}

function ForceHTTPS()
{
    if (empty($_SERVER['HTTPS']) || ($_SERVER['HTTPS'] === "off"))
    {
        $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $location);
        exit;
    }
}

function Utils_GetCurrentPageWithGETdata()
{
    return Utils_GetExecutionPathRelativeToRoot() . Utils_Get_HTTP_GET_String();
}

function Utils_Get_HTTP_GET_String(int $max_size = 4096): string
{
    $result = "";

    if (!empty($_GET))
    {
        foreach ($_GET as $get_key => $get_val)
        {
            if (is_array($get_val))
            {
                foreach ($get_val as $key => $val)
                {
                    if (!is_numeric($val) && !is_string($val))
                    {
                        continue;
                    }

                    $result .= ("&" . $get_key . "[" . $key . "]") . (!empty($val) ? ("=" . $val) : "");
                }
            }
            else if (is_string($get_val) || is_numeric($get_val))
            {
                $result .= ("&" . $get_key) . (!empty($get_val) ? ("=" . $get_val) : "");
            }
            //            else
            //            {
            //            }

            if (strlen($result) >= $max_size)
            {
                break;
            }
        }
        if (!empty($result))
        {
            # Remove first '&'
            $result = substr($result, 1);
            $result = "?" . $result;
            $result = substr($result, 0, $max_size);
        }
    }

    # do not return anything if dangerous characters are present in result
    if (!Utils_IsValidHttpGetString($result, $max_size))
    {
        return "";
    }

    return $result;
}

function Utils_IsValidDateTimeString($input_str): bool
{
    if( empty($input_str) )
        return false;

    if(!IsNumericOrString($input_str, 6, 64 ))
        return false;

    if( strtotime($input_str) )
        return true;

    return false;
}

function Utils_Update_HttpGET_Value($url, $param_name, $new_param_value)
{
    if (empty($url))
    {
        return "?$param_name=$new_param_value";
    }

    $page_link = "";
    if (($res = explode("?", $url, 2)))
    {
        $page_link = $res[0];
    }
    else
    {
        return $url . "?$param_name=$new_param_value";
    }

    $url_params = array();
    parse_str(parse_url($url, PHP_URL_QUERY), $url_params);
    $url_params[$param_name] = $new_param_value;
    return $page_link . "?" . http_build_query($url_params);
}

function Utils_GetExecutionPathRelativeToRoot(): string
{
    return rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), "/");
}

function Utils_GetAllHeaders(): array
{
    $headers = array();
    foreach ($_SERVER as $key => $value)
    {
        if (strpos($key, 'HTTP_') !== 0)
        {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    return $headers;
}

function Utils_GetStringMaxSize($input_str, $max_size_chars): string
{
    if (strlen($input_str) <= $max_size_chars)
    {
        return $input_str;
    }

    return substr($input_str, 0, $max_size_chars);
}

function Utils_GetStringBetween($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini === 0)
    {
        return '';
    }
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function Utils_GetAllStringBetween($string, $start, $end)
{
    $n = explode($start, $string);
    $result = Array();
    foreach ($n as $val)
    {
        $pos = strpos($val, $end);
        if ($pos !== false)
        {
            $result[] = substr($val, 0, $pos);
        }
    }
    return $result;
}

function Utils_ExtractNumberFromStr($in_str, $nTh = 0)
{
    $matches = null;
    preg_match_all('!\d+!', $in_str, $matches);

    if (isset($matches[$nTh]))
    {
        return implode("", $matches[$nTh]);
    }

    return null;
}

function Utils_RoundPrice(float $price): float
{
    return round($price, 2);
}

function Utils_MergeCommaSeparatedListsNoDuplicates(string $list1, string $list2, $case_insensitive = true): string
{
    $arr1 = (!empty($list1) ? explode(",", $list1) : array());
    $arr2 = (!empty($list2) ? explode(",", $list2) : array());

    foreach ($arr2 as $el2)
    {
        $found = false;
        foreach ($arr1 as $el1)
        {
            if ($case_insensitive && (strtolower($el1) === strtolower($el2)))
            {
                $found = true;
                break;
            }
            else if ($el1 === $el2)
            {
                $found = true;
                break;
            }
        }

        if (!$found && !empty($el2))
        {
            $arr1[] = $el2;
        }
    }

    if (!empty($arr1))
    {
        return implode(",", $arr1);
    }

    return "";
}

function Utils_OrderCommaSeparatedList(string $comma_separated_list): string
{
    if (empty($comma_separated_list))
    {
        return "";
    }

    $tmp = explode(",", $comma_separated_list);
    sort($tmp);
    return implode(",", $tmp);
}

function Utils_GetCapitalizedFirstLetter(string $input_str): string
{
    return ucfirst(strtolower($input_str));
}

function Utils_IntToBinRepresentation($int_val)
{
    return (chunk_split(sprintf('%08b', $int_val), 4, ' '));
}

function Utils_ArrContainKey(array &$arr, $key)
{
    foreach ($arr as $k => $v)
    {
        if ($k === $key)
        {
            return true;
        }
    }
    return false;
}

function Utils_ArrContainVal(array &$arr, $val)
{
    foreach ($arr as $k => $v)
    {
        if ($v === $val)
        {
            return true;
        }
    }
    return false;
}

function Utils_GetCallStack()
{
    return ((new Exception)->getTraceAsString());
}

function Utils_TruncateString(string $input_str, $max_len)
{
    if (strlen($input_str) <= $max_len)
    {
        return $input_str;
    }

    return substr(0, $max_len);
}

function Utils_StringStartsWith(string $input_str, string $prefix): bool
{
    return (strncmp($input_str, $prefix, strlen($prefix)) === 0);
}

function Utils_RemoveSubstringFromString(string $input_str, string $substr): string
{
    return str_replace($substr, "", $input_str);
}

function Utils_GetPathRelativeToRootFromRelative($filename)
{
    $parsed_filename = $filename;
    if (Utils_StrContains($parsed_filename, "?"))
    {
        $parsed_filename = explode("?", $parsed_filename, 2)[0];
    }

    # If filename starts with "/", it means it is relative to root already
    if( Utils_StringStartsWith($filename, "/") )
    {
        $file_abs_path = (string)realpath(Utils_GetDocumentRoot() . $filename);
    }
    else
    {
        $file_abs_path = (string)realpath($parsed_filename);
    }

    # Check whether path si outside of webroot
    if( empty($file_abs_path) )
    {
        return "file_not_exists";
    }

    if( !Utils_StringStartsWith($file_abs_path, Utils_GetDocumentRoot()) )
    {
        return "path_outside_webroot";
    }

    # Make sure we do not leave website root. In case file is upper, than it will start from root
    $file_rel_path_to_root = str_replace(Utils_GetDocumentRoot(), "", $file_abs_path);
    $file_abs_path = Utils_GetDocumentRoot() . $file_rel_path_to_root;

    # Make sure file exists although already checked with 'realpath()'
    if (!file_exists($file_abs_path))
    {
        return "file_not_exist";
    }

    // Remove full path and return the string
    return $file_rel_path_to_root;
}

function Utils_ExtractFirstIpAddress($str)
{
    $regex_ip4 = '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/';
    $regex_ip6 = '/^(((?=(?>.*?(::))(?!.+3)))3?|([dA-F]{1,4}(3|:(?!$)|$)|2))(?4){5}((?4){2}|(25[0-5]|(2[0-4]|1d|[1-9])?d)(.(?7)){3})z/i';

    if (preg_match($regex_ip4, $str, $ip_match))
    {
        return $ip_match[0];
    }

    if (preg_match($regex_ip6, $str, $ip_match))
    {
        return $ip_match[0];
    }

    return "";
}

function Utils_SafeStrCmp($safe, $user)
{
    // Prevent issues if string length is 0
    $safe .= chr(0);
    $user .= chr(0);

    $safeLen = strlen($safe);
    $userLen = strlen($user);

    // Set the result to the difference between the lengths
    $result = $safeLen - $userLen;

    // Note that we ALWAYS iterate over the user-supplied length
    // This is to prevent leaking length information
    for ($i = 0; $i < $userLen; $i++)
    {
        // Using % here is a trick to prevent notices
        // It's safe, since if the lengths are different
        // $result is already non-0
        $result |= (ord($safe[$i % $safeLen]) ^ ord($user[$i]));
    }

    // They are only identical strings if $result is exactly 0...
    return $result === 0;
}

function CanBeString($item)
{
    return (!is_array($item)) &&
        ((!is_object($item) && settype($item, 'string') !== false) ||
            (is_object($item) && method_exists($item, '__toString')));
}

function Utils_GetClassNameFromObject($obj): string
{
    if (empty($obj))
    {
        return "class_not_found";
    }

    if (!is_object($obj))
    {
        return "class_is_not_object";
    }

    return get_class($obj);
}

function VarDumpExit($var)
{
    /** @noinspection ForgottenDebugOutputInspection */
    var_dump($var);
    exit(1);
}

function Utils_ExtractAllEmailsNoDuplicates(string &$string)
{
    preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
    return array_values(array_unique($matches[0]));
}

function Utils_FindIndexOfKey($key_to_index, &$array)
{
    return array_search($key_to_index, array_keys($array), true);
}

function Utils_FindIndexOfVal($val_to_index, &$array)
{
    $position = 0;
    foreach ($array as $k => $v)
    {
        if ($v === $val_to_index)
        {
            return $position;
        }
        $position++;
    }
    return $position;
}

function Utils_REDIRECT($new_location)
{
    header("Location: $new_location");
    exit();
}

function Utils_DeleteFile($file_name)
{
    if (is_file($file_name))
    {
        return unlink($file_name);
    }
    return false;
}

function Utils_DeleteDir($dirPath)
{
    if (!is_dir($dirPath))
    {
        return false;
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) !== '/')
    {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file)
    {
        if (is_dir($file))
        {
            Utils_DeleteDir($file);
        }
        else
        {
            unlink($file);
        }
    }
    rmdir($dirPath);
    return true;
}

function DownloadFileOverwriteExisting($src, $dst): bool
{
    Utils_DeleteFile($dst);
    if (($in_data = @fopen($src, 'rb')))
    {
        return file_put_contents($dst, $in_data);
    }
    return false;
}

function SearchFileRecursiveAndCopyIfExists($search_path, $file_name, $copy_to)
{
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($search_path));

    foreach ($rii as $file)
    {
        if ($file->isDir())
        {
            continue;
        }

        if ($file->getFileName() === $file_name)
        {
            return copy($file->getPathname(), $copy_to);
        }
    }
    return false;
}

function IsAscii($input)
{
    return mb_check_encoding($input, 'ASCII');
}

function Utils_ReplaceAllNonAlphaCharacters($input_string, $replace_with)
{
    return preg_replace( '/[\W]/', $replace_with, $input_string);
}

function Utils_DownloadHttpsPage($page_url, $additional_headers = array())
{
    if (!function_exists('curl_init'))
    {
        return "error_while_downloading_page";
    }

    if (empty($additional_headers))
    {
        $additional_headers = array();
        $additional_headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0";
        $additional_headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $additional_headers[] = "Accept-Language: en-US,en;q=0.5";
        $additional_headers[] = "Connection: keep-alive";
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $page_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $additional_headers);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function Utils_ForceDownloadFile($file_name, $file_content)
{
    if (empty($file_name) || empty($file_content))
    {
        return FALSE;
    }

    // Generate the server headers
    if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE)
    {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-Transfer-Encoding: binary");
        header('Pragma: public');
        header("Content-Length: " . strlen($file_content));
    }
    else
    {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header("Content-Transfer-Encoding: binary");
        header('Expires: 0');
        header('Pragma: no-cache');
        header("Content-Length: " . strlen($file_content));
    }

    exit($file_content);
}


function Utils_ArrayValidValuesAgainstClassConstValues(array $input_array, string $class_name_with_const_values): array
{
    return array_values(array_intersect(Utils_GetClassConstantsAsKeyVal($class_name_with_const_values), $input_array));
}

function GetBoolStr($anything): string
{
    if( $anything )
        return "true";
    return "false";
}