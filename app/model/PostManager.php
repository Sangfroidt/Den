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
    
	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }
    
	public function findAll(): Nette\Database\Table\Selection
	{
		return $this->database->table('posts');
    }
    
	public function findById(int $id): Nette\Database\Table\ActiveRow
	{
		return $this->findAll()->get($id);
	}

	public function findbyWhere(string $column, int $where_condition): Nette\Database\Table\ActiveRow
	{
		$this->column = $column;
		$this->where_condition = $where_condition;
		$result = $this->database->table('posts')->where($column, $where_condition)->fetch();
		return $result;
    }
	
	public function update(iterable $values, int $postId): void
	{
		$this->postId = $postId;
		$this->database->table('posts')->where('id', $postId)->update($values);
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
	public function updateKarma(int $postId): void
	{
		$this->postId = $postId;
		$this->database->table('posts')->where('id', $postId)->update([
            'karma+=' => 1]);
	}
}