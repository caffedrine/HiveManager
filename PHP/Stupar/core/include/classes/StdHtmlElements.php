<?php

function HtmlElements_GenerateKeyValRow($key, $val, $filter = true, $tag_class = "col-4 col-sm-4 col-md-3", $value_class = "col-8 col-sm-8 col-md-9")
{
    return "    
        <div class=\"row mb-1\">
            <div class=\"$tag_class\">
                <span class=\"small text-muted\">" . (($filter)?(htmlspecialchars($key, ENT_QUOTES | ENT_HTML5)):($key)) . "</span>
            </div>
            <div class=\"$value_class\">
                <span>". (($filter)?(htmlspecialchars($val, ENT_QUOTES | ENT_HTML5)):($val)) ."</span>
            </div>
        </div>";
}