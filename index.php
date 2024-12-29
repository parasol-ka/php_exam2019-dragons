<?php 
    include "_connexionBD.php";

    if (isset($_GET['dragon'])){
        
        $checkDragon=$bd->prepare("SELECT * FROM especes WHERE id_espece=:id_espece;");
        if ($_GET['dragon']!='15'){
            $checkDragon->bindvalue("id_espece", (int)$_GET['dragon']);
            $checkDragon->execute();
            $dragon=$checkDragon->fetch();

            if (!empty($dragon['id_espece'])){
                $espece=$dragon['espece'];
                $id_espece=$_GET['dragon'];
                $titre="<h2>Observations espèce $espece</h2>";
                
                $reqObservations=$bd->prepare("SELECT o.id_espece, e.espece, o.nombre, o.saison, o.annee, o.nuit, o.commentaire FROM observations as o JOIN especes AS e ON o.id_espece=e.id_espece WHERE o.id_espece=:id_espece");
                $reqObservations->bindvalue("id_espece", $id_espece);
                $reqObservations->execute();
            }
        }else {header("Location:'index.php'");}

    }elseif (isset($_GET['year_observation'])){
        $checkYear=$bd->prepare("SELECT * FROM observations WHERE annee=:year_observation;");
        $checkYear->bindvalue("year_observation", (int)$_GET['year_observation']);
        $checkYear->execute();
        $year_checking=$checkYear->fetch();

        if (!empty($year_checking['annee'])){
            $year_checked=$_GET['year_observation'];
            $titre="<h2>Observations en $year_checked</h2>";
            
            $reqObservations=$bd->prepare("SELECT o.id_espece, e.espece, o.nombre, o.saison, o.annee, o.nuit, o.commentaire FROM observations as o JOIN especes AS e ON o.id_espece=e.id_espece WHERE o.annee=:year_checked");
            $reqObservations->bindvalue("year_checked", $year_checked);
            $reqObservations->execute();
        }else {header("Location:'index.php'");}

    }elseif (isset($_GET['saison'])){
        $checkSaison=$bd->prepare("SELECT * FROM observations WHERE saison=:saison_number;");
        $checkSaison->bindvalue("saison_number", (int)$_GET['saison']);
        $checkSaison->execute();
        $saison_checking=$checkSaison->fetch();

        if (!empty($saison_checking['saison'])){
            $saison_checked=$_GET['saison'];

            if ($saison_checked=='1'){
                $saison_name='Printemps';
            }elseif ($saison_checked=='2'){
                $saison_name='Été';
            }elseif ($saison_checked=='3'){
                $saison_name='Automne';
            }elseif ($saison_checked=='4'){
                $saison_name='Hiver';
            }else {$saison_name='Inconnu';}

            $titre="<h2>Observations saison $saison_name</h2>";
            
            $reqObservations=$bd->prepare("SELECT o.id_espece, e.espece, o.nombre, o.saison, o.annee, o.nuit, o.commentaire FROM observations as o JOIN especes AS e ON o.id_espece=e.id_espece WHERE o.saison=:saison_checked");
            $reqObservations->bindvalue("saison_checked", $saison_checked);
            $reqObservations->execute();
        }else {header("Location:'index.php'");}

    }else {
        $reqObservations=$bd->prepare("SELECT o.id_espece, e.espece, o.nombre, o.saison, o.annee, o.nuit, o.commentaire FROM observations as o JOIN especes AS e ON o.id_espece=e.id_espece WHERE o.id_espece!=15");
        $reqObservations->execute();
        $titre="<h2>Observations</h2>";}
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
    <a href="index.php" id="observations_link">Toutes les observations</a>
    <div id="observation_container">
        <?php
            echo $titre;
            

            echo "<div id='dragons_container'>";
            while($observations=$reqObservations->fetch()){
                $dragon_id=$observations['id_espece'];
                $image_number=str_pad($dragon_id, 3, "0", STR_PAD_LEFT);
                $observation_number=$observations['nombre'];
                $dragon_name=$observations['espece'];
                $year_observation=$observations['annee'];
                $night=$observations['nuit'];
                $comment=$observations['commentaire'];
                $saison_number=$observations['saison'];

                if ($saison_number=='1'){
                    $saison='Printemps';
                }elseif ($saison_number=='2'){
                    $saison='Été';
                }elseif ($saison_number=='3'){
                    $saison='Automne';
                }elseif ($saison_number=='4'){
                    $saison='Hiver';
                }else {$saison='Inconnu';}

                echo "<div id='dragon_line'>";
                for ($i=0; $i < $observation_number; $i++) { 
                    echo "<a href='index.php?dragon=$dragon_id'><img src='img/$image_number-dragon.svg' alt='dragon image' class='dragon_image'></a>";
                }
                echo "<p>Observation : </p><a href='index.php?saison=$saison_number' class='saisons'>$saison</a> <a href='index.php?year_observation=$year_observation' class='year'>$year_observation</a>";
                echo "<img src='img/nuit$night.svg' alt='night or day image' class='night_image'><p class='comment'>$comment</p></div>";
            }
            echo "</div>";
        ?>
    </div>
</body>
</html>