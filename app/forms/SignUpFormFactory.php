<?php

declare(strict_types=1);
namespace App\Forms;
use App\Model;
use Nette;
use Nette\Application\UI\Form;


class SignUpFormFactory
{
  use Nette\SmartObject;
  //konstanta na minimální délku hesla
  private const PASSWORD_MIN_LENGTH = 6;
	/** @var FormFactory */
	private $factory;
	/** @var Model\UserManager */
    private $userManager;
    
  //konstruktor UserManageru
  public function __construct(FormFactory $factory, Model\UserManager $userManager)
  {
    $this->factory = $factory;
    $this->userManager = $userManager;
  }
  //Form na registraci + success
  public function create(callable $onSuccess): Form
  {
    //vytvoří formu dle funkce z FormFactory.php
    $form = $this->factory->create();

    //textbox na name
    $form->addText('name', 'Zadejte jméno:')
      ->setRequired('Povinný údaj');

    //textbox na password
    $form->addPassword('password', 'Vaše heslo')
      ->setOption('description', sprintf('minimálně %d písmen', self::PASSWORD_MIN_LENGTH))
      ->setRequired('Povinný údaj')
      ->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH);

    //kontrola hesla
    $form->addPassword('password1', 'Potvrzení hesla')
      ->setOption('description', sprintf('minimálně %d písmen', self::PASSWORD_MIN_LENGTH))
      ->setRequired('Povinný údaj')
      ->addConditionOn($form['password'],Form::FILLED)
      ->addRule(Form::EQUAL, "Hesla se musí shodovat!", $form["password"]);

    $form->addSubmit('send', 'Zaregistrovat se');
    //ošetření registrace
    $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
     //předá údaje userManagerovi a vyhodí vyjímku, pokud už je v databázi
      try { 
        $this->userManager->add($values->name, $values->password, 'registrován');
      } catch (Model\DuplicateNameException $e){
        $form['name']->addError('Toto jméno je už zabrané');
        return;
      }
      $onSuccess();
    };
    return $form;
  }
}