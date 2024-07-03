<?php
include '../init.php';
include '../core/includes/head.php';
if ($users->signed_in()) {
	if ($users->get_user_info('user_rights') != 'Администратор системы') {
		header('Location: ' . $website_url);
		die();
	}
	?>
	<div class="toast-container position-fixed bottom-0 end-0 p-3">
		<div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
			<div class="d-flex">
				<div class="toast-body">
					Изменения сохранены.
				</div>
				<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Закрыть"></button>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo $website_url; ?>">Главная</a></li>
					<li class="breadcrumb-item active" aria-current="page">Пользователи</li>
				</ol>
			</nav>
			<br>
			<br>
			<h4>Пользователь:
				<?php echo $users->get_user_info('firstname') . ' ' . $users->get_user_info('lastname'); ?>
			</h4>
			<p>Права доступа:
				<?php echo $users->get_user_info('user_rights') ?>
			</p>
		</div>
		<div class="row">
			<div class="col-md-12">

				<div class="card">
					<h5 class="card-header">Управление доступом пользователей к темам</h5>
					<div class="card-body">
						<table class="table">
							<thead>
								<tr>
									<th scope="col" class="col-1" style="min-width: 100px;">Тема</th>
									<th scope="col" class="col-3" style="min-width: 200px;">Пользователи</th>
								</tr>
							</thead>
							<tbody>
								<?php echo $users->get_themes_table(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
} else {
	header('Location: ' .$website_url. 'authenticate');
	die();
}
include '../core/includes/foot.php';
?>