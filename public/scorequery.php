<?php
require('../includes/query_conf.php');

// check input
if (isset($_GET['id'])) {
    if (ctype_digit($_GET['id']) ){
        $id = $_GET['id'];
    }
    else {
        echo json_encode(['id'=> $id, 'score' => 'bad request']);  // identify a bad id
        exit();
    }
}
else {
    echo json_encode(['id'=> $id, 'score' => 'bad request']);  // identify a bad id
    exit();
}

// get model data
$model = query("SELECT `DOI`, `compiled`, `uncompiled`, `language`, `inpCUI`, `inpdatatype`, `upper`, `lower`  FROM `models` WHERE `id` = " . $id )[0];

// clean input, then expand CUIs via derived CUIs
$CUIs = array();
$CUI_vals = [];
prep_CUIs($CUIs,$CUI_vals,$_GET);

// unpack model data
$inputs = json_decode($model['inpCUI']);
$datatypes = json_decode($model['inpdatatype']);
$uppers = json_decode($model['upper']);
$lowers = json_decode($model['lower']);

// assemble and CHECK model arguments
$modelargs = array();
foreach ($inputs as $index => $CUI) {               // TODO check CUIs and arguments
    // get the CUI's value
    $arg = $CUI_vals[$CUI];
    
    if ($datatypes[$index] == 'bool') {     // bool, convert to integer
        if ($arg == 'true') {
            $arg = '1';
        } else if ($arg == 'false') {
            $arg = '0';
        } else {
            echo json_encode(['id'=> $id, 'score' => 'bad boolean CUI: ' . $CUI]);  // identify a bad bool
            exit();
        }
    } 
    else if ($datatypes[$index] == 'integer' or $datatypes[$index] == 'int') { // integer
        if ( ctype_digit($arg) ){
            // argument is already okay
        } else if ( ctype_digit(str_replace('.','',$arg)) ) { // only non-numbers are decimal points
            if ( substr_count($arg,'.') == 1 && strlen($arg) > 1 ) { 
                // if there is only one decimal point, round to nearest integer
                $arg = (string)round((float)$arg);
            } else {
                // otherwise, error
                echo json_encode(['id'=> $id, 'score' => 'bad integer CUI: ' . $CUI]);  // identify a CUI
                exit();
            }
        } else {
            echo json_encode(['id'=> $id, 'score' => 'bad integer CUI: ' . $CUI]);  // identify a bad CUI
            exit();
        }
    } 
    else  {    // float ($datatypes[$index] == 'float')
        if (ctype_digit($arg) ) {
            // if already integer, tell python it is a float
            $arg .= '.0';
        } else if (ctype_digit(str_replace('.','',$arg)) ) {
            if (substr_count($arg,'.') == 1 && strlen($arg) > 1 ) {
                // arg is already fine
            }
            else {
                echo json_encode(['id'=> $id, 'score' => 'bad float CUI: ' . $CUI]);  // identify a bad float
                exit();
            }
        } else {
            echo json_encode(['id'=> $id, 'score' => 'bad float CUI: ' . $CUI]);  // identify a bad float
            exit();
        }
    }
    array_push($modelargs,$arg);
}


// calculate risk score
$command = null;
if ( strtolower($model['language']) == 'python' or strtolower($model['language']) == 'py') {
    $command = 'python ../scripts/pythonrisk.py "' . MODELROOT . '" "' . $model['DOI'] . '"';
    $command .= " " . json_decode($model['uncompiled'])[0];
    foreach( $modelargs as $arg) {
        $command .= " " . $arg;
    }
} else if ( strtolower($model['language']) == 'r' ) {
    //TODO
} else if ( strtolower($model['language']) == 'sas' ) {
    //TODO
}

// calculate risk score
$score = null;
$modeloutput = array();
exec($command,$modeloutput);
if (count($modeloutput) != 0) {
    $score = $modeloutput[0];
} else {
    $score = "no response from model";
}

// return id and score
$data = [
    'id'    => $id,
    'score' => $score,
];
echo json_encode($data);
?>
