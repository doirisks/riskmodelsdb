<!DOCTYPE html>

<html>

    <head>

        <link href="/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="/css/bootstrap-theme.min.css" rel="stylesheet"/>
        <link href="/css/styles.css" rel="stylesheet"/>

        <?php if (isset($title)): ?>
            <title>DORI: <?= htmlspecialchars($title) ?></title>
        <?php else: ?>
            <title>DORI: DOI tagged Risk Models</title>
        <?php endif ?>
        
        <?php setlocale(LC_MONETARY, 'en_US'); ?>

        <?php if (isset($riskfactors)) {
            echo '<script type="text/javascript" >';
            echo 'badCUIs = [];';       // a list of bad CUIs which the user has tried to look up
            echo 'hiddenCUIs = {};';    // an associative array of good CUIs which the user has removed
            echo 'riskfactors = {};';
            foreach ($riskfactors as $riskfactor) {
                if ($riskfactor != "") {
                    echo 'riskfactors["'.$riskfactor.'"] = null;';
                }
            }
        } 
        echo '</script>' ;
        ?>
        <script src="/js/jquery-1.11.1.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/scripts.js"></script>

    </head>

    <body>

        <div class="container">

            <div id="top">
                <h2 class = "pagetitle">DORI: Digital Object Risk Models by Identifier</h2>
                <!-- just debugging
                <script type="text/javascript">console.log(Object.keys(riskfactors)[0].toString()) ;</script> -->
                <span><p><a href="index.php" >Home</a> | <a href="about.php" >About</a> | <a href="datasets.php" >Score Data Set</a> | <a href="getrisks.php">Score Multiple People</a> | <a href = "upload.php">Contribute a Model</a></p></span>
            </div>
            
            <div id="middle">
