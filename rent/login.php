<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>로그인</title>
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/login.css">
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
        <!-- 로그인이 되었을 때 나타나는 로그아웃 페이지 -->
        <div class="header__logout" id="header__logout">
          <form action="#" method="post">
            <button type="button" name="logout_submit" id="logout_submit">
              로그아웃
            </button>
          </form>
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
      </div>
    </header>

    <div class="loginbox">
    <h2>로그인</h2>
    <form action="#" method="post">
      <fieldset>
        <label for="loginid">아이디(고객번호)</label>
        <input type="cno" name="loginid" id="loginid" placeholder="아이디(고객번호)을 입력해 주세요" required>
        <label for="loginpw">비밀번호</label>
        <input type="password" name="loginpw" id="loginpw" placeholder="비밀번호를 입력해 주세요" required>
        <button type="submit" name = "login_submit">로그인</button>
      </fieldset>
    </form>
  </div>
  <?php
    session_start();

    // 데이터베이스 연결 및 설정
    $tns = "(DESCRIPTION=
        (ADDRESS_LIST= (ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
        (CONNECT_DATA= (SERVICE_NAME=XE))
    )";
    $dsn = "oci:dbname=".$tns.";charset=utf8";
    $username = 'tp3';
    $password = '1234';

    // 로그인 폼에서 제출된 데이터 가져오기
    if (isset($_POST['login_submit'])) {
        $loginid = $_POST['loginid'];
        $loginpw = $_POST['loginpw'];

        try {
            $conn = new PDO($dsn, $username, $password);
            $query = "SELECT * FROM customer WHERE cno = :cno AND passwd = :passwd";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':cno', $loginid);
            $stmt->bindValue(':passwd', $loginpw);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // 로그인 성공
                $_SESSION['cno'] = $result['CNO'];
                $_SESSION['name'] = $result['NAME'];
                // 필요한 경우 세션에 추가할 데이터를 설정할 수 있다.

                echo "<script>alert('환영합니다!');
                location.href='./index.php'</script>";

            } else {
                // 로그인 실패
                echo "<script>alert('존재하지 않는 아이디 또는 비밀번호입니다.')</script>";
            }
        } catch (PDOException $e) {
            echo "에러 내용: " . $e->getMessage();
        }
    }
?>
</body>
</html>