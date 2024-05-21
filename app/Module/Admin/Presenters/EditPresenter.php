<?php
namespace App\Module\Admin\Presenters;

use Nette;
use App\Model\PostFacade;
use Nette\Application\UI\Form;

final class EditPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private PostFacade $facade
	) {
	}

protected function createComponentPostForm(): Form
{
	$form = new Form;
	$form->addText('title', 'Titulek:')
		->setRequired();
	$form->addTextArea('content', 'Obsah:')
		->setRequired();
	$statuses = [
            'OPEN' => 'OTEVŘENÝ',
            'CLOSED' => 'UZAVŘENÝ',
            'ARCHIVED' => 'ARCHIVOVANÝ'
        ];
    $form->addSelect('status', 'Stav:', $statuses)
            ->setDefaultValue('OPEN');
	$form->addUpload('image','Soubor')
	     ->setRequired()
	     ->addRule(Form::IMAGE, 'Thumbnail must be JPEG, PNG or GIF');
	$form->addSubmit('send', 'Uložit a publikovat');
	$form->onSuccess[] = $this->postFormSucceeded(...);
	return $form;
}
public function postFormSucceeded($form, $data): void {
	$postId = $this->getParameter('postId');
	if (filesize($data->image) > 0) {
		if ($data->image->isOk()) {
			$data->image->move('upload/' . $data->image->getSanitizedName());
			$data['image'] = ('upload/' . $data->image->getSanitizedName());
		} else {
		   $this->flashMessage('Soubor nebyl přidán','Failed');
		}
		if ($postId) {
			$post = $this->facade->editPost($postId, (array) $data);
		} else {
			$post = $this->facade->insertPost((array) $data);
		}
	}
$this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
$this->redirect('Post:show', $post->id);
}
private function postFormSucceed(array $data): void { 
$postId = $this->getParameter('postId');
if ($postId) {
		$post = $this->database
			->table('posts')
			->get($postId);
		$post->update($data);

} else {
		$post = $this->database
			->table('posts')
			->insert($data);
}
$this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
$this->redirect('Post:show', $post->id);
}
public function handleDeleteImage(int $postId) {
        
	$post = $this->postFacade->getPostById($postId);
	
			if($post) {
				unlink($post['image']);
			  
	$data['image'] = null;
			   $this->facade->editPost($postId, $data);
			   $this->flashMessage('Obrázek k příspěvku byl smazán');
		   }
	
	
		   
		}
public function renderEdit(int $postId): void
{
	$post = $this->postFacade
		->table('posts')
		->get($postId);

	if (!$post) {
		$this->error('Post not found');
	}

	$this->getComponent('postForm')
		->setDefaults($post->toArray());
}
}