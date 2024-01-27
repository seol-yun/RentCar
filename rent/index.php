<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>CNU렌트카</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/header.css" />
    <link rel="stylesheet" href="./css/form.css" />
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
      <div class="center-form">
        <form method="post" class = "reserve_form" action="reserve.php">
          <label for="start_date">시작 날짜:</label>
          <input type="date" id="start_date" name="start_date" required><br>

          <label for="end_date">종료 날짜:</label>
          <input type="date" id="end_date" name="end_date" required><br>

          <label for="suv">SUV:</label>
          <input type="checkbox" id="suv" name="car_type[]" value="suv">

          <label for="전기차">전기차:</label>
          <input type="checkbox" id="전기차" name="car_type[]" value="전기차">

          <label for="승합">승합:</label>
          <input type="checkbox" id="승합" name="car_type[]" value="승합"><br>

          <label for="대형">대형:</label>
          <input type="checkbox" id="대형" name="car_type[]" value="대형">
          
          <label for="소형">소형:</label>
          <input type="checkbox" id="소형" name="car_type[]" value="소형">

          <label for="전체">전체:</label>
          <input type="checkbox" id="전체" name="car_type[]" value="전체" onclick="toggleAllCheckboxes(this)"><br>
    

          <input type="submit" value="차량 조회">
        </form>
      </div>

  </body>

  <!-- 체크박스 전부 선택하기 -->
  <script>
    function toggleAllCheckboxes(checkbox) {
        var checkboxes = document.getElementsByName('car_type[]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = checkbox.checked;
        }
    }
</script>
<script type="text/javascript" src="./js/logout.js"></script>
</html>
