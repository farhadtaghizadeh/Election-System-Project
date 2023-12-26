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
echo "Hello Student, Your account ID is $accountID , Your school ID is $schoolID , Your school name is $schoolName";
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
            $sql = "SELECT * FROM test WHERE $whereClause AND studentid = $accountID ORDER BY testname, score DESC";
        }
        else {
            $sql = "SELECT * FROM test WHERE studentid = $accountID ORDER BY testname, score DESC";
        }
        echo '<br>';
        echo "Query Output: $sql";
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

            $testPercentiles = array();
            for ($i = 0; $i<count($dataArray); $i++){
                $greaterCount = 0;
                $lesserCount = 0;
                $sameTestNameCount = 0;
                for ($i2 = 0; $i2<count($testArray); $i2++){
                    if ($testArray[$i2]['score'] > $dataArray[$i]['score'] and $testArray[$i2]['testname'] == $dataArray[$i]['testname']){
                        $greaterCount = $greaterCount + 1;
                    }
                    elseif ($testArray[$i2]['score'] < $dataArray[$i]['score'] and $testArray[$i2]['testname'] == $dataArray[$i]['testname']){
                        $lesserCount = $lesserCount + 1;
                    }
                    if ($testArray[$i2]['testname'] == $dataArray[$i]['testname']){
                        $sameTestNameCount = $sameTestNameCount + 1;
                    }
                }
                $percentile = (
                    ($sameTestNameCount - $greaterCount)/$sameTestNameCount
                    - $lesserCount/$sameTestNameCount 
                    )/2 + $lesserCount/$sameTestNameCount;
                $testPercentiles[] = array($dataArray[$i]['testname'], $dataArray[$i]['schoolid'], $dataArray[$i]['testdate'], $dataArray[$i]['score'], $percentile);
            }
            function comparePercentile($a, $b) {
                if ($a[0] == $b[0]) {
                    if ($a[4] == $b[4]) {
                        return 0;
                    }
                    return ($a[4] < $b[4]) ? 1 : -1;
                }
                return ($a[0] < $b[0]) ? -1 : 1;
            }
            usort($testPercentiles, 'comparePercentile');
            echo '<br>';
            print("Test Percentiles Are:");
            echo '<br>';
            foreach ($testPercentiles as $outerArray) {
                $keys = array("Test Name", "School ID", "Test Date", "Score", "Percentile");
                $outerArray = array_combine($keys, $outerArray);
                print_r($outerArray);
                echo '<br>';
            } 

        } else {
            echo "Error: " . $conn->error;
        }

    }
    
}
$conn->close();

?>

<!DOCTYPE html>
<html>
<body>
    <h1>Student Page</h1>
    <form action="studentpage.php" method="post">
        <h2>Query Database</h2>
        <label for="testname">Test Name: </label>
        <input type="text" id = "testname" name="testname">
        <label for="schoolIDQuery">School ID:</label>
        <input type="text" id= "schoolIDQuery" name="schoolIDQuery">
        <label for="proctorID">Proctor ID:</label>
        <input type="text" id = "proctorID" name="proctorID">
        <label for="testdate">Test Date:</label>
        <input type="text" id = "testdate" name="testdate">
        <label for="score">Score:</label>
        <input type="text" id = "score" name="score">
        <button type = "submit" name="query">Submit Query</button>
    </form>
</body>
</html>