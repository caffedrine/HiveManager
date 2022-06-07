<?php

require_once "./appl/config.php";
require_once CORE_INCLUDE_PATH . "/generic/PrimitiveUtils.php";
require_once CORE_DATABASE_PATH . "/Database.php";
require_once APPL_DATABASE_PATH . "/Database.hives_sensors.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (empty($_GET['date']) || !Utils_IsValidDateTimeString($_GET['date']) )
{
    http_response_code(405);
    die("Invalid or not date provided");
}
$date = $_GET['date'];
$file = 'data/' . $date . '.txt';

?>

<html>
    <head>
        <title>Vizualizare date <?=$date?></title>
    </head>
<body>
<h1>Raport <?=$date?></h1>
<pre id="feed"></pre>
<script type="text/javascript">
    var refreshtime=100;
    function tc()
    {
        asyncAjax("GET","<?=$file?>",Math.random(),display,{});
        setTimeout(tc,refreshtime);
    }
    function display(xhr,cdat)
    {
        if( typeof prevText == 'undefined' ) {
            prevText = "";
        }

        if(xhr.readyState===4 && xhr.status===200)
        {
            if( xhr.responseText !== prevText )
            {
                document.getElementById("feed").innerHTML = xhr.responseText;
                prevText = xhr.responseText;
            }
        }
    }
    function asyncAjax(method,url,qs,callback,callbackData)
    {
        var xmlhttp=new XMLHttpRequest();
        //xmlhttp.cdat=callbackData;
        if(method=="GET")
        {
            url+="?"+qs;
        }
        var cb=callback;
        callback=function()
        {
            var xhr=xmlhttp;
            //xhr.cdat=callbackData;
            var cdat2=callbackData;
            cb(xhr,cdat2);
            return;
        }
        xmlhttp.open(method,url,true);
        xmlhttp.onreadystatechange=callback;
        if(method=="POST"){
            xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            xmlhttp.send(qs);
        }
        else
        {
            xmlhttp.send(null);
        }
    }
    tc();
</script>
</body>
</html>

