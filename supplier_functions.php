<?php
include('dbconnection.php');

// Handle AJAX requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch ($action) {
        case 'add':
            $supplier_name = $_POST['supplierName'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $address = $_POST['address'];
            
            if (addSupplier($supplier_name, $contact, $email, $address)) {
                echo "success";
            } else {
                echo "error";
            }
            break;

        case 'update':
            $id = $_POST['id'];
            $supplier_name = $_POST['supplierName'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $address = $_POST['address'];
            
            if (updateSupplier($id, $supplier_name, $contact, $email, $address)) {
                echo "success";
            } else {
                echo "error";
            }
            break;

        case 'delete':
            $id = $_POST['id'];
            if (deleteSupplier($id)) {
                echo "success";
            } else {
                echo "error";
            }
            break;
    }
    exit;
}


// Add new supplier
function addSupplier($supplier_name, $contact, $email, $address) {
    global $conn;
    $sql = "INSERT INTO suppliers (supplier_name, contact, email, address) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $supplier_name, $contact, $email, $address);
    return $stmt->execute();
}

// Update supplier
function updateSupplier($id, $supplier_name, $contact, $email, $address) {
    global $conn;
    $sql = "UPDATE suppliers SET supplier_name=?, contact=?, email=?, address=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $supplier_name, $contact, $email, $address, $id);
    return $stmt->execute();
}

// Delete supplier
function deleteSupplier($id) {
    global $conn;
    $sql = "DELETE FROM suppliers WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Get all suppliers
function getSuppliers() {
    global $conn;
    $sql = "SELECT * FROM suppliers";
    $result = $conn->query($sql);
    return $result;
}
?>