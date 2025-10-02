<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Error handling function
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

try {
    include 'dbconnection.php';

    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$minPrice = isset($_GET['minPrice']) ? floatval($_GET['minPrice']) : 0;
$maxPrice = isset($_GET['maxPrice']) ? floatval($_GET['maxPrice']) : PHP_FLOAT_MAX;

// Build the query
$query = "SELECT * FROM inventory WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $query .= " AND product_name LIKE ?";
}

// Add category condition
if (!empty($category)) {
    $query .= " AND category = ?";
}

// Add price range condition
$query .= " AND price >= ? AND price <= ?";

// Prepare the statement
$stmt = $conn->prepare($query);

// Create array of parameters
$params = array();
$types = "";

if (!empty($search)) {
    $searchParam = "%" . $search . "%";
    $params[] = &$searchParam;
    $types .= "s";
}

if (!empty($category)) {
    $params[] = &$category;
    $types .= "s";
}

$params[] = &$minPrice;
$params[] = &$maxPrice;
$types .= "dd";

// Bind parameters if any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Fetch all products
$products = array();
while ($row = $result->fetch_assoc()) {
    $products[] = array(
        'id' => $row['id'],
        'product_name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'price' => $row['price'],
        'category' => $row['category']
    );
}

    // Add pagination info
    $totalProducts = count($products);
    $response = [
        'success' => true,
        'products' => $products,
        'totalProducts' => $totalProducts,
        'filters' => [
            'search' => $search,
            'category' => $category,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    sendError($e->getMessage(), 500);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
