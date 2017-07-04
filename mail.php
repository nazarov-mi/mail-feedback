<?php

$CONFIG = [
	'from_mail' => 'no-reply@' . $_SERVER['HTTP_HOST'],
	'from_name' => 'Feedback form',

	'to_mail'   => 'm@nazarov-mi.ru',
	'to_name'   => 'Nazarov MI',
];

$FORMS = [
	'form-1' => [
		'title' => 'Form 1',
		'fields' => [
			'name' => [
				'title' => 'Имя',
				'rules' => [
					'required' => true,
					'minlength' => 3,
					'maxlength' => 20,
				]
			],
			'mail' => [
				'title' => 'E-Mail',
				'rules' => [
					'required' => true,
					'minlength' => 10,
					'maxlength' => 20,
					'pregmatch' => '/^.+@.+\..{2,}/is',
				]
			],
			'message' => [
				'title' => 'Сообщение',
				'rules' => [
					'required' => true,
					'minlength' => 50,
					'maxlength' => 500,
				]
			],
		]
	],
];

// ============================================================

init($_REQUEST, $FORMS, $CONFIG);

function init($request, $forms, $config)
{
	$data = $_POST;
	$act  = $data['act'];

	if (!isset($act)) {
		errorResponse();
	}

	$form = $forms[$act];

	if (isset($form)) {
		submitForm($form, $data, $config);
	} else {
		errorResponse();
	}
}


function submitForm($form, $data, $config)
{
	$fields  = $form['fields'];
	$success = $form['succes'];
	$error   = $form['error'];

	$res = validateFields($fields, $data);

	if ($res !== true) {
		errorResponse('Некорректно заполнены данные', $res);
	}

	$fromMail = $config['from_mail'];
	$fromName = $config['from_name'];

	$toMail   = $config['to_mail'];
	$toName   = $config['to_name'];

	$message = [];

	foreach ($fields as $name => $params) {
		$value = $data[$name];
		$title = $params['title'];

		$message[] = $title . ': ' . $value;
	}

	$message = '
		<html>
			<head>
				<title>' . $form['title'] . '</title>
			</head>
			<body>
				<h4>' . $form['title'] . '</h4>
				<p>' . implode('</p><p>', $message) . '</p>
			</body>
		</html>';

	$status = sendMail($fromMail, $fromName, $toMail, $toName, $message);

	if ($status) {
		successResponse($success);
	} else {
		errorResponse($error);
	}
}

function validateFields($fields, $data)
{
	$errors = [];

	foreach ($fields as $name => $params) {
		$value = $data[$name];
		$rules = $params['rules'];
		
		$res = validateField($value, $rules);

		if ($res !== true) {
			$errors[$name] = $res;
		}
	}

	if (count($errors) > 0) {
		return $errors;
	}

	return true;
}

function validateField($value, $rules)
{
	$len = mb_strlen($value);

	$rule = $rules['required'];

	if (isset($rule) && empty($value)) {
		return 'Необходимо заполнить поле';
	}

	$rule = $rules['minlength'];

	if (isset($rule) && $len < $rule) {
		return 'Минимум ' . $rule . ' знаков';
	}

	$rule = $rules['maxlength'];

	if (isset($rule) && $len > $rule) {
		return 'Максимум ' . $rule . ' знаков';
	}

	$rule = $rules['pregmatch'];

	if (isset($rule) && $len > 0 && !preg_match($rule, $value)) {
		return 'Некорректное значение';
	}

	return true;
}

function sendMail($mail, $name, $mailTo, $subject, $message)
{
	$from    = '=?UTF-8?B?' . base64_encode($name)    . '=?= <' . $mail . '>';
	$subject = '=?UTF-8?B?' . base64_encode($subject) . '=?=';

	$headers = [
		'X-Priority: 3',
		'MIME-Version: 1.0',
		'Content-Transfer-Encoding: 8bit',
		'Content-Type: text/html; charset="UTF-8"',
		'Return-Path: <' . $mail . '>',
		'From: ' . $from,
		'Reply-To: ' . $from,
	];

	$headers = implode("\r\n", $headers);
	$status = false;

	try {
		$status = mail($mailTo, $subject, $message, $headers);
	} catch(Exception $e) {
		return false;
	}

	return $status;
}

function successResponse($text = null)
{
	$text = $text ?: 'Заявка успешно отправлена';

	$data = [
		'status' => true,
		'text' => $text
	];

	response(200, 'OK', $data);
}

function errorResponse($text = null, $errors = [])
{
	$text = $text ?: 'Не удалось отправить заявку';

	$data = [
		'status' => false,
		'text' => $text,
		'errors' => $errors
	];

	response(200, 'OK', $data);
}

function response($code, $text, $data)
{
	header('Content-Type: application/json; charset="UTF-8"', false, $code);
	header(sprintf('HTTP/1.0 %s %s', $code, $text), true, $code);

	echo json_encode($data);
	exit();
}