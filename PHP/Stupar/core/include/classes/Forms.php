<?php

abstract class FormTypes
{
    public const TEXT = "text";
    public const CHECKBOX = "checkbox";
    public const TEXTAREA = "textarea";
    public const SELECT = "select";
    public const EMAIL = "email";
}

class FormElementStyleParams
{
    public string $Style_MainDivClass = "";
    public string $Style_LabelClass = "form-label-full-width";
    public string $Style_LabelDivClass = "text-nowrap";
    public string $Style_InputElementClass = "form-control";
    public string $Style_MessageClass = "invalid-feedback";
    public string $Style_FillHintClass = "border-0 mb-0 small text-muted";
    public string $Style_ReadOnlyInput = "disabled=\"disabled\"";
    public string $Style_CustomAspectRatioKeys = "";
    public string $Style_CustomAspectRatioValues = "";

}

class FormInput
{
    public int $MAX_FIELD_SIZE = 2048;

    # HTML Builder
    public FormElementStyleParams $Params;
    # Select from params to be selected or checkboxes or radio boxes etc. To be provided as a key->value
    public array $SelectOptions = array();
    public bool $SelectEnableSearch = false;

    /* All params are public because are needed to be accessed by template */
    public string $DisplayName = "";
    public string $Name = "";
    public ?string $Placeholder = "";
    public ?string $Value = "";
    public string $Type = "";
    public string $Message = "";
    public bool $Mandatory = false;
    public ?string $MethodAllowed = "POST";
    public ?string $Pattern = "";
    public ?string $Title = "";
    public string $FillHint = "";
    public bool $IsValid = false;
    public bool $ReadOnly = false;
    public string $AdditionalTagParams = "";
    public bool $IsInlined = false;
    public bool $ValidationEnabled = true;

    // flag to be used by user when passing form from one component to another
    public string $UserFlag = "";
    // Flag that indicate whether method Populate() was executed
    public bool $Populated = false;

    /**
     * FormInput constructor.
     * @param string $DisplayName
     * @param string $FieldName
     * @param string $FormType
     * @param null $ValidationFuncRef
     * @param string $Placeholder
     * @param bool $Mandatory
     * @param string $MethodAllowed_
     * @param string $Pattern
     * @param string $Title
     * @param string $FillHint
     */
    public function __construct(string $DisplayName,
                                string $FieldName,
                                string $FormType,
                                $ValidationFuncRef = null,
                                ?string $Placeholder = "",
                                ?bool $Mandatory = true,
                                ?string $MethodAllowed_ = "POST",
                                ?string $Pattern = "",
                                ?string $Title = "",
                                ?string $FillHint = "")
    {
        if (empty($FieldName) || empty($FormType))
        {
            throw new RuntimeException("Invalid parameters on a field detected!");
        }

        $this->DisplayName = $DisplayName;
        $this->Name = $FieldName;
        $this->Placeholder = $Placeholder;
        $this->Type = $FormType;
        $this->ValidationFuncRef = $ValidationFuncRef;
        $this->Mandatory = $Mandatory;
        $this->MethodAllowed = (string)$MethodAllowed_ ?: "POST";
        $this->Pattern = $Pattern;
        $this->Title = $Title;
        $this->FillHint = $FillHint;

        $this->Params = new FormElementStyleParams();
    }

    public function ClearVal(): void
    {
        $this->Value = "";
    }

    public function ClearMsg(): void
    {
        $this->Message = "";
    }

    public function SetMsg($new_message): void
    {
        $this->Message = $new_message;
    }

    /** Get value that can be shown on HTML */
    public function GetValueFiltered(): string
    {
        if (is_string($this->Value) || is_numeric($this->Value))
        {
            return htmlentities($this->Value, ENT_QUOTES | ENT_HTML5);
        }

        return "";
    }

