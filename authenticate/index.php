<?php

// Подключение файла инициализации

include '../init.php';

// Проверка, авторизован ли пользователь

if ($users->signed_in()) {
	// Если пользователь авторизован, перенаправление на главную страницу
	header('Location: ' . $website_url);
	die();
}
// Подключение файла с заголовком страницы
include '../core/includes/head.php';
?>
<div class="container">
	<div class="row text-algin-center">
		<div class="mx-auto p-2" style="width: 250px; margin-top: 50px;">
			<!-- Форма авторизации -->
			<form id="auth" action="" method="post" class="text-center">
				<i class="fa fa-user-circle-o fa-5x text-danger" aria-hidden="true"></i>
				<br>
				<br>
				<h1 class="h3 mb-3 fw-normal">Войти</h1>
				<div class="form-floating">
					<input type="text" class="form-control" id="floatingInput" name="user" placeholder="Пользователь">
					<label for="floatingInput">Пользователь</label>
				</div>
				<br>
				<div class="form-floating">
					<input type="password" class="form-control" id="floatingPassword" name="password"
						placeholder="Пароль">
					<label for="floatingPassword">Пароль</label>
				</div>
				<br>
				<button class="btn btn-danger w-100 py-2" type="submit">Войти</button>
			</form>
			<br>
			<!-- Блок для отображения уведомлений -->
			<div id="alerts"></div>
		</div>
	</div>
</div>
<?php
// Подключение файла с подвалом страницы
include '../core/includes/foot.php';
?>