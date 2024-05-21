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
	public function getUserLikeStatus($userId, $postId)
    {
        $likeStatus = $this->database->table('rating')
            ->where('id', $userId)
            ->where('post_id', $postId)
            ->fetch();

        return $likeStatus ? $likeStatus->like_status : null;
    }
	public function likePost($userId, $postId)
    {
        $this->database->table('rating')->where('user_id', $userId)->where('post_id', $postId)->delete();
        $this->database->table('rating')->insert([
            'user_id' => $userId,
            'post_id' => $postId,
            'like_status' => 1
        ]);
    }
	public function dislikePost($userId, $postId)
    {
        $this->database->table('rating')->where('id', $userId)->where('post_id', $postId)->delete();
        $this->database->table('rating')->insert([
            'user_id' => $userId,
            'post_id' => $postId,
            'like_status' => -1
        ]);
    }
	public function updateRating(int $postId, int $like) {
		$post = Post::find($postId);
		$post->rating += $like;
		$post->save();
	}
}