    /**  Function to check whether inserted value is valid */
    public function IsValid()
    {
        $result = false;

        if (($this->ValidationFuncRef !== null) && function_exists($this->ValidationFuncRef)) # Callback function exists?
        {
            if (!empty($this->Value)) # Value not empty
            {
                $input_len = strlen(serialize($this->Value));
                if ($input_len <= $this->MAX_FIELD_SIZE) # Validate length
                {
                    # Further validate input - only allow strings or numbers as datatypes
                    # Temporary fix: allow only strings or numeric
                    if (is_numeric($this->Value) || is_string($this->Value)) # Validate datatype
                    {
                        # Trigger callback function to validate the input
                        $result = call_user_func($this->ValidationFuncRef, $this->Value); # Trigger callback
                        if (!$result)
                        {
                            $this->Message = "Invalid input data";
                            $result = false;
                        }
                        else
                        {
                            $result = true;
                            $this->Message = "";
                        }

                        # If input is valid and it is an CheckBox check whether value is within dropdown menu variants
                        if ($result && ($this->Type === FormTypes::SELECT) && !empty($this->SelectOptions))
                        {
                            if (!array_key_exists($this->Value, $this->SelectOptions))
                            {
                                $this->Message = "Selected option does not exists";
                            }
                            else
                            {
                                $result = true;
                            }
                        }
                    }
                    else
                    {
                        $this->Message = "Input needs to be string or a number";
                        $result = false;
                    }
                }
                else
                {
                    $result = false;
                    $this->Message = "Input length '' is too long. Max accepted is {$this->MAX_FIELD_SIZE}";
                }
            }
            else
            {
                if (!$this->Mandatory || $this->ReadOnly || $this->Type === FormTypes::CHECKBOX)
                {
                    $result = true;
                    $this->Message = "";
                }
                else
                {
                    $result = false;
                    $this->Message = "This field cannot be empty!";
                }
            }
        }
        else
        {
            # This field did not received a validation function reference.
            $this->Message = "Cannot check this field!";
            $result = false;
        }

        # Store status in case template require it
        $this->IsValid = (bool)($result);

        return $result;
    }

    public function IsEmptyValue(): bool
    {
        return empty($this->Value);
    }

    /** Reference to a function which shall validate this field*/
    public $ValidationFuncRef = null;

    public function GetValidationResult(): bool
    {
        return $this->IsValid;
    }

    public function SetValue($value)
    {
        $this->Value = $value;
        return $this->IsValid();
    }

    public function GetValue(): string
    {
        if ($this->Type === FormTypes::CHECKBOX)
        {
            if ($this->Value)
            {
                return true;
            }

            return false;
        }

        return $this->Value;
    }

    /** Extract data from HTTP request and fill all the content inside internal structure */
    public function Populate()
    {
        $this->Value = "";

        if ($this->MethodAllowed === "POST")
        {
            if (!empty($_POST[$this->Name]) && (is_string($_POST[$this->Name]) || is_numeric($_POST[$this->Name])))
            {
                $this->Value = (string)$_POST[$this->Name];
            }
        }
        else if ($this->MethodAllowed === "GET")
        {
            if (!empty($_GET[$this->Name]) && (is_string($_GET[$this->Name]) || is_numeric($_GET[$this->Name])))
            {
                $this->Value = (string)$_GET[$this->Name];
            }
        }
        else if ($this->MethodAllowed === "ANY")
        {
            if (!empty($_REQUEST[$this->Name]) && (is_string($_REQUEST[$this->Name]) || is_numeric($_REQUEST[$this->Name])))
            {
                $this->Value = (string)$_REQUEST[$this->Name];
            }
        }

        # null can be problematic as it can't be added to database - replace it with empty string
        if (empty($this->Value))
        {
            $this->Value = "";
        }

        $this->Populated = true;
        return $this->IsValid();
    }

