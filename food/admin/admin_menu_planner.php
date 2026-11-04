<?php
session_start();
include('includes/config.php');

if(strlen($_SESSION['alogin'])==0){
    header('location:index.php');
}

$week = isset($_GET['week']) ? $_GET['week'] : 'THIS';
$days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
$meals = ['Breakfast','Lunch','Dinner','Tiffin'];

if(isset($_POST['save_menu'])){

    $week_type = $_POST['week_type'];

    mysqli_query($con,"DELETE FROM rrsv_food_daily_menu WHERE week_type='$week_type'");

    foreach($_POST['menu'] as $day=>$mealsData){
        foreach($mealsData as $meal=>$items){
            foreach($items as $item){
                if($item['item_id']!=''){
                    mysqli_query($con,"
                        INSERT INTO rrsv_food_daily_menu
                        (week_type,day,meal,item_id,qty)
                        VALUES (
                            '$week_type',
                            '$day',
                            '$meal',
                            '{$item['item_id']}',
                            '{$item['qty']}'
                        )
                    ");
                }
            }
        }
    }

    $msg = "Menu Saved Successfully";
}
?>
