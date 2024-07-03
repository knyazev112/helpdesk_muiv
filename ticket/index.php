<?php
include '../init.php';
include '../core/includes/head.php';

if (isset($_GET['id']) && $users->signed_in() && $tickets->is_ticket($_GET['id'])) {
	if (!$tickets->my_ticket($_GET['id'])) {
		if ($users->get_user_info('user_rights') == 'Студент') {
			header('Location: ' . WEBSITE_URL . '/authenticate');
			die();
		}
	}

	?>

	<div class="container">
		<div class="row">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo WEBSITE_URL; ?>">Главная</a></li>
					<li class="breadcrumb-item active" aria-current="page">Заявка</li>
				</ol>
			</nav>
		</div>
		<br><br>

		<div class="row">
			<div class="card mb-3">
				<div class="row g-0">
					<div class="col-md-4 card-body" style="padding-left: 0px;">
						<div class="card" style="height: 100%;">
							<div class="card-body">
								<h5 class="card-title">
									<?php echo $users->id_to_column($tickets->ticket_info($_GET['id'], 'user'), 'firstname') . ' ';
									echo $users->id_to_column($tickets->ticket_info($_GET['id'], 'user'), 'lastname');
									?>
								</h5>
								<h6 class="card-subtitle mb-2 text-body-secondary">
									<?php echo $users->get_user_group($tickets->ticket_info($_GET['id'], 'user')) ?>
								</h6>
								<p class="card-text"></p>
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<div class="card-body">
							<h5 class="card-title">
								<?php echo $tickets->ticket_info($_GET['id'], 'title'); ?>
							</h5>
							<p class="card-text" style="margin-bottom: 30px;">
								<?php echo 'Тема: <span id="theme-name" data-id="' . $tickets->get_ticket_field($_GET['id'], 'theme') . '">' . $tickets->get_themes_name($_GET['id']) . '</span>'; ?>
							</p>
							<p class="card-text" style="margin-bottom: 30px;">
								<?php echo nl2br($tickets->ticket_info($_GET['id'], 'init_msg')); ?>
							</p>
							<?php
							$files = $tickets->ticket_file_info($_GET['id']);
							if ($files) {
								echo '<p class="card-text" style="margin-bottom: 30px;">Загруженные файлы:<br>';
								echo $files;
								echo '</p>';
							}
							?>
							<p class="card-text text-end"
								style="margin-bottom: 0px;display: block;	position: absolute;	bottom: 10px;right: 16px;">
								<small class="text-body-secondary">
									<?php if ($tickets->ticket_info($_GET['id'], 'resolved') == 0 && $tickets->ticket_info($_GET['id'], 'user') == $_COOKIE['user']) { ?>
										<a id="no_longer_help" href="#">Отменить заявку</a>
									<?php } else if ($tickets->ticket_info($_GET['id'], 'user') == $_COOKIE['user']) { ?>
											Заявка выполнена.
									<?php } else if (!$users->get_user_info('user_rights') == 'Студент' && $tickets->ticket_info($_GET['id'], 'resolved') == 0) { ?>
												</li>
												<button id="close_ticket" style="margin-right:15px;">Закрыть заявку</button>
									<?php } else { ?>
												Заявка закрыта.
									<?php } ?>
									Создана:
									<?php echo ' ' . $time->ago($tickets->ticket_info($_GET['id'], 'date')); ?>
								</small>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php $ticket = $tickets->ticket_replies($_GET['id']);
		if (count($ticket) > 0) {
			echo '<h4>Ответы по заявке</h4>';
		}
		foreach ($ticket as $v) {
			$user = $users->get_user_group($v['user']);
			?>
			<div class="row">
				<div class="card mb-3">
					<div class="row g-0">
						<div class="col-md-4 card-body" style="padding-left: 0px;">
							<div class="card" style="height: 100%;">
								<div class="card-body">
									<h5 class="card-title">
										<?php echo $users->id_to_column($v['user'], 'firstname') . ' ';
										echo $users->id_to_column($v['user'], 'lastname');
										?>
									</h5>
									<h6 class="card-subtitle mb-2 text-body-secondary">
										<?php echo $user; ?>
									</h6>
									<p class="card-text"></p>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div class="card-body">
								<p class="card-text" style="margin-bottom: 30px;">
									<?php echo $v['text']; ?>
								</p>
								<?php
								$files = $tickets->reply_file_info($v['id']);
								if ($files) {
									echo '<p class="card-text" style="margin-bottom: 30px;">Загруженные файлы:<br>';
									echo $files;
									echo '</p>';
								}
								?>
								<p class="card-text text-end"
									style="margin-bottom: 0px;display: block;	position: absolute;	bottom: 10px;right: 16px;">
									<small class="text-body-secondary">
										Ответ:
										<?php echo ' ' . $time->ago($v['date']); ?>
									</small>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="row">
			<div class="col-md-8 offset-md-4">
				<?php if ($tickets->ticket_info($_GET['id'], 'resolved') == 1) { ?>
					<div class="alert warning">Заявка закрыта.</div>
				<?php } ?>
				<?php if ($users->get_user_info('user_rights') != 'Студент') { ?>
					<div class="col-12">
						<p>Сменить тему обращения</p>
						<select class="form-select" id="theme">
							<?php
							$tickets->get_themes();
							?>
						</select>
						<div class="row">
							<div class="col-6 mt-4 mb-4">
								<button class="btn btn-primary" id="change-theme">Сменить тему</button>
							</div>
						</div>
					</div>
				<?php } ?>
				<form>
					<div class="col-12">
						<textarea id="reply-text" class="form-control" rows="6" placeholder="Комментарий..."></textarea>
					</div>
					<br>
					<div class="col-12">
						<label for="formFile" class="form-label">Загрузить файлы</label>
						<input class="form-control" type="file" id="form-file" multiple>
					</div>
					<div class="row">
						<div class="col-6">
							<br>
							<?php if ($tickets->ticket_info($_GET['id'], 'resolved') == 0) { ?>
								<button class="btn btn-primary" type="submit" id="reply">Добавить комментарий</button>
							<?php } else { ?>
								<button class="btn btn-primary" type="submit" id="reply">Добавить комментарий и вернуть заявку в
									работу</button>
							<?php } ?>
						</div>
						<div class="col-6">
							<br>
							<?php
							if ($users->get_user_info('user_rights') != 'Студент' && $tickets->ticket_info($_GET['id'], 'resolved') == 0) {
								echo '<button class="btn btn-primary" id="reply-and-close">Добавить комментарий и закрыть заявку</button>';
							}
							?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	</div>
	<?php
} else {
	header('Location: ../authenticate');
	die();
}
include '../core/includes/foot.php';
?>