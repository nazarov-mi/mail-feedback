<!DOCTYPE html>
<html>
<head>
	<title>Mail feedback</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script type="text/javascript" src="mail.js"></script>
	<style type="text/css">
		input, textarea {
			width: 100%;
		}
		
		textarea {
			height: 130px;
		}

		.fb-form {
			width: 300px;
			margin: 100px auto 0;
			padding: 1.5em;
			border: 1px solid #dedede;
			transition: border-color 150ms ease-in;
		}

		.fb-form_loading {
			border-color: #06f;
		}

		.fb-form_success {
			border-color: #66bb6a;
		}

		.fb-form__error {
			border-color: #f60;
		}

		.fb-input_error {
			border-color: #f60;
		}

		.fb-label {
			color: #f60;
		}

		.fb-status_success {
			color: #66bb6a;
		}

		.fb-status_error {
			color: #f60;
		}
	</style>
</head>
<body>
	<form name="form-1" class="fb-form">
		<h4>Form #1</h4>
		<p class="fb-status"></p>
		<p>
			<input type="text" name="name" value="Максим" class="fb-input">
		</p>
		<p class="fb-label" rel="name"></p>
		<p>
			<input type="text" name="mail" value="m@nazarov-mi.ru" class="fb-input">
		</p>
		<p class="fb-label" rel="mail"></p>
		<p>
			<textarea name="message" class="fb-input">Бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла, бла.</textarea>
		</p>
		<p class="fb-label" rel="message"></p>
		<p>
			<button class="fb-disabled">Send</button>
		</p>
	</form>
</body>
</html>