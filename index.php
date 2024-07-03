<?php
//
// инициализация страницы и генерация шапки страницы
//
include 'init.php';

// Проверка, авторизован ли пользователь
if ($users->signed_in()) {
	// Подключение файла с заголовком страницы
	include 'core/includes/head.php';
	?>

	<div class="container">
		<div class="row">
			<!-- Навигационная цепочка -->
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item active" aria-current="page">Главная</li>
				</ol>
			</nav>
		</div>
		<br>
		<br>
		<h4>Пользователь:
			<!-- Отображение имени и фамилии пользователя -->
			<?php echo $users->get_user_info('firstname') . ' ' . $users->get_user_info('lastname'); ?>
		</h4>
		<p>Права доступа:
			<!-- Отображение прав доступа пользователя -->
			<?php echo $users->get_user_info('user_rights') ?>
		</p>

	</div>

	<br><br>
	<div class="container">
		<?php
		// Проверка, является ли пользователь студентом
		if ($users->get_user_info('user_rights') == 'Студент') {
			?>
			<div class="row g-5">
				<div class="col-md-6 col-xs-6 g-5">
					<div class="card text-center">
						<br>
						<i class="fa fa-question-circle-o fa-4x text-danger" aria-hidden="true"></i>

						<div class="card-body">
							<h5 class="card-title">Новая заявка</h5>
							<p class="card-text">Создание новой заявки по интересующему вопросу.</p>
							<a href="#" class="btn btn-primary" id="btn-create-ticket">Создать заявку</a>
						</div>
					</div>
				</div>
				<div class="col-md-6 col-xs-6 g-5">
					<div class="card text-center">
						<br>
						<i class="fa fa-question-circle fa-4x primary-color" aria-hidden="true"></i>

						<div class="card-body">
							<h5 class="card-title">Мои заявки</h5>
							<p class="card-text">Просмотр и управление всеми моими заявками.</p>
							<a href="#" class="btn btn-primary" id="btn-all-ticket">Посмотреть</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container hidden" id="container-show-tickets">
			<br>
			<div class="row g-5">
				<div class="col">
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th>Тема</th>
								<th>Статус</th>
								<th>Последний ответ</th>
								<th>Время</th>
							</tr>
						</thead>
						<tbody class="table-group-divider">
							<!-- Отображение заявок студента -->
							<?php $tickets->my_tickets(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="container hidden" id="container-new-ticket">
			<main>
				<div class="py-5 text-center">
					<i class="fa fa-question-circle-o fa-4x text-danger" aria-hidden="true"></i>
					<h2>Новая заявка</h2>
					<p class="lead">Пожалуйста, введите описание проблемы и выберите тему обращения</p>
				</div>

				<div class="row g-5">
					<div class="col-md-5 col-lg-4 order-md-last">
						<ul class="list-group mb-3">
							<li class="list-group-item d-flex" id="theme-info-li">
								<span id="no-choose-theme">Выберите тему обращения</span>
								<?php
								// Получение информации по темам заявок
								$tickets->get_themes_info();
								?>
							</li>
						</ul>
					</div>
					<div class="col-md-7 col-lg-8">
						<form>
							<div class="row g-3">
								<div class="col-12">
									<label for="theme" class="form-label">Тема обращения</label>
									<select class="form-select" id="theme" required>
										<?php
										// Получение списка тем заявок
										$tickets->get_themes();
										?>
									</select>
								</div>
								<div class="col-12">
									<label for="heading" class="form-label">Заголовок</label>
									<input type="text" class="form-control" id="heading" aria-describedby="heading">
								</div>
								<div class="col-12">
									<label for="message" class="form-label">Текст</label>
									<textarea class="form-control" id="message" rows="10"></textarea>
								</div>
								<div class="col-12">
									<label for="form-file" class="form-label">Загрузить файлы</label>
									<input class="form-control" type="file" id="form-file" multiple>
								</div>
							</div>

							<hr class="my-4">

							<button class="w-50 btn btn-primary btn-lg" id="create-ticket">Отправить заявку</button>
						</form>
					</div>
				</div>
			</main>
		</div>
	<?php } else { ?>
		<div class="container">
			<div class="row g-5">
				<div class="col">
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th>Тема</th>
								<th>Заголовок</th>
								<th>Последний ответ</th>
								<th>Время</th>
							</tr>
						</thead>
						<tbody class="table-group-divider">
							<!-- Отображение заявок для менеджера -->
							<?php $tickets->my_tickets_manager(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php }
} else {
	// Перенаправление на страницу авторизации
	header('Location: ' . $website_url . 'authenticate/');
}
// Подключение файла с подвалом страницы
include 'core/includes/foot.php';
?>