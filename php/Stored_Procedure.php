<?php

    // 1. Establish database connection
$pdo = new PDO('mysql:host=localhost;dbname=test_db', 'username', 'password');

    // 2. Prepare the stored procedure call with placeholders (?)
$stmt = $pdo->prepare("CALL spLoadExtractData(?, ?)");

    // 3. Define your parameter variables
$status = 'active';
$limit = 10;

    // 4. Bind parameters and execute
$stmt->bindParam(1, $status, PDO::PARAM_STR);
$stmt->bindParam(2, $limit, PDO::PARAM_INT);
$stmt->execute();

    // 5. Fetch results
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>