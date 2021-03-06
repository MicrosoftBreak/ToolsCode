<?php
//登录控制器
class LoginController extends Controller{
	//载入登录界面
	public function loginAction(){
		include CUR_VIEW_PATH . "login.html";
	}
	//处理用户登录的动作
	public function signinAction(){
		// 0. 验证验证码
		$captcha = trim($_POST['captcha']);
		if (strtolower($captcha) != $_SESSION['captcha']){
			$this->jump('index.php?p=admin&c=login&a=login',"别灌水了",3);
		}
		// 1. 收集用户名和密码
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);

		//对用户名和密码进行转义
		// $username = addslashes($username);
		// $password = addslashes($password);
		//载入辅助函数
		$this->helper('input');
		$username = deepslashes($username);
		$password = deepslashes($password);
		
		// 2. 验证和处理数据
		// 3. 调用模型来进行验证,给出提示
		$adminModel = new AdminModel("admin");
		$uesrinfo = $adminModel->checkUser($username,$password);
		if (empty($uesrinfo)){
			//不存在该用户
			$this->jump("index.php?p=admin&c=login&a=login","用户名和密码错误,请重试",3);
		} else {
			//登录成功,保存用户登录状态，跳转到后台首页
			$_SESSION['admin'] = $uesrinfo;
			$this->jump("index.php?p=admin&c=index&a=index","",0);
		}
	}

	//注销
	public function logoutAction(){
		//销毁session
		unset($_SESSION['admin']);
		session_destroy();
		$this->jump('index.php?p=admin&c=login&a=login','',0);
	}

	//生成验证码
	public function captchaAction(){
		//载入验证码类
		$this->library("Captcha");
		//实例化验证码类对象，并调用方法生成验证码
		$captcha = new Captcha();
		$captcha->generateCode();
		//保存到session中
		$_SESSION['captcha'] = $captcha->getCode();
	}
}