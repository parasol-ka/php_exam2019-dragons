<?php 
    include "_connexionBD.php";

    # Verification des variables GET, filtres

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
        $checkYear=$bd->prepare("SELECT * FROM observations WHERE annee=:year_observation and id_espece!=15;");
        $checkYear->bindvalue("year_observation", (int)$_GET['year_observation']);
        $checkYear->execute();
        $year_checking=$checkYear->fetch();

        if (!empty($year_checking['annee'])){
            $year_checked=$_GET['year_observation'];
            $titre="<h2>Observations en $year_checked</h2>";
            
            $reqObservations=$bd->prepare("SELECT o.id_espece, e.espece, o.nombre, o.saison, o.annee, o.nuit, o.commentaire FROM observations as o JOIN especes AS e ON o.id_espece=e.id_espece WHERE o.annee=:year_checked and o.id_espece!=15");
            $reqObservations->bindvalue("year_checked", $year_checked);
            $reqObservations->execute();
        }else {header("Location:'index.php'");}

    }elseif (isset($_GET['saison'])){
        $checkSaison=$bd->prepare("SELECT * FROM observations WHERE saison=:saison_number and id_espece!=15;");
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
            
            $reqObservations=$bd->prepare("SELECT o.id_espece, e.espece, o.nombre, o.saison, o.annee, o.nuit, o.commentaire FROM observations as o JOIN especes AS e ON o.id_espece=e.id_espece WHERE o.saison=:saison_checked and o.id_espece!=15");
            $reqObservations->bindvalue("saison_checked", $saison_checked);
            $reqObservations->execute();
        }else {header("Location:'index.php'");}

    }else {
        $reqObservations=$bd->prepare("SELECT o.id_espece, e.espece, o.nombre, o.saison, o.annee, o.nuit, o.commentaire FROM observations as o JOIN especes AS e ON o.id_espece=e.id_espece WHERE o.id_espece!=15");
        $reqObservations->execute();
        $titre="<h2>Observations</h2>";}

    #Verification du formulaire
    if (isset($_POST['year_new_observation']) and isset($_POST['day_night_radio']) and 
        isset($_POST['espece_select']) and isset($_POST['number_especes']) and isset($_POST['saison_select']) ) {

            $year_value=(int)($_POST['year_new_observation']);
            $number_especes=(int)($_POST['number_especes']);
            $saison_select=(int)($_POST['saison_select']);
            $espece_select=(int)($_POST['espece_select']);
            $day_night=(int)($_POST['day_night_radio']);

            if (isset($_POST['commentaire'])){
                $commentaire=strip_tags($_POST['commentaire']);
                $commentaire=trim($_POST['commentaire']);
            }else {$commentaire='';}

            if ( ($year_value) and ($number_especes) and ($espece_select) and ($saison_select) and ($day_night)) {
                $insertObservation=$bd->prepare("INSERT INTO observations (id_espece, nombre, annee, saison, nuit, commentaire) VALUES (:espece_select, :number_especes, :year_value, :saison_select, :day_night, :commentaire)");
                $insertObservation->bindvalue("espece_select", $espece_select);
                $insertObservation->bindvalue("number_especes", $number_especes);
                $insertObservation->bindvalue("year_value", $year_value);
                $insertObservation->bindvalue("saison_select", $saison_select);
                $insertObservation->bindvalue("day_night", $day_night);
                $insertObservation->bindvalue("commentaire", $commentaire);
                $insertObservation->execute();
                header("Location:index.php?dragon=$espece_select");
            }else {header("Location:index.php");}

        }
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
    <div id="form_container">

        <form action="index.php" id="insert_observation_form" method="POST">
            <label for="year_new_observation">- Année : </label>
            <input type="number" name="year_new_observation" min='0' id="year_new_observation" required>

            <label for="saison_select">- Saison : </label>
            <select name="saison_select" id="saison_select" required>
                <option value="1">Printemps</option>
                <option value="2">Été</option>
                <option value="3">Automne</option>
                <option value="4">Hiver</option>
            </select>
            <p> - </p>
            <input type="radio" name="day_night_radio" value="0">
            <label for="day_radio">Jour</label>
            <input type="radio" name="day_night_radio" value="1">
            <label for="night_radio">Nuit</label>

            <label for="espece_select">- Espèce : </label>
            <select name="espece_select" id="espece_select" required>
                <?php
                    $reqEspeces=$bd->prepare("SELECT * FROM especes");
                    $reqEspeces->execute();
                    while($especes=$reqEspeces->fetch()){
                        $id_especes=$especes['id_espece'];
                        $espece_name=$especes['espece'];
                        echo "<option value='$id_especes'>$espece_name</option>";}?>
            </select>
            
            <label for="number_especes">- Nombre : </label>
            <input type="number" name="number_especes" id="number_especes" min='0' required>
            
            <label for="commentaire">- Commentaire : </label>
            <input type="text" name='commentaire' id="commentaire">

            <input type="submit" value="Enregistrer observation">

        </form>

    </div>
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