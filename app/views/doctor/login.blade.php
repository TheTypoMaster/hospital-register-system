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
		<div class="login-board-wrapper">
			<div class="login-board">
				<div class="login-board-head">
					用户登录
				</div>
				<form action="/" method="post" class="login-form">
					<div class="login-input login-input-name">
						<img src="/images/doc_web/login_name.png">
						<input type="text" placeholder="用户名">
					</div>
					<div class="login-input login-input-psd">
						<img src="/images/doc_web/login_psd.png">
						<input type="password" placeholder="密码">
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
</body>
</html>