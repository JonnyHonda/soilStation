<?php

$taskLocation = realpath(dirname(__FILE__));
chdir($taskLocation);
include ("config.inc.php");
$folder_list = array();
//var_dump($folder_list);
// Parse with sections
$ini_array = parse_ini_file("status.ini", true);
//print_r($ini_array);
$lastupdated_string = $ini_array['lastupdated'];
if ($lastupdated_string == "") {
    // No lastupdated so set one
    $lastupdated_string = "2000-01-01 00:00:00";
}
$lastupdated = date_create($lastupdated_string);
$lastupdated_seconds = strtotime($lastupdated_string);
find_files('./data', $folder_list, $lastupdated_seconds);
$folder_count = count($folder_list);


// interate throught the file list
for ($floop = 0; $floop < $folder_count; $floop++) {
    foreach (current($folder_list) as $file_to_be_processed) {
        $full_file_path = key($folder_list) . "/" . $file_to_be_processed . "\n";
        echo "DEBUG: line#" . __LINE__ . " - File being processed is:" . $full_file_path . "\n";
        $file_contents = file(trim($full_file_path));
        // Now process the line in the file
        // by the time we get only files that need to be processed are in our arry
        // we need to make sure that each entry in the file is later than last updated time
        foreach ($file_contents as $line_in_file) {
            //           echo "DEBUG: " . $line_in_file;
            $values_in_line_array = explode(",", trim($line_in_file));
            //  print "DEBUG: {$values_in_line_array[0]}\n";
            if (strtotime($values_in_line_array[0]) < $lastupdated_seconds) {
                echo "DEBUG: is past \n";
            } else {
                echo "DEBUG: is not past \n";

                $params = array("date" => $values_in_line_array[0],
                    "air_temp" => $values_in_line_array[1],
                    "soil_temp_100" => $values_in_line_array[2],
                    "soil_temp_30" => $values_in_line_array[3],
                    "concrete_temp" => $values_in_line_array[4],
                    "soil_temp_10" => $values_in_line_array[5],
                    "grass_temp" => $values_in_line_array[6]
                );
                if (!MUTE) {
                    httpPost(WEATHER_WEBSITE, $params);
                }
            }

            //curl to mysql server
        }
    }
    next($folder_list);
}
file_put_contents("status.ini", "lastupdated = " . date("Y-m-d H:i:s"));

/* find_files( string, &array )
 * * Recursive function to return a multidimensional array of folders and files
 * * that are contained within the directory given
 */

function find_files($dir, &$dir_array, $lastupdated) {
    //  print "DEBUG: $lastupdated";
    // Create array of current directory
    $files = scandir($dir);

    if (is_array($files)) {
        foreach ($files as $val) {
            // Skip home and previous listings
            if ($val == '.' || $val == '..')
                continue;

            // If directory then dive deeper, else add file to directory key
            if (is_dir($dir . '/' . $val)) {
                // Add value to current array, dir or file
                //    $dir_array[$dir][] = $val;

                find_files($dir . '/' . $val, $dir_array, $lastupdated);
            } else {
                // echo "DEBUG: " . str_replace(".txt", " 23:59:59", $val) . "\n";
                $file_date = str_replace(".txt", " 23:59:59", $val);
                // We only want file written EQUAL or AFTER the lastupdated time
                //    print "DEBUG: $lastupdated - " . strtotime($file_date) ."\n";
                if (strtotime($file_date) < $lastupdated) {
                    //   echo "DEBUG: is past \n";
                } else {
                    //   echo "DEBUG: is not past \n";
                    $dir_array[$dir][] = $val;
                }
            }
        }
    }
    ksort($dir_array);
}

function httpPost($url, $params) {
    $postData = '';
    //create name value pairs seperated by &
    foreach ($params as $k => $v) {
        $postData .= $k . '=' . $v . '&';
    }
    rtrim($postData, '&');
    print $postData;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, count($postData));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $output = curl_exec($ch);

    curl_close($ch);
    return $output;
}

?>
