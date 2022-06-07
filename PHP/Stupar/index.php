<?php 

require_once "header.php";

// Get database files
$files = glob("data/*.txt", null);

?>

<body>
<h1>Date stupi</h1>
    <ul>
        <?php
            foreach ($files as $file)
            {
                $file_name = str_replace("data/", "", $file);
                $date = str_replace(".txt", "", $file_name);

               echo "<li><a href='view.php?date={$date}'>$date</a></li>\n";
            }
        ?>
    </ul>
</body>

<?php
require_once "footer.php";
?>