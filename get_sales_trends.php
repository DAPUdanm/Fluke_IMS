<?php
require_once 'dbconnection.php';

header('Content-Type: application/json');

try {
    // Get sales data for the last 7 days
    $query = "SELECT 
                DATE(sale_date) as date,
                SUM(total_amount) as daily_sales,
                COUNT(*) as order_count
              FROM sales 
              WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
              AND status = 'completed'
              GROUP BY DATE(sale_date)
              ORDER BY date";

    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception($conn->error);
    }

    $salesData = array();
    while ($row = $result->fetch_assoc()) {
        $salesData[] = array(
            'date' => $row['date'],
            'sales' => floatval($row['daily_sales']),
            'orders' => intval($row['order_count'])
        );
    }

    echo json_encode(array(
        'success' => true,
        'data' => $salesData
    ));

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
?>
