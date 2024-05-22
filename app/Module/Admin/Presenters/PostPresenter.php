<?php
namespace App\Module\Admin\Presenters;


use Nette;
use App\Model\PostFacade;
use Nette\Application\UI\Form;
final class PostPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private PostFacade $facade,
	) {
	}

    public function renderShow($id, $postId): void
    {
        $post = $this->facade->getPostById($id);
        $this->facade->addView($postId);
        
        if (!$post) {
            $this->error('Stránka nebyla nalezena');
        }

        $userId = $this->getUser()->getId();
        $likeStatus = $this->facade->getUserLikeStatus($userId, $postId);

        $this->template->post = $post;
        $this->template->likeStatus = $likeStatus;

        if ($likeStatus !== null) {
            if ($likeStatus == 1) {
                $this->template->showDislikeButton = true;
            } elseif ($likeStatus == -1) {
                $this->template->showLikeButton = true;
            }
        } else {
            $this->template->showLikeButton = true;
            $this->template->showDislikeButton = true;
        }
    }
    public function renderDefault($postId)
    {
        $userId = $this->getUser()->getId();
        $likeStatus = $this->facade->getUserLikeStatus($userId, $postId);

        $this->template->postId = $postId;
        $this->template->likeStatus = $likeStatus;

        // Debug output to verify the value of likeStatus
        bdump($likeStatus, 'likeStatus');
    }

    protected function createComponentLikeForm(): Form
    {
        $form = new Form;
        $form->addHidden('postId');
        $form->addSubmit('like', 'Líbí se mi');
        $form->onSuccess[] = [$this, 'likeFormSucceeded'];
        return $form;
    }

    protected function createComponentDislikeForm(): Form
    {
        $form = new Form;
        $form->addHidden('postId');
        $form->addSubmit('dislike', 'Nelíbí se mi');
        $form->onSuccess[] = [$this, 'dislikeFormSucceeded'];
        return $form;
    }

    public function likeFormSucceeded(Form $form, \stdClass $values): void
    {
        $userId = $this->getUser()->getId();
        $postId = $values->postId;

        $this->facade->likePost($userId, $postId);

        $this->redirect('this', ['postId' => $postId]);
    }

    public function dislikeFormSucceeded(Form $form, \stdClass $values): void
    {
        $userId = $this->getUser()->getId();
        $postId = $values->postId;

        $this->facade->dislikePost($userId, $postId);

        $this->redirect('this', ['postId' => $postId]);
    }
    protected function createComponentCommentForm(): Form
{
	$form = new Form; 

	$form->addText('name', 'Jméno:')
		->setRequired();

	$form->addEmail('email', 'E-mail:');

	$form->addTextArea('content', 'Komentář:')
		->setRequired();

	$form->addSubmit('send', 'Publikovat komentář');

	$form->onSuccess[] = $this->commentFormSucceeded(...);
	return $form;
}
	private function commentFormSucceeded(\stdClass $data): void {
	$postId = $this->getParameter('postId');

	$this->flashMessage('Děkuji za komentář', 'success');
	$this->redirect('this');
}
	public function getComments(int $postId) {
        $this->template->comment = $this->facade->getCommentById($postId);
		 $this->table('comment');
		 $this->get($postId);
	}
	public function addViews(int $postId, int $views) {
		$this->template->views = $this->facade->addViews($postId, $views);
		
		 $this->table('posts');
		 $this->count($views);
		 $this->update($views);
	}
    public function show($postId)
    {
        return $this->facade->showPost($postId);
    }
	public function handlehandleLike() {
		if ($this->isUserLoggedIn()) {
			$this->updateRating($postId, $like);
		}
		else {
			throw new Exception('Uživatel není přihlášen.');
		}
	}
}

