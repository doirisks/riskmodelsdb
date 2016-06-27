<?php
require('../includes/query_conf.php');

if ($_GET['short'] == NULL) {
    if ($_GET['CUI'] != NULL) {
        $CUI = htmlspecialchars($_GET['CUI']);
        // send the query
        $raw_data = query( "SELECT name1, datatype, units FROM `CUIs` WHERE CUI = '". htmlspecialchars ($CUI) ."'" )[0];
        
        // wrap the data in a reference to the CUI
        $data = [];
        $data[$CUI] = [];
        if ($raw_data == NULL) {
            $data[$CUI] = NULL;
        }
        else {
            $data[$CUI]['name'] = $raw_data['name1'];       // this is really the only processing of the raw_data that happens...
            $data[$CUI]['datatype'] = $raw_data['datatype'];
            $data[$CUI]['units'] = $raw_data['units'];
        }

        echo json_encode($data);
    } else {
        //$data = [];
        //$data[$CUI] = NULL
        //echo json_encode($data);
    }
}
else {
    echo 'booop'; //TODO
}
?>
