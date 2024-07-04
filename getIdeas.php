<?php
include 'config.php';

header('Content-Type: application/json');

$query = "SELECT * FROM ideas";
$result = $conn->query($query);

$ideas = [];
while ($row = $result->fetch_assoc()) {
    $ideas[] = $row;
}

echo json_encode($ideas);

$conn->close();
?>