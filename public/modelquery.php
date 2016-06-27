<?php

require("../includes/query_conf.php");

// generally, find all models which can be scored from the CUIs obtained
if ((!isset($_GET['byID'])) and (!isset($_GET['byDOI']))) {
    // clean input, then expand CUIs via derived CUIs
    $CUIs = array();
    $CUI_vals = [];
    prep_CUIs($CUIs,$CUI_vals,$_GET);


    // build a query to get must, mustnot, and input columns for each CUI
    $to_query = "SELECT `CUI`,`must`,`mustnot`,`input` FROM `CUIs` WHERE CUI = '";
    foreach($CUIs as $CUI) {
        $to_query .= htmlspecialchars ($CUI);
        $to_query .= "' OR CUI = '";
        // debug submitted CUIs
        //echo $CUI . "<br>";
    }
    $to_query .= "'";

    // debug query
    //echo "<p>" . htmlspecialchars($to_query) . "</p>";

    // send query
    $CUI_data = query($to_query);

    // debug reply
    /*foreach($CUI_data as $CUI_datum) {
        echo "<p>" . $CUI_datum['CUI'].": ". $CUI_datum['input'] . "</p>";
    }*/

    // count up the inputs for each id
    $inputIDcounts = [];
    foreach ($CUI_data as $datum) {
        $inputIDs = json_decode($datum['input']);
        foreach ($inputIDs as $ID) {
            if ( array_key_exists($ID,$inputIDcounts) ) {
                $inputIDcounts[$ID] += 1;
            }
            else {
                $inputIDcounts[$ID] = 1;
            }
        }
    }


    $modelIDs = array_keys($inputIDcounts);     // this line used in debug AND in actual code
    // debug counting
    /*foreach( $modelIDs as $modelID ) {
        echo "<p>". $modelID . ": " . $inputIDcounts[$modelID]."</p>\n";
    }*/

    // get keys
    $to_query = "SELECT `id`, `authors`, `yearofpub`, `outcome`, `outcometime`, `DOI`, `papertitle`, `modeltitle`, `inpCUI` FROM `models` WHERE ( id = ";
    foreach ($modelIDs as $modelID) {
        $to_query .= htmlspecialchars ($modelID);
        $to_query .= " AND numofinputs = ";
        $to_query .= htmlspecialchars ($inputIDcounts[$modelID]);
        $to_query .= " ) OR ( id = ";
    }
    if (strlen($to_query) > 134) {
        $to_query = substr($to_query,0, -11);
    }
    else {
        echo json_encode(array());
        exit();
    }
    //echo '<p>'.$to_query."</p>";

    $models = query($to_query);
    $data = array();
    foreach($models as $model) {
        // process all of the non-JSON fields
        $model['inpCUI'] = json_decode($model['inpCUI']); 
        $model['authors'] = json_decode($model['authors']);
        array_push($data,$model);
    }
    echo json_encode($data);
    exit();
}

// otherwise, try multiple options
$data = array();
// get models by DOI
if (isset($_GET['byDOI']) and ($_GET['byDOI'] == 'true')) {
    if (isset($_GET['DOI']) and strstr($_GET['DOI'],"'") === false) {
        
        // write the query string
        $to_query = "SELECT `id`, `authors`, `yearofpub`, `outcome`, `outcometime`, `DOI`, `papertitle`, `modeltitle`, `inpCUI` FROM `models` WHERE DOI = '" . $_GET['DOI'] . "'";
        
        $models = query($to_query);
        foreach($models as $model) {
            // process all of the non-JSON fields
            $model['inpCUI'] = json_decode($model['inpCUI']); 
            $model['authors'] = json_decode($model['authors']);
            array_push($data,$model); // add to array
        }
    }
    else { 
        // if invalid DOI was sent, ignore.
    }
} 
//get models by ID
if (isset($_GET['byID']) and ($_GET['byID'] == 'true')) {
    if (isset($_GET['ID']) and ctype_digit($_GET['ID'])) {
        
        // write the query string
        $to_query = "SELECT `id`, `authors`, `yearofpub`, `outcome`, `outcometime`, `DOI`, `papertitle`, `modeltitle`, `inpCUI` FROM `models` WHERE id = " . $_GET['ID'] . "";
        
        $models = query($to_query);
        $data = array();
        
        foreach($models as $model) {
            // process all of the non-JSON fields
            $model['inpCUI'] = json_decode($model['inpCUI']); 
            $model['authors'] = json_decode($model['authors']);
            array_push($data,$model); // add to array
        }
    }
    else { 
        // if invalid ID was sent, ignore.
    }
}
echo json_encode($data);

?>
