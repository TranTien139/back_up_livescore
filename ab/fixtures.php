<?php

require_once 'config.php';

$LeagueID = isset($_GET['LeagueID']) ? $_GET['LeagueID'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;

$data = array();
$data['data'] = [];

if ($date != null) {
    if ($LeagueID != null) {
        $sql = "SELECT*FROM ketqua WHERE LeagueID=" . $LeagueID . " AND date_query=" . "'$date'" . " AND is_finish= 2";
    }
} else {
    $date = date('d/m/Y');
    if ($LeagueID != null) {
        $sql = "SELECT*FROM ketqua WHERE LeagueID=" . $LeagueID . " AND is_finish= 2";
    }
}
try {
    if ($LeagueID == null) {
        $sql_selecteam = "SELECT*FROM danh_sach_giai_dau";
        $result_selecteam = mysqli_query($con, $sql_selecteam);

        while ($row0 = mysqli_fetch_array($result_selecteam)) {
            $list = array();
            $LeagueID = $row0['LeagueID'];
            if ($date != null) {
                if ($LeagueID != null) {
                    $sql = "SELECT*FROM ketqua WHERE LeagueID=" . $LeagueID . " AND date_query=" . "'$date'" . " AND is_finish= 2";
                }
            } else {
                $date = date('d/m/Y');
                if ($LeagueID != null) {
                    $sql = "SELECT*FROM ketqua WHERE LeagueID=" . $LeagueID . " AND date_query=" . "'$date'" . " AND is_finish= 2";
                }
            }

            $result = mysqli_query($con, $sql);

            while ($row = mysqli_fetch_array($result)) {
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                $time_start = strtotime($row['time_start']);
                $list[] = array("id" => $row['id'],
                    "home_club_name" => $row['home_club_name'],
                    "away_club_name" => $row['away_club_name'],
                    "home_goal" => $row['home_goal'],
                    "away_goal" => $row['away_goal'],
                    "first_time_home_goal" => 0,
                    "first_time_away_goal" => 0,
                    "is_postponed" => 2,
                    "is_finish" => $row['is_finish'],
                    "time_start" => $time_start
                );
            }

            $data['data'][] = array(
                "league_id" => $row0['LeagueID'],
                "league_name" => $row0['name'],
                "league_logo" => $row0['flag'],
                "matches" => $list
            );
        }

    } else {

        $sql1 = "SELECT*FROM danh_sach_giai_dau WHERE LeagueID=" . $LeagueID;
        $result1 = mysqli_query($con, $sql1);
        $name_team = null;
        while ($row1 = mysqli_fetch_array($result1)) {
            $id = $row1['id'];
        }

        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($result)) {
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $time_start = strtotime($row['time_start']);
            $data['data'][] = array("id" => $row['id'],
                "home_club_name" => $row['home_club_name'],
                "away_club_name" => $row['away_club_name'],
                "home_goal" => $row['home_goal'],
                "away_goal" => $row['away_goal'],
                "first_time_home_goal" => 0,
                "first_time_away_goal" => 0,
                "is_postponed" => 2,
                "is_finish" => $row['is_finish'],
                "time_start" => $time_start
            );
        }
    }
} catch (Exception $e) {
    $data['data'] = [];
}

$data = json_encode($data);

if ($LeagueID != null) {
    $fp = fopen('C:/xampp/htdocs/ab/ktt/fixtures/'.$id.'_vi.js', "w+");
    if (!$fp) {

    } else {
        fwrite($fp, $data, strlen($data));
        fclose($fp);
    }
}


echo (string)$data;

?>