    public function GenerateHtmlForm(?string $CustomAspectRatioKeys = "",
                                     ?string $CustomAspectRatioValues = "",
                                     ?string $main_div_css_class = "",
                                     ?string $label_css_class = "",
                                     ?string $div_label_name_css_class = "",
                                     ?string $form_element_css_class = "",
                                     ?string $message_css_class = "",
                                     ?string $filling_hint_css_class = ""
    ):
    string
    {
        if ($main_div_css_class)
        {
            $this->Params->Style_MainDivClass = (string)$main_div_css_class;
        }

        if ($label_css_class)
        {
            $this->Params->Style_LabelClass = (string)$label_css_class;
        }

        if ($div_label_name_css_class)
        {
            $this->Params->Style_LabelDivClass = (string)$div_label_name_css_class;
        }

        if ($form_element_css_class)
        {
            $this->Params->Style_InputElementClass = (string)$form_element_css_class;
        }

        if ($message_css_class)
        {
            $this->Params->Style_MessageClass = (string)$message_css_class;
        }

        if ($filling_hint_css_class)
        {
            $this->Params->Style_FillHintClass = (string)$filling_hint_css_class;
        }

        if ($CustomAspectRatioKeys)
        {
            $this->Params->Style_CustomAspectRatioKeys = (string)$CustomAspectRatioKeys;
        }

        if ($CustomAspectRatioValues)
        {
            $this->Params->Style_CustomAspectRatioValues = (string)$CustomAspectRatioValues;
        }

        $label = $this->GenerateHtmlForm_GetLabelAndDiv();

        #
        # Generate HTML form
        #

        if ($this->Type === FormTypes::TEXTAREA)
        {
            return str_replace("{{input_element}}", $this->GenerateHtmlForm_Textarea(), $label);
        }

        if ($this->Type === FormTypes::SELECT)
        {
            return str_replace("{{input_element}}", $this->GenerateHtmlForm_Select(), $label);
        }

        if ($this->Type === FormTypes::CHECKBOX)
        {
            return str_replace("{{input_element}}", $this->GenerateHtmlForm_Checkbox(), $label);
        }

        return str_replace("{{input_element}}", $this->GenerateHtmlForm_InputForm(), $label);
    }

    private function GenerateHtmlForm_Checkbox(): string
    {
        $output = "";
        if ($this->IsInlined)
        {
            $output = sprintf("
            <div class=\"row mb-3 %s\">
                <div class=\"%s\">
                    <label class=\"col-form-label mr-2\" for=\"%s\">%s%s</label>
                </div>
                <div class=\"%s\">
                   <input class=\"form-check d-inline-block\" type=\"checkbox\" name=\"%s\" id=\"%s\" %s %s%s>&nbsp;
                   <label class=\"col-form-label mr-2\" for=\"%s\">%s</label>
                    %s%s
                </div>
            </div>",
                ($this->Params->Style_MainDivClass),
                ($this->Params->Style_CustomAspectRatioKeys?:"col-12 col-sm-3 col-md-4"),
                ($this->GetFormID()),
                ($this->DisplayName),
                ($this->Mandatory ? ("&nbsp;<span class=\"text-danger\">*</span>") : ("")),
                ($this->Params->Style_CustomAspectRatioValues?:"col-12 col-12 col-sm-9 col-md-8"),
                ($this->Name),
                ($this->GetFormID()),
                ($this->ReadOnly) ? (" " . $this->Params->Style_ReadOnlyInput) : (""),
                $this->AdditionalTagParams,
                ($this->Value ? " checked" : ""),
                ($this->GetFormID()),
                (!empty($this->Title)?($this->Title):("Check/Uncheck this option")),
                ((!empty($this->Message)) ? ("<div class=\"{$this->Params->Style_MessageClass} mt-0\">" . htmlspecialchars($this->Message, ENT_QUOTES | ENT_HTML5) . "</div>") : ("")),
                (!empty($this->FillHint) ? ("<p class=\"{$this->Params->Style_FillHintClass} mt-0\">" . htmlspecialchars($this->FillHint, ENT_QUOTES | ENT_HTML5) . "</p>") : (""))
            );
        }
        else
        {
            $output = sprintf("
        <div class=\"form-check mb-3\">
          <input class=\"form-check-input\" type=\"checkbox\" name=\"%s\" id=\"%s\"%s>&nbsp;
          <label class=\"form-check-label\" for=\"%s\">
            %s
          </label>
          %s%s
        </div>
        ",
                $this->Name,
                $this->GetFormID(),
                ($this->Value ? " checked" : ""),
                ($this->GetFormID()),
                $this->DisplayName,
                ((!empty($this->Message)) ? ("<div class=\"{$this->Params->Style_MessageClass} mt-0\">" . htmlspecialchars($this->Message, ENT_QUOTES | ENT_HTML5) . "</div>") : ("")),
                ((!empty($this->FillHint)) ? ("<p class=\"{$this->Params->Style_FillHintClass} mt-0\">" . htmlspecialchars($this->FillHint, ENT_QUOTES | ENT_HTML5) . "</p>") : (""))
            );
        }

        return $output;
    }

