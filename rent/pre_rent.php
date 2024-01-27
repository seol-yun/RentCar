<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>이전대여내역</title>
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
        if (isset($_SESSION['cno'])) {
        $cno = $_SESSION['cno'];
        // 이전대여내역에서 정보를 가져온다. 대여 시작일을 기준으로 내림차순으로 한다. 
        $selectQuery = "SELECT daterented, licenseplateno, datereturned, payment, cno 
        FROM previousrental
        WHERE cno = :cno
        ORDER BY daterented desc
        ";

        $selectStmt = $conn->prepare($selectQuery);
        $selectStmt->bindParam(':cno', $cno);
        $selectStmt->execute();
        $result = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        //결과 테이블
        if (count($result) > 0) {
            echo "<table class='table table-light'>";
            echo "<tr class='table-dark'><th>대여 날짜</th><th>차량 번호</th><th>반납 날짜</th><th>결제 금액</th><th>고객 번호</th></tr>";

            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>" . $row['DATERENTED'] . "</td>";
                echo "<td>" . $row['LICENSEPLATENO'] . "</td>";
                echo "<td>" . $row['DATERETURNED'] . "</td>";
                echo "<td>" . $row['PAYMENT'] . "</td>";
                echo "<td>" . $row['CNO'] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "이전 대여 기록이 없습니다.";
        }

      }
      else{
        echo "로그인이 필요합니다.";
      }
      
    } catch (PDOException $e) {
        echo("에러 내용: " . $e->getMessage());
    }
    ?>
</body>
<script type="text/javascript" src="./js/logout.js"></script>
</html>