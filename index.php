<?php
//ini_set('display_errors', 1);

// convert the csv to json
// HNGi9 CSV FILE - Sheet1.csv
Class CSVParser
{

    private function convertCSV($csv_file){
if (($handle = fopen('uploads/'.$csv_file, "r")) !== FALSE) {
    $csvs = [];
    while(! feof($handle)) {
       $csvs[] = fgetcsv($handle);
    }
    $datas = [];
    $column_names = [];
    foreach ($csvs[0] as $single_csv) {
        $column_names[] = $single_csv;
    }
    foreach ($csvs as $key => $csv) {
        if ($key === 0) {
            continue;
        }
        foreach ($column_names as $column_key => $column_name) {
            $datas[$key-1][$column_name] = $csv[$column_key];
        }
    }
 
    $json = json_encode($datas);
    fclose($handle);
    

    return $json;

}
    }


    // convert json to csv
    private function convertJsonToCSV($uploaded_file){
       //write hash to a csv file

       $converted_array = self::convertCSV($uploaded_file);

       $json_array = json_decode($converted_array, true);

        // limit to just one team for now
    //$json_array = array_slice($json_array, 0, 20, true);


     $new_csv = fopen("modified_csv/"."filename_output.csv", "w");
     foreach ($json_array as $line) {
         $hash = hash_hmac('sha256',json_encode($line), false);
         $line["hash"] = $hash;
         fputcsv($new_csv, $line);
     }
   
     return "Csv generated successfully!";
    }


    // hash the json
    private function hashJson($array){
        $hashed_arr = hash('sha256',(serialize($array)));
        
        return $hashed_arr;
    }

    // processes the converted json, stores to an array and write json files for each of the names
    public function processCSV($csv_file){
        $ok_to_process = 0;
        $file_info = $_FILES;

        if($file_info['csv']['type'] === 'text/csv'){
            $file_name = $_FILES['csv']['name'];

            move_uploaded_file($_FILES["csv"]["tmp_name"], "uploads/" . $file_name);
            $ok_to_process = 1;
        }

        else{
            http_response_code(401);
            echo json_encode(array("message" => "invalid file format", "status" => 0)); 
            $ok_to_process = 0;
        }

      

        if($ok_to_process){
            $path = 'uploads/';
            $uploaded_file = fopen($path . $file_name, 'r+');
                
      $converted_array = self::convertCSV($file_name);

     $json_array = json_decode($converted_array, true);

     // limit to just one team for now
    //$json_array = array_slice($json_array, 0, 20, true);

    $total = count($json_array);

    foreach($json_array as $key){
        $name = $key['Filename'];
        $minting_tool = $key['TEAM NAMES'];
        $description = $key['Description'];
        $series_number = $key['Series Number'];
        $series_total = $total;
        $attr = $key['Attributes'];
        $gender = $key['Gender'];
        $uuid = $key['UUID'];

        $attri_arr = explode(';', $attr);
       
    
        $new_array[] = array(
            'format' => 'CHIP-0007',
            "name" => $name,
                "description" => $description,
                "minting_tool" =>  $minting_tool,
                "sensitive_content" => false,
                "Gender" => $gender,
                "series_number" => $series_number,
                "series_total" =>  $series_total,
                "attributes" => array(
                    array(
                       "trait_type" =>  strtok($attri_arr[0], ':'),
                        "value" => substr(strstr($attri_arr[0], ':'), 1)
                    ),
                    array(
                        "trait_type" =>  strtok($attri_arr[1], ':'),
                        "value" => substr(strstr($attri_arr[1], ':'), 1)
                    ),
                    
                    array(
                        "trait_type" => strtok($attri_arr[2], ':'),
                        "value" => substr(strstr($attri_arr[2], ':'), 1)
                    ),
                    
                    array(
                        "trait_type" =>  strtok($attri_arr[3], ':'),
                        "value" =>substr(strstr($attri_arr[3], ':'), 1)
                    ),
                    
                    array(
                        "trait_type" =>  strtok($attri_arr[4], ':'),
                        "value" => substr(strstr($attri_arr[4], ':'), 1)
                    ),
                    
                    array(
                        "trait_type" =>  strtok($attri_arr[5], ':'),
                        "value" => substr(strstr($attri_arr[5], ':'), 1)
                    ),
                    
                    array(
                        "trait_type" =>  strtok($attri_arr[6], ':'),
                        "value" => substr(strstr($attri_arr[6], ':'), 1)
                    ),
                    
                    array(
                       "trait_type" =>  strtok($attri_arr[7], ':'),
                        "value" => substr(strstr($attri_arr[7], ':'), 1)
                    ),
                    
                    
        
                ),
                "UUID" => $uuid,
            );

    }
 
    $ok_to_generate = 0;
    foreach($new_array as $arr){
    $json_file = fopen("json_files/".$arr["name"].".json", "a");
    fwrite($json_file, json_encode($arr));
    fclose($json_file);

   $ok_to_generate = 1;
    }

    if($ok_to_generate){
        $csvConv = self::convertJsonToCSV($file_name);
        echo json_encode(array("message" => "Csv file has been generated OK", "status" => 1));
    }
    else {
        http_response_code(401);
        echo json_encode(array("message" => "failed to generate file", "status" => 0));
    }
    

    
   
}
    }


}

$run = new CSVParser();
$run->processCSV("test");