<?php
// Source DB
$source = new mysqli("localhost","u353140863_db","Rrsv_2026","u353140863_db");

// Target DB
$target = new mysqli("localhost","u353140863_rrsv","Rrsv_2026","u353140863_rrsv");

if ($source->connect_error || $target->connect_error) {
    die("Connection failed");
}

$totalUpdated = 0;

$q = mysqli_query($source,"
    SELECT id,dise_code,dise_date
    FROM rrsv_student_registration
    WHERE dise_code='xxxxx'
");

while($row = mysqli_fetch_assoc($q))
{
    $id = $row['id'];
    $dise_code = mysqli_real_escape_string($target,$row['dise_code']);
    $dise_date = $row['dise_date'];

    mysqli_query($target,"
        UPDATE rrsv_student_registration
        SET dise_code='$dise_code',
            dise_date='$dise_date'
        WHERE id='$id'
    ");

    // Count updated rows
    $affected = mysqli_affected_rows($target);

    if($affected > 0){
        $totalUpdated += $affected;

        echo "Updated ID: ".$id."<br>";
    }
}

echo "<hr>";
echo "Total Updated Rows: <b>".$totalUpdated."</b>";
?>