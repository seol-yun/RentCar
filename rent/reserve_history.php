<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>예약정보</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/header.css">
    
</head>
<body>
<header class="header">
      <div class="header__wrapper">
        <!-- 초기 검색화면으로 돌아감 -->
        <h1 class="header__logo">
          <a href="./index.php">CNU rent</a>
        </h1>
        <!-- 로그인 페이지(로그인이 되었을때는 나타나지 않음) -->
        <div class="header__login" id="header__login">
          <a href="./login.php">로그인</a>
        </div>
        <!-- 로그아웃 -->
        <div class="header__logout" id="header__logout">
        <form  action="#" method="post">
          <button type="button" name="logout_submit" id="logout_submit">로그아웃</button>
        </form>
        <?php
        if(isset($_POST['logout_submit'])){//로그인이 되어있으면 로그아웃을 할 수 있다.
          session_start();
          if(isset($_SESSION['cno'])){
            session_unset();
            session_destroy();
          }
        }
         ?>
        </div>
        <!-- 로그인이 되었을 때 예약내역이 뜸 -->
        <div class="header__reserve-history" id="reserve_history">
          <a href="./reserve_history.php">예약내역</a>
        </div>
        <div class="header__rent-history" id="rent_history">
          <a href="./rent_history.php">대여내역</a>
        </div>
        <div class="header__pre-rent" id="pre_rent">
          <a href="./pre_rent.php">이전대여내역</a>
        </div>
        <div class="header__statistic" id="statistic">
          <a href="./statistic.php">통계정보</a>
        </div>
        <div class="header__profile">
        <?php
          session_start();
          if(isset($_SESSION['cno'])){
          echo $_SESSION['cno'];
          echo "<script type='text/javascript' src = './js/inout.js'></script>";
        }
         ?>
        </div>
      </div>
      
    </header>

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

        // 로그인이 되었으면
        if (isset($_SESSION['cno'])) {
            $cno = $_SESSION['cno'];

            //예약내역 중에 예약시작날짜가 현재 날짜 이하가 되면 예약테이블에서 삭제하고 rentcar에서 값들을 수정한다.
            $updateQuery = "MERGE INTO rentcar
                USING reserve
                ON (rentcar.licenseplateno = reserve.licenseplateno)
                WHEN MATCHED THEN
                    UPDATE SET rentcar.daterented = reserve.startdate,
                               rentcar.datedue = reserve.enddate,
                               rentcar.cno = :cno
                WHERE reserve.startdate <= SYSDATE";

            $deleteQuery = "DELETE FROM reserve WHERE startdate <= SYSDATE";

            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(':cno', $_SESSION['cno']);

            $deleteStmt = $conn->prepare($deleteQuery);

            $updateStmt->execute();
            $deleteStmt->execute();
                        



            // 고객 넘버와 같은 값들을 reserve테이블에서 가져온다
            $query = "SELECT r.startdate, r.licenseplateno, r.reservedate, r.enddate, r.cno, cm.modelname
                        FROM reserve r
                        JOIN rentcar rc ON r.licenseplateno = rc.licenseplateno
                        JOIN carmodel cm ON rc.modelname = cm.modelname
                        WHERE r.cno = :cno
                        ORDER BY r.startdate desc
                        ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':cno', $cno);

            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<form action='./reserve_cancel.php' method='post'>";
            echo "<table class='table table-light'>
            <tr class='table-dark'>
                <th>시작 날짜</th>
                <th>차량 번호</th>
                <th>예약 날짜</th>
                <th>종료 날짜</th>
                <th>고객 번호</th>
                <th>모델 이름</th>
                <th>예약 취소</th>
            </tr>";

            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>" . $row['STARTDATE'] . "</td>";
                echo "<td>" . $row['LICENSEPLATENO'] . "</td>";
                echo "<td>" . $row['RESERVEDATE'] . "</td>";
                echo "<td>" . $row['ENDDATE'] . "</td>";
                echo "<td>" . $row['CNO'] . "</td>";
                echo "<td>" . $row['MODELNAME'] . "</td>";
                echo "<td><input type='radio' name='selected_car' value='" . $row['LICENSEPLATENO'] . "'></td>";
                echo "<input type='hidden' name='start_date' value='" .$row['STARTDATE'] . "'>";
                echo "<input type='hidden' name='end_date' value='" .$row['ENDDATE'] . "'>";
            }

            echo "</table>";
            echo "<button type='submit' name='cancel_submit'>예약 취소</button>";
            echo "</form>";

        } else {
            echo "로그인이 필요합니다.";
        }
    } catch (PDOException $e) {
        echo("에러 내용: " . $e->getMessage());
  }
?>
</body>
<script type="text/javascript" src="./js/logout.js"></script>
</html>