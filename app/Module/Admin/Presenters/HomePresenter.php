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
	
	public function renderDefault(int $postId): void { 
		$this->template->posts = $this->facade
		   ->getPublicArticles()
		   ->limit(5);
	}
	public function actionShow(int $postId) {
	    $this->flashMessage('Nemáš právo vidět archived, kámo !');
		$this->redirect('Homepage:');
	}

}

