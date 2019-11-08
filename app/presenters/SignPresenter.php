<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;


final class SignPresenter extends Nette\Application\UI\Presenter
{
  	/** @var Forms\SignUpFormFactory */
	private $signUpFactory;
	/** @var Forms\SigninFormFactory */
	private $signInFactory;

	public function __construct(Forms\SignUpFormFactory $signUpFactory, Forms\SignInFormFactory $signInFactory)
    {
	  $this->signUpFactory = $signUpFactory;
	  $this->signInFactory = $signInFactory;
    }
	/**
	 * Sign-up form factory.
	 */
	protected function createComponentSignUpForm(): Form
    {
      return $this->signUpFactory->create(function () {
		
        $this->redirect('Sign:in');
       });
	}
	/**
	 * Sign-in form factory.
	 */
	protected function createComponentSignInForm(): Form
    {
      return $this->signInFactory->create(function () {
        $this->redirect('Homepage:');
       });
    }


	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect('Homepage:');
	}
}
