<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;" charset="UTF-8" />
        <title>Dynamic VR Spaces</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta property="og:title" content="Dynamic Virtual Space Designer " />
        <meta property="og:description" content="DataScribe Ltd Dynamic Desginer V0 (March 2024)" />
        <meta property="og:image" content="https://ukcpg.co.uk/images/gallery2.jpg" />
        <meta property="og:url" content="https://ukcpg.co.uk/scripts/dynamic.php?building=1" />
        <meta property="og:type" content="website" />        
          
        <!-- Babylon.js -->
        <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
        
<!--        <script src="https://cdn.babylonjs.com/ammo.js"></script>-->
<!--        <script src="https://cdn.babylonjs.com/cannon.js"></script>-->
        <script src="https://cdn.babylonjs.com/havok/HavokPhysics_umd.js"></script>
        <script>
            // before initializing your babylon scene:
            let havokInstance;
            HavokPhysics().then((havok) => {
                // Havok is now available
                havokInstance = havok;
            });
        </script>
        
        <script src="https://cdn.babylonjs.com/babylon.js"></script>
<!--        <script src="https://cdn.babylonjs.com/materialsLibrary/babylonjs.materials.min.js"></script>-->
<!--        <script src="https://cdn.babylonjs.com/loaders/babylonjs.loaders.min.js"></script>-->
<!--        <script src="https://cdn.babylonjs.com/postProcessesLibrary/babylonjs.postProcess.min.js"></script>-->
<!--        <script src="https://cdn.babylonjs.com/proceduralTexturesLibrary/babylonjs.proceduralTextures.min.js"></script>-->
<!--        <script src="https://cdn.babylonjs.com/serializers/babylonjs.serializers.min.js"></script>-->
<!--        <script src="https://cdn.babylonjs.com/gui/babylon.gui.min.js"></script>-->
<!--        <script src="https://cdn.babylonjs.com/inspector/babylon.inspector.bundle.js"></script>-->
<!--        <script src="https://cdn.babylonjs.com/viewer/babylon.viewer.js"></script>-->

        <style>
            html, body {
                overflow: hidden;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                touch-action: none;
                -ms-touch-action: none;
                font-family: arial;
            }
            
            #renderCanvas {
                width: 100%;
                height: 100%;
                touch-action: none;
            }
            
            #cell {
                position:fixed;
                right:1vw;
                top:1vh;
                width:5vw;
                height:5vw;
                background-color:black;
            }
            
            .fps{
                position:absolute;
                top:1vh;
                left:1vw; 
                opacity:0.5;
                border-radius:10px;
                background-color:black;
                color:orange;
                font-size:1.5em;
                padding:0.5vw;
                opacity:0.5;
                font-family:arial;
                font-weight:bold;
            }  
            
            .energy{
                position:absolute;
                bottom:1vh;
                left:1vw; 
                opacity:0.5;
                border-radius:10px;
                background-color:black;
                color:orange;
                font-size:1.5em;
                padding:0.5vw;
                opacity:0.5;
                font-family:arial;
                font-weight:bold;
                width:40vw;
                text-align:left;
            }  
            
            .energybar {
                width:100%;
                height:5vh;
                background-color:green;
            }
            
            .start {
                position:absolute;
                top:0;
                left:0;
                width:100%;
                height:100%;
                padding-top:15vh;
                font-size:2.5vw;
                text-align:center;
                background-color:aliceblue;
                color:silver;
                font-weight:bold;
                cursor:pointer;
                opacity:1;

            }            
            
            .loading {
                position:absolute;
                top:0;
                left:0;
                width:100%;
                height:100%;
                padding-top:15vh;
                font-size:2.5vw;
                text-align:center;
                background-color:white;
                color:silver;
                font-weight:bold;
                opacity:1;
            }
            
            
            .title {
                position:absolute;
                top:0;
                left:0;
                width:100%;
                height:100%;
                padding-top:15vh;
                font-size:2.5vw;
                text-align:center;
                background-color:white;
                color:grey;
                font-weight:bold;
                cursor:pointer;
                opacity:1;
            }   
            
            .gameOver {
                position:absolute;
                top:0;
                left:0;
                width:100%;
                height:100%;
                padding-top:33vh;
                font-size:8vw;
                text-align:center;
                background-color:indianred;
                color:white;
                font-weight:bold;
                cursor:pointer;
                opacity:0.9; 
                display:none;
            }

            a {text-decoration:none; color:white;}
        </style>
    </head>
    <body>
    <?php
        ini_set('max_execution_time', 900);
        ini_set('display_errors', 1);
        ini_set('memory_limit', '128M');
        
        require("dynamicCNF.php");
        
        // Basic Site Configuration
        $table = "config";
        $val0 = $userdb->query("SELECT 1 FROM `$table` LIMIT 1");

        if (!$val0){
            if (!$userdb->query("CREATE TABLE `$table` ("
                    . "`configID` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, "
                    . "`configName` VARCHAR(255), "
                    . "`configValue` VARCHAR(255), "
                    . "`configDescription` VARCHAR(255), "
                    . "`configUpload` TINYINT(4) DEFAULT '0', "
                    . "`configType` INT(6) DEFAULT '1', "
                    . "`configDataType` VARCHAR(16) DEFAULT '1', "
                    . "`configEncode` INT(6) DEFAULT '2', "
                    . "`configCreated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, "
                    . "`configUpdated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP "
                    . ")")){die("$table Table Creation Failed");}
            else {
                // configType 0 = Please Encrypt, 1 = Encrypted, 2 = No Encryption
                // configDataType 1 = text, 2 = integer, 3 = decimal, 4 = date, 5 = datetime, 6 = password
                if (!$userdb->query("INSERT INTO `$table` (`configName`, `configValue`, `configDescription`,`configUpload`,`configType`,`configDataType`,`configEncode`) "
                        . "VALUES ('siteName', 'Site Name', 'Application Name', '0', '1', '1','2') ")){die("Record Creation Failed ".mysqli_error($userdb));} 
                if (!$userdb->query("INSERT INTO `$table` (`configName`, `configValue`, `configDescription`,`configUpload`,`configType`,`configDataType`,`configEncode`) "
                        . "VALUES ('siteDescription', 'Site Description', 'Application Description', '0', '1', '1', '2') ")){die("Record Creation Failed ".mysqli_error($userdb));}
                if (!$userdb->query("INSERT INTO `$table` (`configName`, `configValue`, `configDescription`,`configUpload`,`configType`,`configDataType`,`configEncode`) "
                        . "VALUES ('siteAdmin', 'administrator@$domain', 'Support Email Address', '0', '1', '1', '2') ")){die("Record Creation Failed ".mysqli_error($userdb));}                
            }
        }
        
        $table = "cells";
        $val1 = $userdb->query("SELECT 1 FROM `$table` LIMIT 1");

        if (!$val1){
            if (!$userdb->query("CREATE TABLE `$table` ("
                    . "`cellsID` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, "
                    . "`building` INT(6), "
                    . "`cellX` INT(6), "
                    . "`cellZ` INT(6), "
                    . "`cellY` INT(6), "
                    . "`celltype` VARCHAR(3) "
                    . ")")){die("$table Table Creation Failed");}      
        }
        
        $table = "cells_backup";
        $val2 = $userdb->query("SELECT 1 FROM `$table` LIMIT 1");

        if (!$val2){
            if (!$userdb->query("CREATE TABLE `$table` ("
                    . "`cellsID` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, "
                    . "`building` INT(6), "
                    . "`cellX` INT(6), "
                    . "`cellZ` INT(6), "
                    . "`cellY` INT(6), "
                    . "`celltype` VARCHAR(3) "
                    . ")")){die("$table Table Creation Failed");}      
        }   
        
        
        if (!$_GET["building"]){$building = 1;} 
        else {$building = $_GET["building"];}
        
        $s = "";
        $e = "";
        $levels  = 7;
        $rows    = 20;
        $columns = 10;      
        $err = 0;
        
        // check db
        $select = "SELECT cellX, COUNT(cellX), cellY, count(cellY), cellZ, count(cellZ), celltype "
                . "FROM cells WHERE building='$building' "
                . "GROUP BY cellX, cellY, cellZ "
                . "HAVING COUNT(cellX)>1 AND COUNT(cellY)>1 AND COUNT(CellZ)>1 ";
        $duplicates = $userdb->query($select);
        if (mysqli_num_rows($duplicates)){
            while ($dup = mysqli_fetch_array($duplicates)){
                echo "<div>X:".$dup["cellX"]." Z:".$dup["cellY"]." Y:".$dup["cellY"]." Type:".$dup["celltype"]."</div>";
            }
            echo "<div>WARNING: Duplicated Cells Definitions Found</div>";
        }
        
        $y=0;
        while ($y<$levels){
            $map[$y] = $s."[";
            $z = 0;
            while ($z<$rows){
                $map[$y].= $s."[";
                $x = 0;
                while ($x<$columns){
                    
                    if (!$cells = $userdb->query("SELECT * FROM `cells` "
                            . "WHERE `building`='$building' "
                            . "AND `cellY`='$y' AND `cellZ`='$z' AND `cellX`='$x' ")){die(mysqli_error($userdb));}
                    if (!mysqli_num_rows($cells)){
                        $map[$y].="'00', ";
                        $err++;
                    } else {
                        $cell = mysqli_fetch_assoc($cells);
                        $map[$y].="'".$cell["celltype"]."', ";
                    }
                    $x++;                     
                }                
                $map[$y].="],".$e;
                $z++;
            }          
            $map[$y].="]".$e;
            //echo $map[$y];
            $y++;
        }
        
        if (!$err){$dbmap="yes";}
    
        function isMobile() {
            return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
        }

    ?>
        <canvas id="renderCanvas"></canvas>
        <div id="cell"></div>
        <div class="fps"><span id="frames">0</span></div>
        <div class="energy">
            <div class="energybar"></div>
        </div>
        <div class="gameOver">
            <div class="gameOverMessage"></div>
                
        </div>
        <div class="start">
            <div>Dynamic Worlds V0.0 (alpha testing)</div>
            <hr>
            <div><img src="textures/build.jpg" style="max-width:60vw;max-height:60vh;opacity:0.3;"></div>

            <div id="startup" style="color:orange;padding-top:5vh;">Click to Enter World</div>                
        </div>
        <div class="loading">
            <div>Dynamic Worlds V0.0 (alpha testing)</div>
            <hr>
            <div><img src="textures/loading.gif" style="max-width:60vw;max-height:50vh;"></div>
            <div>Rendering 3D Assets</div>
        </div>
        <div class="title">
            <div>Dynamic Worlds V0.0 (alpha testing)</div>
            <hr>
            <div style="font-size:2vw;">This application uses the 3D Engine BabylonJS.</div>
            <div style="font-size:2vw;">The world has to be rendered before you can play.</div>

            <div style="padding-top:5vh;color:indianred;">Click on the screen to start rendering.</div>

            <div style="padding-top:5vh;color:lightsalmon;opacity:0.6;">DataScribe Limited</div>
            <div style="color:lightblue;opacity:0.6;">Creative Software Design</div>
        </div>

        <script>

            console.log("Start Scripts");
            // Global Functions  
            // Create 1 Dimensional Array
            function Create1DArray(rows) {
              var arr = [];
              for (var i=0;i<rows;i++) {
                 arr[i] = [];
              }
              return arr;
            }

            // Create 2 Dimentional Array
            function Create2DArray(rows1,rows2) {
              var arr = [];
              for (var i=0;i<rows1;i++) {
                 arr[i] = [];
                 for (var j=0;j<rows2;j++){
                     arr[i][j]= [];
                 }
              }
              return arr;
            }
            // Create 3 Dimentional Array
            function Create3DArray(rows1,rows2,rows3) {
              var arr = [];
              for (var i=0;i<rows1;i++) {
                 arr[i] = [];
                 for (var j=0;j<rows2;j++){
                     arr[i][j]= [];
                     for (var k=0;k<rows3;k++){
                        arr[i][j][k]= [];  
                    }
                 }
              }
              return arr;
            }     
            
            function refreshLoop() {
                window.requestAnimationFrame(() => {
                const now = performance.now();
                    while (times.length > 0 && times[0] <= now - 1000) {
                        times.shift();
                    }
                times.push(now);
                fps = times.length;
                refreshLoop();
                });
            }
            
            function getRndInt(max) {
                return Math.floor(Math.random() * max);
            }
            
            console.log("Set Variables");
            
            var building = "<?=$building;?>";
            var dbmap = "<?=$dbmap;?>";
            if (dbmap=="yes"){
                    var wallmap0 = <?=$map[0];?>;
                    var wallmap1 = <?=$map[1];?>;
                    var wallmap2 = <?=$map[2];?>;
                    var wallmap3 = <?=$map[3];?>;
                    var wallmap4 = <?=$map[4];?>;
                    var wallmap5 = <?=$map[5];?>;
                    var wallmap6 = <?=$map[6];?>;
            } else {
                // Build Wall Definitions
                // Ground
                var wallmap0 = [
                    [ "05", "31", "25", "31", "01", "01", "31", "25", "31", "06"],
                    [ "34", "15", "15", "15", "15", "15", "15", "15", "15", "32"],
                    [ "28", "15", "42", "15", "15", "15", "15", "44", "15", "26"],
                    [ "34", "15", "15", "16", "16", "16", "16", "15", "15", "32"],
                    [ "04", "15", "15", "16", "16", "16", "16", "15", "15", "02"],
                    [ "04", "15", "15", "16", "16", "16", "16", "15", "15", "02"],
                    [ "34", "15", "15", "16", "16", "16", "16", "15", "15", "32"],
                    [ "28", "15", "42", "15", "15", "15", "15", "44", "15", "26"],
                    [ "34", "15", "15", "15", "15", "15", "15", "15", "15", "32"],
                    [ "08", "33", "27", "33", "03", "03", "33", "27", "33", "07"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],    
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],                     
                ];   
                // 1st Floor
                var wallmap1 = [
                    [ "05", "31", "01", "31", "01", "01", "31", "01", "31", "06"],
                    [ "34", "44", "16", "16", "15", "15", "16", "16", "42", "32"],
                    [ "04", "16", "00", "16", "15", "15", "16", "00", "16", "02"],
                    [ "34", "16", "16", "00", "00", "00", "00", "16", "16", "32"],
                    [ "34", "16", "16", "00", "00", "00", "00", "16", "16", "02"],
                    [ "34", "16", "16", "00", "00", "00", "00", "16", "16", "02"],
                    [ "34", "16", "16", "00", "00", "00", "00", "16", "16", "32"],
                    [ "04", "16", "00", "16", "16", "16", "16", "00", "16", "02"],
                    [ "34", "44", "16", "16", "16", "16", "16", "16", "42", "32"],
                    [ "08", "33", "03", "03", "33", "33", "03", "03", "33", "07"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],  
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],                     
                ]; 
                // 2nd Floor
                var wallmap2 = [
                    [ "05", "01", "01", "31", "01", "01", "31", "01", "01", "06"],
                    [ "04", "17", "17", "19", "18", "18", "19", "17", "17", "02"],
                    [ "34", "17", "17", "19", "18", "18", "19", "17", "17", "32"],
                    [ "34", "19", "19", "19", "19", "19", "19", "19", "19", "32"],
                    [ "04", "17", "17", "19", "17", "17", "19", "17", "17", "02"],
                    [ "04", "17", "17", "19", "17", "17", "19", "17", "17", "02"],
                    [ "34", "19", "19", "19", "19", "19", "19", "19", "19", "32"],
                    [ "34", "17", "17", "19", "17", "17", "19", "17", "17", "32"],
                    [ "04", "17", "17", "19", "17", "17", "19", "17", "17", "02"],
                    [ "08", "03", "33", "03", "03", "03", "03", "33", "03", "07"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],  
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"], 
                ];  
                // 3rd Floor
                var wallmap3 = [
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"], 
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],                     
                ];     
                
                // 4th Floor
                var wallmap4 = [
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],  
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],                     
                ];  
                
                // 5th Floor
                var wallmap5 = [
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],  
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],                     
                ];                  
                // 6th Floor
                var wallmap6 = [
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],  
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],
                    [ "00", "00", "00", "00", "00", "00", "00", "00", "00", "00"],                     
                ];      
            }
                       
            // cell types
