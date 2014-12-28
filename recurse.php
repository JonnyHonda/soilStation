<?php
function printCurrentDirRecursively($originDirectory, $printDistance=0){
   
    // just a little html-styling
    if($printDistance==0)echo '<div style="color:#35a; font-family:Verdana; font-size:11px;">';
    $leftWhiteSpace = "";
    for ($i=0; $i < $printDistance; $i++)  $leftWhiteSpace = $leftWhiteSpace."&nbsp;";
   
   
    $CurrentWorkingDirectory = dir($originDirectory);
    while($entry=$CurrentWorkingDirectory->read()){
        if($entry != "." && $entry != ".."){
            if(is_dir($originDirectory."\\".$entry)){
                echo $leftWhiteSpace."<b>".$entry."</b><br>\n";
                printCurrentDirRecursively($originDirectory."\\".$entry, $printDistance+2);
             }
            else{
                echo $leftWhiteSpace.$entry."<br>\n";
            }
        }
    }
    $CurrentWorkingDirectory->close();
   
    if($printDistance==0)echo "</div>";
}

//TEST IT!
printCurrentDirRecursively(getcwd()."/data");

?>
