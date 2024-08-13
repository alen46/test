<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Emp{

    public function addemp() {
        //header('Content-Type: application/json');
        require("conn.php");
    
        try {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $dob = $_POST['dob'];
            $gender = $_POST['gendr'];
            $position = $_POST['position'];
            $jdate = $_POST['jdate'];
            $dept = $_POST['dept'];
            $ddate = $_POST['ddate'];


            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

                $uploadDir = 'uploads/';
                $uploadFile = $uploadDir . basename($_FILES['image']['name']);
                $check = getimagesize($_FILES['image']['tmp_name']);
                if ($check !== false) {

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {

                        if($name && $email && $phone && $jdate && $position && $dob && $gender && $check){

                                $idqry = "select COUNT(*) AS count from employees";
                                $stmt = $conn->query($idqry);
                                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                                $count = $res['count'];
                                if($count == 0){
                                    $sql = $conn->prepare("insert into employees(emp_no, name, email, phone, birth_date, gender, position, hire_date,image) VALUES (1000,:name,:email,:phone,:dob,:gender,:position,:jdate,:image)");
                                }else{
                                    $sql = $conn->prepare("insert into employees(name, email, phone, birth_date, gender, position, hire_date,image) VALUES (:name,:email,:phone,:dob,:gender,:position,:jdate,:image)");
                                }
                                $sql->bindParam(':name',$name);
                                $sql->bindParam(':email',$email);
                                $sql->bindParam(':phone',$phone);
                                $sql->bindParam(':dob',$dob);
                                $sql->bindParam(':gender',$gender);
                                $sql->bindParam(':position',$position);
                                $sql->bindParam(':jdate',$jdate);
                                $sql->bindParam(':image',$uploadFile);
                                $sql->execute();

                                $lastInsertId = $conn->lastInsertId();
                                $sql2 = $conn->prepare('INSERT INTO `dept_emp`(`emp_no`, `dept_no`, `from_date`) VALUES (:empno, :deptno, :ddate)');
                                $sql2->bindParam('empno',$lastInsertId);
                                $sql2->bindParam('deptno',$dept);
                                $sql2->bindParam('ddate',$ddate);
                                $sql2->execute();
                                
                                echo json_encode(array("response" =>"$name inserted successfully"));
                        }else{
                            echo json_encode(array("response" =>"Insert All Data"));
                        }
                    }
                }
            } 
            } 
            else{
                echo json_encode(array("response" =>"No Data Recieved"));
            }
            $conn = null;
    } catch (PDOException $e) {
        echo json_encode(array("response" => "Database error: " . $e->getMessage()));
    } catch (Exception $e) {
        echo json_encode(array("response" => "General error: " . $e->getMessage()));
    } finally {
        $conn = null;
    }
    }
    
    public function fetchoptions(){
        require("conn.php");
        $sql = "SELECT dept_no,dept_name FROM departments";
        $stmt = $conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $options[] = $row;
        }
        $conn = null;
        echo json_encode($options);
    }
    public function update(){
        header('Content-Type: application/json');
        require("conn.php");
        
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dob = $_POST['dob'];
        $gender = $_POST['gendr'];
        $position = $_POST['position'];
        $jdate = $_POST['jdate'];
        $dept = $_POST['dept'];
        $ddate = $_POST['ddate'];
        $empno = $_POST['empno'];


        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($_FILES['image']['name']);
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check !== false) {

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {

                    if($name && $email && $phone && $jdate && $position && $dob && $gender && $check){

                            $sql = $conn->prepare("UPDATE employees SET name=:name ,email=:email,phone=:phone,birth_date=:dob,gender=:gender,position=:position,hire_date=:jdate,image=:image WHERE emp_no=:empno");
                        
                            $sql->bindParam(':name',$name);
                            $sql->bindParam(':email',$email);
                            $sql->bindParam(':phone',$phone);
                            $sql->bindParam(':dob',$dob);
                            $sql->bindParam(':gender',$gender);
                            $sql->bindParam(':position',$position);
                            $sql->bindParam(':jdate',$jdate);
                            $sql->bindParam(':image',$uploadFile);
                            $sql->bindParam(':empno',$empno);
                            $sql->execute();

                            $lastInsertId = $conn->lastInsertId();
                            $sql2 = $conn->prepare('UPDATE dept_emp SET emp_no =:empno ,dept_no=:deptno,from_date=:ddate WHERE emp_no = :empno');
                            $sql2->bindParam('empno',$lastInsertId);
                            $sql2->bindParam('deptno',$dept);
                            $sql2->bindParam('ddate',$ddate);
                            $sql2->bindParam(':empno',$empno);
                            $sql2->execute();
                            
                            echo json_encode(array("response" =>"$name updated successfully"));
                    }else{
                        echo json_encode(array("response" =>"Insert All Data"));
                    }
                }
            }
        else{
            echo json_encode(array("response" =>"No Data Recieved"));
        }
        }
        $conn = null;

    }

    public function datatable(){
        require('conn.php');
        try {
            $sql = "SELECT emp_no, name, email, hire_date FROM employees";
        
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            if (!empty($data)) {
                header('Content-Type: application/json');
                echo json_encode($data);
            } else {
                echo json_encode(array("message" => "No data found"));
            }
        
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn = null;
    }

    public function getdetails(){
        header('Content-Type: application/json');
        require("conn.php");
        $id = $_GET['id'];
        try{
            $sql = "call get_details(:id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $details = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($details);
        }catch(PDOException $e){{
            echo json_encode(array('response'=> $e->getMessage()));
        }
        $conn = null;
    }

}
}

try{if ($_SERVER['REQUEST_METHOD'] === 'POST'){
if(isset($_POST['function'])){
    $emp = new Emp();
    call_user_func(array($emp, $_POST['function']));
}}
else if($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(isset($_GET['function'])){
        $emp = new Emp();
        call_user_func(array($emp,$_GET['function']));
    }
}    
}catch(Exception $e){
    echo json_encode(array(''=> $e->getMessage()));
}