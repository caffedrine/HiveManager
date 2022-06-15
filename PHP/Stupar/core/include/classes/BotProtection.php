<?php

class BotProtection
{
    private string $CspNonce = "";
    private string $FailedReason = "";


    public function __construct($csp_nonce)
    {
        $this->CspNonce = $csp_nonce;
    }

    public function Check(): bool
    {
        $result = true;

        # Only do check when there is some POST content to be protected
        if( empty($_POST) )
        {
            return true;
        }

        if( $result )
        {
            if( !isset($_POST['mandatory']) || !empty($_POST['mandatory']) )
            {
                $result = false;
                unset($_POST['mandatory']);
            }
        }

        if( $result )
        {
            if (!isset($_POST['not_to_be_filled']))
            {
                $result = false;
            }
            else if ($_POST['not_to_be_filled'] !== "n_o_b_o_t_w_a_s_h_e_r_e")
            {
                $result = false;
                unset($_POST['not_to_be_filled']);
            }
            else
            {
                unset($_POST['not_to_be_filled']);
            }
        }

        if($result)
        {
            if ((isset($_POST['middlee'])))
            {
                $result = false;
                unset($_POST['middlee']);
            }
        }

        if( $result )
        {
            if( !isset($_POST['extra_check']) ||  !preg_match('/[a-z\-0-9]/i', $_POST['extra_check']) || (strpos($_POST['extra_check'], '-') === false))
            {
                $result = false;
            }
            else
            {
                $str1 = explode("-", $_POST['extra_check'], 2)[0];
                $str2 = explode("-", $_POST['extra_check'], 3)[1];

                if( !Utils_SafeStrCmp(md5($str1), $str2))
                {
                    $result = false;
                }

                unset($_POST['extra_check']);
            }
        }

        return $result;
    }

    public function GetCssStyle(): string
    {
        return "";
    }

    public function GetHtmlForms(): string
    {
        $html_forms = '';
        $html_forms .= '<div class="form-optional">';
        $html_forms .= '<input type="text" name="mandatory" class="display-none" />';
        $html_forms .= '<input type="text" class="not_to_be_filled display-none" name="not_to_be_filled" />';
        $html_forms .= '<input type="text" class="middlee display-none" name="middlee" />';
        $html_forms .= '<input type="secret" class="extra_check display-none" name="extra_check" value="user-'.md5(uniqid(Crypto_GetRandomString(32), true)).'"/>';
        $html_forms .= '</div>';
        return $html_forms;
    }

    public function GetJavascript(): string
    {
        $javascript = "
            <script nonce=\"{$this->CspNonce}\" type=\"module\" src=\"/core/libs/js/BotProtection.js\"></script>
            <script nonce=\"{$this->CspNonce}\">    
            document.addEventListener('readystatechange', (event) =>
            {
                if (event.target.readyState === \"complete\")
                {
                    setTimeout( function()
                    {
                        BotProtection();
                    }, 50); // Timeout is important for tables which loads content later.
                }
            });            
            
            function BotProtection()
            {
                /* Get all forms protected */
                let forms = document.getElementsByClassName(\"form-optional\");
                for( let idx = 0; idx < forms.length; idx++ )
                {                     
                    /* Set the magic val expected by PHP script */
                    forms[idx].getElementsByClassName(\"not_to_be_filled\")[0].value = \"n_o_b_o_t_w_a_s_h_e_r_e\";
            
                    /* Delete that post field - PHP will reject any request with that field */
                    if( forms[idx].getElementsByClassName(\"middlee\")[0] )
                    {
                        forms[idx].getElementsByClassName(\"middlee\")[0].remove();
                    }
                    
                    /* Calculate magic val */
                    let seed = forms[idx].getElementsByClassName(\"extra_check\")[0].value;
                    forms[idx].getElementsByClassName(\"extra_check\")[0].value = BotProtection_GetMagic(seed);
                }
            }
            </script>
            ";
        return $javascript;
    }

    /**
     * @return string
     */
    public function GetFailedReason(): string
    {
        return $this->FailedReason;
    }
}