<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "raspberry";
$dbname = "1232023";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$accountID = $_SESSION["accountID"];
$schoolID = $_SESSION["schoolID"];

$sql = "SELECT schoolname FROM school WHERE schoolid = $schoolID";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $schoolName = $row["schoolname"];
} else {
    echo "No matching school found for ID: $schoolID";
}
echo "Hello Admin, Your account ID is $accountID , Your school ID is $schoolID , Your school name is $schoolName";
$queryAll = "SELECT * FROM test";
$result = $conn->query($queryAll);
if ($result) {
    $testArray = array();
    while ($row = $result->fetch_assoc()) {
        $testArray[] = $row;
    }
    /* echo '<br>';
    foreach ($testArray as $outerArray) {
        print_r($outerArray);
        echo '<br>';
    } */

} else {
    echo "Error: " . $conn->error;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["query"])) {
        $testName = $_POST["testname"];
        $studentID = $_POST["studentID"];
        $schoolIDQuery = $_POST["schoolIDQuery"];
        $proctorID = $_POST["proctorID"];
        $testDate = $_POST["testdate"];
        $score = $_POST["score"];
        
        $testWhereClause = '';
        if (!empty($testName)){
            $values = explode(",", $testName);
            foreach($values as $value){
                $value = trim($value);
                $testWhereClause .= " OR testname = '$value'";
            }
            $testWhereClause = ltrim($testWhereClause, ' OR ');
        }
        
        $studentWhereClause = '';
        if ($studentID != ""){
            $values = explode(",", $studentID);
            foreach($values as $value){
                $value = trim($value);
                if (strpos($value, '-') !== false) {
                    list($start, $end) = explode('-', $value);
                    if (strpos($value, '-') === 0) {
                        $start = ltrim($value, '-');
                        $studentWhereClause .= " OR studentid <= '$start'";
                    } elseif (strrpos($value, '-') === strlen($value) - 1) {
                        $end = rtrim($value, '-');
                        $studentWhereClause .= " OR studentid >= '$end'";
                    } else {
                        $studentWhereClause .= " OR (studentid >= '$start' AND studentid <= '$end')";
                    }
                } else {
                    $studentWhereClause .= " OR studentid = '$value'";
                }
            }
            $studentWhereClause = ltrim($studentWhereClause, ' OR ');
        }

        $schoolWhereClause = '';
        if ($schoolIDQuery != ""){
            $values = explode(",", $schoolIDQuery);
            foreach($values as $value){
                $value = trim($value);
                if (strpos($value, '-') !== false) {
                    list($start, $end) = explode('-', $value);
                    if (strpos($value, '-') === 0) {
                        $start = ltrim($value, '-');
                        $schoolWhereClause .= " OR schoolid <= '$start'";
                    } elseif (strrpos($value, '-') === strlen($value) - 1) {
                        $end = rtrim($value, '-');
                        $schoolWhereClause .= " OR schoolid >= '$end'";
                    } else {
                        $schoolWhereClause .= " OR (schoolid >= '$start' AND schoolid <= '$end')";
                    }
                } else {
                    $schoolWhereClause .= " OR schoolid = '$value'";
                }
            }
            $schoolWhereClause = ltrim($schoolWhereClause, ' OR ');
        }
        
        $proctorWhereClause = '';
        if ($proctorID != ""){
            $values = explode(",", $proctorID);
            foreach($values as $value){
                $value = trim($value);
                if (strpos($value, '-') !== false) {
                    list($start, $end) = explode('-', $value);
                    if (strpos($value, '-') === 0) {
                        $start = ltrim($value, '-');
                        $proctorWhereClause .= " OR proctorid <= '$start'";
                    } elseif (strrpos($value, '-') === strlen($value) - 1) {
                        $end = rtrim($value, '-');
                        $proctorWhereClause .= " OR proctorid >= '$end'";
                    } else {
                        $proctorWhereClause .= " OR (proctorid >= '$start' AND proctorid <= '$end')";
                    }
                } else {
                    $proctorWhereClause .= " OR proctorid = '$value'";
                }
            }
            $proctorWhereClause = ltrim($proctorWhereClause, ' OR ');
        }

        $dateWhereClause = '';
        if ($testDate != ""){
            $values = explode(",", $testDate);
            foreach($values as $value){
                $value = trim($value);
                if (strpos($value, '-') !== false) {
                    list($start, $end) = explode('-', $value);
                    if (strpos($value, '-') === 0) {
                        $start = ltrim($value, '-');
                        $dateWhereClause .= " OR testdate <= '$start'";
                    } elseif (strrpos($value, '-') === strlen($value) - 1) {
                        $end = rtrim($value, '-');
                        $dateWhereClause .= " OR testdate >= '$end'";
                    } else {
                        $dateWhereClause .= " OR (testdate >= '$start' AND testdate <= '$end')";
                    }
                } else {
                    $dateWhereClause .= " OR testdate = '$value'";
                }
            }
            $dateWhereClause = ltrim($dateWhereClause, ' OR ');
        }

        $scoreWhereClause = '';
        if ($score != ""){
            $values = explode(",", $score);
            foreach($values as $value){
                $value = trim($value);
                if (strpos($value, '-') !== false) {
                    list($start, $end) = explode('-', $value);
                    if (strpos($value, '-') === 0) {
                        $start = ltrim($value, '-');
                        $scoreWhereClause .= " OR score <= '$start'";
                    } elseif (strrpos($value, '-') === strlen($value) - 1) {
                        $end = rtrim($value, '-');
                        $scoreWhereClause .= " OR score >= '$end'";
                    } else {
                        $scoreWhereClause .= " OR (score >= '$start' AND score <= '$end')";
                    }
                } else {
                    $scoreWhereClause .= " OR score = '$value'";
                }
            }
            $scoreWhereClause = ltrim($scoreWhereClause, ' OR ');
        }
        

        $whereClause = '';
        if (!empty($testWhereClause)){
            $whereClause .= "($testWhereClause) AND ";
        }
        if (!empty($studentWhereClause)) {
            $whereClause .= "($studentWhereClause) AND ";
        }
        if (!empty($schoolWhereClause)) {
            $whereClause .= "($schoolWhereClause) AND ";
        }
        if (!empty($proctorWhereClause)) {
            $whereClause .= "($proctorWhereClause) AND ";
        }
        if (!empty($dateWhereClause)) {
            $whereClause .= "($dateWhereClause) AND ";
        } 
        if (!empty($scoreWhereClause)) {
            $whereClause .= "($scoreWhereClause) AND ";
        }
        $whereClause = rtrim($whereClause, ' AND ');
        
        if (!empty($whereClause)){
            $sql = "SELECT * FROM test WHERE $whereClause ORDER BY testname, score DESC, studentid";
        }
        else {
            $sql = "SELECT * FROM test ORDER BY testname, score DESC, studentid";
        }
        echo '<br>';
        echo "Query is: $sql";
        $result = $conn->query($sql);
        if ($result) {
            $dataArray = array();
            while ($row = $result->fetch_assoc()) {
                $dataArray[] = $row;
            }
            echo '<br>';
            foreach ($dataArray as $outerArray) {
                print_r($outerArray);
                echo '<br>';
            }
            echo "Entry Count Is: " . count($dataArray);
            echo '<br>';
            
            $testNames = array();
            $students = array();
            $schools = array();
            $proctors = array();
            for ($i = 0; $i < count($testArray); $i++) {
                if (!in_array($testArray[$i]['studentid'], $students)) {
                    array_push($students, $testArray[$i]['studentid']);
                }
                if (!in_array($testArray[$i]['testname'], $testNames)) {
                    array_push($testNames, $testArray[$i]['testname']);
                }
                if (!in_array($testArray[$i]['schoolid'], $schools)) {
                    array_push($schools, $testArray[$i]['schoolid']);
                }
                if ((!in_array(array($testArray[$i]['proctorid'], $testArray[$i]['schoolid']), $proctors))) {
                    array_push($proctors, array($testArray[$i]['proctorid'], $testArray[$i]['schoolid']));
                }
            }
            $studentsQueried = array();
            $schoolsQueried = array();
            $proctorsQueried = array();
            $testNamesQueried = array();
            for ($i = 0; $i < count($dataArray); $i++){
                if (!in_array($dataArray[$i]['studentid'], $studentsQueried)){
                    array_push($studentsQueried, $dataArray[$i]['studentid']);
                }
                if (!in_array($dataArray[$i]['schoolid'], $schoolsQueried)) {
                    array_push($schoolsQueried, $dataArray[$i]['schoolid']);
                }
                if ((!in_array(array($dataArray[$i]['proctorid'], $dataArray[$i]['schoolid']), $proctorsQueried))) {
                    array_push($proctorsQueried, array($dataArray[$i]['proctorid'], $dataArray[$i]['schoolid']));
                }
                if (!in_array($dataArray[$i]['testname'], $testNamesQueried)) {
                    array_push($testNamesQueried, $dataArray[$i]['testname']);
                }
            }

            $schoolAverages = array();
            for ($i = 0; $i<count($schools); $i++){
                for ($i2 = 0; $i2<count($testNames); $i2++){
                    $currentTotal = 0;
                    $count = 0;
                    for ($i3 = 0; $i3<count($testArray); $i3++){
                        if ($testArray[$i3]['testname'] == $testNames[$i2] and $testArray[$i3]['schoolid'] == $schools[$i]) {
                            $currentTotal = $currentTotal + $testArray[$i3]['score'];
                            $count = $count + 1;
                        }
                    }
                    if ($count > 0){
                        $entry = array($schools[$i], $testNames[$i2], $currentTotal / $count);
                        array_push($schoolAverages, $entry);
                    }
                }
            }
            
            for ($i = 0; $i<count($schoolAverages); $i++){
                $greaterCount = 0;
                $lesserCount = 0;
                $sameTestNameCount = 0;
                for ($i2 = 0; $i2<count($schoolAverages); $i2++){
                    if ($schoolAverages[$i2][2] > $schoolAverages[$i][2] and $schoolAverages[$i2][1] == $schoolAverages[$i][1]){
                        $greaterCount = $greaterCount + 1;
                    }
                    elseif ($schoolAverages[$i2][2] < $schoolAverages[$i][2] and $schoolAverages[$i2][1] == $schoolAverages[$i][1]){
                        $lesserCount = $lesserCount + 1;
                    }
                    if ($schoolAverages[$i2][1] == $schoolAverages[$i][1]){
                        $sameTestNameCount = $sameTestNameCount + 1;
                    }
                }
                $percentile = (
                    ($sameTestNameCount - $greaterCount)/$sameTestNameCount
                    - $lesserCount/$sameTestNameCount 
                    )/2 + $lesserCount/$sameTestNameCount;
                $schoolAverages[$i][] = $percentile;
            }
            function comparePercentileSchool($a, $b) {
                if ($a[1] == $b[1]) {
                    if ($a[3] == $b[3]) {
                        return 0;
                    }
                    return ($a[3] < $b[3]) ? 1 : -1;
                }
                return ($a[1] < $b[1]) ? -1 : 1;
            }
            usort($schoolAverages, 'comparePercentileSchool');
            echo '<br>';
            print("School Percentiles Are:");
            echo '<br>';
            foreach ($schoolAverages as $outerArray) {
                $keys = array("School ID", "Test Name", "Average Score", "Percentile");
                $outerArray = array_combine($keys, $outerArray);
                if (in_array($outerArray['School ID'], $schoolsQueried) and in_array($outerArray['Test Name'], $testNamesQueried)){
                    print_r($outerArray);
                    echo '<br>';
                }
            } 

            $proctorAverages = array();
            for ($i = 0; $i<count($proctors); $i++){
                $currentTotal = 0;
                $count = 0;
                for ($i2 = 0; $i2<count($testArray); $i2++){
                    if ($testArray[$i2]['proctorid'] == $proctors[$i][0] and $testArray[$i2]['schoolid'] == $proctors[$i][1]) {
                        $currentTotal = $currentTotal + $testArray[$i2]['score'];
                        $count = $count + 1;
                    }
                }
                $entry = array($proctors[$i][0], $proctors[$i][1], $currentTotal / $count);
                array_push($proctorAverages, $entry);
            }
            for ($i = 0; $i<count($proctorAverages); $i++){
                $greaterCount = 0;
                $lesserCount = 0;
                $proctorCount = count($proctors);
                for ($i2 = 0; $i2<count($proctorAverages); $i2++){
                    if ($proctorAverages[$i2][2] > $proctorAverages[$i][2]){
                        $greaterCount = $greaterCount + 1;
                    }
                    elseif ($proctorAverages[$i2][2] < $proctorAverages[$i][2]){
                        $lesserCount = $lesserCount + 1;
                    }
                }
                $percentile = (($proctorCount - $greaterCount)/$proctorCount - $lesserCount/$proctorCount)/2 + $lesserCount/$proctorCount;
                $proctorAverages[$i][] = $percentile;
            }
            function comparePercentileProctor($a, $b) {
                if ($a[1] == $b[1]) {
                    if ($a[2] == $b[2]) {
                        return 0;
                    }
                    return ($a[2] < $b[2]) ? 1 : -1;
                }
                return ($a[1] < $b[1]) ? -1 : 1;
            }
            usort($proctorAverages, 'comparePercentileProctor');
            echo '<br>';
            print("Proctor Percentiles Are:");
            echo '<br>';
            foreach ($proctorAverages as $outerArray) {
                $keys = array("Proctor ID", "School ID", "Average Score", "Percentile");
                $outerArray = array_combine($keys, $outerArray);
                if (in_array($outerArray['School ID'], $schoolsQueried) and in_array(array($outerArray['Proctor ID'], $outerArray['School ID']), $proctorsQueried)){
                    print_r($outerArray);
                    echo '<br>';
                }
                
            } 

            $studentAverages = array();
            for ($i = 0; $i<count($students); $i++){
                for ($i2 = 0; $i2<count($testNames); $i2++){
                    $currentTotal = 0;
                    $count = 0;
                    for ($i3 = 0; $i3<count($testArray); $i3++){
                        if ($testArray[$i3]['testname'] == $testNames[$i2] and $testArray[$i3]['studentid'] == $students[$i]) {
                            $currentTotal = $currentTotal + $testArray[$i3]['score'];
                            $count = $count + 1;
                        }
                    }
                    if ($count > 0){
                        $entry = array($students[$i], $testNames[$i2], $currentTotal / $count);
                        array_push($studentAverages, $entry);
                    }
                }
            }
            
            for ($i = 0; $i<count($studentAverages); $i++){
                $greaterCount = 0;
                $lesserCount = 0;
                $sameTestNameCount = 0;
                for ($i2 = 0; $i2<count($studentAverages); $i2++){
                    if ($studentAverages[$i2][2] > $studentAverages[$i][2] and $studentAverages[$i2][1] == $studentAverages[$i][1]){
                        $greaterCount = $greaterCount + 1;
                    }
                    elseif ($studentAverages[$i2][2] < $studentAverages[$i][2] and $studentAverages[$i2][1] == $studentAverages[$i][1]){
                        $lesserCount = $lesserCount + 1;
                    }
                    if ($studentAverages[$i2][1] == $studentAverages[$i][1]){
                        $sameTestNameCount = $sameTestNameCount + 1;
                    }
                }
                $percentile = (
                    ($sameTestNameCount - $greaterCount)/$sameTestNameCount
                    - $lesserCount/$sameTestNameCount 
                    )/2 + $lesserCount/$sameTestNameCount;
                $studentAverages[$i][] = $percentile;
            }
            function comparePercentile($a, $b) {
                if ($a[1] == $b[1]) {
                    if ($a[3] == $b[3]) {
                        return 0;
                    }
                    return ($a[3] < $b[3]) ? 1 : -1;
                }
                return ($a[1] < $b[1]) ? -1 : 1;
            }
            usort($studentAverages, 'comparePercentile');
            echo '<br>';
            print("Student Percentiles Are:");
            echo '<br>';
            foreach ($studentAverages as $outerArray) {
                $keys = array("Student ID", "Test Name", "Average Score", "Percentile");
                $sql = "SELECT dateofbirth FROM student WHERE student.studentid = $outerArray[0]";
                $result = $conn->query($sql);
                if ($result) {
                    $dataArray = array();
                    while ($row = $result->fetch_assoc()) {
                        $dataArray[] = $row;
                    }
                    $dateOfBirthReceived = $dataArray[0]['dateofbirth'];
                    array_splice($outerArray, 1, 0, $dateOfBirthReceived);
                    $keys = array("Student ID", "Date Of Birth", "Test Name", "Average Score", "Percentile");
                } else {
                    echo "Error: " . $conn->error;
                }
                $outerArray = array_combine($keys, $outerArray);
                if (in_array($outerArray['Student ID'], $studentsQueried) and in_array($outerArray['Test Name'], $testNamesQueried)){
                    print_r($outerArray);
                    echo '<br>';
                }
            } 
    
        } else {
            echo "Error: " . $conn->error;
        }
        

    }
    elseif (isset($_POST["addStudent"])) {
        $studentID = $_POST["studentIDStudent"];
        $dateOfBirth = $_POST["dateofbirth"];
        $passwordAdd = $_POST["passwordStudent"];
        $hashedPassword = password_hash($passwordAdd, PASSWORD_BCRYPT);
        $sql = "INSERT INTO student (studentid, schoolid, dateofbirth, password) VALUES ('$studentID', '$schoolID', '$dateOfBirth', '$hashedPassword')";
        if ($conn->query($sql) === TRUE) {
            echo "New Student record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        } 
    }
    elseif (isset($_POST["addTest"])) {
        $testName = $_POST["testnameTest"];
        $studentID = $_POST["studentIDTest"];
        $proctorID = $_POST["proctorIDTest"];
        $testDate = $_POST["testdateTest"];
        $score = $_POST["scoreTest"];
        $sql = "INSERT INTO test (testname, studentid, schoolid, proctorid, testdate, score) VALUES ('$testName', '$studentID', '$schoolID', '$proctorID', '$testDate', '$score')";
        if ($conn->query($sql) === TRUE) {
            echo '<br>';
            echo "New Test record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    elseif (isset($_POST["addAdmin"])) {
        $adminIDAdd = $_POST["adminID"];
        $passwordAdd = $_POST["passwordAdmin"];
        $hashedPassword = password_hash($passwordAdd, PASSWORD_BCRYPT);
        $sql = "INSERT INTO admin (adminid, schoolid, password) VALUES ('$adminIDAdd', '$schoolID', '$hashedPassword')";
        if ($conn->query($sql) === TRUE) {
            echo '<br>';
            echo "New Admin record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

}
$conn->close();

?>

<!DOCTYPE html>
<html>
<body>
    <h1>Admin Page</h1>
    <form action="adminpage.php" method="post">
        <h2>Query Database</h2>
        <label for="testname">Test Name: </label>
        <input type="text" id = "testname" name="testname">
        <label for="studentID">Student ID:</label>
        <input type="text" id= "studentID" name="studentID">
        <label for="schoolIDQuery">School ID:</label>
        <input type="text" id= "schoolIDQuery" name="schoolIDQuery">
        <label for="proctorID">Proctor ID:</label>
        <input type="text" id = "proctorID" name="proctorID">
        <label for="testdate">Test Date:</label>
        <input type="text" id = "testdate" name="testdate">
        <label for="score">Score:</label>
        <input type="text" id = "score" name="score">
        <button type = "submit" name="query">Submit Query</button>

        <h2>Add Student</h2>
        <label for="studentIDStudent">Student ID:</label>
        <input type="text" id= "studentIDStudent" name="studentIDStudent">
        <label for="dateofbirth">Date Of Birth:</label>
        <input type="text" id= "dateofbirth" name="dateofbirth">
        <label for="passwordStudent">Password:</label>
        <input type="text" id= "passwordStudent" name="passwordStudent">
        <button type= "submit" name = "addStudent">Submit Student</button>
        <h2>Add Test</h2>
        <label for="testnameTest">Test Name:</label>
        <input type="text" id= "testnameTest" name="testnameTest">
        <label for="studentID">Student ID:</label>
        <input type="text" id= "studentIDTest" name="studentIDTest">
        <label for="proctorID">Proctor ID:</label>
        <input type="text" id= "proctorIDTest" name="proctorIDTest">
        <label for="testdate">Test Date:</label>
        <input type="text" id= "testdateTest" name="testdateTest">
        <label for="score">Score:</label>
        <input type="text" id= "scoreTest" name="scoreTest">
        <button type="submit" name="addTest">Submit Test</button>
        <h2>Add Admin</h2>
        <label for="adminID">Admin ID:</label>
        <input type="text" id= "adminID" name="adminID">
        <label for="passwordAdmin">Password:</label>
        <input type="text" id= "passwordAdmin" name="passwordAdmin">
        <button type="submit" name="addAdmin">Submit Admin</button>
    </form>
</body>
</html>
