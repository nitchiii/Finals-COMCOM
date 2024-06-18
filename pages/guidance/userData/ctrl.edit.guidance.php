<?php
require '../../../includes/conn.php';
session_start();

$guidance_id = $_SESSION['guidance_id'];

if (isset($_POST['saveImg'])) {

    if (!empty($_FILES['image']['tmp_name'])) {
        $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
        $updated_by = $_SESSION['name'] . " <br> (" . $_SESSION['role'] . ")";

        $updateImg = mysqli_query($db, "UPDATE tbl_guidances SET img = '$image', updated_by = '$updated_by', last_updated = CURRENT_TIMESTAMP WHERE guidance_id = '$guidance_id'") or die(mysqli_error($db));
        $_SESSION['successImg'] = true;
        if ($_SESSION['role'] == "Super Administrator") {
            header("location: ../edit.guidance.php?guidance_id=" . $guidance_id);
        } else {
            header("location: ../edit.guidance.php");
        }
    } else {
        $_SESSION['emptyImg'] = true;
        if ($_SESSION['role'] == "Super Administrator") {
            header("location: ../edit.guidance.php?guidance_id=" . $guidance_id);
        } else {
            header("location: ../edit.guidance.php");
        }
    }
}

if (isset($_POST['save'])) {

    $lname = mysqli_real_escape_string($db, $_POST['lname']);
    $fname = mysqli_real_escape_string($db, $_POST['fname']);
    $mname = mysqli_real_escape_string($db, $_POST['mname']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $username = mysqli_real_escape_string($db, $_POST['username']);;
    $updated_by = $_SESSION['name'] . " <br> (" . $_SESSION['role'] . ")";

    $getAllUsername = mysqli_query($db, "SELECT username FROM tbl_admissions WHERE username = '$username' UNION ALL SELECT username FROM tbl_presidents WHERE username = '$username' UNION ALL SELECT username FROM tbl_faculties WHERE username = '$username' UNION ALL SELECT username FROM tbl_admins WHERE username = '$username' UNION ALL SELECT username FROM tbl_students WHERE username = '$username' UNION ALL SELECT username FROM tbl_super_admins WHERE username = '$username' UNION ALL SELECT username FROM tbl_accounting WHERE username = '$username' UNION ALL SELECT username FROM tbl_faculties_staff WHERE username = '$username'") or die(mysqli_error($db));
    $check = mysqli_num_rows($getAllUsername);

    if ($check == 0) {
        $q = $db->query("SELECT * FROM tbl_guidances WHERE username = '$username'") or die($db->error);
        $check2 = mysqli_num_rows($q);
        while ($row = mysqli_fetch_array($q)) {
            $getID = $row['guidance_id'];
        }
        if ($getID == $guidance_id || $check2 < 1) {
            $updateInfo = mysqli_query($db, " UPDATE tbl_guidances SET guidance_lastname='$lname',guidance_firstname='$fname', guidance_middlename='$mname', email='$email', username='$username', updated_by = '$updated_by', last_updated = CURRENT_TIMESTAMP WHERE guidance_id = '$guidance_id'") or die(mysqli_error($db));
            $_SESSION['successUpdate'] = true;
            if ($_SESSION['role'] == "Super Administrator") {
                header("location: ../edit.guidance.php?guidance_id=" . $guidance_id);
            } else {
                header("location: ../edit.guidance.php");
            }
        } else {
            $_SESSION['usernameExist'] = true;
            if ($_SESSION['role'] == "Super Administrator") {
                header("location: ../edit.guidance.php?guidance_id=" . $guidance_id);
            } else {
                header("location: ../edit.guidance.php");
            }
        }
    } else {
        $_SESSION['usernameExist'] = true;
        if ($_SESSION['role'] == "Super Administrator") {
            header("location: ../edit.guidance.php?guidance_id=" . $guidance_id);
        } else {
            header("location: ../edit.guidance.php");
        }
    }
}

if (isset($_POST['savePass'])) {

    if ($_SESSION['role'] == "Guidance") {

        $oldpassword = mysqli_real_escape_string($db, $_POST['oldPass']);

        $checkPass = mysqli_query($db, "SELECT * FROM tbl_guidances WHERE guidance_id = '$guidance_id'");
        while ($row = mysqli_fetch_array($checkPass)) {
            $checkHashPass = password_verify($oldpassword, $row['password']);
            if ($checkHashPass == false) {
                $_SESSION['oldNotMatch'] = true;
                header("location: ../edit.guidance.php");
            } elseif ($checkHashPass == true) {

                $password = mysqli_real_escape_string($db, $_POST['password']);
                $confirmPass = mysqli_real_escape_string($db, $_POST['confirmPass']);
                $updated_by = $_SESSION['name'] . " <br> (" . $_SESSION['role'] . ")";

                if ($password == $confirmPass) {
                    $hashedPwd = password_hash($confirmPass, PASSWORD_DEFAULT);

                    $updatePass = mysqli_query($db, " UPDATE tbl_guidances SET password='$hashedPwd', updated_by = '$updated_by', last_updated = CURRENT_TIMESTAMP WHERE guidance_id = '$guidance_id'") or die(mysqli_error($db));
                    $_SESSION['successPass'] = true;
                    header("location: ../edit.guidance.php");
                } else {
                    $_SESSION['newNotMatch'] = true;
                    header("location: ../edit.guidance.php");
                }
            }
        }
    } else {
        $password = mysqli_real_escape_string($db, $_POST['password']);
        $confirmPass = mysqli_real_escape_string($db, $_POST['confirmPass']);
        $updated_by = $_SESSION['name'] . " <br> (" . $_SESSION['role'] . ")";

        if ($password == $confirmPass) {
            $hashedPwd = password_hash($confirmPass, PASSWORD_DEFAULT);

            $updatePass = mysqli_query($db, " UPDATE tbl_guidances SET password='$hashedPwd', updated_by = '$updated_by', last_updated = CURRENT_TIMESTAMP WHERE guidance_id = '$guidance_id'") or die(mysqli_error($db));
            $_SESSION['successPass'] = true;
            header("location: ../edit.guidance.php?guidance_id=" . $guidance_id);
        } else {
            $_SESSION['newNotMatch'] = true;
            header("location: ../edit.guidance.php?guidance_id=" . $guidance_id);
        }
    }
}