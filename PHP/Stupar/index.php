<?php 

// Get database files
$files = glob("data/*.txt", null);

?>

<html>
<head>
    <title>Vizualizare stupar</title>
</head>
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

</html>
