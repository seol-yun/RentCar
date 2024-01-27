<?php
session_start();

// 로그인이 되었는지 확인
if (!isset($_SESSION['cno'])) {
    echo "<script>alert('로그인이 필요합니다.');
        location.href='./index.php';
        </script>";
    exit;
}

// 차량을 선택했는지 확인
if (!isset($_POST['selected_car'])) {
    echo "<script>alert('차량을 선택해야 합니다.');
        location.href='./index.php';
        </script>";
    exit;
}



$tns = "
    (DESCRIPTION=
    (ADDRESS_LIST= (ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
    (CONNECT_DATA= (SERVICE_NAME=XE))
    )";
$dsn = "oci:dbname=".$tns.";charset=utf8";
$username = 'tp3';
$password = '1234';
try {
    $conn = new PDO($dsn, $username, $password);

    $licenseplateno = $_POST['selected_car'];
    $startdate = $_POST['start_date'];
    $enddate = $_POST['end_date'];
    $cno = $_SESSION['cno'];

    // 선택한 날짜에 이미 예약이 있으면 예약안됨 
    $checkQuery = "SELECT COUNT(*) as count
                   FROM reserve
                   WHERE cno = :cno and ((:start_date between startdate and enddate) or(:end_date between startdate and enddate)) ";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':cno', $cno);
    $checkStmt->bindParam(':start_date', $_POST['start_date']);
    $checkStmt->bindParam(':end_date', $_POST['end_date']);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();


    // 같은 차종을 다른 고객이 같은 날짜에 선택했다면 예약안됨
    $checkQuery = "SELECT COUNT(*) as count
                   FROM reserve
                   WHERE licenseplateno = :licenseplateno and ((:start_date between startdate and enddate) or(:end_date between startdate and enddate)) ";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':cno', $cno);
    $checkStmt->bindParam(':start_date', $_POST['start_date']);
    $checkStmt->bindParam(':end_date', $_POST['end_date']);
    $checkStmt->execute();
    $count += $checkStmt->fetchColumn();


    if ($count > 0) {
        echo "<script>alert('해당 날짜에 이미 예약이 있습니다.');
        location.href='./reserve_history.php';
        </script>";
        exit;
    }
    else{ //예약 할 수 있을때
        // 차량의 정보를 reserve테이블에 넣음
        $query = "INSERT INTO reserve (startdate, licenseplateno, reservedate, enddate, cno)
                VALUES (:start_date, :licenseplateno, to_date(sysdate,'yyyy.mm.dd hh24:mi'), :end_date, :cno)";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':start_date', $_POST['start_date']);
        $stmt->bindParam(':licenseplateno', $licenseplateno);
        $stmt->bindParam(':end_date', $_POST['end_date']);
        $stmt->bindParam(':cno', $cno);
        $stmt->execute();

        // 돌아오기
        header("Location: ./reserve_history.php");
    }
    } catch (PDOException $e) {
        echo("에러 내용: ".$e->getMessage());
    }
?>