<?php

$username = $email = $password = $confirmPassword = '';

$errors = array('username'=>'','email'=>'','password'=>'','confirmPassword'=>'');

if(isset($_POST['create'])){
    require_once('./account-manager.php');
    require_once('./dbconfig.php');

    $conn = DBConfig::getConnection();
    $username = $_POST['username'];
    $email = $_POST['email'];

    $accountValidator = new AccountValidator($conn);

    $errors['username'] = $accountValidator->checkUsername($username);
    $errors['email'] = $accountValidator->checkEmail($email);
    $errors['password'] = $accountValidator->checkPassword($password);
    $errors['confirmPassword'] = $accountValidator->confirmPassword($password,$confirmPassword);


    if($errors['email'] == '' && $errors['username'] == ''){
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if email is taken

        $sql = <<<EOSQL
            SELECT * FROM USERS where email = :email;
        EOSQL;

        $query = $conn->prepare($sql);

        try {
            $query->execute(['email' => $email]);
            $query->setFetchMode(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $user = $query->fetch();
        if($user){
            $errors['email'] = "This e-mail address is already taken!";
        }

        // Check if username is taken

        $sql = <<<EOSQL
            SELECT * FROM USERS where name = :name;
        EOSQL;

        $query = $conn->prepare($sql);

        try {
            $query->execute(['name' => $username]);
            $query->setFetchMode(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $user = $query->fetch();
        if($user){
            $errors['username'] = "This username is already taken!";
        }
    }
    if(!array_filter($errors)){
        echo 'Account ready to be created!';
    }
}


?>


<!DOCTYPE html>
<html>

<head>
<title>Create an account</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
<section class="container text-center bg-light">
        <h4 class="title">Create an account</h4>
        <form class="" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <label for="email">Username:</label></br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username)?>"></br>
            <div class="text-danger"><?php echo $errors['username'];?></div>

            <label for="email">E-mail:</label></br>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email)?>"></br>
            <div class="text-danger"><?php echo $errors['email'];?></div>

            <label for="password">Password:</label></br>
            <input type="password" id="password" name="password"></br>
            <div class="text-danger"><?php echo $errors['password'];?></div>

            <label for="password">Confirm Pasword:</label></br>
            <input type="password" id="confirmPassword" name="confirmPassword"></br>
            <div class="text-danger"><?php echo $errors['confirmPassword'];?></div>

            <div class="center" style="margin:10px;">
                <input type="submit" name="create" value="Create" class="btn btn-primary text-white">
            </div>
        </form>
    </section>
</body>
