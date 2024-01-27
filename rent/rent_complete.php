<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'includes/PHPMailer.php';
    require 'includes/Exception.php';
    require 'includes/SMTP.php';

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
        $cno = $_SESSION['cno'];
        $selectedLicensePlateNo = $_POST['selected_car'];

        // previousrental과 메일에 사용할 정보 가져오기
        $selectQuery = "SELECT r.daterented, r.modelname, r.datedue, (r.datedue-r.daterented)*c.rentrateperday as pay
        FROM rentcar r join carmodel c on r.modelname = c.modelname
        WHERE r.licenseplateno = :licenseplateno";    
        $stmt = $conn->prepare($selectQuery);
        $stmt->bindParam(':licenseplateno', $selectedLicensePlateNo);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // 반납된 차량의 정보를 previousrental에 저장한다
        $insertQuery = "INSERT INTO previousrental (daterented, licenseplateno, datereturned, payment, cno) 
                        VALUES (:daterented, :licenseplateno, :datedue, :pay, :cno)";

        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':daterented', $result['DATERENTED']);
        $insertStmt->bindParam(':licenseplateno', $selectedLicensePlateNo);
        $insertStmt->bindParam(':datedue', $result['DATEDUE']);
        $insertStmt->bindParam(':pay', $result['PAY']);
        $insertStmt->bindParam(':cno', $cno);
        $insertStmt->execute();

        // rentcar에서 반납된 차량의 정보를 null값으로 바꿈
        $updateQuery = "UPDATE rentcar SET daterented = NULL, datedue = NULL, cno = NULL 
        WHERE licenseplateno = :licenseplateno";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':licenseplateno', $selectedLicensePlateNo);
        $updateStmt->execute();

        // 완료 메일 보내기
        $mail = new PHPMailer(true); 
        
        $message = "This is return information.:\n\n";
        $message .= "rent car model: " . $result['MODELNAME'] . "\n"; 
        $message .= "rent car license number: " . $_POST['selected_car'] . "\n"; 
        $message .= "rent start date: " . $result['DATERENTED'] . "\n";
        $message .= "rent end date: " . $result['DATEDUE'] . "\n";
        $message .= "total price: " . $result['PAY'] . "\n";
        
        $mail->IsSMTP();                          
        $mail->SMTPAuth   = true;                 
        $mail->Port       = 465;                    
        $mail->SMTPSecure = "ssl";
        $mail->CharSet    = "EUC-KR";
        $mail->Encoding   = "base64";
        
        $mail->Host = "smtp.naver.com";    
        $mail->Username   = "dyj07132@naver.com";    // 계정
        $mail->Password   = "qkqh123";            // 패스워드
        
        $mail->SetFrom('dyj07132@naver.com'); // 보내는 사람 email 주소와 표시될 이름
        $mail->AddAddress("dyj07132@naver.com"); // 받을 사람 email 주소와 표시될 이름 
        $mail->Subject = "Your rental car has been returned!";        // 메일 제목
        $mail->MsgHTML($message);    // 메일 내용 
        if($mail->Send()){
            echo "완료";
        }
        else{
            echo $mail->ErrorInfo;
        }
        $mail->smtpClose();

        header("Location: ./rent_history.php");
    } catch (PDOException $e) {
        echo("에러 내용: " . $e->getMessage());
    }

?>