    private function GenerateHtmlForm_Textarea(): string
    {
        $is_valid = $this->GetValidationClass();

        # Build form class
        $form_class = $this->Params->Style_InputElementClass . (!empty($is_valid) ? (" {$is_valid}") : (""));

        $output = sprintf("<textarea%sname=\"%s\"%s%s%s>%s</textarea>",
            ((!empty($form_class)) ? (" class=\"{$form_class}\" ") : (" ")),
            (htmlspecialchars($this->Name, ENT_QUOTES | ENT_HTML5)),
            (($this->Mandatory) ? (" required") : ("")),
            ($this->AdditionalTagParams ? (" {$this->AdditionalTagParams}") : ("")),
            ($this->ReadOnly) ? (" " . $this->Params->Style_ReadOnlyInput) : (""),
            (htmlspecialchars($this->Value, ENT_QUOTES | ENT_HTML5))
        );

        return (string)$output;
    }

    private function GenerateHtmlForm_InputForm(): string
    {
        $is_valid = $this->GetValidationClass();

        # Build form class
        $form_class = $this->Params->Style_InputElementClass . (!empty($is_valid) ? (" {$is_valid}") : (""));

        $output = sprintf("<input%s%sname=\"%s\" id=\"%s\" value=\"%s\"%s%s%s%s%s>",
            (" type=\"{$this->Type}\""),
            ((!empty($form_class)) ? (" class=\"{$form_class}\" ") : (" ")),
            (htmlspecialchars($this->Name, ENT_QUOTES | ENT_HTML5)),
            (htmlspecialchars($this->GetFormID(), ENT_QUOTES | ENT_HTML5)),
            (htmlspecialchars($this->Value, ENT_QUOTES | ENT_HTML5)),
            (($this->Mandatory) ? (" required") : ("")),
            ((!empty($this->Placeholder)) ? (" placeholder=\"{$this->Placeholder}\"") : ("")),
            ($this->AdditionalTagParams ? (" {$this->AdditionalTagParams}") : ("")),
            ((!empty($this->Pattern)) ? (" pattern=\"{$this->Pattern}\"") : ("")),
            ($this->ReadOnly) ? (" " . $this->Params->Style_ReadOnlyInput) : (""),
        );

        return (string)$output;
    }

    private function GenerateHtmlForm_Select(): string
    {
        $is_valid = $this->GetValidationClass();

        # Build form class
        $form_class = $this->Params->Style_InputElementClass . (!empty($is_valid) ? (" {$is_valid}") : (""));

        $output = "<select id=\"". $this->GetFormID() ."\" name=\"{$this->Name}\"";
        # Add class
        if ($form_class)
        {
            $output .= " class=\"{$form_class} custom-select" . (($this->SelectEnableSearch && !$this->ReadOnly) ? (" fstdropdown-select") : ("")) . "\"";
        }
        # Mandatory field HTML tag
        if ($this->Mandatory)
        {
            $output .= " required";
        }
        if ($this->AdditionalTagParams)
        {
            $output .= (" " . $this->AdditionalTagParams);
        }
        if ($this->ReadOnly)
        {
            $output .= (" " . $this->Params->Style_ReadOnlyInput);
        }
        # close tag
        $output .= ">";
        $output .= "<option selected=\"selected\" disabled=\"\">Select " . strtolower($this->DisplayName) . "</option>";

        foreach ($this->SelectOptions as $key => $val)
        {
            $output .= "<option value=\"$key\"" . (((string)$this->Value === (string)$key) ? (" selected=\"selected\"") : ("")) . ">" . htmlspecialchars($val, ENT_QUOTES | ENT_HTML5) . "</option>";
        }
        # End tag
        $output .= "</select>";

        return (string)$output;
    }

    # Frames generators for the form itself

