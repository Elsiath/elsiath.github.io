<?php

// Валидация на стороне сервера
function isValid() {
    if($_POST['name'] != "" && $_POST['email'] != "" && $_POST['comment'] != "") {
        return true;
    } else {
        return false;
    }

}

$error_output = '';
$success_output = '';

if(isValid()) {
    // Составляем POST-запрос, чтобы получить от Google оценку reCAPTCHA v3
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = '6Ld-4KIdAAAAAGTsSS6qmTZcAZp_YqajzN5LIU9z'; // Insert your secret key here
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Выполняем POST-запрос
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);

    $recaptcha = json_decode($recaptcha);
    // Принимаем действие на основе возвращаемой оценки
    if ($recaptcha->success == true && $recaptcha->score >= 0.5) {
        // Это человек. Вставляем сообщение в базу данных или отправляем на электронную почту
        $success_output = "Your message sent successfully ".$recaptcha_response;
    } else {
        // Оценка меньше 0.5 означает подозрительную активность. Возвращаем ошибку
        $error_output = "Something went wrong. Please try again later".$recaptcha;
    }
} else {
    // Валидация на стороне сервера не прошла
    $error_output = "Please fill all the required fields";
}

$output = array(
    'error'     =>  $error_output,
    'success'   =>  $success_output
);

// Вывод должен быть в формате JSON
echo json_encode($output);

?>