//            var celltypes = [
//                "00","01","02","03","04","05","06","07","08","09","10",
//                "11","12","13","14","15","16","17","18","19","20","40",
//                "21","22","23","24","25","26","27","28","29","30",
//                "31","32","33","34","41","42","43","44"
//            ];
            
            // Reduced cell types
            var celltypes = [
                "00","01","02","03","04","05","06","07","08","09","10",
                "15","16","17","18","19",
                "21","22","23","24","25","26","27","28",
                "31","32","33","34","41","42","43","44"
            ];            
            
            // Floor Levels
            var levels    = <?=$levels;?>;
            var gridsizeX = <?=$columns-1;?>;
            var gridsizeZ = <?=$rows-1;?>;
            
            // Build Variables
            var gwidth = 200;  // Ground Width
            var gdepth = 200;  // Ground Depth
            
            var xs = 5; // X scale
            var ys = 5; // Y scale
            var zs = 5; // Z scale
            
            var worldx = gwidth;
            var worldz = gdepth;
                        
            var wallwidth  = xs;
            var walldepth  = zs/25; //was 50
            var wallheight = ys;
            
            var floordepth   = zs/100;
            var ceilingdepth = zs/100;
            var barrierwidth = zs*0.99;
            var barrierheight = wallheight/3.1;
            
            var iheight = 2.2
            var sunX = 0;
            var sunZ = 0;
            var sunY = 15;
            var sunD = 4; // Diameter
            
            // Edit Cell Start Point
            var cellPosX = 2
            var cellPosZ = 2;
            var cellPosY = 0;
            
            // Camera Cell Start Point
            var cameraPosX = 6;
            var cameraPosZ = 6;
            var cameraPosY = 0;
            
            var fps = 0;
            var times = [];
            
            // Global Animation Times
            var speed1 = 25; //frames per second move
            var tf     = 200; //No of seconds
            
            var framerate = 25; //frames per second
            var totalframes = 200; //total frames in sequence
            
            var rotate = [];
            rotate[1] = -Math.PI;    // 0
            rotate[2] = Math.PI/2;  // 90
            rotate[3] = 0;   // 180
            rotate[4] = -Math.PI/2; // 270
            
            var offsetx = [];
            offsetx[1] = 0;
            offsetx[2] = 1;
            offsetx[3] = 0;
            offsetx[4] = -1;
            
            var offsetz = [];
            offsetz[1] = -1;
            offsetz[2] = 0;
            offsetz[3] = 1;
            offsetz[4] = 0;   
            
            var rotatex = [];
            rotatex[1] = 0;
            rotatex[2] = -1;
            rotatex[3] = 0;
            rotatex[4] = -1;      
            
             var rotatez = [];
            rotatez[1] = -1;
            rotatez[2] = 0;
            rotatez[3] = -1;
            rotatez[4] = 0;   
            
            console.log("Create 3D Arrays");
            
            //  Create3DArray(x,z,y)
            var cell        = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var wall0       = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var wall1       = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var wall2       = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var wall3       = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var floor       = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var ceiling     = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var door1       = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var door2       = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var hinge1      = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var hinge2      = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);            
            var doorstatus  = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var halfwindow  = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var subwall     = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var ramp        = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var stair       = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var handrail1   = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var handrail2   = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var barrier0    = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var barrier1    = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var barrier2    = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var barrier3    = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var skirt0      = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var skirt1      = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var skirt2      = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var skirt3      = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);            
            var column1     = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var column2     = Create3DArray(gridsizeX+1, gridsizeZ+1, levels); 
            var exitbox     = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var wallLight   = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            var floormat    = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            
            var floorAggregate = Create3DArray(gridsizeX+1, gridsizeZ+1, levels);
            
            var nchar          = 5;
            var startMovement  = false;
            var energy = 100;
            
            var character          = Create1DArray(nchar);
            var characterBody      = Create1DArray(nchar);
            var characterShape     = Create1DArray(nchar);
            var characterAggregate = Create1DArray(nchar);
            
            var characterSound = Create1DArray(nchar);
            var charPosX       = Create1DArray(nchar);
            var charPosY       = Create1DArray(nchar);
            var charPosZ       = Create1DArray(nchar);
            var charRot        = Create1DArray(nchar);
            var charMoving     = Create1DArray(nchar);
            var charStatus     = Create1DArray(nchar);
            var charPhysics    = Create1DArray(nchar);
            var charHits       = Create1DArray(nchar);
            var chmove         = Create1DArray(nchar);
            var chrotate       = Create1DArray(nchar);
            
            var brainMaterial     = Create1DArray(nchar);
            var characterMaterial = Create1DArray(nchar);
            
            var charMass = 1;
            var charFriction = .5;
            var charRestitution = .1;
            
            var rampFriction = -0.1;
            var rampRestitution = -0.1;
            
            var bulletMass = 0.01;
            var bulletFriction = 10;
            var bulletRestitution = 0;
            
            var maxbullets      = 10;
            var firedBullet    = Create1DArray(maxbullets);
            var fbBody         = Create1DArray(maxbullets);
            var fbShape        = Create1DArray(maxbullets);
            var fbAggregate    = Create1DArray(maxbullets);
            var bulletCount    = 0;
            
            var showWallLight = null;
            
            console.log("Set Character Locations");
            var ic = 0;
            while (ic<nchar){
                var sflag=0;
                var tries =0;
                while (sflag==0){
                    var tempX = getRndInt(gridsizeX);
                    var tempY = getRndInt(3);
                    //var tempY = 0;
                    var tempZ = getRndInt(gridsizeZ);
                    // check if already occupied;
                    var check = true;
                    var ncheck = 0;
                    while (ncheck<ic){
                       if (tempX===charPosX[ncheck] && tempY===charPosY[ncheck] && tempZ===charPosZ[ncheck]){
                           check = false;
                           break;
                       } 
                       ncheck++;
                    }
                    
                    if (check===true){
                        charPosX[ic]   = tempX;
                        charPosY[ic]   = tempY;
                        charPosZ[ic]   = tempZ;                    

                        var celltype = window["wallmap"+charPosY[ic]][charPosZ[ic]][charPosX[ic]];
                        if(celltype==="15" || celltype==="16" || celltype==="18" || tries>10){
                            sflag=1;
                        } else {tries++;}
                    }
                }
                //console.log("Set Position "+ic+" X:"+charPosX[ic]+" Z:"+charPosZ[ic]+" Y:"+charPosY[ic]);
                charMoving[ic]  = false;
                charStatus[ic]  = "alive";
                charPhysics[ic] = "false";
                charHits[ic]    = 20;
                ic++;
            }
            
            var objects = [
                "wall0","wall1","wall2","wall3","floor","ceiling","door1","door2","hinge1","hinge2",
                "halfwindow","subwall","ramp","stair","handrail1","handrail2",
                "barrier0","barrier1","barrier2","barrier3","column1","column2","skirt0","skirt1","skirt2","skirt3",
                "exitbox","wallLight","floormat"
            ];
            
            var moving    = false;
            var editalpha = 0.5;  
            var alphaStatus;
            var editing = false;
            console.log("End of Javascript Variables & Start of BabylonJS");  
            const canvas = document.getElementById("renderCanvas"); // Get the canvas element
            const engine = new BABYLON.Engine(canvas, true); // Generate the BABYLON 3D engine
            
    $(".title").on("click",function(){    
        $(".title").fadeOut(1000);    
        function start(){
            const createScene = async function () {                                 
                    
                $(".start").on("click",function(){ 
                                       
                    createCharacters();                    
                    $(".start").fadeOut(1000);                   
                    bindkeys();  
                    
//                    const music = new BABYLON.Sound("dreaming", "music/Dreaming.mp3", scene, null, {
//                            loop: true,
//                            autoplay: true,
//                            volume:0.1
//                    });

                });               
                                                        
                function bindkeys (){
                    var shoot = new BABYLON.Sound("shoot","sounds/blockhit.wav",scene,null, {
                        loop:false,
                        autoplay: false
                    });
 
                    document.onkeydown = function(event) {
                        var key_code   = event.keyCode;  
                        var key_press  = String.fromCharCode(event.keyCode);
                        
                        if (key_code==69){ //Start Editing
                            editing = true;
                            var startview = new BABYLON.Vector3(Xpos(cellPosX),Ypos(cellPosY)+iheight,Zpos(cellPosZ));
                            camera.setTarget(startview);
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = true;
                            startMovement = false;
                        }
                        else if (key_code==70){ // End Editing
                            setMaterials(1);
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = false;
                            editing = false;
                            startMovement = true;
                        }
                        else if (key_code==32){ 
                            shoot.play();
                            firedBullet[bulletCount] = bullet.createInstance("firedBullet_"+bulletCount);
                            firedBullet[bulletCount].position.x = camera.position.x;
                            firedBullet[bulletCount].position.z = camera.position.z;
                            firedBullet[bulletCount].position.y = camera.position.y;
                            firedBullet[bulletCount].checkCollisions = true;                    
                            firedBullet[bulletCount].isVisible = true;
                            
                            fbAggregate[bulletCount] = new BABYLON.PhysicsAggregate(firedBullet[bulletCount], BABYLON.PhysicsShapeType.BOX, { mass:bulletMass, friction:bulletFriction, restitution:bulletRestitution}, scene);
                            fbAggregate[bulletCount].body.setCollisionCallbackEnabled(true);
                            fbAggregate[bulletCount].body.applyImpulse(camera.getDirection(BABYLON.Vector3.Forward()),firedBullet[bulletCount].absolutePosition); 
                            
                            killBullet(bulletCount);                         
                            bulletCount++;
                            if (bulletCount>=maxbullets){bulletCount=0;}                           
                        }
                    };

                    document.onkeyup = function(event) {
                        var key_code   = event.keyCode; 
                        var key_press  = String.fromCharCode(event.keyCode);
                        //console.log(key_press + " " + key_code);
                        // Delete Key
                        if (key_code=="8" && editing==true){
                            unbindkeys();
                            //console.log("Delete Key Pressed");
                            window["wallmap"+cellPosY][cellPosX][cellPosZ]="00";
                            setCell(cellPosX,cellPosZ,cellPosY,"00");
                            cellMaterial.alpha = 0.2;
                        }
                        // Up key
                        else if (key_code=="38" && editing==true){
                            //console.log("Up Key Pressed");
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = false;
                            cellPosZ++;
                            if (cellPosZ>gridsizeZ){cellPosZ=gridsizeZ;}
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = true;
                            var thisCell = window["wallmap"+cellPosY][cellPosZ][cellPosX];
                            $("#cell").html("<img src='cells/"+thisCell+".png' style='max-width:100%;max-height:100%;'>");
                            cellMaterial.alpha = 0.5;
                            setMaterials(0.5);
                        }    
                        // Down key
                        else if (key_code=="40" && editing==true){
                            //console.log("Down Key Pressed");
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = false;
                            cellPosZ--;
                            if (cellPosZ<0){cellPosZ=0;}
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = true;
                            var thisCell = window["wallmap"+cellPosY][cellPosZ][cellPosX];
                            $("#cell").html("<img src='cells/"+thisCell+".png' style='max-width:100%;max-height:100%;'>");
                            cellMaterial.alpha = 0.5;  
                            setMaterials(0.5);
                        }                        
                        // Right key
                        else if (key_code=="39" && editing==true){
                            //console.log("Right Key Pressed");
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = false;
                            cellPosX++;
                            if (cellPosX>gridsizeX){cellPosX=gridsizeX;}
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = true;
                            var thisCell = window["wallmap"+cellPosY][cellPosZ][cellPosX];
                            $("#cell").html("<img src='cells/"+thisCell+".png' style='max-width:100%;max-height:100%;'>");
                            cellMaterial.alpha = 0.5;   
                            setMaterials(0.5);
                        }
                        // Left key
                        else if (key_code=="37" && editing==true){
                            //console.log("Left Key Pressed");
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = false;
                            cellPosX--;
                            if (cellPosX<0){cellPosX=0;}
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = true;
                            var thisCell = window["wallmap"+cellPosY][cellPosZ][cellPosX];
                            $("#cell").html("<img src='cells/"+thisCell+".png' style='max-width:100%;max-height:100%;'>");
                            cellMaterial.alpha = 0.5; 
                            setMaterials(0.5);
                        } 
                        // U key - up a level
                        else if (key_code=="85" && editing==true){                       
                            //console.log("U Key Pressed");
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = false;
                            cellPosY++;
                            if (cellPosY>(levels-1)){cellPosY=(levels-1);}
                            //console.log(levels+" Pos: "+cellPosX+" "+cellPosZ+" "+cellPosY);
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = true; 
                            walk (camera, camera.position.x, camera.position.z, Ypos(cellPosY));
                            var thisCell = window["wallmap"+cellPosY][cellPosZ][cellPosX];
                            $("#cell").html("<img src='cells/"+thisCell+".png' style='max-width:100%;max-height:100%;'>");
                            cellMaterial.alpha = 0.5; 
                            setMaterials(0.5);
                        }  
                        // D key - down a level
                        else if (key_code=="68" && editing==true){ 
                            //console.log("D Key Pressed");
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = false;
                            cellPosY--;
                            if (cellPosY<0){cellPosY=0;}
                            cell[cellPosX][cellPosZ][cellPosY].isVisible = true;
                            walk (camera, camera.position.x, camera.position.z, Ypos(cellPosY));
                            var thisCell = window["wallmap"+cellPosY][cellPosZ][cellPosX];
                            $("#cell").html("<img src='cells/"+thisCell+".png' style='max-width:100%;max-height:100%;'>");  
                            cellMaterial.alpha = 0.5; 
                            setMaterials(0.5);
                        }   
                        // . key - Advance Forward
                        else if (key_code=="190" && editing==true){ 
                            unbindkeys();
                            var current = window["wallmap"+cellPosY][cellPosX][cellPosZ];
                            let index = celltypes.indexOf(current); 
                            if (index==(celltypes.length-1)){index=0;}else{index++;}
                            //console.log("Index:"+index);
                            setCell(cellPosX,cellPosZ,cellPosY,celltypes[index]);
                            // Is cell below empty ?
//                            if (cellPosY>0){
//                                var spacecheck = window["wallmap"+(cellPosY-1)][cellPosX][cellPosZ];
//                                if (spacecheck=="00"){setCell(cellPosX,cellPosZ,cellPosY-1,19);}
//                                if (spacecheck=="16"){setCell(cellPosX,cellPosZ,cellPosY-1,15  );}                              
//                            }
                            // set new map value
                            window["wallmap"+cellPosY][cellPosX][cellPosZ]=celltypes[index];
                            // update screen indicator
                            $("#cell").html("<img src='cells/"+celltypes[index]+".png' style='max-width:100%;max-height:100%;'>");
                            cellMaterial.alpha = 0.1;
                            setMaterials(0.8);
                        }
                        // , key - Advance Backwards
                        else if (key_code=="188" && editing==true){ 
                            unbindkeys();
                            var current = window["wallmap"+cellPosY][cellPosX][cellPosZ];
                            let index = celltypes.indexOf(current);
                            if (index==0){index=(celltypes.length-1);}else{index--;}
                            //console.log("Index:"+index);
                            setCell(cellPosX,cellPosZ,cellPosY,celltypes[index]);
                            window["wallmap"+cellPosY][cellPosX][cellPosZ]=celltypes[index];
                            $("#cell").html("<img src='cells/"+celltypes[index]+".png' style='max-width:100%;max-height:100%;'>");
                            cellMaterial.alpha = 0.2;
                            setMaterials(0.8);
                        } 
                        // Press Delete to Reset Building Layout
                        else if (key_code=="191" && editing==true){
                            unbindkeys();
                            clearBuilding(building,1);
                        }
                        // Press ~ to Completely Clear Building Layout
                        else if (key_code=="192" && editing==true){
                            unbindkeys();
                            clearBuilding(building,2);
                        }                        
                        // Press to backup building 
                        else if (key_code=="67" && editing==true){
                            unbindkeys();
                            saveBuilding(building);
                        }
                        
                    };
                };
        
                function unbindkeys(){
                    document.onkeydown = function(event){};
                    document.onkeyup = function(event){};
                }
                
                function setMaterials(thisAlpha){
                    floorMaterial.alpha    = thisAlpha;
                    wallMaterial.alpha     = thisAlpha;
                    ceilingMaterial.alpha  = thisAlpha;
                    windowMaterial.alpha   = thisAlpha;
                    doorMaterialL.alpha     = thisAlpha;
                    doorMaterialR.alpha     = thisAlpha;
                    postMaterial.alpha     = thisAlpha;
                    lboxMaterial.alpha     = thisAlpha;
                    barrierMaterial.alpha  = thisAlpha;
                    postMaterial.alpha     = thisAlpha;
                    lightMaterial.alpha    = thisAlpha; 
                    floormatMaterial.alpha = thisAlpha;
                    skirtMaterial.alpha    = thisAlpha;
                }
                
                function clearBuilding(thisBuilding,style){
                    var answer = window.confirm("Are you sure you want to Reset Building "+thisBuilding+" Design ?");
                    if (answer===true){
                        $.post("resetBuilding.php",{building:thisBuilding,style:style},function(data){
                            if(data!=="success"){alert(data);}
                            else{location.reload();}
                        });
                    }
                }
                
                function saveBuilding(thisBuilding){
                    var answer = window.confirm("Are you sure you want to Save this Building "+thisBuilding+" Design ?");
                    if (answer===true){
                        $.post("saveBuilding.php",{building:thisBuilding},function(data){
                            if(data!=="success"){alert(data);}
                            else{location.reload();}
                        });
                    }
                }    
                
                function killBullet(bulletNumber){
                    setTimeout(function(){
                        firedBullet[bulletNumber].dispose();
                    },500);
                    
                }               
                
                function createCharacters(){
                    var ic = 0;
                    var cx,cy,cz;
                    var armoffset = xs/12;

                    while (ic<nchar){
                        cx = Xpos(charPosX[ic]);
                        cy = Ypos(charPosY[ic])+(ys/4)+floordepth;
                        cz = Zpos(charPosZ[ic]);
                        
                        characterMaterial[ic] = new BABYLON.StandardMaterial("characterMaterial_"+ic);
                        characterMaterial[ic].emissiveColor = new BABYLON.Color3(1, 0, 0);
                        characterMaterial[ic].alpha = 0;
                        
                        character[ic] = BABYLON.MeshBuilder.CreateCylinder("character_"+ic,{diameter:xs/3, height:ys/2});
                        character[ic].material = characterMaterial[ic];
                        character[ic].position = new BABYLON.Vector3(cx,cy,cz);
                        character[ic].rotation.y = (Math.PI*2) / Math.random(4);                   
                        character[ic].checkCollisions = true;
                        character[ic].isVisible = true;

                        characterAggregate[ic] = new BABYLON.PhysicsAggregate(character[ic], BABYLON.PhysicsShapeType.BOX, { mass: charMass, friction: charFriction, restitution: charRestitution }, scene);
                        characterAggregate[ic].body.setCollisionCallbackEnabled(true);
                        charPhysics[ic]=true;
                        charMoving[ic]=false;
                        charHits[ic]=20;
                        
                        var body = BABYLON.MeshBuilder.CreateBox("bCharacter_"+ic,{width:xs/8, height:ys/8, depth:zs/8,faceUV:faceUVrobot}); 
                        body.parent = character[ic];
                        body.position.y = ys/9;
                        body.material = characterBodyMaterial;
                        body.receiveShadows = true;
                        shadowGenerator.getShadowMap().renderList.push(body);
                        
                        characterSound[ic] = new BABYLON.Sound("robot2", "sounds/robot2.mp3", scene, null, {
                            loop: false,
                            autoplay: false,
                            spatialSound: true,
                            distanceModel: "exponential",
                            rolloffFactor: 1
                        });
                        // Sound will now follow the box mesh position
                        characterSound[ic].attachToMesh(character[ic]);
                        
                        var head = BABYLON.MeshBuilder.CreateSphere("scharacter_"+ic,{diameter:xs/10});
                        head.parent = body;
                        head.position.y = ys/18;
                        head.material = characterHeadMaterial;  
                        head.isVisible = false;
                        
                        brainMaterial[ic] = new BABYLON.StandardMaterial("brainMaterial_"+ic);
                        brainMaterial[ic].emissiveColor = new BABYLON.Color3(1, 1, 1);                       
                        
                        var brain = BABYLON.MeshBuilder.CreateSphere("brain_"+ic,{diameter:xs/12});
                        brain.parent = body;
                        brain.position.y = ys/13;
                        brain.material = brainMaterial[ic];                         
                        
                        var arm1 = BABYLON.MeshBuilder.CreateSphere("a1character_"+ic,{diameter:xs/18});
                        arm1.parent = body;
                        arm1.position.x = armoffset;                   
                        arm1.material = characterArmMaterial;
                        
                        var gun1 = BABYLON.MeshBuilder.CreateCylinder("g1character_"+ic,{diameter:xs/50,height:xs/12});
                        gun1.parent = body;
                        gun1.position.x = armoffset;
                        gun1.position.z = -zs/20;
                        gun1.rotation.x = Math.PI/2;
                        
                        var arm2 = BABYLON.MeshBuilder.CreateSphere("a2character_"+ic,{diameter:xs/18});
                        arm2.parent = body;
                        arm2.position.x = -armoffset; 
                        arm2.material = characterArmMaterial;     
                        
                        var gun2 = BABYLON.MeshBuilder.CreateCylinder("g2character_"+ic,{diameter:xs/100,height:xs/7});
                        gun2.parent = body;
                        gun2.position.x = -armoffset;
                        gun2.position.z = -zs/16;
                        gun2.rotation.x = Math.PI/2;    
                        
                        var electro = BABYLON.MeshBuilder.CreateSphere("e2electro_"+ic,{diameter:xs/28});
                        electro.parent = gun2;
                        electro.position.y = -ys/16;
                        
                        //console.log("Created Character:"+ic+" X:"+charPosX[ic]+" Y:"+charPosY[ic]+" Z:"+charPosZ[ic]);
                        ic++;
                    }
                    startMovement=true;
                }
                
                const faceUV =[]; // Used for Walls
                faceUV[0] = new BABYLON.Vector4(0, 0, 0, 0); //rear face x1,y1,x2,y2
                faceUV[1] = new BABYLON.Vector4(0, 0, 1, 1); //front face
                faceUV[2] = new BABYLON.Vector4(0, 0, 0, 0); //right face
                faceUV[3] = new BABYLON.Vector4(0, 0, 0, 0); //left face
                faceUV[4] = new BABYLON.Vector4(0, 0, 0, 0); //top face
                faceUV[5] = new BABYLON.Vector4(0, 0, 0, 0); //bottom face 
                
                var c,r;
                const faceUVrobot =[]; // Used for Robot
                c=2; r=1;
                faceUVrobot[0] = new BABYLON.Vector4((c * 1) / 3, (r * 1) / 2, ((c + 1) * 1) / 3, ((r + 1) * 1) / 2); //rear face x1,y1,x2,y2
                c=1; r=0;
                faceUVrobot[1] = new BABYLON.Vector4((c * 1) / 3, (r * 1) / 2, ((c + 1) * 1) / 3, ((r + 1) * 1) / 2); //front face
                c=0; r=0;
                faceUVrobot[2] = new BABYLON.Vector4((c * 1) / 3, (r * 1) / 2, ((c + 1) * 1) / 3, ((r + 1) * 1) / 2); //right face
                c=0; r=0;
                faceUVrobot[3] = new BABYLON.Vector4((c * 1) / 3, (r * 1) / 2, ((c + 1) * 1) / 3, ((r + 1) * 1) / 2); //left face
                c=0; r=0;
                faceUVrobot[4] = new BABYLON.Vector4((c * 1) / 3, (r * 1) / 2, ((c + 1) * 1) / 3, ((r + 1) * 1) / 2); //top face
                c=1; r=1;
                faceUVrobot[5] = new BABYLON.Vector4((c * 1) / 3, (r * 1) / 2, ((c + 1) * 1) / 3, ((r + 1) * 1) / 2); //bottom face                 
                
                const faceUVinternal =[]; // Used for Walls
                faceUVinternal[0] = new BABYLON.Vector4(0, 0, 1, 1); //rear face x1,y1,x2,y2
                faceUVinternal[1] = new BABYLON.Vector4(0, 0, 1, 1); //front face
                faceUVinternal[2] = new BABYLON.Vector4(0, 0, 0, 0); //right face
                faceUVinternal[3] = new BABYLON.Vector4(0, 0, 0, 0); //left face
                faceUVinternal[4] = new BABYLON.Vector4(0, 0, 0, 0); //top face
                faceUVinternal[5] = new BABYLON.Vector4(0, 0, 0, 0); //bottom face    
                
                const faceUVinternalDoor =[]; // Used for Doores
                faceUVinternalDoor[0] = new BABYLON.Vector4(0, 0, 1, 1); //rear face x1,y1,x2,y2
                faceUVinternalDoor[1] = new BABYLON.Vector4(0, 0, 1, 1); //front face
                faceUVinternalDoor[2] = new BABYLON.Vector4(0, 0, 0, 0); //right face
                faceUVinternalDoor[3] = new BABYLON.Vector4(0, 0, 0, 0); //left face
                faceUVinternalDoor[4] = new BABYLON.Vector4(0, 0, 0, 0); //top face
                faceUVinternalDoor[5] = new BABYLON.Vector4(0, 0, 0, 0); //bottom face                  
                
                const faceUVexternal =[]; // Used for Walls
                faceUVexternal[0] = new BABYLON.Vector4(0, 0, 0, 0); //rear face x1,y1,x2,y2
                faceUVexternal[1] = new BABYLON.Vector4(0, 0, 1, 1); //front face
                faceUVexternal[2] = new BABYLON.Vector4(0, 0, 0, 0); //right face
                faceUVexternal[3] = new BABYLON.Vector4(0, 0, 0, 0); //left face
                faceUVexternal[4] = new BABYLON.Vector4(0, 0, 0, 0); //top face
                faceUVexternal[5] = new BABYLON.Vector4(0, 0, 0, 0); //bottom face    
                
                const faceUVinternal4 =[]; // Used for Walls
                faceUVinternal4[0] = new BABYLON.Vector4(0, 0, 1, .25); //rear face x1,y1,x2,y2
                faceUVinternal4[1] = new BABYLON.Vector4(0, 0, 1, .25); //front face
                faceUVinternal4[2] = new BABYLON.Vector4(0, 0, 0, 0); //right face
                faceUVinternal4[3] = new BABYLON.Vector4(0, 0, 0, 0); //left face
                faceUVinternal4[4] = new BABYLON.Vector4(0, 0, 0, 0); //top face
                faceUVinternal4[5] = new BABYLON.Vector4(0, 0, 0, 0); //bottom face     
                
                const faceUVexternal4 =[]; // Used for Walls
                faceUVexternal4[0] = new BABYLON.Vector4(0, 0, 0, 0); //rear face x1,y1,x2,y2
                faceUVexternal4[1] = new BABYLON.Vector4(0, 0, 1, .25); //front face
                faceUVexternal4[2] = new BABYLON.Vector4(0, 0, 0, 0); //right face
                faceUVexternal4[3] = new BABYLON.Vector4(0, 0, 0, 0); //left face
                faceUVexternal4[4] = new BABYLON.Vector4(0, 0, 0, 0); //top face
                faceUVexternal4[5] = new BABYLON.Vector4(0, 0, 0, 0); //bottom face                   
                
                const faceUVceiling =[]; // Used for Ceilings
                faceUVceiling[0] = new BABYLON.Vector4(0, 0, 0, 0); //rear face x1,y1,x2,y2
                faceUVceiling[1] = new BABYLON.Vector4(0, 0, 0, 0); //front face
                faceUVceiling[2] = new BABYLON.Vector4(0, 0, 0, 0); //right face
                faceUVceiling[3] = new BABYLON.Vector4(0, 0, 0, 0); //left face
                faceUVceiling[4] = new BABYLON.Vector4(0, 0, 0, 0); //top face
                faceUVceiling[5] = new BABYLON.Vector4(0, 0, 1, 1); //bottom face  
                
                const faceUVceiling1 =[]; // Used for Glass Ceiling
                faceUVceiling1[0] = new BABYLON.Vector4(0, 0, 0, 0); //rear face x1,y1,x2,y2
                faceUVceiling1[1] = new BABYLON.Vector4(0, 0, 0, 0); //front face
                faceUVceiling1[2] = new BABYLON.Vector4(0, 0, 0, 0); //right face
                faceUVceiling1[3] = new BABYLON.Vector4(0, 0, 0, 0); //left face
                faceUVceiling1[4] = new BABYLON.Vector4(0, 0, 1, 1); //top face
                faceUVceiling1[5] = new BABYLON.Vector4(0, 0, 1, 1); //bottom face                  
                
                const faceUVfloor =[]; // Used for Walls
                faceUVfloor[0] = new BABYLON.Vector4(0, 0, 0, 0); //rear face x1,y1,x2,y2
                faceUVfloor[1] = new BABYLON.Vector4(0, 0, 0, 0); //front face
                faceUVfloor[2] = new BABYLON.Vector4(0, 0, 0, 0); //right face
                faceUVfloor[3] = new BABYLON.Vector4(0, 0, 0, 0); //left face
                faceUVfloor[4] = new BABYLON.Vector4(0, 0, 1, 1); //top face
                faceUVfloor[5] = new BABYLON.Vector4(0, 0, 0, 0); //bottom face  

                const faceUVstep =[]; // Used for Stairs
                faceUVstep[0] = new BABYLON.Vector4(0, 0, 0, 0); //rear face x1,y1,x2,y2
                faceUVstep[1] = new BABYLON.Vector4(0, 0, 1, 1); //front face
                faceUVstep[2] = new BABYLON.Vector4(0, 0, 0, 0); //right face
                faceUVstep[3] = new BABYLON.Vector4(0, 0, 0, 0); //left face
                faceUVstep[4] = new BABYLON.Vector4(0, 0, 1, 1); //top face
                faceUVstep[5] = new BABYLON.Vector4(0, 0, 0, 0); //bottom face  
                
                // Building functions
                const walls = function(x,z,y,rotation,option,counter){                   
                    //console.log("X:"+x+" Z:"+z+" Y:"+y+" Rotation:"+rotation+" Option:"+option+ " Counter:"+counter);                   
                    var thisfaceUV = faceUVinternal;
                    if (z==0 || z==gridsizeZ || x==0 || x==gridsizeX){thisfaceUV = faceUVinternal;}                   
                    var thisWallheight = wallheight;
                    var offsety        = 0;
                    var skirtoffset = -walldepth/1.75;
                    
                    // opening or door
                    if (option>0){
                        thisWallheight = wallheight/4; offsety = (wallheight/4)*1.5;
                        thisfaceUV = faceUVinternal4;
                        if (z==0 || z==gridsizeZ || x==0 || x==gridsizeX){thisfaceUV = faceUVinternal4;}
                    }
                    
                   // door
                   if (option==2){
                        var doorheight = wallheight*0.731; 
                        var dooroffset = -wallheight/8.5;
                       
                        hinge1[x][z][y] = new BABYLON.MeshBuilder.CreateBox("hinge1_"+x+"_"+z+"_"+y, {width:wallwidth/50, depth:walldepth/50, height:doorheight/10, faceUV:faceUVinternal, wrap:true});
                        hinge1[x][z][y].parent = cell[x][z][y]; 
                        //hinge1[x][z][y].rotation.y = rotation[rotation];
                        hinge1[x][z][y].position.z = (offsetz[rotation]*(zs/2))-(offsetx[rotation]*(zs/2));
                        hinge1[x][z][y].position.x = (offsetx[rotation]*(zs/2))-(offsetz[rotation]*(zs/2));
                        hinge1[x][z][y].material   = hingeMaterial;
                        hinge1[x][z][y].isVisible  = false;
                        
                        door1[x][z][y] = new BABYLON.MeshBuilder.CreateBox("door1_"+x+"_"+z+"_"+y, {width:wallwidth/2, depth:walldepth/4, height:doorheight, faceUV:faceUVinternalDoor, wrap:true});
                        door1[x][z][y].parent     = hinge1[x][z][y];   
                        door1[x][z][y].rotation.y = rotate[rotation];
                        door1[x][z][y].position.z = offsetx[rotation]*wallwidth/4;
                        door1[x][z][y].position.x = offsetz[rotation]*wallwidth/4;                      
                        door1[x][z][y].position.y = dooroffset;                       
                        door1[x][z][y].material = doorMaterialL;
                        door1[x][z][y].receiveShadows = true;
                        door1[x][z][y].checkCollisions = true;
                        
                        var doorknob1 = new BABYLON.MeshBuilder.CreateCylinder("knob1_"+x+"_"+z+"_"+y,{diameter:doorheight/23,height:doorheight/3});
                        doorknob1.parent = hinge1[x][z][y];
                        doorknob1.material = postMaterial;
                        doorknob1.rotation.y = rotate[rotation];
                        doorknob1.position.z = offsetx[rotation]*wallwidth/2.2;
                        doorknob1.position.x = offsetz[rotation]*wallwidth/2.2;
                        //shadowGenerator.getShadowMap().renderList.push(door1[x][z][y]); 
                        //door1[x][z][y].isVisible=false;
                        
                        hinge2[x][z][y] = new BABYLON.MeshBuilder.CreateBox("hinge2_"+x+"_"+z+"_"+y, {width:wallwidth/50, depth:walldepth/50, height:doorheight/10, faceUV:faceUVinternal, wrap:true});
                        hinge2[x][z][y].parent = cell[x][z][y];                        
                        hinge2[x][z][y].position.z = (offsetz[rotation]*(zs/2))+(offsetx[rotation]*(zs/2));
                        hinge2[x][z][y].position.x = (offsetx[rotation]*(zs/2))+(offsetz[rotation]*(zs/2));
                        hinge2[x][z][y].material   = hingeMaterial;
                        hinge2[x][z][y].isVisible  = false;
                        
                        door2[x][z][y] = new BABYLON.MeshBuilder.CreateBox("door2_"+x+"_"+z+"_"+y, {width:wallwidth/2, depth:walldepth/4, height:doorheight, faceUV:faceUVinternalDoor, wrap:true});
                        door2[x][z][y].parent     = hinge2[x][z][y];  
                        door2[x][z][y].rotation.y = rotate[rotation];
                        door2[x][z][y].position.z = -offsetx[rotation]*wallwidth/4;
                        door2[x][z][y].position.x = -offsetz[rotation]*wallwidth/4;                       
                        door2[x][z][y].position.y = dooroffset;                      
                        door2[x][z][y].material = doorMaterialR;  
                        door2[x][z][y].receiveShadows = true;
                        door2[x][z][y].checkCollisions = true;
                        
                        var doorknob2 = new BABYLON.MeshBuilder.CreateCylinder("knob1_"+x+"_"+z+"_"+y,{diameter:doorheight/23,height:doorheight/3});
                        doorknob2.parent = hinge2[x][z][y];
                        doorknob2.material = postMaterial;
                        doorknob2.rotation.y = rotate[rotation];
                        doorknob2.position.z = -offsetx[rotation]*wallwidth/2.2;
                        doorknob2.position.x = -offsetz[rotation]*wallwidth/2.2;

                        //shadowGenerator.getShadowMap().renderList.push(door2[x][z][y]);         
                        //door2[x][z][y].isVisible=false;
                        
                        exitbox[x][z][y] = new BABYLON.MeshBuilder.CreateBox("exitbox_"+x+"_"+z+"_"+y, {width:xs/4, depth:walldepth, height:wallheight/10});
                        exitbox[x][z][y].parent = cell[x][z][y];
                        exitbox[x][z][y].rotation.y = rotate[rotation];
                        exitbox[x][z][y].position.y = wallheight/2.3;
                        exitbox[x][z][y].material = lboxMaterial;
            
                        var exitlight = new BABYLON.MeshBuilder.CreateBox("exitlight_"+x+"_"+z+"_"+y, {width:(xs/4)*.8, depth:walldepth/2, height:(wallheight/10)*.7, faceUV:faceUV});
                        exitlight.parent = exitbox[x][z][y];
                        exitlight.position.z = -walldepth/2;
                        exitlight.material = elightMaterial;
                        
                        floormat[x][z][y] = new BABYLON.MeshBuilder.CreateBox("floormat_"+x+"_"+z+"_"+y, {width:xs*0.9, depth:zs*0.9, height:floordepth/2});
                        floormat[x][z][y].parent = cell[x][z][y];
                        floormat[x][z][y].rotation.y = rotate[rotation];
                        floormat[x][z][y].position.y = -wallheight/2.1;
                        floormat[x][z][y].material = floormatMaterial;
                        
                        doorstatus[x][z][y]=0;
                   }
                   // half height window                  
                   if (option==3){
                       var windowheight = wallheight*0.5;                     
                        halfwindow[x][z][y] = new BABYLON.MeshBuilder.CreateBox("halfwindow_"+x+"_"+z+"_"+y, {width:wallwidth, depth:walldepth/8, height:windowheight, faceUV:faceUVinternal, wrap:true});
                        halfwindow[x][z][y].parent = cell[x][z][y];                
                        halfwindow[x][z][y].rotation.y = rotate[rotation]; 
                        halfwindow[x][z][y].position.z = (offsetz[rotation]*(zs/2));
                        halfwindow[x][z][y].position.x = (offsetx[rotation]*(zs/2));
                        halfwindow[x][z][y].position.y = 0;
                        halfwindow[x][z][y].material = windowMaterial; 
                        halfwindow[x][z][y].receiveShadows = true;
                        shadowGenerator.getShadowMap().renderList.push(halfwindow[x][z][y]);
                        
                        subwall[x][z][y] = new BABYLON.MeshBuilder.CreateBox("subwall_"+x+"_"+z+"_"+y, {width:wallwidth, depth:walldepth, height:thisWallheight, faceUV:thisfaceUV, wrap:true});
                        subwall[x][z][y].parent = cell[x][z][y];                
                        subwall[x][z][y].rotation.y = rotate[rotation];                   
                        subwall[x][z][y].position.z = offsetz[rotation]*(zs/2);
                        subwall[x][z][y].position.x = offsetx[rotation]*(zs/2);
                        subwall[x][z][y].position.y = -offsety;
                        subwall[x][z][y].material = wallMaterial;
                        subwall[x][z][y].receiveShadows = true;
                        shadowGenerator.getShadowMap().renderList.push(subwall[x][z][y]);  
                        subwall[x][z][y].checkCollisions = true;
                        new BABYLON.PhysicsAggregate(subwall[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                        //subwallAggregate[x][z][y] = new BABYLON.PhysicsAggregate(subwall[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                        //subwallAggregate[x][z][y].body.setCollisionCallbackEnabled(true);
                   }                   
                    
                    if (counter==0){
                        wall0[x][z][y] = new BABYLON.MeshBuilder.CreateBox("wall0_"+x+"_"+z+"_"+y, {width:wallwidth, depth:walldepth, height:thisWallheight, faceUV:thisfaceUV, wrap:true});
                        wall0[x][z][y].parent = cell[x][z][y];                
                        wall0[x][z][y].rotation.y = rotate[rotation];                   
                        wall0[x][z][y].position.z = offsetz[rotation]*(zs/2);
                        wall0[x][z][y].position.x = offsetx[rotation]*(zs/2);
                        wall0[x][z][y].position.y = offsety;
                        wall0[x][z][y].material = wallMaterial;
                        wall0[x][z][y].receiveShadows = true;
                        shadowGenerator.getShadowMap().renderList.push(wall0[x][z][y]); 
                        wall0[x][z][y].checkCollisions = true;
                        new BABYLON.PhysicsAggregate(wall0[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);                     
                                               
                        skirt0[x][z][y] = new BABYLON.MeshBuilder.CreateBox("skirt0"+x+"_"+z+"_"+y,{width:wallwidth, depth:walldepth/8, height:wallheight/20});
                        skirt0[x][z][y].parent = wall0[x][z][y];
                        skirt0[x][z][y].position.z = skirtoffset;
                        skirt0[x][z][y].material = skirtMaterial;
                        skirt0[x][z][y].receiveShadows = true;
                        
                        if (option=="1" || option=="2"){skirt0[x][z][y].isVisible = false;} // hide if doorway
                        if (thisWallheight == wallheight){skirt0[x][z][y].position.y = (-wallheight/2.18);}
                        else{skirt0[x][z][y].position.y = -wallheight/1.2;}
                        
                        if (thisWallheight == wallheight && showWallLight){
                            wallLight[x][z][y] = new BABYLON.MeshBuilder.CreateCylinder("wallLight"+x+"_"+z+"_"+y,{diameter:xs/10,height:wallheight/10});
                            wallLight[x][z][y].parent = wall0[x][z][y];
                            wallLight[x][z][y].position.z = -walldepth*2.5;
                            wallLight[x][z][y].position.y = wallheight/4;
                            
                            var wallSphere = new BABYLON.MeshBuilder.CreateSphere("wallSphere"+x+"_"+z+"_"+y,{diameter:xs/20});
                            wallSphere.position.y = -ys/20;
                            wallSphere.parent = wallLight[x][z][y];
                            wallSphere.material = lightMaterial;
                            
                            //var downlight = BABYLON.MeshBuilder.CreateSphere("downlight"+x+"_"+z+"_"+y, {arc:0.5, diameterX: 3, diameterY:5, diameterZ: 1});
                            //downlight.parent = wallLight[x][z][y];
                            //downlight.rotation.z = Math.PI;
                            //downlight.position.y = -wallheight/1.8;
                            //downlight.position.x = -xs/10;
                            //downlight.material = downlightMaterial;
                        }
                    }
                    else if (counter==1){
                        wall1[x][z][y] = new BABYLON.MeshBuilder.CreateBox("wall1_"+x+"_"+z+"_"+y, {width:wallwidth, depth:walldepth, height:thisWallheight, faceUV:thisfaceUV});
                        wall1[x][z][y].parent = cell[x][z][y];                
                        wall1[x][z][y].rotation.y = rotate[rotation];                   
                        wall1[x][z][y].position.z = offsetz[rotation]*(zs/2);
                        wall1[x][z][y].position.x = offsetx[rotation]*(zs/2);
                        wall1[x][z][y].position.y = offsety;
                        wall1[x][z][y].material = wallMaterial;
                        wall1[x][z][y].receiveShadows = true;
                        shadowGenerator.getShadowMap().renderList.push(wall1[x][z][y]); 
                        wall1[x][z][y].checkCollisions = true;
                        new BABYLON.PhysicsAggregate(wall1[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);                     
                        
                        skirt1[x][z][y] = new BABYLON.MeshBuilder.CreateBox("skirt0_"+x+"_"+z+"_"+y,{width:wallwidth, depth:walldepth/8, height:wallheight/20});
                        skirt1[x][z][y].parent = wall1[x][z][y];
                        skirt1[x][z][y].position.z = skirtoffset;
                        skirt1[x][z][y].material = skirtMaterial;
                        skirt1[x][z][y].receiveShadows = true;
                        if (option=="1" || option=="2"){skirt1[x][z][y].isVisible = false;} // hide if doorway
                        if (thisWallheight == wallheight){skirt1[x][z][y].position.y = (-wallheight/2.18);}
                        else{skirt1[x][z][y].position.y = -wallheight/1.2;}
                        
                    }
                    else if (counter==2){
                        wall2[x][z][y] = new BABYLON.MeshBuilder.CreateBox("wall2_"+x+"_"+z+"_"+y, {width:wallwidth, depth:walldepth, height:thisWallheight, faceUV:thisfaceUV});
                        wall2[x][z][y].parent = cell[x][z][y];                
                        wall2[x][z][y].rotation.y = rotate[rotation];                   
                        wall2[x][z][y].position.z = offsetz[rotation]*(zs/2);
                        wall2[x][z][y].position.x = offsetx[rotation]*(zs/2);
                        wall2[x][z][y].position.y = offsety;
                        wall2[x][z][y].material = wallMaterial;
                        wall2[x][z][y].receiveShadows = true;
                        shadowGenerator.getShadowMap().renderList.push(wall2[x][z][y]);   
                        wall2[x][z][y].checkCollisions = true;
                        new BABYLON.PhysicsAggregate(wall2[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);                      
                        
                        skirt2[x][z][y] = new BABYLON.MeshBuilder.CreateBox("skirt0"+x+"_"+z+"_"+y,{width:wallwidth, depth:walldepth/8, height:wallheight/20});
                        skirt2[x][z][y].parent = wall2[x][z][y];
                        skirt2[x][z][y].position.z = skirtoffset;
                        skirt2[x][z][y].material = skirtMaterial;
                        skirt2[x][z][y].receiveShadows = true;
                        if (option=="1" || option=="2"){skirt2[x][z][y].isVisible = false;} // hide if doorway
                        if (thisWallheight == wallheight){skirt2[x][z][y].position.y = (-wallheight/2.18);}
                        else{skirt0[x][z][y].position.y = -wallheight/1.2;}
                    }
                    else if (counter==3){
                        wall3[x][z][y] = new BABYLON.MeshBuilder.CreateBox("wall3_"+x+"_"+z+"_"+y, {width:wallwidth, depth:walldepth, height:thisWallheight, faceUV:thisfaceUV});
                        wall3[x][z][y].parent = cell[x][z][y];                
                        wall3[x][z][y].rotation.y = rotate[rotation];                   
                        wall3[x][z][y].position.z = offsetz[rotation]*(zs/2);
                        wall3[x][z][y].position.y = offsety;
                        wall3[x][z][y].material = wallMaterial;
                        wall3[x][z][y].receiveShadows = true;
                        shadowGenerator.getShadowMap().renderList.push(wall3[x][z][y]);   
                        wall3[x][z][y].checkCollisions = true;
                        new BABYLON.PhysicsAggregate(wall3[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);                     
                        
                        skirt3[x][z][y] = new BABYLON.MeshBuilder.CreateBox("skirt0"+x+"_"+z+"_"+y,{width:wallwidth, depth:walldepth/8, height:wallheight/20});
                        skirt3[x][z][y].parent = wall3[x][z][y];
                        skirt3[x][z][y].position.z = skirtoffset;
                        skirt3[x][z][y].receiveShadows = true;
                        if (option=="1" || option=="2"){skirt3[x][z][y].isVisible = false;} // hide if doorway
                        if (thisWallheight == wallheight){skirt3[x][z][y].position.y = (-wallheight/2.18);}
                        else{skirt3[x][z][y].position.y = -wallheight/1.2;}
                    }
                    else {alert("Error");}                   
                };
                
                const floors = function(x,z,y,style){
                    floor[x][z][y] = new BABYLON.MeshBuilder.CreateBox("floor_"+x+"_"+z+"_"+y, {width:xs, depth:zs, height:floordepth, faceUV:faceUVfloor, wrap:true});
                    floor[x][z][y].parent = cell[x][z][y]; 
                    floor[x][z][y].position.y = (-wallheight/2)+floordepth;
                    floor[x][z][y].material = floorMaterial;
                    floor[x][z][y].receiveShadows = true;
                    floor[x][z][y].checkCollisions = true;
                    floorAggregate[x][z][y] = new BABYLON.PhysicsAggregate(floor[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                };
                
                const ceilings = function(x,z,y,style){     
                    
                    if (style=="2"){var thisfaceUV = faceUVceiling1;}else{var thisfaceUV = faceUVceiling;}                  
                    ceiling[x][z][y] = new BABYLON.MeshBuilder.CreateBox("ceiling_"+x+"_"+z+"_"+y, {width:xs, depth:zs, height:ceilingdepth, faceUV:thisfaceUV, wrap:true});
                    ceiling[x][z][y].parent = cell[x][z][y]; 
                    ceiling[x][z][y].position.y = (wallheight/2)-ceilingdepth;
                    
                    if (style=="1"){
                        ceiling[x][z][y].material = ceilingMaterial;
                        var lightbbox = new BABYLON.MeshBuilder.CreateBox("lightbbox_"+x+"_"+z+"_"+y, {width:xs/8, depth:zs/8, height:ceilingdepth, faceUV:faceUVceiling, wrap:true}); 
                        lightbbox.parent = ceiling[x][z][y];
                        lightbbox.position.y = -ceilingdepth*2;
                        //lightbbox.isVisible = false;
                        
                        var light = new BABYLON.MeshBuilder.CreateBox("light_"+x+"_"+z+"_"+y, {width:xs/8, depth:zs/8, height:ceilingdepth, faceUV:faceUVceiling, wrap:true}); 
                        light.parent = lightbbox;
                        light.position.y = -ceilingdepth;
                        light.material = lightMaterial; 
                    }
                    else if (style=="2"){
                        ceiling[x][z][y].material = glassCeilingMaterial;
                    }
                    else if (style=="3"){
                        ceiling[x][z][y].isVisible = false;
                    }  
                    else if (style=="4"){
                        ceiling[x][z][y].material = ceilingMaterial;                       
                    }
                    else {
                        ceiling[x][z][y].material = ceilingMaterial;
                    }
                    ceiling[x][z][y].receiveShadows = true;
                    shadowGenerator.getShadowMap().renderList.push(ceiling[x][z][y]);
                };  
                
                const stairs = function(x,z,y,rotation){
                    
                    var stairwidth = xs/1.3; // Width of Staircase
                    var stairdepth = zs*1.5; // Total Depth of Staircase
                    
                    var numsteps  = 15;
                    var stepHeight  = ys/40; // Height of Step 
                    var stepDepth   = (stairdepth)/numsteps; // Depth of Individual Step
                    var railWidth   = xs/20;
                    var railDepth   = ys/20;
                    var postHeight  = ys/2.5;
                    var postXoffset = (stairwidth/2)-railWidth;
                    
                    
                    var sX = 0;
                    var sY = ys/numsteps;          // Individual Step Y Offset
                    var sZ = stairdepth/numsteps;  // Individual Step X Offset
                    var Yo = (-ys/2)+(ys/numsteps);//starting Y (height) Offset of stairs
                    var Zo = (stepDepth/2)-zs;     //starting Z Offset of Stairs
                    
                    var hrYoffset = (postHeight)+(stepHeight);// Hand Rail Y Offset
                    var hrZoffset = (stepDepth/2)-zs/3;   // Hand Rail Z Offset
                    
                    var rampYoffset = ys/2;
                    var rampXoffset = xs/2;
                    var rampZoffset = -zs/4;
                    
                    var railLength = Math.sqrt(Math.pow(ys,2)+Math.pow(stairdepth,2));
                    var handrailRotation = -Math.atan(ys/(stairdepth));
                    
                    stair[x][z][y] = new BABYLON.MeshBuilder.CreateBox("stair_"+x+"_"+z+"_"+y, {width:stairwidth, depth:zs, height:ceilingdepth, faceUV:faceUVceiling, wrap:true});
                    stair[x][z][y].parent = cell[x][z][y];
                    stair[x][z][y].rotation.y = rotate[rotation];
                    stair[x][z][y].isVisible = false;
                    stair[x][z][y].isPickable = false;
                    
                    ramp[x][z][y] = new BABYLON.MeshBuilder.CreateBox("ramp_"+x+"_"+z+"_"+y, {width:stairwidth, depth:railLength, height:ceilingdepth, faceUV:faceUVceiling, wrap:true}); 
                    ramp[x][z][y].parent = stair[x][z][y];                   
                    ramp[x][z][y].rotation.x = handrailRotation;                  
                    ramp[x][z][y].material = rampMaterial;
                    ramp[x][z][y].isVisible = true;
                    
                    ramp[x][z][y].position = new BABYLON.Vector3(0,0,rampZoffset);

                    ramp[x][z][y].checkCollisions = true;
                    new BABYLON.PhysicsAggregate(ramp[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0, friction:rampFriction, restitution:rampRestitution }, scene);
                    
                    var steps = [];
                    var posts1 = [];
                    var posts2 = [];
                    
                    for (let step=0;step<numsteps;step++){
                        steps[step] = BABYLON.MeshBuilder.CreateBox("step", {width:stairwidth, height:stepHeight, depth:stepDepth, faceUV:faceUVstep, wrap:true});                       
                        steps[step].parent = stair[x][z][y];
                        
                        let stairXoffset = 0;
                        let stairYoffset = (step*sY)+Yo;
                        let stairZoffset = (step*sZ)+Zo;
                        
                        steps[step].position = new BABYLON.Vector3(stairXoffset,stairYoffset,stairZoffset);
                        steps[step].material = stepMaterial;
                        steps[step].receiveShadows = true;
                        steps[step].isVisible = true;
                        steps[step].checkCollisions = false;
                        steps[step].isPickable = false;
                        steps[step].isVisible = false;
                        //new BABYLON.PhysicsAggregate(steps[step], BABYLON.PhysicsShapeType.BOX, { mass: 0, friction:rampFriction, restitution:rampRestitution }, scene);
                        shadowGenerator.getShadowMap().renderList.push(steps[step]);
                        
                        posts1[step] = BABYLON.MeshBuilder.CreateCylinder("post",{diameter:xs/60, height:postHeight});
                        posts1[step].parent = stair[x][z][y];
                        posts1[step].position = new BABYLON.Vector3(postXoffset,stairYoffset+(postHeight/2),stairZoffset);
                        posts1[step].material = postMaterial;
                        
                        posts2[step] = BABYLON.MeshBuilder.CreateCylinder("post",{diameter:xs/60, height:postHeight});
                        posts2[step].parent = stair[x][z][y];
                        posts2[step].position = new BABYLON.Vector3(-postXoffset,stairYoffset+(postHeight/2),stairZoffset);
                        posts2[step].material = postMaterial;                     
                    }                   
                    
                    handrail1[x][z][y] = BABYLON.MeshBuilder.CreateBox("handrail1_"+x+"_"+z+"_"+y, {width:railWidth, depth:railLength, height:railDepth, faceUV:faceUVceiling, wrap:true});
                    handrail1[x][z][y].parent = stair[x][z][y];
                    handrail1[x][z][y].position = new BABYLON.Vector3(postXoffset,hrYoffset,hrZoffset);
                    handrail1[x][z][y].rotation.x = handrailRotation;
                    handrail1[x][z][y].material = floorMaterial;
                    handrail1[x][z][y].receiveShadows = true;
                    shadowGenerator.getShadowMap().renderList.push(handrail1[x][z][y]);
                    //handrail1[x][z][y].checkCollisions = true;
                    
                    handrail2[x][z][y] = BABYLON.MeshBuilder.CreateBox("handrail2_"+x+"_"+z+"_"+y, {width:railWidth, depth:railLength, height:railDepth, faceUV:faceUVceiling, wrap:true});
                    handrail2[x][z][y].parent = stair[x][z][y];
                    handrail2[x][z][y].position = new BABYLON.Vector3(-postXoffset,hrYoffset,hrZoffset);
                    handrail2[x][z][y].rotation.x = handrailRotation;  
                    handrail2[x][z][y].material = floorMaterial;
                    handrail2[x][z][y].receiveShadows = true;
                    shadowGenerator.getShadowMap().renderList.push(handrail1[x][z][y]);
                    //handrail2[x][z][y].checkCollisions = true;
                };
                
                const barriers = function (x,z,y){
                    
                    if (y>0){
                        wmap = window["wallmap"+y];                 
                        var type = wmap[z][x];
                        
                        // if front wall, window, opening ... or open floor .. or stair - checked OK
                        // ------------------------------------------------------------
                        if (type=="01" || type=="21" || type=="25" || type=="31" || type=="15" || type=="16" || type=="18" || type=="43"){                            
                            // check behind
                            var zc = Number(Number(z)+1);
                            if (zc<wmap.length){
                               var check = wmap[zc][x];                              
                               if (y==1){var below = wallmap0[zc][x];}
                               else if (y==2){var below = wallmap1[zc][x];}
                               else if (y==3){var below = wallmap2[zc][x];}
                               else {var below="00";}
                           } else {check="00"; below="00";}
                               // check OK
                               if ((check=="00" || check=="17" ||  check=="19" || check=="20" || check=="40") && below!="41"){ 
                                    // console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Barrier at Back
                                    barrier2[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier2_"+x+"_"+z+"_"+y+"_B1", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier2[x][z][y].parent = cell[x][z][y];
                                    barrier2[x][z][y].material = barrierMaterial;
                                    barrier2[x][z][y].position = new BABYLON.Vector3(0,(-wallheight/3)+floordepth,zs/2);
                                    barrier2[x][z][y].receiveShadows = true;
                                    barrier2[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier2[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier2[x][z][y],x,z,y);
                               }                              

                            // check right if stairs
                            var xc = Number(Number(x)-1);
                            if (xc>=0){                            
                                var check = wmap[z][xc];                              
                                if (y==1){var below = wallmap0[z][xc];}
                                else if (y==2){var below = wallmap1[z][xc];}
                                else if (y==3){var below = wallmap2[z][xc];}
                                else {var below="00";}
                            } else {check="00"; below="00";}
                               // check OK
                               if ((check=="00" || check=="17" || check=="19" || check=="20" || check=="40") && below!=="42"){
                                    // console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Barrier to Right
                                    barrier1[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier1_"+x+"_"+z+"_"+y+"_R1", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier1[x][z][y].parent = cell[x][z][y];
                                    barrier1[x][z][y].material = barrierMaterial;
                                    barrier1[x][z][y].rotation.y = Math.PI/2;
                                    barrier1[x][z][y].position = new BABYLON.Vector3(-xs/2,(-wallheight/3)+floordepth,0);
                                    barrier1[x][z][y].receiveShadows = true;
                                    barrier1[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier1[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier1[x][z][y],x,z,y);
                               }                              
                             
                            // check left if stairs
                            var xc = Number(Number(x)+1);
                            if (xc<wmap[z].length){                            
                                var check = wmap[z][xc];                                
                                if (y==1){var below = wallmap0[z][xc];}
                                else if (y==2){var below = wallmap1[z][xc];}
                                else if (y==3){var below = wallmap2[z][xc];}
                                else {var below="00";}  
                            } else {check="00"; below="00";}
                               // check OK 
                               if ((check=="00" || check=="17" || check=="19" || check=="20" || check=="40") && below!=="44"){
                                    //console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Barrier to Left
                                    barrier3[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier3_"+x+"_"+z+"_"+y+"_L1", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier3[x][z][y].parent = cell[x][z][y];
                                    barrier3[x][z][y].material = barrierMaterial;
                                    barrier3[x][z][y].rotation.y = -Math.PI/2;
                                    barrier3[x][z][y].position = new BABYLON.Vector3(xs/2,(-wallheight/3)+floordepth,0);
                                    barrier3[x][z][y].receiveShadows = true;
                                    barrier3[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier3[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier3[x][z][y],x,z,y);
                                }                              
                                                            
                        }
                        
                        // if right wall, window, opening ... or open floor .. or stair Check OK
                        // ------------------------------------------------------------
                        if (type=="02" || type=="22" || type=="26" || type=="32" || type=="15" || type=="16" || type=="18" || type=="44"){ 
                            var xc = Number(Number(x)-1);
                            if (xc>=0){                            
                                var check = wmap[z][xc];                              
                                if (y==1){var below = wallmap0[z][xc];}
                                else if (y==2){var below = wallmap1[z][xc];}
                                else if (y==3){var below = wallmap2[z][xc];}
                                else {var below="00";}                                
                            } else {check="00"; below="00";}   
                               if ((check=="00" || check=="17" || check=="19" || check=="20" || check=="40") && below!=="42"){
                                    // console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Barrier to Left
                                    barrier3[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier3_"+x+"_"+z+"_"+y+"_L2", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier3[x][z][y].parent = cell[x][z][y];
                                    barrier3[x][z][y].material = barrierMaterial;
                                    barrier3[x][z][y].rotation.y = Math.PI/2;
                                    barrier3[x][z][y].position = new BABYLON.Vector3(-xs/2,(-wallheight/3)+floordepth,0);
                                    barrier3[x][z][y].receiveShadows = true;
                                    barrier3[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier3[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier3[x][z][y],x,z,y);                                   
                               }                              
                            
                            // check front
                            var zc = Number(Number(z)-1);
                            if (zc>=0){                            
                                var check = wmap[zc][x];                                  
                                if (y==1){var below = wallmap0[zc][x];}
                                else if (y==2){var below = wallmap1[zc][x];}
                                else if (y==3){var below = wallmap2[zc][x];}
                                else {var below="00";}                                 
                            } else {check="00"; below="00";}   
                               if ((check=="00" || check=="17" || check=="19" || check=="20" || check=="40") && below!=="43"){
                                    // console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Barrier to Front
                                    barrier0[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier0_"+x+"_"+z+"_"+y+"_F1", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier0[x][z][y].parent = cell[x][z][y];
                                    barrier0[x][z][y].material = barrierMaterial;
                                    barrier0[x][z][y].position = new BABYLON.Vector3(0,(-wallheight/3)+floordepth,-zs/2);
                                    barrier0[x][z][y].receiveShadows = true;
                                    barrier0[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier0[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier0[x][z][y],x,z,y);                                   
                               }                              
                                                       
                            // check back
                            var zc = Number(Number(z)+1);
                            if (zc<wmap.length){
                                var check = wmap[zc][x];                        
                                if (y==1){var below = wallmap0[zc][x];}
                                else if (y==2){var below = wallmap1[zc][x];}
                                else if (y==3){var below = wallmap2[zc][x];}
                                else {var below="00";}                                 
                            } else {check="00"; below="00";}   
                               if ((check=="00" || check=="17" ||  check=="19" || check=="20" || check=="40") && below!=="41"){  
                                    // console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Barrier at Back
                                    barrier2[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier3_"+x+"_"+z+"_"+y+"_B2", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier2[x][z][y].parent = cell[x][z][y];
                                    barrier2[x][z][y].material = barrierMaterial;
                                    barrier2[x][z][y].position = new BABYLON.Vector3(0,(-wallheight/3)+floordepth,zs/2);
                                    barrier2[x][z][y].receiveShadows = true;
                                    barrier2[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier2[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier2[x][z][y],x,z,y);                                    
                               }                                                                                                               
                        }                         
                        
                        // if Back wall, window, opening ... or open floor
                        // ----------------------------------------------
                        if (type=="33" || type=="23" || type=="03" || type=="15" || type=="16" || type=="18" || type=="41"){                           
                            var zc = Number(Number(z)-1);
                            if (zc>=0){                            
                               var check = wmap[zc][x]; 
                               if (y==1){var below = wallmap0[zc][x];}
                               else if (y==2){var below = wallmap1[zc][x];}
                               else if (y==3){var below = wallmap2[zc][x];}
                               else {var below="00";}  
                            } else {check="00"; below="00";}   
                               if ((check=="00" || check=="17" || check=="19" || check=="20" || check=="40") && below!=="43"){
                                    // console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Barrier to Front
                                    barrier0[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier_"+x+"_"+z+"_"+y+"_F2", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier0[x][z][y].parent = cell[x][z][y];
                                    barrier0[x][z][y].material = barrierMaterial;
                                    barrier0[x][z][y].position = new BABYLON.Vector3(0,(-wallheight/3)+floordepth,-zs/2);
                                    barrier0[x][z][y].receiveShadows = true;
                                    barrier0[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier0[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier0[x][z][y],x,z,y);                                     
                               }                              
                                                      
                            // check right
                            var xc = Number(Number(x)-1);
                            if (xc>=0){                            
                               var check = wmap[z][xc];
                                if (y==1){var below = wallmap0[z][xc];}
                                else if (y==2){var below = wallmap1[z][xc];}
                                else if (y==3){var below = wallmap2[z][xc];}
                                else {var below="00";}                               
                            } else {check="00"; below="00";}   
                               if ((check=="00" || check=="17" || check=="19" || check=="20" || check=="40") && below!=="42"){
                                    //console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Barrier to Right
                                    barrier1[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier1_"+x+"_"+z+"_"+y+"_R2", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier1[x][z][y].parent = cell[x][z][y];
                                    barrier1[x][z][y].material = barrierMaterial;
                                    barrier1[x][z][y].rotation.y = Math.PI/2;
                                    barrier1[x][z][y].position = new BABYLON.Vector3(-xs/2,(-wallheight/3)+floordepth,0);
                                    barrier1[x][z][y].receiveShadows = true;
                                    barrier1[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier1[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier1[x][z][y],x,z,y);                                    
                               }                              
                                
                            // check left
                            var xc = Number(Number(x)+1);
                            if (xc<wmap[z].length){                            
                                var check = wmap[z][xc];
                                if (y==1){var below = wallmap0[z][xc];}
                                else if (y==2){var below = wallmap1[z][xc];}
                                else if (y==3){var below = wallmap2[z][xc];}
                                else {var below="00";} 
                            } else {check="00"; below="00";}    
                               // check OK 
                               if ((check=="00" || check=="17" || check=="19" || check=="20" || check=="40") && below!=="44"){
                                    //console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Barrier to Left
                                    barrier3[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier1_"+x+"_"+z+"_"+y+"_L3", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier3[x][z][y].parent = cell[x][z][y];
                                    barrier3[x][z][y].material = barrierMaterial;
                                    barrier3[x][z][y].rotation.y = -Math.PI/2;
                                    barrier3[x][z][y].position = new BABYLON.Vector3(xs/2,(-wallheight/3)+floordepth,0);
                                    barrier3[x][z][y].receiveShadows = true;
                                    barrier3[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier3[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier3[x][z][y],x,z,y);                                    
                                }                                                                                        
                        }       
                        
                        // if left wall, window, opeing ... or open floor
                        // ----------------------------------------------
                        if (type=="04" || type=="24" || type=="34" || type=="15" || type=="16" || type=="18" || type=="42"){ 
                            var xc = Number(Number(x)+1);
                            if (xc<wmap[z].length){                            
                                var check = wmap[z][xc];
                                if (y==1){var below = wallmap0[z][xc];}
                                else if (y==2){var below = wallmap1[z][xc];}
                                else if (y==3){var below = wallmap2[z][xc];}
                                else {var below="00";}  
                            } else {check="00"; below="00";}    
                               if ((check=="00" || check=="17" || check=="19" || check=="20" || check=="40") && below!=="44"){
                                    // console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Right Barrier
                                    barrier1[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier_"+x+"_"+z+"_"+y+"_R3", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier1[x][z][y].parent = cell[x][z][y];
                                    barrier1[x][z][y].material = barrierMaterial;
                                    barrier1[x][z][y].rotation.y = -Math.PI/2;
                                    barrier1[x][z][y].position = new BABYLON.Vector3(xs/2,(-wallheight/3)+floordepth,0);
                                    barrier1[x][z][y].receiveShadows = true;
                                    barrier1[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier1[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier1[x][z][y],x,z,y);                                     
                               }                              
                             
                            // check front
                            var zc = Number(Number(z)-1);
                            if (zc>=0){                            
                               var check = wmap[zc][x]; 
                                if (y==1){var below = wallmap0[zc][x];}
                                else if (y==2){var below = wallmap1[zc][x];}
                                else if (y==3){var below = wallmap2[zc][x];}
                                else {var below="00";} 
                            } else {check="00"; below="00";}    
                               if ((check=="00" || check=="17" || check=="19" || check=="20" || check=="40") && below!=="43"){
                                    // console.log("Type:"+type+" x:"+x+" z:"+z+" y:"+y+" check:"+check+" below:"+below);
                                    // Draw Front Barrier
                                    barrier0[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier_"+x+"_"+z+"_"+y+"_F3", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier0[x][z][y].parent = cell[x][z][y];
                                    barrier0[x][z][y].material = barrierMaterial;
                                    barrier0[x][z][y].position = new BABYLON.Vector3(0,(-wallheight/3)+floordepth,-zs/2);
                                    barrier0[x][z][y].receiveShadows = true;
                                    barrier0[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier0[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier0[x][z][y],x,z,y);                                   
                               }                              
                                                       
                            // check back
                            var zc = Number(Number(z)+1);
                            if (zc<wmap.length){
                                var check = wmap[zc][x];
                                if (y==1){var below = wallmap0[zc][x];}
                                else if (y==2){var below = wallmap1[zc][x];}
                                else if (y==3){var below = wallmap2[zc][x];}
                                else {var below="00";} 
                            } else {check="00"; below="00";}    
                                if ((check=="00" || check=="17" ||  check=="19" || check=="20" || check=="40") && below!=="41"){  
                                    // Draw Barrier at Back
                                    barrier2[x][z][y] = BABYLON.MeshBuilder.CreateBox("barrier3_"+x+"_"+z+"_"+y+"_B3", {width:barrierwidth, depth:ceilingdepth, height:barrierheight, faceUV:faceUVinternal, wrap:true});
                                    barrier2[x][z][y].parent = cell[x][z][y];
                                    barrier2[x][z][y].material = barrierMaterial;
                                    barrier2[x][z][y].position = new BABYLON.Vector3(0,(-wallheight/3)+floordepth,zs/2);
                                    barrier2[x][z][y].receiveShadows = true;
                                    barrier2[x][z][y].checkCollisions = true;
                                    new BABYLON.PhysicsAggregate(barrier2[x][z][y], BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                                    
                                    poles(barrier2[x][z][y],x,z,y);                                    
                                }                                                           
                        }                                               
                    }
                };
                
                const walk = function (cam, wx,wz,wy){
                    if (moving===false){
                        unbindkeys();
                        moving=true;
                        var targetEndPos = new BABYLON.Vector3(wx,wy+iheight,wz);
                        var speed1 = 25; //frames per second move
                        var tf     = 100; //No of seconds
                        var ease = new BABYLON.CubicEase();
                        ease.setEasingMode(BABYLON.EasingFunction.EASINGMODE_EASEINOUT);
                        var mwalk = BABYLON.Animation.CreateAndStartAnimation('at4', cam, 'position', speed1, tf, cam.position, targetEndPos, 0, ease); // Move to target
                        mwalk.onAnimationEnd = function () { 
                            pointer.isVisible = false; 
                            moving=false; 
                            bindkeys();
                        };
                    }
                };
                
                const opendoor = function(doorx,doorz,doory){
                    //console.log("open door");
                    doorstatus[doorx][doorz][doory]=2;
                    //for door to open and close
                    var rsweep = new BABYLON.Animation("rsweep", "rotation.y", 25, BABYLON.Animation.ANIMATIONTYPE_FLOAT, BABYLON.Animation.ANIMATIONLOOPMODE_CONSTANT);
                    var rsweep_keys = []; 
                    rsweep_keys.push({frame: 0, value: 0});
                    rsweep_keys.push({frame: 50, value: Math.PI/2});
                    rsweep.setKeys(rsweep_keys);

                    var lsweep = new BABYLON.Animation("lsweep", "rotation.y", 25, BABYLON.Animation.ANIMATIONTYPE_FLOAT, BABYLON.Animation.ANIMATIONLOOPMODE_CONSTANT);
                    var lsweep_keys = []; 
                    lsweep_keys.push({frame: 0, value: 0});
                    lsweep_keys.push({frame: 50, value: -Math.PI/2});
                    lsweep.setKeys(lsweep_keys);

                    var animStatus1 = scene.beginDirectAnimation(hinge1[doorx][doorz][doory], [rsweep], 0, 50, false);
                    var animStatus2 = scene.beginDirectAnimation(hinge2[doorx][doorz][doory], [lsweep], 0, 50, false);
                    animStatus1.onAnimationEnd = function () {doorstatus[doorx][doorz][doory]=1;};
                    animStatus2.onAnimationEnd = function () {};         
                };

                const closedoor = function(doorx,doorz,doory){
                    //console.log("close door");
                    doorstatus[doorx][doorz][doory]=2;
                    var rsweep = new BABYLON.Animation("rsweep", "rotation.y", 25, BABYLON.Animation.ANIMATIONTYPE_FLOAT, BABYLON.Animation.ANIMATIONLOOPMODE_CONSTANT);
                    var rsweep_keys = []; 
                    rsweep_keys.push({frame: 0, value: Math.PI/2});
                    rsweep_keys.push({frame: 50, value: 0});
                    rsweep.setKeys(rsweep_keys);

                    var lsweep = new BABYLON.Animation("lsweep", "rotation.y", 25, BABYLON.Animation.ANIMATIONTYPE_FLOAT, BABYLON.Animation.ANIMATIONLOOPMODE_CONSTANT);
                    var lsweep_keys = []; 
                    lsweep_keys.push({frame: 0, value: -Math.PI/2});
                    lsweep_keys.push({frame: 50, value: 0});
                    lsweep.setKeys(lsweep_keys);

                    var animStatus1 = scene.beginDirectAnimation(hinge1[doorx][doorz][doory], [rsweep], 0, 50, false);
                    var animStatus2 = scene.beginDirectAnimation(hinge2[doorx][doorz][doory], [lsweep], 0, 50, false);

                    animStatus1.onAnimationEnd = function () {doorstatus[doorx][doorz][doory]=0;}
                    animStatus2.onAnimationEnd = function () {};         
                };
                
                function Xpos(xp){
                    return (xp*xs)-50;
                }
                function Zpos(zp){
                    return (zp*zs)-50;
                }
                function Ypos(yp){
                    return (yp*ys);
                }
                
                function setCell(x,z,y,type){                  
                    let i=0;
                    while (i<objects.length){
                        if (scene.getMeshByName(objects[i]+"_"+x+"_"+z+"_"+y)) {
                            //console.log(objects[i]+"_"+x+"_"+z+"_"+y);
                            window[objects[i]][x][z][y].dispose();
                        }
                        i++;
                    }
                    
                    $.post("setcell.php",{building:building,cellX:x,cellZ:z,cellY:y,celltype:type},function(data){
                        if (data.error !=="none"){console.log(data.error);}
                        else {
                            switch (data.type){
                                case "00":   break;  //nothing
                                case "01": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,front,0,0); break;  // Front Wall
                                case "02": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,right,0,0); break;  // Right Wall
                                case "03": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,back,0,0);  break;  // Rear Wall
                                case "04": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,left,0,0);  break;  // Left Wall
                                case "05": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,front,0,0); walls(data.x,data.z,data.y,left,0,1);  break;  // Front Left Wall
                                case "06": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,front,0,0); walls(data.x,data.z,data.y,right,0,1); break;  // Front Right Wall
                                case "07": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,back,0,0);  walls(data.x,data.z,data.y,right,0,1); break;  // Rear Right Wall
                                case "08": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,back,0,0);  walls(data.x,data.z,data.y,left,0,1);  break;  // Rear Left Wall
                                case "09": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,front,0,0); walls(data.x,data.z,data.y,back,0,1);  break;  // Front & Back
                                case "10": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,left,0,0);  walls(data.x,data.z,data.y,right,0,1); break;  // Left & Right
                                case "11": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,front,0,0); walls(data.x,data.z,data.y,left,0,1);  walls(data.x,data.z,data.y,right,0,2);  break;  // Front Left & Right Wall
                                case "12": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,right,0,0); walls(data.x,data.z,data.y,front,0,1); walls(data.x,data.z,data.y,back,0,2);  break;  // Right Front & Back Wall
                                case "13": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,back,0,0);  walls(data.x,data.z,data.y,left,0,1);  walls(data.x,data.z,data.y,right,0,2);  break;  // Back Left & Right Wall
                                case "14": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,left,0,0);  walls(data.x,data.z,data.y,front,0,1); walls(data.x,data.z,data.y,back,0,2);  break;  // Left Front & Back Wall
                                case "15": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); break; // Floor and Normal Ceiling
                                case "16": floors(data.x,data.z,data.y,1); break; // Floor (No Ceiling)
                                case "17": ceilings(data.x,data.z,data.y,2); break; // No Floor Glass Ceiling
                                case "18": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,2); break; //Floor and Glass Ceiling
                                case "19": ceilings(data.x,data.z,data.y,1); break; // Standard ceiling no floor
                                case "20": ceilings(data.x,data.z,data.y,3); break; // floor landing opening with no ceiling
                                case "40": ceilings(data.x,data.z,data.y,1); break; // floor landing with standard ceiling above
                                case "21": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,front,1,0); break; // front wall with opening
                                case "22": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,right,1,0); break;  // right wall with opening
                                case "23": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,back,1,0);  break;  // rear wall with opening
                                case "24": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,left,1,0);  break;  // left wall with opening
                                case "25": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,4); walls(data.x,data.z,data.y,front,2,0); break; // front wall with double door
                                case "26": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,4); walls(data.x,data.z,data.y,right,2,0); break;  // right wall with double door
                                case "27": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,4); walls(data.x,data.z,data.y,back,2,0);  break;  // rear wall with double door
                                case "28": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,4); walls(data.x,data.z,data.y,left,2,0);  break;  // left wall with double door                                    
                                case "29": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,front,1,0); walls(data.x,data.z,data.y,back,1,1);  break;  // Front & Back with opening
                                case "30": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,left,1,0);  walls(data.x,data.z,data.y,right,1,1); break;  // Left & Right with opening      
                                case "31": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,front,3,0); break; // front wall with half window
                                case "32": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,right,3,0); break;  // right wall with half window
                                case "33": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,back,3,0);  break;  // rear wall with half window
                                case "34": floors(data.x,data.z,data.y,1); ceilings(data.x,data.z,data.y,1); walls(data.x,data.z,data.y,left,3,0);  break;  // left wall with half window  
                                case "41": floors(data.x,data.z,data.y,1); stairs(data.x,data.z,data.y,front); break;
                                case "42": floors(data.x,data.z,data.y,1); stairs(data.x,data.z,data.y,right); break;
                                case "43": floors(data.x,data.z,data.y,1); stairs(data.x,data.z,data.y,back); break;
                                case "44": floors(data.x,data.z,data.y,1); stairs(data.x,data.z,data.y,left); break;
                            }
                            barriers (data.x,data.z,data.y);
                        }
                    },"json");                      
                }
                
                // Detect Mesh Under Pointer
                const pointerDown = (mesh, fid)=> {
                    var meshID = mesh.id;
                    //console.log("Clicked on "+meshID);

                    //if (meshID === "ground" || meshID.substring(0,5)=="floor" || meshID.substring(0,4)=="step" ){
                    if (meshID.substring(0,4)=="ramp" ){
                        if (moving==false){
                            let ray = scene.createPickingRay(scene.pointerX, scene.pointerY, BABYLON.Matrix.Identity(), null);
                            let hit = scene.pickWithRay(ray);
                            let pickedPoint = hit.pickedPoint;
                            pointer.position = new BABYLON.Vector3(pickedPoint.x,(pickedPoint.y),pickedPoint.z);
                            pointer.isVisible = true;
                            walk (camera, pickedPoint.x, pickedPoint.z, pickedPoint.y);  
                        }
                    }
                    
                    if ((meshID.substring(0,9)==="character")){
                        var data = meshID.split("_");
                        console.log(data[0]+" "+data[1]+" Status:"+charStatus[data[1]]+" move:"+charMoving[data[1]]+" physics:"+charPhysics[data[1]]+" Hits:"+charHits[data[1]]);
                    }
                    
                    if ((meshID.substring(0,4)==="door")){
                        var doorData = meshID.split("_");
                        var doorx = doorData[1];
                        var doorz = doorData[2];
                        var doory = doorData[3];
                        //console.log("Door "+doorx+" "+doorz+" "+doory+" Status:"+doorstatus[doorx][doorz][doory]);

                        if (doorstatus[doorx][doorz][doory]===0){
                            opendoor(doorx,doorz,doory);
                        }
                        else if (doorstatus[doorx][doorz][doory]===1){
                            closedoor(doorx,doorz,doory);               
                        }
                        else {var donothing = 0;}
                    }     
                };
                               
                
                function poles(mesh,x,z,y){
                    var pole1 = BABYLON.MeshBuilder.CreateCylinder("pole1_"+x+"_"+z+"_"+y,{diameter:xs/50,height:wallheight/3});
                    pole1.parent = mesh;
                    pole1.material = poleMaterial;     
                    var pole2 = BABYLON.MeshBuilder.CreateCylinder("pole2_"+x+"_"+z+"_"+y,{diameter:xs/50,height:wallheight/3});
                    pole2.parent = mesh;
                    pole2.position.x = xs/2;
                    pole2.material = poleMaterial; 
                    var pole3 = BABYLON.MeshBuilder.CreateCylinder("pole3_"+x+"_"+z+"_"+y,{diameter:xs/50,height:wallheight/3});
                    pole3.parent = mesh;
                    pole2.position.x = -xs/2;
                    pole3.material = poleMaterial; 
                }
                                  
                // Creates a basic Babylon Scene object
                const scene = new BABYLON.Scene(engine);
                //scene.useOrderIndependentTransparency = true;
                // set gravity and collisions
                 // initialize plugin
                const havokInstance = await HavokPhysics();
                // pass the engine to the plugin
                const hk = new BABYLON.HavokPlugin(true, havokInstance);
                // enable physics in the scene with a gravity
                //scene.enablePhysics();
                scene.enablePhysics(new BABYLON.Vector3(0, -1, 0), hk);
                scene.collisionsEnabled = true;
                
                //camera
                // Parameters : name, position, scene
                var startpos = new BABYLON.Vector3(Xpos(cameraPosX),Ypos(cameraPosY)+iheight,Zpos(cameraPosZ));
                var startview = new BABYLON.Vector3(Xpos(cellPosX),Ypos(cellPosY)+iheight,Zpos(cellPosZ));
                
                var camera = new BABYLON.UniversalCamera("UniCamera", startpos, scene);                             
                camera.ellipsoid = new BABYLON.Vector3(xs/6, iheight/2, zs/6);  
                camera.applyGravity = true;
                camera.checkCollisions = true;               
                camera.speed=1;
                //camera.angularSpeed = .5;
                camera.angularSensibility=400; //Camera Pan Speed (smaller Faster)
                camera.minZ = .1;
                camera.inertia = -1;
                camera.fovMode = BABYLON.Camera.FOVMODE_HORIZONTAL_FIXED; 
                
                camera.attachControl(canvas, true); // allows user input        
                camera.inputs.addMouseWheel();
                camera.inputs.attached.mousewheel.wheelPrecisionY = 0.5;
                camera.inputs.attached.mousewheel.wheelPrecisionX = 0.5;
                
                camera.inputs.removeByType("FreeCameraKeyboardMoveInput");
                //camera.inputs.removeByType("FreeCameraMouseInput");
                            
                camera.setTarget(startview);
                
                var plight = new BABYLON.DirectionalLight("plight", new BABYLON.Vector3(0, -2, 0), scene);
                plight.position = new BABYLON.Vector3(sunX, sunY, sunZ);
                var lighttarget = new BABYLON.Vector3( Xpos(5), Ypos(1), Zpos(5) );
                plight.setDirectionToTarget(lighttarget);
                plight.intensity = 0.4;
                
                var shadowGenerator = new BABYLON.ShadowGenerator(1024, plight);
                //shadowGenerator.useBlurExponentialShadowMap = true;
                shadowGenerator.usePercentageCloserFiltering = true;
                //shadowGenerator.useContactHardeningShadow = true;
                //shadowGenerator.contactHardeningLightSizeUVRatio = 0.0075;

                // Built-in 'ground' shape.
                var groundMaterial = new BABYLON.StandardMaterial("groundMaterial");
                groundMaterial.emissiveColor = new BABYLON.Color3(0.5, 0.5, 0.5);
                groundMaterial.diffuseTexture = new BABYLON.Texture("textures/patio.jpg", scene);
                groundMaterial.diffuseTexture.uScale = gdepth/4;   //Repeat n times on the Vertical Axes
                groundMaterial.diffuseTexture.vScale = gwidth/2;    //Repeat n times on the Horizontal Axes
                
                const ground = new BABYLON.MeshBuilder.CreateGround("ground", {width: gwidth, height: gdepth}, scene);
                ground.material = groundMaterial;
                ground.receiveShadows = true;
                ground.checkCollisions = true;
                var groundAggregate = new BABYLON.PhysicsAggregate(ground, BABYLON.PhysicsShapeType.BOX, { mass: 0 }, scene);
                
                var skybox = BABYLON.Mesh.CreateBox("skyBox", gwidth*2, scene);
                var skyboxMaterial = new BABYLON.StandardMaterial("skyBox", scene);
                skyboxMaterial.backFaceCulling = false;
                skyboxMaterial.reflectionTexture = new BABYLON.CubeTexture("textures/TropicalSunnyDay", scene);
                skyboxMaterial.reflectionTexture.coordinatesMode = BABYLON.Texture.SKYBOX_MODE;
                skyboxMaterial.diffuseColor = new BABYLON.Color3(0, 0, 0);
                skyboxMaterial.specularColor = new BABYLON.Color3(0, 0, 0);
                skyboxMaterial.disableLighting = true;
                skybox.material = skyboxMaterial;    
                
                var sunMaterial = new BABYLON.StandardMaterial("sunMaterial", scene);
                sunMaterial.emissiveColor = new BABYLON.Color3(1, 1, 0.7);
                
                var sun = BABYLON.MeshBuilder.CreateSphere("sphere", {diameter:sunD}, scene);
                sun.position = new BABYLON.Vector3(sunX,sunY,sunZ);
                sun.material = sunMaterial;
                
                var cellMaterial = new BABYLON.StandardMaterial("cellMaterial", scene);
                cellMaterial.emissiveColor = new BABYLON.Color3(0.3, 1, 0.3); // Green
                cellMaterial.alpha = 0.5;

                var wallMaterial = new BABYLON.StandardMaterial("wallMaterial", scene);
                wallMaterial.emissiveColor = new BABYLON.Color3(0.3, 0.3, 0.3); 
                //wallMaterial.diffuseColor = new BABYLON.Color3(1, 1, 1);
                wallMaterial.diffuseTexture = new BABYLON.Texture("textures/concrete.jpg", scene);
                wallMaterial.diffuseTexture.hasAlpha = true;
                wallMaterial.useAlphaFromDiffuseTexture = true;
                wallMaterial.alpha = 1;
                
                var ceilingMaterial = new BABYLON.StandardMaterial("ceilinglMaterial", scene);
                ceilingMaterial.emissiveColor = new BABYLON.Color3(0.7, 0.7, 0.7); 
                ceilingMaterial.diffuseTexture = new BABYLON.Texture("textures/ceiling1.jpg", scene);
                ceilingMaterial.alpha = 1;
                
                var lightMaterial = new BABYLON.StandardMaterial("ceilinglMaterial", scene);
                lightMaterial.emissiveColor = new BABYLON.Color3(1, 1, 1); 
                lightMaterial.alpha = 1;                
                
                var glassCeilingMaterial = new BABYLON.StandardMaterial("glassCeilingMaterial", scene);
                glassCeilingMaterial.emissiveColor = new BABYLON.Color3(0.8, 0.8, 0.8); 
                glassCeilingMaterial.diffuseTexture = new BABYLON.Texture("textures/skylight.png", scene);   
                glassCeilingMaterial.diffuseTexture.hasAlpha = true;
                glassCeilingMaterial.useAlphaFromDiffuseTexture = true;
                glassCeilingMaterial.alpha = 1; 
                
                var barrierMaterial = new BABYLON.StandardMaterial("barrierMaterial", scene);
                barrierMaterial.emissiveColor = new BABYLON.Color3(0.2, 0.25, 0.3); 
                barrierMaterial.diffuseColor = new BABYLON.Color3(0, 0, 0);
                barrierMaterial.specularColor = new BABYLON.Color3(1, 1, 1);
                //barrierMaterial.diffuseTexture = new BABYLON.Texture("textures/barrier2.png", scene);   
                //barrierMaterial.diffuseTexture.hasAlpha = true;
                //barrierMaterial.useAlphaFromDiffuseTexture = true;  
                barrierMaterial.alpha = 0.5;  
                
                var poleMaterial = new BABYLON.StandardMaterial("poleMaterial",scene);
                poleMaterial.emissiveColor = new BABYLON.Color3(0.3, 0.3, 0.3);
                
                var skirtMaterial = new BABYLON.StandardMaterial("skirtMaterial", scene); 
                skirtMaterial.emissiveColor = new BABYLON.Color3(0.3, 0.3, 0.3); 
                skirtMaterial.diffuseColor = new BABYLON.Color3(0.5, 0.5, 0.5);
                skirtMaterial.specularColor = new BABYLON.Color3(1, 1, 1);
                
                var floorMaterial = new BABYLON.StandardMaterial("floorMaterial", scene);
                floorMaterial.emissiveColor = new BABYLON.Color3(0.3, 0.3, 0.3); 
                floorMaterial.specularColor = new BABYLON.Color3(1, 1, 0.9);
                floorMaterial.diffuseTexture = new BABYLON.Texture("textures/floorsmall.jpg", scene);
                //floorMaterial.alpha = 1;
                
                var stepMaterial = new BABYLON.StandardMaterial("stepMaterial", scene);
                stepMaterial.emissiveColor = new BABYLON.Color3(0.3, 0.3, 0.3);                
                stepMaterial.diffuseTexture = new BABYLON.Texture("textures/stairs.jpg", scene);
                //stepMaterial.alpha = 1;
                
                var rampMaterial = new BABYLON.StandardMaterial("rampMaterial", scene);
                rampMaterial.emissiveColor = new BABYLON.Color3(0.4, 0.4, 0.4);                
                rampMaterial.alpha = 1;    
                
                var postMaterial = new BABYLON.StandardMaterial("postMaterial", scene);
                postMaterial.emissiveColor = new BABYLON.Color3(0.3, 0.3, 0.3);              
                //postMaterial.alpha = 1;                  
                
                var doorMaterialL = new BABYLON.StandardMaterial("doorMaterialL", scene);
                doorMaterialL.emissiveColor = new BABYLON.Color3(0.6, 0.6, 0.6); 
                doorMaterialL.specularColor = new BABYLON.Color3(1, 1, 0.9);
                doorMaterialL.diffuseTexture = new BABYLON.Texture("textures/darkwood3.jpg", scene);   
                //doorMaterialL.diffuseTexture.hasAlpha = true;
                //doorMaterialL.useAlphaFromDiffuseTexture = true; 
                //doorMaterialL.alpha = 1;
                
                var doorMaterialR = new BABYLON.StandardMaterial("doorMaterialR", scene);
                doorMaterialR.emissiveColor = new BABYLON.Color3(0.6, 0.6, 0.6); 
                doorMaterialR.specularColor = new BABYLON.Color3(1, 1, 0.9);
                doorMaterialR.diffuseTexture = new BABYLON.Texture("textures/darkwood3.jpg", scene);   
                //doorMaterialR.diffuseTexture.hasAlpha = true;
                //doorMaterialR.useAlphaFromDiffuseTexture = true; 
                //doorMaterialR.alpha = 1;       
                
                var hingeMaterial = new BABYLON.StandardMaterial("hingeMaterial", scene);
                hingeMaterial.emissiveColor = new BABYLON.Color3(0.5, 0.5, 0.5); 
                
                var windowMaterial = new BABYLON.StandardMaterial("windowMaterial", scene);
                //windowMaterial.diffuseColor = new BABYLON.Color3(0.8, 0.4, 0.4); // warmish
                windowMaterial.emissiveColor = new BABYLON.Color3(0.5,0.5,0.5); 
                windowMaterial.specularColor = new BABYLON.Color3(1, 1, 0.9);
                windowMaterial.diffuseTexture = new BABYLON.Texture("textures/window3.png", scene);   
                windowMaterial.diffuseTexture.hasAlpha = true;
                windowMaterial.useAlphaFromDiffuseTexture = true; 
                windowMaterial.alpha = 1;
                
                var pointerMaterial = new BABYLON.StandardMaterial("pointerMaterial");
                pointerMaterial.emissiveColor = new BABYLON.Color3(0.5, 1, 0.5);
                pointerMaterial.alpha = 0.5;
                
                var pointer = new BABYLON.MeshBuilder.CreateCylinder("pointer", {diameter:xs/6, height:ys/60});
                pointer.material = pointerMaterial;
                pointer.isVisible = false;
                //var pointerhead = new BABYLON.MeshBuilder.CreateSphere("pointer", {diameter:xs/14});
                //pointerhead.position.y = ys/7;
                //pointerhead.material = pointerMaterial;
                //pointerhead.parent = pointer;
                
                var lboxMaterial = new BABYLON.StandardMaterial("lbMaterial");
                lboxMaterial.emissiveColor = new BABYLON.Color3(0.25, 0.2, 0.05);
                lboxMaterial.specularColor = new BABYLON.Color3(1, 1, 0.9);
    
                var elightMaterial = new BABYLON.StandardMaterial("elMaterial");
                elightMaterial.diffuseTexture = new BABYLON.Texture("textures/exit.jpg", scene);
                elightMaterial.emissiveColor = new BABYLON.Color3(.7, .8, .7);
                elightMaterial.specularColor = new BABYLON.Color3(0.1, .7, .1);
                
                var downlightMaterial = new BABYLON.StandardMaterial("downlightMaterial");
                downlightMaterial.diffuseTexture = new BABYLON.Texture("textures/dlight2.png", scene);
                downlightMaterial.emissiveColor = new BABYLON.Color3(.5, .5, .2);
                //downlightMaterial.specularColor = new BABYLON.Color3(1, 1, 1);     
                downlightMaterial.diffuseTexture.hasAlpha = true;
                downlightMaterial.useAlphaFromDiffuseTexture = true; 
                //downlightMaterial.alpha = 0.1;
                
                var floormatMaterial = new BABYLON.StandardMaterial("floormatMaterial");
                floormatMaterial.diffuseTexture = new BABYLON.Texture("textures/floormat3.jpg", scene);
                floormatMaterial.emissiveColor = new BABYLON.Color3(0.3, 0.3, 0.3);
                floormatMaterial.diffuseColor = new BABYLON.Color3(0.1, 0.1, 0.1);
                //floormatMaterial.specularColor = new BABYLON.Color3(1, 1, 0.0);
                             
                var characterArmMaterial = new BABYLON.StandardMaterial("characterMaterial");
                characterArmMaterial.emissiveColor = new BABYLON.Color3(0.1, 0.1, 0.1);               
                
                var redMaterial = new BABYLON.StandardMaterial("characterMaterial");
                redMaterial.emissiveColor = new BABYLON.Color3(1, 0, 0);
                
                var characterBodyMaterial = new BABYLON.StandardMaterial("characterBodyMaterial");
                characterBodyMaterial.diffuseTexture = new BABYLON.Texture("textures/robot.jpg", scene);
                characterBodyMaterial.emissiveColor = new BABYLON.Color3(0.4, 0.4, 0.4);    
                
                var characterHeadMaterial = new BABYLON.StandardMaterial("characterHeadMaterial");
                characterHeadMaterial.emissiveColor = new BABYLON.Color3(1, 1, 1); 
                characterHeadMaterial.alpha = 0.4;               
                
                var bulletMaterial = new BABYLON.StandardMaterial("bulletMaterial");
                bulletMaterial.emissiveColor = new BABYLON.Color3(1, 0, 0);
                
                var bullet = BABYLON.MeshBuilder.CreateBox("bullet", {width: xs/30, height: ys/30, depth: zs/30});
                bullet.material = bulletMaterial;
                bullet.isVisible = false;
                
                var malfunction = new BABYLON.Sound("malfunction", "sounds/malfunct.wav", scene, null, {
                            loop: false,
                            autoplay: false
                 });
                 
                var hit = new BABYLON.Sound("hit", "sounds/metalhit.wav", scene, null, {
                            loop: false,
                            autoplay: false,
                            volume: 0.05
                 });  
                 var collision = new BABYLON.Sound("hit", "sounds/crash.mp3", scene, null, {
                            loop: false,
                            autoplay: false,
                            volume: 0.1
                 }); 
                
                // loop through map
                var y=0;             
                var front = 1;
                var right = 2;
                var back  = 3;
                var left  = 4;
                for(let y = 0; y < levels; y++) {
                    
                    var wallmap = window["wallmap"+y];
                    
                    for(let z = 0; z < wallmap.length; z++) {
                        for(let x = 0; x < wallmap[z].length; x++) {
                            cell[x][z][y] = BABYLON.MeshBuilder.CreateBox("cell_"+x+"_"+z+"_"+y, {width: xs, height: ys, depth: zs});
                            cell[x][z][y].position = new BABYLON.Vector3((x*xs)-50, (y*ys)+(ys/2), (z*zs)-50);
                            cell[x][z][y].material = cellMaterial;
                            cell[x][z][y].isPickable = false;
                            cell[x][z][y].isVisible = false;                           
                            if (wallmap[z][x]!=="00"){
                                setCell(x,z,y,wallmap[z][x]);
                            }                           
                        }
                    } 
                }
                    
                // click on screen
                scene.onPointerObservable.add((pointerInfo) => {      		
                    switch (pointerInfo.type) {
                        case BABYLON.PointerEventTypes.POINTERDOWN:
                            if(pointerInfo.pickInfo.hit) {
                                pointerDown(pointerInfo.pickInfo.pickedMesh);
                            }
                        break;
                    }
                });
                
                var lcounter = 0;
                var scounter = 0;
                var suncounter = 0;
                
                var alpha = Math.PI;
                var sdia = 100;
                var sunoffset = -35;  
                
                var currentCharacter=0;
                  
                scene.registerBeforeRender(function(){                    
                    suncounter++;
                    if (suncounter>100){
                        suncounter = 0;
                        sunX =+ Math.cos(alpha)*sdia;
                        sunY =+ Math.cos(alpha)*20;
                        sunZ =+ Math.sin(alpha)*sdia;

                        plight.position.x = sunoffset + sunX;
                        plight.position.y = 30 + sunY;
                        plight.position.z = sunoffset + sunZ;

                        sun.position.x = sunoffset + sunX;
                        sun.position.y = 30 + sunY;
                        sun.position.z = sunoffset + sunZ;

                        plight.setDirectionToTarget(BABYLON.Vector3.Zero());
                        alpha += .0001;
                        
                    }
                                       
                    // move interval counter
                    scounter++;
                    if (scounter>=(1000/nchar)){                                              
                        if (startMovement===true){
                            moveCharacter(currentCharacter);
                            currentCharacter++;
                            if (currentCharacter>=nchar){currentCharacter=0;} 
                        }
                        scounter = 0;                    
                    }  
                    
                    
                    camera.onCollide = function (mesh) {
                                             
                       var meshID = mesh.name;
                       var meshSplit = meshID.split("_");
                       
                       if (meshSplit[0]!=="floor"){console.log(mesh.name);}
                       
                       if (meshSplit[0]==="character"){
                            var chID = meshSplit[1];
                            if (charStatus!=="attack"){
                                charStatus[chID]="attack";
                                if (charMoving[chID]===true){
                                    chmove[chID].stop();
                                    chrotate[chID].stop();  
                                }                             
                                charMoving[chID]=false;  
                                characterAggregate[chID].dispose();
                                brainMaterial[chID].emissiveColor = new BABYLON.Color3(1, 0, 0);
                                characterMaterial[chID].alpha=0.1;
                                var pos = camera.position;
                                var dv = pos.subtract(character[chID].position);
                                var yaw = -Math.atan2(dv.z, dv.x) - Math.PI / 2;
                                var dest = new BABYLON.Quaternion.RotationYawPitchRoll(yaw, 0, 0);
                                
                                var ease = new BABYLON.CubicEase();
                                ease.setEasingMode(BABYLON.EasingFunction.EASINGMODE_EASEINOUT);
                                var rotQtr = BABYLON.Animation.CreateAndStartAnimation("quickRotate", character[chID], "rotationQuaternion", framerate, totalframes/4, character[chID].rotationQuaternion, dest, 0, ease);
                                //character[chID].rotationQuaternion = dest;
                                
                                setTimeout(function(){
                                    charStatus[chID]="alive";
                                    brainMaterial[chID].emissiveColor = new BABYLON.Color3(1, 1, 1);
                                    characterMaterial[chID].alpha=0;
                                },5000);
                            }
                            
                            
                            collision.play();                                                  
                            energy=energy-0.5;
                            $(".energybar").css("width",energy+"%");
                            if (energy<0){gameover();}
                        }
                    };
                    
                });
                
                setTimeout(function(){                  
                    $(".loading").fadeOut(1000);
                },12000);
                
                function gameover(){                   
                    $(".gameOverMessage").html("<div>You Are Dead</div><div id='restart' style='font-size:2vw'>Click Here to Continue</div>");
                    $(".gameOver").fadeIn();
                    $("#restart").on("click",function(){
                        energy=100;
                        $(".energybar").css("width","100%");
                        $(".gameOverMessage").html("");
                        $(".gameOver").fadeOut();
                    });
                }
                
                function enableCharacterPhysics(characterNo){
                    brainMaterial[characterNo].emissiveColor = new BABYLON.Color3(1, 1, 1);
                    characterAggregate[characterNo] = new BABYLON.PhysicsAggregate(character[characterNo], BABYLON.PhysicsShapeType.BOX, { mass: charMass, friction: charFriction, restitution: charRestitution }, scene);
                    characterAggregate[characterNo].body.setCollisionCallbackEnabled(true);                                  
                    charMoving[characterNo]=false;  
                    charPhysics[characterNo]=true; 
                    characterMaterial[characterNo].alpha = 0;
                    //console.log("Character "+characterNo+" has stopped moving.");
                }
                
                function disableCharacterPhysics(characterNo){
                    brainMaterial[characterNo].emissiveColor = new BABYLON.Color3(0, 1, 0);
                    characterAggregate[characterNo].dispose();
                    charMoving[characterNo]=true;
                    charPhysics[characterNo]=false;
                    characterMaterial[characterNo].alpha = 0.05;
                    //console.log("Character "+characterNo+" has started moving.");        
                }
                
                function setDead(characterNo){
                    charStatus[characterNo]="dead";
                    charPhysics[characterNo]=true;
                    charMoving[characterNo]=false;  
                    malfunction.play();
                    brainMaterial[characterNo].emissiveColor = new BABYLON.Color3(1, 0, 0);
                    characterMaterial[characterNo].alpha = 0;
                    setTimeout(function(){
                        brainMaterial[characterNo].emissiveColor = new BABYLON.Color3(0, 0, 0);
                        brainMaterial[characterNo].diffuseColor = new BABYLON.Color3(0.3, 0.3, 0.3);
                        brainMaterial[characterNo].specularColor = new BABYLON.Color3(0, 0, 0);
                    },1000);  
                }
                
                const moveCharacter = (characterNo)=> {
                                    
                    if (charMoving[characterNo]===false && charStatus[characterNo]==="alive"){
                        
                        var currentCellContent = window["wallmap"+charPosY[characterNo]][charPosZ[characterNo]][charPosX[characterNo]];                      
                        var directionX = Math.round(Math.random()*2)-1;
                        var directionZ = Math.round(Math.random()*2)-1;                      
                        var directionY = 0;
                    
                        var moveX=0;
                        var moveZ=0;
                        var moveY=0;

                        if (Number(charPosX[characterNo])+Number(directionX)<gridsizeX && Number(charPosX[characterNo])+Number(directionX)>0){
                            moveX = Number(directionX);
                        }
                        if (Number(charPosZ[characterNo])+Number(directionZ)<gridsizeZ && Number(charPosZ[characterNo])+Number(directionZ)>0){
                            moveZ = Number(directionZ);
                        }  

                        // Destination Cell 
                        var tempX = Number(charPosX[characterNo]) + Number(moveX);
                        var tempY = Number(charPosY[characterNo]) + Number(moveY);
                        var tempZ = Number(charPosZ[characterNo]) + Number(moveZ);
                        var tempRot = character[characterNo].rotation.y + (Math.PI*2)/(Math.random(3)+1);
                        var rotateEnd = new BABYLON.Vector3(0,tempRot,0);
                        // check if already occupied;
                        var check = true;
                        var ncheck = 0;
                        while (ncheck<nchar){
                           if (tempX===charPosX[ncheck] && tempY===charPosY[ncheck] && tempZ===charPosZ[check]){
                               check = false;
                               break;
                           } 
                           ncheck++;
                        }

                        if (check===true){
                            var cellContent = window["wallmap"+tempY][tempZ][tempX];
                            if ( (moveY===0 && (cellContent==="15"|| cellContent==="16"|| cellContent==="18" || (cellContent=="00" && charPosY[characterNo]==0) )) || (moveY===-1 && (cellContent==="16")) || (moveY===1 && (cellContent==="17" || cellContent==="19")) ){
                                charPosX[characterNo]=tempX;
                                charPosY[characterNo]=tempY;
                                charPosZ[characterNo]=tempZ;
                                
                                var finalX = Xpos(tempX)+(Math.random(xs/2)-Math.random(xs/2));
                                var finalZ = Zpos(tempZ)+(Math.random(zs/2)-Math.random(zs/2));
                                var finalY = Ypos(tempY)+(ys/4)+floordepth

                                var destination = new BABYLON.Vector3(finalX,finalY,finalZ);
                                brainMaterial[characterNo].emissiveColor = new BABYLON.Color3(0, 1, 0);
                                characterAggregate[characterNo].dispose();
                                charMoving[characterNo]=true;
                                charPhysics[characterNo]=false;
                                characterSound[characterNo].play();
                                characterMaterial[characterNo].alpha = 0.05;
                                //console.log("Character "+characterNo+" has started moving.");
                                var ease = new BABYLON.CubicEase();
                                ease.setEasingMode(BABYLON.EasingFunction.EASINGMODE_EASEINOUT);
                                chmove[characterNo] = BABYLON.Animation.CreateAndStartAnimation("characterMove", character[characterNo], "position", framerate, totalframes, character[characterNo].position, destination, 0,ease);
                                chrotate[characterNo] = BABYLON.Animation.CreateAndStartAnimation("characterRotate", character[characterNo], "rotation", framerate, totalframes, character[characterNo].rotation, rotateEnd, 0,ease);
                                chmove[characterNo].onAnimationEnd = function () {
                                    enableCharacterPhysics(characterNo);
                                };
                                //rotate.onAnimationEnd = function () {};                                 
                            }  
                        }
                    }
                };
                
                hk.onCollisionObservable.add((ev) => {
                    
                    var object1   = ev.collider.transformNode.name;
                    var object2   = ev.collidedAgainst.transformNode.name;
                    var object1ID = object1.split("_");
                    var object2ID = object2.split("_");
                    
                    if (object1ID[0]!=="floor" && object2ID[0]!=="floor"){
                        //console.log(ev.type+" "+object1+" "+object2);
                        if (ev.type==="COLLISION_STARTED"){
                            if (object1ID[0]==="character" && object2ID[0]==="firedBullet"){
                                if (charStatus[object1ID[1]]!=="dead"){
                                    hit.play();
                                    charHits[object1ID[1]]--;                              
                                    //console.log("Character "+object1ID[1]+" Remain:"+charHits[object1ID[1]]);
                                    if (charHits[object1ID[1]]<0){
                                        setDead(object1ID[1]);
                                    }
                                }                                                                                         
                            }
                            else if (object2ID[0]==="character" && object1ID[0]==="firedBullet"){
                                if (charStatus[object2ID[1]]!=="dead"){
                                    hit.play();
                                    charHits[object2ID[1]]--;
                                    //console.log("Character "+object2ID[1]+" Remain:"+charHits[object2ID[1]]);
                                    if (charHits[object2ID[1]]<0){
                                        setDead(object2ID[1]);
                                    }
                                }                                                              
                            }
                        }
                    }
                                     
                });
                
                return scene;

            };
        
        createScene().then((scene) => {
            engine.runRenderLoop(function () {
                if (scene) {
                    scene.render();
                }
            });
        });
        
    }
        
//        const scene = createScene(); //Call the createScene function
//        // Register a render loop to repeatedly render the scene
//        engine.runRenderLoop(function () {
//                scene.render();
//        });
       
        // Watch for browser/canvas resize events
        window.addEventListener("resize", function () {
                engine.resize();
        });

        
        
        $(function(){

            start();
            refreshLoop();
            
            setInterval(() => {               
                if (fps>=50){$(".fps").css("color","green");}
                else if (fps<50 && fps>=25){$(".fps").css("color","yellow");}
                else if (fps<25 && fps>=15){$(".fps").css("color","orange");}
                else if (fps<15){$(".fps").css("color","red");}
                $("#frames").html(fps);
            }, 1000);

        }); 
        
        });
	</script>
    </body>
</html>