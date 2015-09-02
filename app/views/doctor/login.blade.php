<!DOCTYPE html>
<html>
<head>

	<meta http-equiv="Content-Type" content="text/html,charset=utf8">
	<title>登录</title>

    <link rel="stylesheet" type="text/css" href="/dist/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/dist/css/base_web.css" />
	<link rel="stylesheet" type="text/css" href="/dist/css/doctor/login.css">
</head>
<body>
	<div class="wrap login">
		<img class="bg" src="/images/doc_web/login_bg.jpg" alt="">
		<div class="login-board-wrapper">
			<img class="bg" src="/images/doc_web/login_shadow.png" alt="">
			<div class="login-board">
				<div class="login-board-head">
					用户登录
				</div>
				<form action="/" method="post" class="login-form">
					<div class="login-input login-input-name">
						<!-- <img class="bg" src="/images/doc_web/login_input_bg.png" alt=""> -->
						
						<input name="account" type="text" placeholder="用户名">
						<img class="login-icon" src="/images/doc_web/login_name.png">
					</div>
					<div class="login-input login-input-psd">
						<!-- <img class="bg" src="/images/doc_web/login_input_bg.png" alt=""> -->
						
						<input name="password" type="password" placeholder="密码">
						<img class="login-icon" src="/images/doc_web/login_psd.png">
					</div>
					<div class="login-words">
						<a href="/">
							<span>忘记密码？</span>
						</a>
					</div>
					<div class="login-btn">
						<!-- <button type="submit" value="">登录</button> -->
						<input type="submit" value="登录" class="login-submit"/>
					</div>
				</form>
			</div>
		</div>
		<div class="slogan-wrapper">
			<img src="/images/doc_web/login_slogan.png">
		</div>
	</div>

	<script src="/dist/js/lib/jquery-1.11.2.min.js"></script>
	<script src="/dist/js/pages/doctor/login.js"></script>
</body>
</html>