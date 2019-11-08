<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;



final class PostPresenter extends Nette\Application\UI\Presenter
{
	private $postManager;
	public $counterMessage;
	public $karma;
	public $postId;


	public function __construct(Model\PostManager $postManager)
	{
		$this->postManager = $postManager;
	}	

	//načtení Post/show.latte
	public function renderShow(int $postId): void
	{
		//vytáhne z databáze post
		$post = $this->postManager->findById($postId);
		$this->postId = $postId;

		if (!$post) {
			$this->error('Post not found');
		}
		$postInfo = $this->postManager->findbyWhere('id', $postId);

		//pokud je autor článku zobrazí se mu možnost editace
		$this->template->showEdit = false;
		if($postInfo->creator_id == $this->getUser()->getIdentity()->getRoles()['id'])
		{
		$this->template->showEdit = true;
		}

		//vykreslí post a komenty
		$this->template->post = $post;
		$this->template->comments = $post->related('comment')->order('created_at');

		$this->template->counterMessage = $this->counterMessage;
		$this->template->clickCounter = "xaaldaa";
		$this->template->karma = $this->karma;

	}


	//při smazání postu ošetřit smazání všech navazujících komentářů
	protected function createComponentDeleteForm(): Form
	{
		$form = new Form;
		$form->addSubmit('delete', 'Delete')
			->setHtmlAttribute('class', 'default')
			->onClick[] = [$this, 'deleteFormSucceeded'];
		$form->addProtection();
		return $form;
	}
	public function deleteFormSucceeded(): void
	{	
		$postId = $this->request->getParameter('postId');
		$this->postManager->deleteById(((int)$postId));
		$this->flashMessage('Článek byl vymazán.');
		$this->redirect('Homepage:');
	}


	//komentáře
	protected function createComponentCommentForm(): Form
	{
		$postId = $this->getParameter('postId');
		$form = new Form;
		$form->addText('name', 'Your name:')
			->setRequired();

		$form->addEmail('email', 'Email:');

		$form->addTextArea('content', 'Comment:')
			->setRequired();

		$form->addHidden('post_id')->setDefaultValue($postId);	

		$form->addSubmit('send', 'Publish comment');
		$form->onSuccess[] = [$this, 'commentFormSucceeded'];

		return $form;
	}

	
	public function commentFormSucceeded(Form $form, array $values): void
	{
		$this->postManager->koment($values);
		$this->flashMessage('Thank you for your comment', 'success');
		$this->redirect('this');
	}

	//načtení Post/create.latte
	public function actionCreate(): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}

	}

	//načtení Post/Edit.latte
	public function actionEdit(int $postId): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
		else{
		$post = $this->postManager->findById($postId);
		$postInfo = $this->postManager->findbyWhere('id', $postId);

		//pokud je autor článku umožní mu edit a delete
		if($postInfo->creator_id == $this->getUser()->getIdentity()->getRoles()['id'])
		{
		if (!$post) {
			$this->error('Post not found');
		}

		$this->template->showEdit = true;

		$this['postForm']->setDefaults($post->toArray());
		}
		else{
			$this->redirect('Homepage:');
		}
		}
	}

	//komponenta na editace
	public function createComponentEditForm(): Form
	{
		if (!$this->getUser()->isLoggedIn()) 
		{
			$this->error('You need to log in to create or edit posts');
		}
		else{
		//pokud je autor článku umožní mu edit a delete
		if($postId->creator_id = $this->user->id)
		{
			$form = new Form;
			$form->addText('title', 'Title:')
				->setRequired();
			$form->addTextArea('content', 'Content:')
				->setRequired();
	
	
			$form->addSubmit('send', 'Save and publish');
			$form->onSuccess[] = [$this, 'postFormSucceeded'];
	
			return $form;
		}
	}
	}
	//updatuje hodnoty po editu
	public function editFormSucceeded(Form $form, array $values): void
	{
		$postId = $this->getParameter('postId');

		if ($postId) {
			$post = $this->postManager->findById($postId);
			$post->update($values);
		}
		$this->flashMessage('Post was edited', 'success');
		$this->redirect('show', $post->id);
	}

	//Upvote karmy
	public function handleUpvoteMe() {
		$postId = $this->getParameter('postId');
		$this->postManager->updateKarma((int)$postId, $this->getUser()->getIdentity()->getRoles()['id']);
		$this->counterMessageUpdate();
		$this->redrawControl('click-counter');
	}
	//Downvote karmy
	public function handleDownvoteMe()
    {
        $postId = $this->getParameter('postId');
        $this->postManager->downvoteKarma((int)$postId, $this->getUser()->getIdentity()->getRoles()['id']);
        $this->counterMessageUpdate();
        $this->redrawControl('click-counter');
	}
	//překreslí karmu
	private function counterMessageUpdate() {
		$postId = $this->getParameter('postId');
		$this->counterMessage = ($this->postManager->findByWhere('id', ((int)$postId))->karma);
	}
	
	//Vytvoří post
	protected function createComponentPostForm(): Form
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('You need to log in to create or edit posts');
		}

		$form = new Form;
		$form->addText('title', 'Title:')
			->setRequired();
		$form->addTextArea('content', 'Content:')
			->setRequired();


		$form->addSubmit('send', 'Save and publish');
		$form->onSuccess[] = [$this, 'postFormSucceeded'];

		return $form;
	}

	
	public function postFormSucceeded(Form $form, array $values): void
	{
		$postId = $this->getParameter('postId');

		if ($postId) {
			$post = $this->postManager->findByWhere('id', (int)$postId);
			$this->postManager->update($values, (int)$postId);
		} else {
			$this->postManager->insert($values, $this->getUser()->getIdentity()->getRoles()['id']);
		}

		$this->flashMessage('Post was published', 'success');
		$this->redirect('Homepage:');
	}
}
