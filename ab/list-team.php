<?php
require_once 'config.php';

$sql = "SELECT*FROM danh_sach_giai_dau";
$result = mysqli_query($con,$sql);
$data = null;
while($row = mysqli_fetch_array($result)) {
    $data['data'][] = array(
        "id"=> $row['id'],
        "have_push"=>"2",
        "league_logo"=> $row['flag'],
        "name"=> $row['name'],
    );
}

$data = json_encode($data);
    $fp = fopen('C:\xampp\htdocs\ab\ktt\team\list_vi.js', "w+");
    if (!$fp) {

    } else {
        fwrite($fp, $data, strlen($data));
        fclose($fp);
    }

echo (string) $data;

?>
