<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_system"; // Change to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine the type of request
if (isset($_POST['sku'])) {
    // Tracking location
    $sku = $_POST['sku'];

    // Construct SQL query to get location using prepared statements
    $stmt = $conn->prepare("SELECT warehouse_location FROM products WHERE sku = ?");
    $stmt->bind_param("s", $sku); // "s" specifies the variable type => 'string'

    // Execute SQL query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if query returned any results
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "Warehouse Location: " . $row["warehouse_location"];
    } else {
        echo "No location found for the given SKU.";
    }
    $stmt->close();

} elseif (isset($_POST['sku_or_name'])) {
    // Retrieving product details
    $sku_or_name = $_POST['sku_or_name'];

    // Construct SQL query using prepared statements
    $stmt = $conn->prepare("SELECT * FROM products WHERE sku = ? OR product_name LIKE ?");
    $likeSkuOrName = "%" . $sku_or_name . "%";
    $stmt->bind_param("ss", $sku_or_name, $likeSkuOrName); // "ss" specifies the variable types => 'string', 'string'

    // Execute SQL query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if query returned any results
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Product Name: " . $row["product_name"] . "<br>" .
                 "SKU: " . $row["sku"] . "<br>" .
                 "Price: $" . $row["price"] . "<br>" .
                 "Stock Status: " . $row["availability"] . "<br>" .
                 "Warehouse: " . $row["warehouse"] . "<br><br>";
        }
    } else {
        echo "No products found for the given criteria.";
    }
    $stmt->close();

} else {
    echo "Invalid request.";
}

// Close connection
$conn->close();
?>
