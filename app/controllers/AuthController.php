<?php
require_once '../app/controllers/BaseController.php';
require_once '../app/models/UserModel.php';

class AuthController extends BaseController
{
  private $userModel;

  public function __construct()
  {
    parent::__construct();
    $this->userModel = new UserModel();
  }

  // Form login
  public function loginForm()
  {
    // Jika sudah login, langsung ke dashboard
    if (isset($_SESSION['id'])) {
      $this->redirectTo('dashboard');
    }

    $this->render('login.php');
  }

  // Login
  public function login()
  {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $this->userModel->getUserByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
      // Set session
      $_SESSION['id'] = $user['id'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['role'] = $user['role'];

      // Set cookie opsional (7 hari)
      setcookie('email', $user['email'], time() + (86400 * 7), "/");

      $this->redirectTo('dashboard');
    } else {
      $error = "Email atau password salah!";
      $this->render('login.php', ['error' => $error]);
    }
  }

  // Logout
  public function logout()
  {
    session_destroy();
    setcookie('email', '', time() - 3600, "/");

    $this->redirectTo('login', 'Anda berhasil logout.', 'success');
  }
}
