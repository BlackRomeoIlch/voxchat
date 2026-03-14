<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Voxchat\Controllers\Admin;

use Modules\Voxchat\Mappers\Chat as ChatMapper;

class Index extends \Ilch\Controller\Admin
{
    public function init()
    {
        $action = $this->getRequest()->getActionName();
        $items  = [
            [
                'name'   => 'manage',
                'active' => ($action !== 'reset'),
                'icon'   => 'fa-solid fa-table-list',
                'url'    => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'index'])
            ],
            [
                'name'   => 'reset',
                'active' => ($action === 'reset'),
                'icon'   => 'fa-solid fa-trash-can',
                'url'    => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'reset'])
            ],
            [
                'name'   => 'settings',
                'active' => false,
                'icon'   => 'fa-solid fa-gears',
                'url'    => $this->getLayout()->getUrl(['controller' => 'settings', 'action' => 'index'])
            ],
        ];

        $this->getLayout()->addMenu('menuIrc', $items);
    }

    public function indexAction()
    {
        $chatMapper = new ChatMapper();
        $pagination = new \Ilch\Pagination();
        $pagination->setRowsPerPage(30);
        $pagination->setPage($this->getRequest()->getParam('page'));

        $this->getLayout()->getAdminHmenu()
            ->add($this->getTranslator()->trans('menuIrc'), ['controller' => 'index', 'action' => 'index'])
            ->add($this->getTranslator()->trans('manage'),  ['action' => 'index']);

        if ($this->getRequest()->getPost('action') === 'delete'
            && $this->getRequest()->getPost('check_entries')
        ) {
            foreach ($this->getRequest()->getPost('check_entries') as $id) {
                $chatMapper->delete((int)$id);
            }
            $this->addMessage('deleteSuccess');
        }

        $userMapper = new \Modules\User\Mappers\User();
        $messages   = $chatMapper->checkDB() ? $chatMapper->getAllMessages($pagination) : [];

        $this->getView()
            ->set('messages',   $messages)
            ->set('userMapper', $userMapper)
            ->set('pagination', $pagination);
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isSecure()) {
            (new ChatMapper())->delete((int)$this->getRequest()->getParam('id'));
            $this->addMessage('deleteSuccess');
        }
        $this->redirect(['action' => 'index']);
    }

    public function resetAction()
    {
        if ($this->getRequest()->isSecure()) {
            (new ChatMapper())->truncate();
            $this->addMessage('deleteSuccess');
            $this->redirect(['action' => 'index']);
        }
    }
}
