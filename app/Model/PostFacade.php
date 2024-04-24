<?php
namespace App\Model;

use Nette;

final class PostFacade
{
	public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}

	public function getPublicArticles()
	{
		return $this->database
			->table('posts')
			->where('created_at < ', new \DateTime)
			->order('created_at DESC');
	}
	public function getPublishedArticlesCount(): int
	{
		return $this->database->fetchField('SELECT COUNT(*) FROM posts WHERE created_at < ?', new \DateTime);
	}
	public function addComments(int $postId, \stdClass $data) {
        $this->database->table('comments')->insert([
            'post_id' => $postId,
            'name' => $data->name,
            'email' => $data->email,
            'content' => $data->content,
        ]);
    }
	public function getComments(int $postId) {
		$post = $this->getPostbyId($postId);
		$comments = $post->related('comments');
	}
	public function getPostbyId($postId) {
		return $this->database->table('posts')->get($postId);
	}
	public function editPost(int $postId, array $data) {
		 $post = $this->database
			->table('posts')
			->get($postId);
		$post->update($data);
	}
	public function insertPost(array $data) {
		$this->template->post = $this->facade;
		 $this->table('posts');
		 $this->get($data);
	}
    public function addView($postId)
    {
        $this->database->table('posts')->wherePrimary($postId)->update([
            'views' => new \Nette\Database\SqlLiteral('views + 1')
        ]);
    }
}

