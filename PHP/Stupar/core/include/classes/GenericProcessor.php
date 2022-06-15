<?php

class RequestInfos
{
    private array $Requests = array();

    public function Clear(): void
    {
        $this->Requests = array();
    }

    public function Add( $status, $message ): void
    {
        $this->Requests[] = new StdRet($status, $message);
    }

    public function Get(): array
    {
        return $this->Requests;
    }

    public function GetCount(): int
    {
        return count( $this->Requests );
    }
}

abstract class GenericProcessor
{
    /* All the processors needs to be singletons */
    protected static $instance = null;

    /* What page shall be shown on page */
    protected string $PageView = "";

    /* @var RequestInfos Information containing the status of the current request */
    protected RequestInfos $RequestInfos;

    /* @var string Base64 encode source code of the captcha image */
    protected ?string $CaptchaImageSrcB64 = null;

    /* Parent as singleton - there is no purpose in allowing instances */
    abstract public static function getInstance();

    /*  Function to read whether captcha is enabled or not*/
    abstract public function IsCaptchaEnabled();

    /* Function used to read captcha source when captcha is enabled */
    public function GetCaptchaSrcB64(): ?string
    {return $this->CaptchaImageSrcB64;}

    /* Function used to enable captcha */
    abstract protected function EnableCaptcha($enb_flag);

    public function GetRequestInfos(): array
    {return $this->RequestInfos->Get();}

    public function  GetPageView(): string
    { return $this->PageView; }

    /* Main function which will execute the main state machine of the processor */
    abstract public function Process();

    /* When called at the end of Process(), this function prevents user from being able to resubmit $_POST content */
    abstract protected function PreventFormResubmission();

    /* Function used to redirect while preserving status to be shown after redirect */
    protected function Redirect(string $url): void
    {
        # the page where needs to be redirected shall be able to read request info
        # $page_name = Utils_GetPathRelativeToRootFromRelative($url);

        # Make statuses unique per page. This way statuses will be always shown in the correct page
        $session_key = "request_info_before_redirect__" . rtrim(parse_url($url, PHP_URL_PATH), "/");

        # Request infos needs to be preserved as well
        if( !empty($this->RequestInfos) && !empty($this->RequestInfos->Get()) )
        {
            $_SESSION[$session_key] = $this->RequestInfos->Get();
        }

        Utils_REDIRECT($url);
        exit();
    }

    /* Function used to fetch messages that were set before redirect */
    protected function FetchStatusesSetBeforeRedirect(): void
    {
        # Make statuses unique per page. This way statuses will be always shown in the correct page
        $session_key = "request_info_before_redirect__" . parse_url(rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), "/"), PHP_URL_PATH);

        if( isset($_SESSION[$session_key]) )
        {
            /* @var $this->RequestInfos RequestInfos */
            foreach($_SESSION[$session_key] as $sess)
            {
                $this->RequestInfos->Add($sess->Status, $sess->Message);
            }
            unset($_SESSION[$session_key]);

            # This part is only executed AFTER redirect. Therefore update internal state here.
            $this->ProcessorApplication_InternalState = ProcessorApplicationInternalState::AFTER_REDIRECT;
        }
    }

    /*  */
    abstract protected function OnAppConstructorCalled();

    /* Event that shall be triggered in case request failed logs were stored in database */
    abstract protected function Event_OnFailedActionLogged($action_name, $uid);

    /* check whether $_POST contains "action" type */
    protected function IsPOSTactionSet(string $actionName = "action"): bool
    {
        $result = true;
        if( $result === true )
        {
            if (empty($_POST))
            {
                $result = false;
            }
        }

        if ($result === true)
        {
            if (empty($_POST[$actionName]) || !Utils_IsAlphanumericAndUnderscore($_POST[$actionName]))
            {
                $result = false;
            }
        }
        return $result;
    }

    /* check whether $_GET contains "action" type */
    protected function IsGETactionSet(string $actionName = "action"): bool
    {
        $result = true;
        if( $result )
        {
            if (empty($_GET))
            {
                $result = false;
            }
        }

        if ($result )
        {
            if (empty($_GET[$actionName]) || !Utils_IsAlphanumericAndUnderscore($_GET[$actionName]))
            {
                $result = false;
            }
        }
        return $result;
    }
}