    private function GenerateHtmlForm_GetLabelAndDiv(): string
    {
        $output = "";

        # Checkboxes are handled specially later
        if ($this->Type === FormTypes::CHECKBOX)
        {
            return "{{input_element}}";
        }

        if ($this->IsInlined)
        {
            $output = sprintf("
<div class=\"row form-group %s\">
    <div class=\"%s\">
        <label class=\"col-form-label mr-2 %s\" for=\"%s\">%s%s</label>
    </div>
    <div class=\"%s\">
        {{input_element}}
        %s%s
    </div>
</div>",
                $this->Params->Style_MainDivClass,
                ($this->Params->Style_CustomAspectRatioKeys?:"col-12 col-sm-3 col-md-4"),
                $this->Params->Style_LabelClass,
                ($this->GetFormID()), ($this->DisplayName),
                ($this->Mandatory ? ("&nbsp;<span class=\"text-danger\">*</span>") : ("")),
                ($this->Params->Style_CustomAspectRatioValues?:"col-12 col-12 col-sm-9 col-md-8"),
                ((!empty($this->Message))? ("<div class=\"{$this->Params->Style_MessageClass} mt-0\">" . htmlspecialchars($this->Message, ENT_QUOTES | ENT_HTML5) . "</div>") : ("")),
                (!empty($this->FillHint) ? ("<p class=\"{$this->Params->Style_FillHintClass} mt-0\">" . htmlspecialchars($this->FillHint, ENT_QUOTES | ENT_HTML5) . "</p>") : (""))
            );
        }
        else
        {

            $output = sprintf("
            <div class=\"form-group %s\">
                <label%s>
                    <div%s>%s%s</div>
                        {{input_element}}
                        %s%s
                </label>
            </div>",
                $this->Params->Style_MainDivClass,
                ((!empty($this->Params->Style_LabelClass)) ? (" class=\"{$this->Params->Style_LabelClass}\"") : ("")),
                ((!empty($this->Params->Style_LabelDivClass)) ? (" class=\"{$this->Params->Style_LabelDivClass}\"") : ("")),
                ($this->DisplayName),
                ($this->Mandatory ? ("&nbsp;<span class=\"text-danger\">*</span>") : ("")),
                ((!empty($this->Message))? ("<div class=\"{$this->Params->Style_MessageClass} mt-0\">" . htmlspecialchars($this->Message, ENT_QUOTES | ENT_HTML5) . "</div>") : ("")),
                (!empty($this->FillHint) ? ("<p class=\"{$this->Params->Style_FillHintClass} mt-0\">" . htmlspecialchars($this->FillHint, ENT_QUOTES | ENT_HTML5) . "</p>") : (""))
            );
        }

        return (string)$output;
    }

    public function GetFormID(): string
    {
        return "form-input-" . $this->Type . "-" . $this->Name;
    }

    public function GetValidationClass(): string
    {
        # return empty class is validation result shall not be shown
        if( !$this->ValidationEnabled )
        {
            return "";
        }

        # Also readOnly fields shall not be validated
        if( $this->ReadOnly )
        {
            return "";
        }

        # If fields was not populated at all also return nothing
        if( !$this->Populated )
        {
            return "";
        }

//        if (!empty($this->Message))
//        {
//            return "is-invalid";
//        }

        # If field is not mandatory, it can be empty
        if( !$this->Mandatory && empty($this->Value) )
        {
            return "";
        }

        if ($this->Mandatory && !empty($this->Value) && $this->GetValidationResult())
        {
            return "is-valid";
        }

        if (!$this->Mandatory && !empty($this->Value) && $this->GetValidationResult())
        {
            return "is-valid";
        }

        return "is-invalid";
    }
}

abstract class GenericForm
{
    // Flag to be used by user when passing form between components
    public string $UserFlag = "";

    protected bool $StopPopulatingOnFirstFail = false;

    public function MakeAllInline(): bool
    {
        $result = true;
        /** @var $field FormInput */
        foreach (get_object_vars($this) as &$field)
        {
            if (is_object($field) && Utils_GetClassNameFromObject($field) === FormInput::class)
            {
                $field->IsInlined = true;
            }
        }
        return $result;
    }

    public function MakeAllValid(): bool
    {
        $result = true;
        /** @var $field FormInput */
        foreach (get_object_vars($this) as &$field)
        {
            if (is_object($field) && Utils_GetClassNameFromObject($field) === FormInput::class)
            {
                $field->IsValid = true;
            }
        }
        return $result;
    }

