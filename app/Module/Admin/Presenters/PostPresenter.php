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
		$post = $this->facade->getPostbyId($id);
		$this->facade->addView($postId);
	if (!$post) {
		$this->error('Stránka nebyla nalezena');
	}

	$this->template->post = $post;	
	
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

