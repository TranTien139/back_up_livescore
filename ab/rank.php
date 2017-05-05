<?php

require_once 'config.php';

$LeagueID  = isset($_GET['LeagueID'])? $_GET['LeagueID'] : null;
$seasion  = isset($_GET['seasion'])? $_GET['seasion'] : null;

$data = null;

if($seasion!= null && $LeagueID != null) {
    $sql = "SELECT*FROM bang_xep_hang WHERE LeagueID=" . $LeagueID . " AND seasion=" . "'$seasion'";
    $result = mysqli_query($con, $sql);

    $sql1 = "SELECT*FROM danh_sach_giai_dau WHERE LeagueID=" . $LeagueID;
    $result1 = mysqli_query($con, $sql1);
    $name_team = null;
    while ($row1 = mysqli_fetch_array($result1)) {
        $name_team = $row1['name'];
        $id = $row1['id'];
    }

    while ($row = mysqli_fetch_array($result)) {
        $data_main = json_decode($row['data']);
        $data = array(
            "id"=> $row["id"],
            "season_time_id" => "54",
            "season_time_name"=> $row['seasion'],
            "result_round" => "35",
            "round_type" => "1",
            "name" => $name_team,
            "list_seasons" => $data_main,
        );
    }
}

$result_final['data']  = $data;
$result_final = json_encode($result_final);

if($seasion!= null && $LeagueID != null) {
    $fp = fopen('C:/xampp/htdocs/ab/ktt/standings/' . $id . '_vi.js', "w+");
    if (!$fp) {

    } else {
        fwrite($fp, $result_final, strlen($result_final));
        fclose($fp);
    }
}

echo (string)$result_final;

?>
