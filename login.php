<?php 
session_start(); 
include "config.php";

header('Content-Type: application/json');

$response = array();

if (isset($_POST['email']) && isset($_POST['password'])) {

    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    if (empty($email)) {
        $response['status'] = 'error';
        $response['message'] = 'Email is required';
        echo json_encode($response);
        exit();
    } else if (empty($password)) {
        $response['status'] = 'error';
        $response['message'] = 'Password is required';
        echo json_encode($response);
        exit();
    } else {
        $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if ($row['email'] === $email && $row['password'] === $password) {
                $_SESSION['email'] = $row['email'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['id'] = $row['id'];

                $response['status'] = 'success';
                $response['id'] = $row['id'];
                $response['name'] = $row['name'];
                $response['email'] = $row['email'];
                $response['redirect'] = 'indexIdea.php';

                echo json_encode($response);
                exit();
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Incorrect email or password';
                echo json_encode($response);
                exit();
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Incorrect email or password';
            echo json_encode($response);
            exit();
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request';
    echo json_encode($response);
    exit();
}
?>