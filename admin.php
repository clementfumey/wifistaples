
<?php
if (count($_POST)) {
    echo "<div class=\"success alert\">Configuration mise a jour</div>";
    $name = $_POST['name'];
    $relay = $_POST['relay'];
    $format = $_POST['format'];
    $columns = array("pl" => 0,"name" => 0);
    if (count($_POST['checkbox'])){
        $test = $_POST['checkbox'];
        foreach ($test as $key => $value){
            $columns[$value]="";
        }
    }
    if (array_pop(array_keys($columns)) == "ecfin"){
        array_pop($columns);
        $columns["time"]=0;
        $columns["ecfin"]=0;

    }else{
        $columns["time"]=0;
    }

    $event_config = file_get_contents('./base_config.json');
    $event_array = json_decode($event_config, true);
    $event_array["eventName"] = $name;
    $event_array["relay"] = $relay;
    $event_array["relay"] = $relay;
    $event_array["columns"] = $columns;
    var_dump($event_array);
    exec("/var/www/wifistaples/script_wifistaples.bash > /dev/null 2>/dev/null &");
    sleep(1);
    file_put_contents('./result/config',json_encode($event_array));



}

?>

