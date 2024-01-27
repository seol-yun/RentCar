<?php
    $tns = "
    (DESCRIPTION=
    (ADDRESS_LIST= (ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
    (CONNECT_DATA= (SERVICE_NAME=XE))
    )";
    $dsn = "oci:dbname=" . $tns . ";charset=utf8";
    $username = 'tp3';
    $password = '1234';
    try {
        $conn = new PDO($dsn, $username, $password);
        session_start();
        $licenseplateno = $_POST['selected_car'];
    
        // 예약내역에서 해당 값을 삭제함. licenseplateno가 같으면 reserve테이블에서 삭제
        $deleteQuery = "DELETE FROM reserve WHERE licenseplateno = :licenseplateno ";
    
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':licenseplateno', $licenseplateno);
    
        $deleteStmt->execute();
    

        
    } catch (PDOException $e) {
        echo("에러 내용: " . $e->getMessage());
  }

  header("Location: " . "./reserve_history.php");
?>