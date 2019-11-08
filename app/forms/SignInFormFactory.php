<?php

declare(strict_types=1);

namespace App\Forms;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

class SignInFormFactory
{
    use Nette\SmartObject;
    
	/** @var FormFactory */
	private $factory;
	/** @var User */
    private $user;
    
	public function __construct(FormFactory $factory, User $user)
	{
		$this->factory = $factory;
		$this->user = $user;
    }
    
	public function create(callable $onSuccess): Form
	{
        $form = $this->factory->create();
        
		$form->addText('name', 'Jméno:')
            ->setRequired('Prosím zadejte jméno.');
            
		$form->addPassword('password', 'Password:')
            ->setRequired('Prosím zadejte heslo.');
            
        $form->addCheckbox('remember', 'Zůstat přihlášen');
        
        $form->addSubmit('send', 'Přihlásit se');
        
		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			try {
                //po určitém čase odhlásí uživatele
                $this->user->setExpiration($values->remember ? '2 days' : '4000 minutes');
                //předá údaje, pokud nejsou dobře, vyhodí error
                $this->user->login($values->name, $values->password);
                
			} catch (Nette\Security\AuthenticationException $e) {
				$form->addError('Nesprávné přihlašovací údaje');
				return;
			}
			$onSuccess();
		};
		return $form;
	}
}