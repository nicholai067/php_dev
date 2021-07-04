<?php namespace App\Controllers;

use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
  


class Users extends BaseController
{
	public function index()
	{
		  
		$mail = new PHPMailer(true);
  
		try {
			// $mail->SMTPDebug = 2;                                       
			// $mail->isSMTP();                                            
			// $mail->Host       = 'ssl://smtp.gmail.com';                    
			// $mail->SMTPAuth   = true;                             
			// $mail->Username   = 'reader2027@gmail.com';                 
			// $mail->Password   = 'Robbie1121&';                        
			// $mail->SMTPSecure = 'tls';                              
			// $mail->Port       = '587';  
		  
			// $mail->setFrom('reader2027@gmail.com', 'Name');  
			// $mail->AddReplyTo('reader2027@gmail.com', 'name');          
			// $mail->addAddress('qr177177@gmail.com');
			// // $mail->addAddress('reader2027@gmail.com', 'Name');
			   
			// $mail->isHTML(true);                                  
			// $mail->Subject = 'Subject';
			// $mail->Body    = 'HTML message body in <b>bold</b> ';
			// $mail->AltBody = 'Body in plain text for non-HTML mail clients';
			// $mail->send();
			// echo "Mail has been sent successfully!";

			$email = new PHPMailer;
			$email->isSMTP();
			$email->SMTPDebug = 1;
			$email->SMTPAuth = true;
			$email->SMTPSecure = 'tls';
			$email->HOST = 'smtp.gmail.com';
			$email->Port = 587;
			$email->isHTML(true);
			$email->Username = 'reader2027@gmail.com';
			$email->Password = 'Robbie1121&';
			$email->setFrom('reader2027@gmail.com','adfs');
			$email->Subject = 'Reset Password';
			$email->Body = 'plz execute quickly';
			$email->addAddress('qr177177@gmail.com','adf');
			if(!$email->Send())
			echo "Mailer Error: ";
			else
			echo "Message has been sent";
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}


		$data = [];
		helper(['form']);


		if ($this->request->getMethod() == 'post') {
			//let's do the validation here
			$rules = [
				'email' => 'required|min_length[6]|max_length[50]|valid_email',
				'password' => 'required|min_length[8]|max_length[255]|validateUser[email,password]',
			];

			$errors = [
				'password' => [
					'validateUser' => 'Email or Password don\'t match'
				]
			];

			if (! $this->validate($rules, $errors)) {
				$data['validation'] = $this->validator;
			}else{
				$model = new UserModel();

				$user = $model->where('email', $this->request->getVar('email'))
											->first();

				$this->setUserSession($user);
				//$session->setFlashdata('success', 'Successful Registration');
				return redirect()->to('dashboard');

			}
		}

		echo view('templates/header', $data);
		echo view('login');
		echo view('templates/footer');
	}

	private function setUserSession($user){
		$data = [
			'id' => $user['id'],
			'firstname' => $user['firstname'],
			'lastname' => $user['lastname'],
			'email' => $user['email'],
			'isLoggedIn' => true,
		];

		session()->set($data);
		return true;
	}

	public function register(){
		$data = [];
		helper(['form']);

		if ($this->request->getMethod() == 'post') {
			//let's do the validation here
			$rules = [
				'firstname' => 'required|min_length[3]|max_length[20]',
				'lastname' => 'required|min_length[3]|max_length[20]',
				'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
				'password' => 'required|min_length[8]|max_length[255]',
				'password_confirm' => 'matches[password]',
			];

			if (! $this->validate($rules)) {
				$data['validation'] = $this->validator;
			}else{
				$model = new UserModel();

				$newData = [
					'firstname' => $this->request->getVar('firstname'),
					'lastname' => $this->request->getVar('lastname'),
					'email' => $this->request->getVar('email'),
					'password' => $this->request->getVar('password'),
				];
				$model->save($newData);
				$session = session();
				$session->setFlashdata('success', 'Successful Registration');
				return redirect()->to('/');

			}
		}


		echo view('templates/header', $data);
		echo view('register');
		echo view('templates/footer');
	}

	public function profile(){
		
		$data = [];
		helper(['form']);
		$model = new UserModel();

		if ($this->request->getMethod() == 'post') {
			//let's do the validation here
			$rules = [
				'firstname' => 'required|min_length[3]|max_length[20]',
				'lastname' => 'required|min_length[3]|max_length[20]',
				];

			if($this->request->getPost('password') != ''){
				$rules['password'] = 'required|min_length[8]|max_length[255]';
				$rules['password_confirm'] = 'matches[password]';
			}


			if (! $this->validate($rules)) {
				$data['validation'] = $this->validator;
			}else{

				$newData = [
					'id' => session()->get('id'),
					'firstname' => $this->request->getPost('firstname'),
					'lastname' => $this->request->getPost('lastname'),
					];
					if($this->request->getPost('password') != ''){
						$newData['password'] = $this->request->getPost('password');
					}
				$model->save($newData);

				session()->setFlashdata('success', 'Successfuly Updated');
				return redirect()->to('/profile');

			}
		}

		$data['user'] = $model->where('id', session()->get('id'))->first();
		echo view('templates/header', $data);
		echo view('profile');
		echo view('templates/footer');
	}

	public function logout(){
		session()->destroy();
		return redirect()->to('/');
	}

	//--------------------------------------------------------------------

}
