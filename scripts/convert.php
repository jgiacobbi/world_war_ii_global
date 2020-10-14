<?php

//This converts xml from the hijacked project into a more civilized json

$file = dirname(__DIR__) . "/old/map/games/ww2global40_2nd_edition.xml";
$xml = simplexml_load_file($file);

$territoryFile = dirname(__DIR__) . "/data/territory.json";
$unitFile = dirname(__DIR__) ."/data/units.json";

$territories = [];
$units = [];

foreach($xml->map->territory as $territory)
{
    $territories[(string) $territory['name']] = ["water" => (bool) $territory['water']];
}

foreach($xml->map->connection as $connection)
{
    $territories[(string) $connection["t1"]]["connections"][] = (string) $connection["t2"];
}

foreach($xml->initialize->ownerInitialize->territoryOwner as $ownerInit)
{
    $territories[(string) $ownerInit["territory"]]["owner"][] = (string) $ownerInit["owner"];
}

//      <unitPlacement unitType="infantry" territory="Portugal" quantity="2" owner="Neutral_True"/>
foreach($xml->initialize->unitInitialize->unitPlacement as $unitStart)
{
    $units
        [(string) $unitStart["territory"]]
        [(string) $unitStart["owner"]]
        [(string) $unitStart["unitType"]] = (int) $unitStart["quantity"];
}

file_put_contents($unitFile, json_encode($units, JSON_PRETTY_PRINT));
file_put_contents($territoryFile, json_encode($territories, JSON_PRETTY_PRINT));
