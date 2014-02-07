<?php
function addTab($dom,$container,$cat,$relay){
    $element = $dom->createElement('li', "");
    $a = $dom->createElement('a', "$cat");
    $a->setAttribute("href", "#");
    $element->appendChild($a);
    if ($relay){
        $element->setAttribute("onclick", "tabsTo(); return false;");
    }else{
        $element->setAttribute("onclick", "tabsTo(); loadResults('$cat'); return false;");
    }
    $element->setAttribute("class","switch");
    $element->setAttribute("gumby-trigger",".$cat | .enhanced-tab-content:not(.$cat)");
    $container->appendChild($element);

}
function addTable($dom,$container,$cat,$cate,$config_class_columns){
    $table = $dom->createElement('table', "");
    $table->setAttribute("id", "$cat");
    $thead = $dom->createElement('thead', "");
    $tr = $dom->createElement('tr', "");
    foreach ($cate as $index => $value){
            $th = $dom->createElement('th', "$value");
            if (array_key_exists("$value", $config_class_columns)) {
                foreach ($config_class_columns[$value] as $class => $attr){

                    $th->setAttribute($class, $attr);
                }
            }else if (strpos($value,"Poste") === 0){
                foreach ($config_class_columns["Control"] as $class => $attr){;
                    $th->setAttribute($class, $attr);
                }
            }else if (strpos($value,"+/-") === 0){
                $th = $dom->createElement('th', "");
                foreach ($config_class_columns["ecart_"] as $class => $attr){
                    $th->setAttribute($class, $attr);
                }
            }
            $tr->appendChild($th);

    }
    $thead->appendChild($tr);
    $tbody = $dom->createElement('tbody', "");
    $table->appendChild($thead);
    $table->appendChild($tbody);
    $container->appendChild($table);

}

function createLegTab($dom, $container ,$cat){
        $element = $dom->createElement('div', "");
        $element ->setAttribute("id", "$cat");
        $element->setAttribute("class", "pill tabs");
        $ul = $dom->createElement('ul',"");
        $ul->setAttribute("class", "tab-nav");
        $element->appendChild($ul);
        $container->appendChild($element);


}

function addLegTab($dom, $container, $key){
    $leg = explode("_",$key)[1];

    $element = $dom->createElement('li', "");
    $a = $dom->createElement('a', "Relais $leg");
    $a->setAttribute("href", "#");
    $element->appendChild($a);
    $element->setAttribute("onclick", "loadResults('$key'); return false;");
    $container->appendChild($element);
}

$config_class_columns = array(
    "pl" => array("class" => "centered-cell","stype" =>"numericOrBlank"),
    "Name" => array("data-class" => "expand","class"=>"no_sort"),
    "Club" => array("data-hide" => "phone","class"=>"no_sort"),
    "nat" => array("data-hide" => "phone"),
    "si" => array("data-hide" => "phone, tablet"),
    "dos" => array("data-hide" => "phone, tablet"),
    "rel" => array("data-hide" => "phone, tablet"),
    "H.Départ" => array("data-hide" => "phone, tablet", "stype" =>"mystring"),
    "H.Arrivée" => array("data-hide" => "phone, tablet", "stype" =>"mystring"),
    "Control" => array("data-hide" => "phone", "stype" =>"mystring"),
    "ecart_" => array("data-hide" => "phone", "class" => "no_sort", "stype" =>"mystring"),
    "Temps" => array("stype" => "mystring"),
    "" => array("class"=>"no_sort")
);





$dom = new DomDocument;
$dom->loadHTMLFile("./base_index.html");

$categoryContent = $dom->getElementById("categoryContent");

$event_config = file_get_contents('./result/config');
$event_array = json_decode($event_config, true);
$dom->getElementById("logo")->nodeValue = $event_array["eventName"];

foreach ($event_array["category"] as $key => $cate){
    if ($event_array["relay"] == "true"){
        $cat = explode("_",$key)[0];
        if ($dom->getElementById($cat) != NULL){
            //Only add the table leg
            addLegTab($dom, $dom->getElementById($cat)->getElementsByTagName("ul")->item(0), $key);
            $div = $dom->createElement('div', "");
            $div->setAttribute("class", "tab-content");
            addTable($dom, $div, $key, $cate,$config_class_columns);
            $dom->getElementById($cat)->appendChild($div);

        }else{
            // Add the tab, the div, the table
            addTab($dom, $categoryContent->getElementsByTagName("ul")->item(0), $cat, true);
            $element = $dom->createElement('div', "");
            $element->setAttribute("class", "enhanced-tab-content $cat");
            createLegTab($dom, $element,$cat);
            addLegTab($dom, $dom->getElementById($cat)->getElementsByTagName("ul")->item(0), $key);

            $div = $dom->createElement('div', "");
            $div->setAttribute("class", "tab-content");
            addTable($dom, $div, $key, $cate,$config_class_columns);
            $dom->getElementById($cat)->appendChild($div);
            $categoryContent->getElementsByTagName("section")->item(0)->appendChild($element);
        }
    }else{
        addTab($dom, $categoryContent->getElementsByTagName("ul")->item(0), $key, false);
        $element = $dom->createElement('div', "");
        $element->setAttribute("class", "enhanced-tab-content $key");
        addTable($dom,$element,$key,$cate,$config_class_columns);
        $categoryContent->getElementsByTagName("section")->item(0)->appendChild($element);
    }





}
$dom->formatOutput = TRUE;
$dom->saveHTMLFile("./index.html")


?>