    public function MakeAllInvalid(): bool
    {
        $result = true;
        /** @var $field FormInput */
        foreach (get_object_vars($this) as &$field)
        {
            if (is_object($field) && Utils_GetClassNameFromObject($field) === FormInput::class)
            {
                $field->IsValid = false;
            }
        }
        return $result;
    }

    public function MakeAllReadOnly(): bool
    {
        $result = true;
        /** @var $field FormInput */
        foreach (get_object_vars($this) as &$field)
        {
            if (is_object($field) && Utils_GetClassNameFromObject($field) === FormInput::class)
            {
                $field->ReadOnly = true;
            }
        }
        return $result;
    }

    public function EnableValidation(): bool
    {
        $result = true;
        /** @var $field FormInput */
        foreach (get_object_vars($this) as &$field)
        {
            if (is_object($field) && Utils_GetClassNameFromObject($field) === FormInput::class)
            {
                $field->ValidationEnabled = true;
            }
        }
        return $result;
    }

    public function DisableValidation(): bool
    {
        $result = true;
        /** @var $field FormInput */
        foreach (get_object_vars($this) as &$field)
        {
            if (is_object($field) && Utils_GetClassNameFromObject($field) === FormInput::class)
            {
                $field->ValidationEnabled = false;
            }
        }
        return $result;
    }

    public function Populate(): bool
    {
        $result = true;
        /** @var $field FormInput */
        foreach (get_object_vars($this) as &$field)
        {
            if (is_object($field) && (Utils_GetClassNameFromObject($field) === FormInput::class))
            {
                if (!$field->Populate())
                {
                    $result = false;
                    if ($this->StopPopulatingOnFirstFail)
                    {
                        break;
                    }
                }
            }
        }
        unset($field);


        # Trigger POST population event if set
        $this->PostPopulateEvent($result);

        return $result;
    }

    protected function PostPopulateEvent($result): void
    {

    }

    public function IsPopulated(): bool
    {
        /** @var $field FormInput */
        foreach (get_object_vars($this) as &$field)
        {
            if (is_object($field) && Utils_GetClassNameFromObject($field) === FormInput::class)
            {
                if(!$field->Populated)
                {
                    return false;
                }
            }
        }
        return true;
    }

    public function Clear(): void
    {
        /** @var $field FormInput */
        foreach (get_object_vars($this) as &$field)
        {
            if (is_object($field) && Utils_GetClassNameFromObject($field) === FormInput::class)
            {
                $field->ClearVal();
                $field->ClearMsg();
                $field->Populated = false;
            }
        }
    }

    public function ClearMessages(): void
    {
        /** @var $field FormInput */
        foreach (get_object_vars($this) as &$field)
        {
            if (is_object($field) && Utils_GetClassNameFromObject($field) === FormInput::class)
            {
                $field->ClearMsg();
            }
        }
    }

    public function GetInvalidValuesJson(): string
    {
        $results = array();

        /** @var $field FormInput */
        foreach (get_object_vars($this) as $field)
        {
            if (is_object($field) && Utils_GetClassNameFromObject($field) === FormInput::class)
            {
                if(!$field->IsValid)
                {
                    $results[$field->Name] = $field->GetValue();
                }
            }
        }
        return json_encode($results, JSON_PRETTY_PRINT);
    }
}

function Fields_Populate(&$Fields)
{
    $result = true;
    /* @var $field FormInput */
    foreach ($Fields as $field)
    {
        if ($field->MethodAllowed === "ANY")
        {
            if (isset($_GET[$field->Name]))
            {
                $field->Value = $_GET[$field->Name];
            }
            else if (isset($_POST[$field->Name]))
            {
                $field->Value = $_POST[$field->Name];
            }
            else
            {
                $result = false;
            }
        }
        else
        {
            if (isset($_GET[$field->Name]) && ($field->MethodAllowed === "GET"))
            {
                $field->Value = $_GET[$field->Name];
            }
            else if (isset($_POST[$field->Name]) && ($field->MethodAllowed === "POST"))
            {
                $field->Value = $_POST[$field->Name];
            }
            else
            {
                $result = false;
            }
        }

        if (!$field->IsValid())
        {
            $result = false;
        }
    }

    return $result;
}

