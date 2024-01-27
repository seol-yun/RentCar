<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>통계</title>
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
  $dsn = "oci:dbname=".$tns.";charset=utf8";
  $username = 'tp3';
  $password = '1234';
  try {
    $conn = new PDO($dsn, $username, $password);

    // 매출 순위 쿼리
    $query = "SELECT licenseplateno, daterented, datereturned, payment ,
    sum(payment) over (partition by licenseplateno) paysum, rank() over (order by paysum desc) rank
    FROM (
      SELECT licenseplateno, daterented, datereturned, payment,
        SUM(payment) OVER (PARTITION BY licenseplateno) AS paysum
      FROM previousrental
    )";

    // 쿼리 실행
    $stmt = $conn->query($query);
    // 결과 가져오기
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // 결과 출력

    echo "<h3>매출순위</h3>";
    echo "<table class='table table-light'>";
    echo "<tr class='table-dark'><th>랭킹</th><th>차량 번호</th><th>대여일</th><th>반납일</th><th>금액</th><th>총금액</th></tr>";
    echo "<tr class='table-dark'></tr>"; 
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . $row['RANK'] . "</td>";
        echo "<td>" . $row['LICENSEPLATENO'] . "</td>";
        echo "<td>" . $row['DATERENTED'] . "</td>";
        echo "<td>" . $row['DATERETURNED'] . "</td>";
        echo "<td>" . $row['PAYMENT'] . "</td>";
        echo "<td>" . $row['PAYSUM'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";



    //대여 횟수 쿼리
    $query = "SELECT rc.modelname, count(*) as modelcount
    FROM previousrental pr, rentcar rc
    WHERE pr.licenseplateno = rc.licenseplateno
    GROUP BY rc.modelname
    ORDER BY modelcount desc"
    ;

    $stmt = $conn->query($query);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
    echo "<h3>대여 횟수</h3>";
    echo "<table class='table table-light'>";
    echo "<tr class='table-dark'><th>모델이름</th><th>횟수</th></tr>";
    echo "<tr class='table-dark'></tr>"; 
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . $row['MODELNAME'] . "</td>";
        echo "<td>" . $row['MODELCOUNT'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

// 최근 반납된 차량
    $query = "SELECT licenseplateno, daterented, datereturned, payment
    FROM (
        SELECT licenseplateno, daterented, datereturned, payment,
            ROW_NUMBER() OVER (ORDER BY datereturned DESC) AS rn
        FROM previousrental
    )
    WHERE rn <= 5";

    
    $stmt = $conn->query($query);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>최근 반납된 차량</h3>";
    echo "<table class='table table-light'>";
    echo "<tr class='table-dark'><th>차량 번호</th><th>대여일</th><th>반납일</th><th>금액</th></tr>";
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . $row['LICENSEPLATENO'] . "</td>";
        echo "<td>" . $row['DATERENTED'] . "</td>";
        echo "<td>" . $row['DATERETURNED'] . "</td>";
        echo "<td>" . $row['PAYMENT'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

  } catch (PDOException $e) {
  echo("에러 내용: ".$e->getMessage());
  }
  ?>


</body>
<script type="text/javascript" src="./js/logout.js"></script>
</html>