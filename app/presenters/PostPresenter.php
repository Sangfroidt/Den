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


	public function __construct(Model\PostManager $postManager)
	{
		$this->postManager = $postManager;
	}	

	//načtení Post/show.latte
	public function renderShow(int $postId): void
	{
		//vytáhne z databáze post
		$post = $this->postManager->findById($postId);

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
		$form = new Form;
		$form->addText('name', 'Your name:')
			->setRequired();

		$form->addEmail('email', 'Email:');

		$form->addTextArea('content', 'Comment:')
			->setRequired();

		$form->addSubmit('send', 'Publish comment');
		$form->onSuccess[] = [$this, 'commentFormSucceeded'];

		return $form;
	}

	
	public function commentFormSucceeded(Form $form, \stdClass $values): void
	{
		$this->database->table('comments')->insert([
			'post_id' => $this->getParameter('postId'),
			'name' => $values->name,
			'email' => $values->email,
			'content' => $values->content,
		]);

		$this->flashMessage('Thank you for your comment', 'success');
		$this->redirect('this');
	}

	//načtení Post/create.latte
	public function actionCreate(): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
		dump($this->getUser()->getIdentity()->getRoles()['id']);
	}

	//načtení Post/Edit.latte
	public function actionEdit(int $postId): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}

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

	public function createComponentEditForm(): Form
	{
		if (!$this->getUser()->isLoggedIn()) 
		{
			$this->error('You need to log in to create or edit posts');
		}
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
	//updatuje hodnoty po editu
	public function editFormSucceeded(Form $form, array $values): void
	{
		$postId = $this->getParameter('postId');

		if ($postId) {
			$post = $this->database->table('posts')->get($postId);
			$post->update($values);
		}
		$this->flashMessage('Post was edited', 'success');
		$this->redirect('show', $post->id);
	}

	//upvote karmy 
	/* public function handleDownVote() {
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('You need to log in to create or edit posts');
		}
		$postId = $this->getParameter('postId');
		$this->postManager->updateKarma(((int)$postId));
		$this->redrawControl('click-counter');
	} */

	public function handleClickMe() {
		$postId = $this->getParameter('postId');
		$this->postManager->updateKarma(((int)$postId));
		$this->counterMessageUpdate();
		$this->redrawControl('click-counter');
	}
	private function counterMessageUpdate() {
		$postId = $this->getParameter('postId');
		$this->counterMessage = ($this->postManager->findByWhere('id', ((int)$postId))->karma);
	}

	public function handleChangeVariable()
    {
		$this->clickCounter = 'pokus';
        if ($this->isAjax()) {
            $this->redrawControl('pokus');
        }
    }
	/*public function createComponentKarmaDownForm(): Form 
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('You need to log in to create or edit posts');
		}
		$form = new Form;
		$form->addSubmit('send', 'Downvote');
		$form->onSuccess[] = [$this, 'handleDownVote'];

		return $form;
	}

	 public function KarmaUpFormSucceeded(): void
	{
		$postId = $this->getParameter('postId');
		$this->postManager->updateKarma(((int)$postId));
		$this->redrawControl('click-counter');
	} */
	
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

	//vyhodí error HTTP HEADER
	public function postFormSucceeded(Form $form, array $values): void
	{
		$postId = $this->getParameter('postId');

		if ($postId) {
			$post = $this->postManager->findByWhere('id', (int)$postId);
			$this->postManager->update($values, (int)$postId);
		} else {
		//	$post = $this->database->table('posts')->insert($values);
			$this->postManager->insert($values, $this->getUser()->getIdentity()->getRoles()['id']);
		}

		$this->flashMessage('Post was published', 'success');
		$this->redirect('Homepage:');
	}
}
