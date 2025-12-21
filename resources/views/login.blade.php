<x-layout title="Login">
	<section class="form-card" aria-label="Login">
		<h1 class="form-title">Login</h1>
		<p class="form-description">Use your username and password to continue.</p>

		<form class="form" method="post" action="#">
			@csrf

			<div class="form-field">
				<label class="form-label" for="username">Username</label>
				<input
					class="form-input"
					id="username"
					name="username"
					type="text"
					inputmode="text"
					autocomplete="username"
					required
				/>
			</div>

			<div class="form-field">
				<label class="form-label" for="password">Password</label>
				<input
					class="form-input"
					id="password"
					name="password"
					type="password"
					autocomplete="current-password"
					required
				/>
			</div>

			<div class="form-actions">
				<label class="form-checkbox" for="remember">
					<input class="form-checkbox__input" id="remember" name="remember" type="checkbox" />
					<span>Remember me</span>
				</label>

				<button class="btn btn--primary" type="submit">Login</button>
			</div>
		</form>
	</section>
</x-layout>