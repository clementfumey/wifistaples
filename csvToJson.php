<?php
function getHeaders($bb,$cc){
    $tab = array();
    foreach($bb as $aa => $index ){
        if ($aa == "ecrad" || $aa == "plrad"){
            //
        }else if ($aa == "rad"){
            foreach($cc as $index => $control){
                array_push($tab,"Poste ".$control[0]);
                if (in_array("ecrad",$bb)){
                    array_push($tab,"+/-");
                }
            }
        }else if ($aa == "name"){
            array_push($tab,"Name");
        }else if ($aa == "time"){
            array_push($tab,"Temps");
        }else if ($aa == "club"){
            array_push($tab,"Club");
        }else if ($aa == "fin"){
            array_push($tab,"H.Arrivée");
        }else if ($aa == "start"){
            array_push($tab,"H.Départ");
        }else if ($aa == "ecfin"){
            array_push($tab,"");
        }else{
            array_push($tab,$aa);
        }

    }
    return $tab;
}
function deleteDixieme($time){
    return explode(",",$time)[0];
}
function formatTime($time){
    $time = explode(",",$time)[0];
    if ($time == "-----"){
        return "-----";
    }
    $time_exploded = explode(":",$time);

    if (count($time_exploded) == 2){
        $time = "00:".firstnumberTime($time_exploded[0]).":".$time_exploded[1];
    }else{
        $time = firstnumberTime($time_exploded[0]).":".$time_exploded[1].":".$time_exploded[2];
    }
    return $time;
}

function firstnumberTime($time){
    if ($time < 10){
        return "0$time";
    }else{
        return $time;
    }
}
function calculate_gap($time, $best){
    $time = formatTime($time);
    $best = formatTime($best);

    $tf = new Datetime("$time");
    $bf = new Datetime("$best");

    $diffTime = $tf->diff($bf);
    if ($diffTime->h == 0 && $diffTime->i == 0 ){
        return "+ ".$diffTime->s;
    }else if ($diffTime->h ==0){
        return "+ ".$diffTime->i.":".firstnumberTime($diffTime->s);
    }else{

        return "+ ".$diffTime->h.":".firstnumberTime($diffTime->i).":".firstnumberTime($diffTime->s);
    }

}

function calculate_gaps($categoryResultList,$categoryControl,$bestRaceTime){
    foreach($categoryResultList as $key => $runnerresult){
        foreach($runnerresult as $index => $column){
            if ($column == "gap"){
                $controlindex = $index-1;
                if ($runnerresult[$controlindex] != ""){
                    $gap = calculate_gap($runnerresult[$controlindex],$bestRaceTime);
                    $categoryResultList[$key][$index] = $gap;
                }
            }else if ((strpos($column,"splitgap_")) === 0){
                $controlindex = $index-1;

                if ($runnerresult[$controlindex] != ""){
                    $controltime = explode("  ",$runnerresult[$controlindex])[0];
                    $controlnumber =(int) explode("_",$column)[1];
                    $gap = calculate_gap($controltime,$categoryControl[$controlnumber-1][2]);
                    $categoryResultList[$key][$index] = $gap;
                }

            }
        }
    }
    return $categoryResultList;
}

$dos = array("Stno", "N° dép.");
$si = array ("puce", "chipno", "SI card");
$pl = array("Place", "Pl");
$name = array("Surname","Nom","Nachname","Name");
$firstname = array("First name", "Prénom", "Vorname");
$club = array("Club", "Ville", "City");
$nat = array("Nat");
$start = array("Départ", "Start");
$fin = array("Arrivée", "Finish");
$time = array("Temps", "Time");


$csv_file = "./result/$argv[1]";
# open the csv_file
$csv = fopen("$csv_file", "r");
$csv_headers = fgetcsv($csv,1000,";");
$num = count($csv_headers);


$event_config = file_get_contents('./result/config');
$event_array = json_decode($event_config, true);
$columns = $event_array["columns"];

//$file_config = file_get_contents("./result/config_".explode(".",$argv[1])[0]);
$file_array = json_decode($event_config,true);

$categoryIndex = "";
foreach($columns as $columns_head => $index){
    if ($columns_head == "rad"){
        for($i=0;$i<$num; $i++){
            if (((strpos($csv_headers[$i],"Control")) === 0 || (strpos($csv_headers[$i],"Poste"))=== 0 ) && ((strpos($csv_headers[$i+1],"Punch")) === 0 || (strpos(utf8_encode($csv_headers[$i+1]),"Poinçon")) === 0)){
                echo("I have detected a Control at csv index $i");
                $file_array["columns"]["rad"] = $i;

                if (in_array("ecrad", $columns)){
                    $file_array["columns"]["ecrad"] = "";
                }
                if (in_array("plrad", $columns) && ((strpos($csv_headers[$i+2],"Pl")) === false)){
                    $key1 = array_search("plrad", $columns);
                    unset($file_array["columns"][$key1]);
                }else if (in_array("plrad", $columns) && ((strpos($csv_headers[$i+2],"Pl")) === 0)){
                    $file_array["columns"]["plrad"] = "";
                }
                break;
            }
        }
    }else if ($columns_head == "ecrad" || $columns_head == "plrad"){
    //nothing
    }else if ($columns_head == "ecfin"){
        $file_array["columns"]["ecfin"] = "";
    }else{
        for($i=0; $i<$num; $i++){
            if (in_array(utf8_encode($csv_headers[$i]), $$columns_head)){
                echo($columns_head);
                $file_array["columns"][$columns_head] = $i;
                break;
            }else if ($csv_headers[$i] == "Court" || $csv_headers[$i] == "Short"){
                $categoryIndex = $i;
                echo("CATEGORYYY");
                continue;
            }else if ($csv_headers[$i] == "Evaluation" || $csv_headers[$i] == "Wertung"){
                $evaluationIndex=$i;
                continue;
            }

        }
    }
}

