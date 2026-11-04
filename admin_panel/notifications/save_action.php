<?php
include '../dbcon.php';

if (isset($_POST['type_name']) && $_POST['type_name'] != '') {
    $name = mysqli_real_escape_string($con, $_POST['type_name']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Generate a unique code automatically based on type_name
    $type_code = strtolower(substr(preg_replace('/\s+/', '_', $name), 0, 10));

    if ($id > 0) {
        // UPDATE
        $query = "UPDATE rrsv_question_type 
                  SET type_name='$name', type_code='$type_code' 
                  WHERE id=$id";
        if (mysqli_query($con, $query)) {
            echo "Type updated successfully!";
        } else {
            echo "Error updating type: " . mysqli_error($con);
        }
    } else {
        // INSERT
        $query = "INSERT INTO rrsv_question_type (type_name, type_code, status) 
                  VALUES ('$name', '$type_code', 'active')";
        if (mysqli_query($con, $query)) {
            echo "Type added successfully!";
        } else {
            echo "Error adding type: " . mysqli_error($con);
        }
    }
}

// DELETE
if (isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $query = "DELETE FROM rrsv_question_type WHERE id=$id";
    if (mysqli_query($con, $query)) {
        echo "Type deleted successfully!";
    } else {
        echo "Error deleting type: " . mysqli_error($con);
    }
}
?>
