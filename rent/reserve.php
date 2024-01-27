<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>예약하기</title>
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
    // 선택한 날짜가 이상할때
    if ($_POST['end_date'] < $_POST['start_date']){
      echo "<script>alert('날짜를 다시 선택하십시오.');
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
      // 폼 데이터 가져오기
      $start_date = $_POST['start_date'];
      $end_date = $_POST['end_date'];
      $car_types = isset($_POST['car_type']) ? $_POST['car_type'] : array();
      // 쿼리 작성
      $query = "SELECT cm.MODELNAME, cm.VEHICLETYPE, cm.RENTRATEPERDAY, cm.fuel, cm.NUMBEROFSEATS, rc.licenseplateno, o.optionname
                  FROM rentcar rc
                  JOIN carmodel cm ON rc.modelname = cm.modelname
                  LEFT JOIN options o ON rc.licenseplateno = o.licenseplateno
                  WHERE cm.VEHICLETYPE IN ('" . implode("', '", $car_types) . "')
                  AND rc.cno IS NULL";
      // 쿼리 실행
      $stmt = $conn->query($query);
      // 결과 가져오기
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      echo "<form action='./reserve_complete.php' method='post'>";
      echo "<table class='table table-light'>
      <tr class='table-dark'>
          <th>모델 이름</th>
          <th>차종</th>
          <th>하루당 가격</th>
          <th>연료</th>
          <th>좌석 수</th>
          <th>차량 번호</th>
          <th>선택</th>
          <th>옵션</th>
          
      </tr>";

      // 중복 제거를 위한 변수 초기화
      $prev_license_plate = null;
      $options = array();

      foreach ($result as $row) {
        // 차량 번호가 이전과 같지 않은 경우에만 새로운 행 시작
        if ($row['LICENSEPLATENO'] !== $prev_license_plate) {
            // 이전 차량 번호에 대한 옵션 출력
            if (!empty($options)) {
                echo "<td>" . implode(", ", $options) . "</td>";
            }

            echo "<tr>";
            echo "<td>" . $row['MODELNAME'] . "</td>";
            echo "<td>" . $row['VEHICLETYPE'] . "</td>";
            echo "<td>" . $row['RENTRATEPERDAY'] . "</td>";
            echo "<td>" . $row['FUEL'] . "</td>";
            echo "<td>" . $row['NUMBEROFSEATS'] . "</td>";
            echo "<td>" . $row['LICENSEPLATENO'] . "</td>";
            
            // 옵션 초기화
            $options = array($row['OPTIONNAME']);
            
            $prev_license_plate = $row['LICENSEPLATENO'];
            echo "<td><input type='radio' name='selected_car' value='" . $row['LICENSEPLATENO'] . "'></td>";
            echo "<input type='hidden' name='start_date' value='" . $_POST['start_date'] . "'>";
            echo "<input type='hidden' name='end_date' value='" . $_POST['end_date'] . "'></td>";
        } else {
            // 같은 차량 번호인 경우 옵션 추가
            $options[] = $row['OPTIONNAME'];
        }
        
      }

      // 마지막 행에 대한 옵션 출력
      if (!empty($options)) {
        echo "<td>" . implode(", ", $options) . "</td>";
      }
      
      echo "</tr>";
      echo "</table>";

      echo "<br>시작 날짜: " . $start_date;
      echo "<br>종료 날짜: " . $end_date . "<br>";

      echo "<button type='submit'>예약하기</button>";
      echo "</form>"; 

    } catch (PDOException $e) {
    echo("에러 내용: ".$e->getMessage());
    }
  ?>


</body>
<script type="text/javascript" src="./js/logout.js"></script>
</html>