<?php
date_default_timezone_set('Asia/Kolkata');
session_start();

include '../../../include/config.php';
include '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
    exit;
}

$teacher_id = intval($_SESSION['user_id'] ?? ($_SESSION['user_data']['id'] ?? 0));
$teacher_code = 'T-' . $teacher_id;

if (isset($_POST['ajax']) && $_POST['ajax'] === 'cancel_order') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid order item ID']);
        exit;
    }

    mysqli_begin_transaction($con);
    try {
        $row = mysqli_fetch_assoc(mysqli_query($con, "
            SELECT oi.*, p.package_name
            FROM rrsv_food_order_items oi
            JOIN rrsv_food_orders o ON o.id = oi.order_id
            JOIN rrsv_menu_package p ON p.id = oi.food_id
            WHERE oi.id = '$id'
              AND (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
            FOR UPDATE
        "));

        if (!$row) {
            throw new Exception('Order item not found or does not belong to you');
        }

        if (strtotime($row['order_date']) < strtotime(date('Y-m-d'))) {
            throw new Exception('Past orders cannot be cancelled');
        }

        if ($row['status'] === 'Cancelled') {
            mysqli_commit($con);
            echo json_encode(['status' => 'ok', 'msg' => 'Order was already cancelled']);
            exit;
        }

        $refund = (float)$row['price'] * (int)($row['quantity'] ?: 1);

        // Update item status
        mysqli_query($con, "UPDATE rrsv_food_order_items SET status = 'Cancelled' WHERE id = '$id'");

        // Refund wallet
        mysqli_query($con, "
            UPDATE rrsv_food_wallet 
            SET balance = balance + $refund 
            WHERE student_id = '$teacher_code' OR student_id = '$teacher_id'
        ");

        // Insert ledger
        $txn_id = 'TXN' . date('ymd') . rand(100, 999);
        $desc = "Order Refund: " . $row['meal_type'] . " on " . date('d-m-Y', strtotime($row['order_date']));
        $safe_desc = mysqli_real_escape_string($con, $desc);
        mysqli_query($con, "
            INSERT INTO rrsv_food_wallet_ledger 
            (student_id, tns_id, amount, type, description, created_at)
            VALUES 
            ('$teacher_code', '$txn_id', $refund, 'Credit', '$safe_desc', NOW())
        ");

        mysqli_commit($con);
        echo json_encode(['status' => 'ok', 'msg' => 'Order cancelled successfully. ₹' . number_format($refund, 2) . ' refunded to your wallet.']);
        exit;
    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'msg' => 'Unknown action']);
