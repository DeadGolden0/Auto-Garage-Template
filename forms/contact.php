<?php
if(!empty($_POST)){
    $errors = [];
    $secret = "SECRET_TOKEN";
    $response = htmlspecialchars($_POST['g-recaptcha-response']);
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $request = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip";

    $get = file_get_contents($request);
    $decode = json_decode($get, true);

    if($_POST['name'] == ''){
        $errors['name'] = "Vous n'avez pas renseigner votre nom";
    }
    if($_POST['email'] == ''){
        $errors['email'] = "Vous n'avez pas renseigner votre email";
    } elseif(!empty($errors)) {
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $errors['email'] = "Votre email n'est pas sous le bon format.";
        }
    }
    if($_POST['subject'] == ''){
        $errors['subject'] = "Vous n'avez pas renseigner votre sujet";
    }
    if($_POST['message'] == ''){
        $errors['message'] = "Vous n'avez pas renseigner votre message";
    }
    if(!$decode['success']){
        $errors['message'] = "Le Captcha n'est pas valide";
    }

    session_start();
    if(!empty($errors)){
        session_destroy();
        session_start();
        $_SESSION['errors'] = $errors;
        $_SESSION['inputs'] = $_POST;
        header('Location: ../index.php#contact');
    } elseif(empty($errors)) {
        session_destroy();
        session_start();
        $_SESSION['success'] = 1;
        $receiveremail = "exempl@gmail.com";
        $sujet = $_POST['subject'];
        $header = "From:" . $_POST['email'];
        $header = "Content-Type:text/html; charset=\"iso-8859-1\"";
        $message = '
        <div style="width: 100%; height: 100%; background-color: #FFFF;">
            <div style="width:500px; margin-left:auto; margin-right:auto; border: 1px solid #ccc!important; border-radius: 25px;">
                <div style="height: 50px;"></div>
                <h1 style="color:#202020; font-weight:500; font-family:sans-serif; font-size:23px; letter-spacing:2px; line-height:26px; text-align:center;"> Nom : ' . $_POST['name'] . '</h1>
                <h1 style="color:#202020; font-weight:500; font-family:sans-serif; font-size:23px; letter-spacing:2px; line-height:26px; text-align:center;"> Email : ' . $_POST['email'] . '</h1>
                <div style="height: 30px;"></div>
                <hr style="border: none; border-top: 3px double #ccc; color: #ccc; overflow: visible; text-align: center; height: 5px;">
                <div style="background-color: #FFFF; padding: 45px;">
                    <p style="color:#202020 !important; font-family:arial, helvetica, sans-serif; font-size:15px; letter-spacing:0.5px; line-height:18px;">
                    ' . $_POST['message'] . '
                    </p>
                </div>
                <div style="height: 50px;"></div>
                <hr style="border: none; border-top: 3px double #ccc; color: #ccc; overflow: visible; text-align: center; height: 5px;">
                <p style="color:#202020; text-align: center; font-family:arial, helvetica, sans-serif; font-size:12px; letter-spacing:0.5px; line-height:18px;">© 2020. Tous droits réservés.</p>
                <div style="height: 30px;"></div>
            </div>
        </div>
        ';
        mail($receiveremail, $sujet, $message, $header);
        header('Location: ../index.php#contact');
    }
}
?>