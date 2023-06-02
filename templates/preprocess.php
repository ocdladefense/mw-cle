<?php


function seminars_preprocess($vars) {

    $vars["foo"] = "bar";
    $seminars = $vars["seminars"];

    foreach($seminars as &$seminar) {
        $date = $seminar["Start_Date__c"];
        $titleDate = new DateTime($date);
        $seminar["date"] = $titleDate->format("F jS, Y");
    }

    $vars["seminars"] = $seminars;

    return $vars;
}