<?php

namespace App\Module\Admin\Presenters;


use App\Model\PostFacade;
use Nette;

final class HomePresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private PostFacade $facade,
	) {
	}
	
	public function renderDefault(int $page = 1): void { 
		$this->template->posts = $this->facade
		   ->getPublicArticles()
		   ->limit(5);
		   $articlesCount = $this->facade->getPublishedArticlesCount();
		   $paginator = new Nette\Utils\Paginator;
		   $paginator->setItemCount($articlesCount); 
		   $paginator->setItemsPerPage(10); 
		   $paginator->setPage($page);
   
		   $articles = $this->facade->getPublicArticles($paginator->getLength(), $paginator->getOffset());
		   $this->template->paginator = $paginator;
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
	public function actionShow(int $postId) {
	    $this->flashMessage('Nemáš právo vidět archived, kámo !');
		$this->redirect('Homepage:');
	}
	public function handleLike(int $postId, int $like) {
		if ($this->isUserLoggedIn()) {
			$this->updateRating($postId, $like);
		}
		else {
			throw new Exception('Uživatel není přihlášen.');
		}
	}
}