$category = "";
$nbControl = 0;
$$category = array();
$categoryControl = array();
$categoryResultList = array();
$bestRaceTime = "23:59:59";
while (($data = fgetcsv($csv, 1000, ";")) !== FALSE) {
    $num = count($data);
    if ($num <= 10){
        break;
    }

    if ($data[(int)$categoryIndex] != $category){
        if($category != ""){
            echo ("La Categorie etait : $category");
            echo("$bestRaceTime");
            $categoryResultList = calculate_gaps($categoryResultList,$categoryControl,$bestRaceTime);
            $jsonString = json_encode($categoryResultList);

            file_put_contents("./result/$category."."json","{\"aaData\":".$jsonString."}");

            $$category = getHeaders($file_array["columns"],$categoryControl);
            if ($event_array["category"][$category] =! $$category){
            $event_array["category"][$category]=$$category;
            file_put_contents('./result/config',json_encode($event_array));
            }

        }

        $category = $data[(int)$categoryIndex];
        echo ("La Categrorie est : $category");
        $$category = array();
        $categoryControl = array();
        $categoryResultList = array();
        $bestRaceTime = "23:59:59";
    }
    $runnerresult = array();
    foreach($file_array["columns"] as $column => $index){
        if ($column == "ecrad"){
            //nothing to do
        }else if ($column == "ecfin"){
            $controltime = end($runnerresult); //Because column ecart is always just after column time
            $controtime = formatTime($controltime);
            if (strtotime($bestRaceTime) > strtotime($controltime) && $controltime !="" && strtotime($controltime) !=FALSE){
                $bestRaceTime = $controltime;
                echo("Best Race Time $bestRaceTime");
            }
            if (strtotime($controltime) != FALSE){
                array_push($runnerresult,"gap");
            }else{
                array_push($runnerresult,"");
            }

        }else if ($column == "name"){
            $name = utf8_encode($data[(int)$index]." ".$data[(int)$index+1]);
            array_push($runnerresult,$name);
        }else if ($column == "plrad"){
            //nothing
        }else if ($column == "rad"){
            if ($index == ""){
                continue;
            }
            $lastSplit = "00:00:00";
            $nbControl = 0;
            for($i=(int)$index+1; $i<$num; $i++){

                $split = formatTime($data[$i]);

                if (in_array("plrad",$event_array["columns"]) && $data[$i+1] != "" && strtotime($split) > strtotime($lastSplit)){
                    $splitandplace = deleteDixieme($data[$i])."  (".$data[$i+1].")";
                    array_push($runnerresult,$splitandplace);
                    $nbControl ++;
                    $lastSplit = $split;
                }else if (strtotime($split) > strtotime($lastSplit)){
                    array_push($runnerresult,deleteDixieme($data[$i]));
                    $nbControl++;
                    $lastSplit = $split;
                }else{
                    if($split == "-----"){
                        array_push($runnerresult,"----");
                    }
                    if(in_array("plrad",$event_array["columns"])){
                        $i = $i +2;
                    }else{
                        $i++;
                    }
                    continue;
                }
                if($nbControl > count($categoryControl)){
                    $control = array($nbControl,$data[$i-1],"23:59:59");
                    array_push($categoryControl,$control);
                }
                if (in_array("ecrad",$file_array["columns"])){
                    if (strtotime($categoryControl[$nbControl-1][2]) > strtotime($split) && strtotime($split) != FALSE){
                        $categoryControl[$nbControl-1][2] = $split;
                    }
                    if (strtotime($split) != FALSE){
                        array_push($runnerresult,"splitgap_".$nbControl);
                    }else{
                        array_push($runnerresult,"");
                    }
                }
                if(in_array("plrad",$file_array["columns"])){
                    $i = $i +2;
                }else{
                    $i++;
                }
            }

        }else if($column == "time"){
            if ($data[(int)$evaluationIndex] == "3"){
                array_push($runnerresult,"PM");
            }else{
                array_push($runnerresult,utf8_encode(deleteDixieme($data[(int)$index])));
            }
        }else if($column == "start"){
            array_push($runnerresult,utf8_encode(deleteDixieme($data[(int)$index])));
        }else if($column == "fin"){
            array_push($runnerresult,utf8_encode(deleteDixieme($data[(int)$index])));
        }else{
            array_push($runnerresult,utf8_encode($data[(int)$index]));
        }

    }
    array_push($categoryResultList,$runnerresult);
}

if($category != ""){
    $categoryResultList = calculate_gaps($categoryResultList,$categoryControl,$bestRaceTime);
    $jsonString = json_encode($categoryResultList);
    file_put_contents("./result/$category."."json","{\"aaData\":".$jsonString."}");

    $headers = getHeaders($file_array["columns"],$categoryControl);
    $event_array["category"][$category]=$headers;
    file_put_contents('./result/config',json_encode($event_array));
}




?>

