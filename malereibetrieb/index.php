<?php
    include("include/config.php");
    include("include/conn.php");
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Mitarbeiter</title>
    <link rel="stylesheet" href="include/style.css">
</head>
<body>
    <h1>Mitarbeiter</h1>
    
    <form method="post">
        <fieldset>
            <legend>Mitarbeiterfilter</legend>
            <label>
                Vorname:
                <input type="text" name="VN_MA" placeholder="Vorname">
            </label>
            <label>
                Nachname:
                <input type="text" name="NN_MA" placeholder="Nachname">
            </label>
            <label>
                <input type="submit" value="filter"> 
            </label>
        </fieldset>
        <fieldset>
            <legend>Kundenfilter</legend>
            <label>
                Vorname:
                <input type="text" name="VN_K" placeholder="Vorname">
            </label>
            <label>
                Nachname:
                <input type="text" name="NN_K" placeholder="Nachname">
            </label>
            <label>
                <input type="submit" value="filter"> 
            </label>
        </fieldset>
    </form> 

    <?php
        $arr = [];
        $where = "";

        /*** Filter für die Mitarbeiter. Falls etwas mit dem Formular bageschickt wird, wird der Filter gesetzt ***/
        if (count($_POST)>0){
            if (strlen($_POST["VN_MA"]) > 0){
                $arr[] = "VorN_Mitarbeiter='" . $_POST["VN_MA"] . "'";
            }
            if (strlen($_POST["NN_MA"]) > 0){
                $arr[] = "NachN_Mitarbeiter='" . $_POST["NN_MA"] . "'";
            }
        }

        if (count($arr)>0){
            $where = " WHERE (" . implode(" AND ",$arr) . ")";
        }

        
        
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($currentPage - 1) * $limit;

        /*** Erste SQL-Abfrage mit dem Filter ***/
        $sql = "SELECT * FROM tbl_mitarbeiter " . $where . " LIMIT $offset, $limit";

        $mitarbeiterliste = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);

        $totalRecordsQuery = "SELECT COUNT(*) AS total FROM tbl_mitarbeiter " . $where;
        $totalRecordsResult = $conn->query($totalRecordsQuery);
        $totalRecordsRow = $totalRecordsResult->fetch_assoc();
        $totalRecords = $totalRecordsRow['total'];
        $totalPages = ceil($totalRecords / $limit);

        /*** Hier wird alles ausgegeben was ausgegeben werden soll 
             Die erste While-Schleife gibt alle gefilterten Infos über die Mitarbeiter aus ***/
        while ($mitarbeiter = $mitarbeiterliste->fetch_object()){
            echo("<ul>" . 
                "<li>
                Nachname: " . $mitarbeiter->NachN_Mitarbeiter . 
                " | Vorname: " . $mitarbeiter->VorN_Mitarbeiter . 
                " | SVNR: " . $mitarbeiter->SVNR . 
                " | Geb.Datum: " . $mitarbeiter->Geburtsdatum . 
                " | Email: " . $mitarbeiter->Email .
                "</li>"
            );
            

            $arr = ["ID_Mitarbeiter = " . $mitarbeiter->ID ];
    
            /*** Neuer Filter für die Kunden ***/
            if (count($_POST)>0){
                if (strlen($_POST["VN_K"])>0){
                    $arr[] = "tbl_kunde.VorN_Kunde = '" . $_POST["VN_K"] . "'";
                }
                if (strlen($_POST["NN_K"])>0){
                    $arr[] = "tbl_kunde.NachN_Kunde = '" . $_POST["NN_K"] . "'";
                }
            }
    
            $where = " WHERE(" . implode(" AND ",$arr) . ") ";
    
            /*** Zweite SQL-Abfrage mit dem Filter der Kunden ***/
            $sql = "SELECT * FROM tbl_auftragsliste 
                    LEFT JOIN tbl_kunde ON tbl_kunde.ID = tbl_auftragsliste.ID_Kunde 
                    " . $where . "
                    ORDER BY tbl_auftragsliste.Arbeitsbeginn ASC, tbl_auftragsliste.Arbeitsende ASC";
     
            $auftragsliste = $conn->query($sql) or die("Fehler in der Query: " . $conn->error . "<br>" . $sql);
            echo("<ul>");
            while ($auftrag = $auftragsliste->fetch_object()){
                echo("<li>
                    Arbeitsbeginn: " . $auftrag->Arbeitsbeginn .
                    " Arbeitsende: " . $auftrag->Arbeitsende .
                    " Vorname: " . $auftrag->VorN_Kunde . 
                    " Nachname: " . $auftrag->NachN_Kunde .
                "</li>");
            }
            echo("</ul>");
    
            echo("</ul>");
        }

        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a href='?page=$i'>$i</a>";
        }

        /*** Debug ***/

        if ($debug) {
            echo "<div class='debug'>";
            echo "<br>Debugmod is on. Set it to false in includes/config.php<br>";
            echo "<br><br>Total Records: $totalRecords<br>";
            echo "Total Pages: $totalPages<br>";
            echo "Current Page: $currentPage";
            echo "</div>";
        }
        
    ?>    
</body>
</html>