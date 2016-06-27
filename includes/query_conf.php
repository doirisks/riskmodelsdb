<?php
    /**
     * constants.php
     *
     * DOI Risk Interface
     *
     * Global constants.
     */

    // your database's name
    define("DATABASE", "doiarchive");

    // your database's password
    define("PASSWORD", "bitnami");

    // your database's server
    define("SERVER", "localhost");

    // your database's username
    define("USERNAME", "doirisks");
    
    define("MODELROOT","/home/fyodr/dori-master/models/");

    /**
     * Executes SQL statement, possibly with parameters, returning
     * an array of all rows in result set or false on (non-fatal) error.
     */
    function query(/* $sql [, ... ] */)
    {
        // SQL statement
        $sql = func_get_arg(0);

        // parameters, if any
        $parameters = array_slice(func_get_args(), 1);

        // try to connect to database
        static $handle;
        if (!isset($handle))
        {
            try
            {
                // connect to database
                $handle = new PDO("mysql:dbname=" . DATABASE . ";host=" . SERVER, USERNAME, PASSWORD);

                // ensure that PDO::prepare returns false when passed invalid SQL
                $handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
            }
            catch (Exception $e)
            {
                // trigger (big, orange) error
                trigger_error($e->getMessage(), E_USER_ERROR);
                exit;
            }
        }

        // prepare SQL statement
        $statement = $handle->prepare($sql);
        if ($statement === false)
        {
            // trigger (big, orange) error
            trigger_error($handle->errorInfo()[2], E_USER_ERROR);
            exit;
        }

        // execute SQL statement
        $results = $statement->execute($parameters);

        // return result set's rows, if any
        if ($results !== false)
        {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Cleans input, expands known CUIs to find directly derived CUIs
     */
    function prep_CUIs(&$CUIs, &$CUI_vals, $QUERYDATA) {// cleaning (and debugging)
        $CUIs = array_keys($QUERYDATA);
        $CUI_vals = [];
        foreach ($CUIs as $CUI) {
            $CUI = htmlspecialchars ($CUI);
            $CUI_vals[$CUI] = $_GET[$CUI];
            // debugging CUIs passed
            //echo "<p>" . $CUI . ", ". $_GET[$CUI] . "</p>\n";
        }
        
        // expanding known CUIs
        // sex (m/f)
        if (in_array('C28421', $CUIs)) {
            array_push($CUIs,'C0086582');               //male sex CUI
            //array_push($CUIs,'');                       //female sex CUI
            if (strcasecmp($CUI_vals['C28421'],'male') == 0) {
                $CUI_vals['C0086582'] = 'true';
                //$CUI_vals['???'] = 'false';               //female sex
            }
            else {
                $CUI_vals['C0086582'] = 'false';
                //$CUI_vals['???'] = 'true';                //female sex
            }
        }

        // BMI
        if (in_array('heightCUI',$CUIs) && in_array('weightCUI',$CUIs)) {
            array_push($CUIs,'BMI_CUI');
            $CUI_vals['BMI_CUI'] = $CUI_vals['BMI_CUI'];
        }
        
        // add disjunctive CUIs ("CUIs" with OR in them)
        // build a query
        $to_query = "SELECT `CUI` FROM `CUIs` WHERE (CUI != '";
        foreach ($CUIs as $CUI) {
            if ( strcasecmp($CUI_vals[$CUI],'true') == 0) {
                $to_query .= $CUI;
                $to_query .= "' AND CUI LIKE '%";
                $to_query .= $CUI;
                $to_query .= "%') OR ( CUI != '";
            }
        }
        // only use the query if something was added to it
        if (strcasecmp($to_query,"SELECT `CUI` FROM `CUIs` WHERE (CUI != '") == 0) {
            // do nothing
        }
        else {
            $to_query = substr($to_query,0,-14);
            // debug here
            //echo htmlspecialchars($to_query);
            $data = query($to_query);
            foreach( $data as $datum ) {
                array_push($CUIs,$datum['CUI']);
                $CUI_vals[$datum['CUI']] = "true";
            }
        }
    }
?>
