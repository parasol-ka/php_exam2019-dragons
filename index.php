<?php 
    include "_connexionBD.php";

    $reqObservations=$bd->prepare("SELECT o.id_espece, e.espece, o.nombre, o.saison, o.annee, o.nuit, o.commentaire FROM observations as o JOIN especes AS e ON o.id_espece=e.id_espece WHERE o.id_espece!=15");
    $reqObservations->execute();

    


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dragons</title>
</head>
<body>
    <header>
        <h1>Observations de dragons</h1>
        <p>Sur l'Île-aux-Dragons, Claytor-le-Sage scrute le ciel à la recherche de dragons et archive 
        scrupuleusement chaque observation dans une base de données MySQL via une interface PHP. </p>
    </header>
    <div id="form_container"></div>
    <a href="index.php">Toutes les observations</a>
    <div id="observation_container">
        <?php
            $observation=$reqObservations->fetch();
            if(!empty($observation)){
                if (isset($_GET['dragon'])){
                    $reqDragon=$bd->prepare("SELECT * FROM especes WHERE id_espece=:id_espece;");
                    if ($_GET['dragon']!='15'){
                        $reqDragon->bindvalue("id_espece", (int)$_GET['dragon']);
                        $reqDragon->execute();
                        $dragon=$reqDragon->fetch();

                        if (!empty($dragon['id_espece'])){
                            $espece=$dragon['espece'];
                            echo "<h2>Observations espèce $espece</h2>";
                        }
                    }else {header("Location:'index.php'");}
                }else {echo "<h2>Observations</h2>";}
            }

            echo "<div id='dragons_container'>";
            while($observations=$reqObservations->fetch()){
                $dragon_id=$observations['id_espece'];
                $image_number=str_pad($dragon_id, 3, "0", STR_PAD_LEFT);
                $observation_number=$observations['nombre'];
                $dragon_name=$observations['espece'];
                $saison=$observations['saison'];
                $year=$observations['annee'];
                $night=$observations['nuit'];
                $comment=$observations['commentaire'];

                echo "<div id='dragon_line'>";
                for ($i=0; $i < $observation_number; $i++) { 
                    echo "<a href='index.php?dragon=$dragon_id'><img src='img/$image_number-dragon.svg' alt='dragon image' class='dragon_image'></a>";
                }
                echo "</div>";
            }
            echo "</div>";
        ?>
    </div>
</body>
</html>