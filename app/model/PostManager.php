<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

class PostManager
{
	use Nette\SmartObject;
	private $database;
	private $column;
	private $where_condition;
	private $postId;
	private $user_id;
	private $columnExists;

    
	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }
    
	public function findAll(): Nette\Database\Table\Selection
	{
		return $this->database->table('posts');
	}
	public function findAllComments(): Nette\Database\Table\Selection
	{
		return $this->database->table('comments');
    }
    
	public function findById(int $id): Nette\Database\Table\ActiveRow
	{
		return $this->findAll()->get($id);
	}
	//pomocná metoda na výběr z databáze pomocí where
	public function findbyWhere(string $column, int $where_condition): Nette\Database\Table\ActiveRow
	{
		$this->column = $column;
		$this->where_condition = $where_condition;
		$result = $this->database->table('posts')->where($column, $where_condition)->fetch();
		return $result;
    }
	//edituje post
	public function update(iterable $values, int $postId): void
	{
		$this->postId = $postId;
		$this->database->table('posts')->where('id', $postId)->update($values);
	}
	//vloží komentář do databáze
	public function koment(iterable $values): void
	{
		$this->findAllComments()->insert($values);
	}

	public function insert(iterable $values, int $user_id): void
	{
		$this->findAll()->insert($values);
		$this->database->table('posts')->where('title', $values['title'])->update([
            'creator_id' => $user_id]);
	}

	public function deleteById(int $id): void
	{
		$this->database->table('comments')->where('post_id', $id)->delete();
		$this->database->table('posts')->where('id', $id)->delete();
	}

	//0 = nevolil, 1 = upvote, 2 = downvote
	public function updateKarma(int $postId, int $user_id): void
	{
		$this->postId = $postId;
		$this->user_id = $user_id;

		//zjistí, jestli existuje záznam v tabulce
		$this->columnExists = $this->database->table('karma')
		->where('post_id = ? AND user_id = ?', $postId, $user_id)->fetch();
		
		//pokud neexistuje vytvoří ho a nastaví voted na to, že upvotoval
		if(!$this->columnExists){
			$this->database->table('karma')->insert([
			'post_id' => $postId,
			'user_id' => $user_id]);
			$this->Upvote($postId, $user_id);
		}

		$this->columnExists = $this->database->table('karma')
		->where('post_id = ? AND user_id = ?', $postId, $user_id)->fetch();
		//pokud naposledy downvotoval může opět upvotovat
		if($this->columnExists->voted == 2)
			{
			$this->Upvote($postId, $user_id);
			}
	}
	public function Upvote($postId, $user_id)
	{
		$this->postId = $postId;
		$this->user_id = $user_id;
		$this->database->table('posts')->where('id', $postId)->update(['karma+=' => 1]);
		$this->database->table('karma')
		->where('post_id', $postId)
		->where('user_id', $user_id)->update([
		'voted' => 1]);
	}

	public function downvoteKarma(int $postId, int $user_id): void
    {
		$this->postId = $postId;
		$this->user_id = $user_id;

		//pokud nevotoval, založí záznam v tabulce
		$this->columnExists = $this->database->table('karma')
		->where('post_id = ? AND user_id = ?', $postId, $user_id)->fetch();
		if(!$this->columnExists){
		$this->database->table('karma')->insert([
		'post_id' => $postId,
		'user_id' => $user_id]);
		$this->Downvote($postId, $user_id);
		}

		$this->columnExists = $this->database->table('karma')
		->where('post_id = ? AND user_id = ?', $postId, $user_id)->fetch();
		//pokud upvotoval, může downvotovat
		if($this->columnExists->voted == 1)
		{
		$this->Downvote($postId, $user_id);
		}
	}

	public function Downvote($postId, $user_id)
	{
		$this->postId = $postId;
		$this->user_id = $user_id;
		$this->database->table('posts')->where('id', $postId)->update(['karma-=' => 1]);
		$this->database->table('karma')
		->where('post_id', $postId)
		->where('user_id', $user_id)->update([
		'voted' => 2]);
	}
}