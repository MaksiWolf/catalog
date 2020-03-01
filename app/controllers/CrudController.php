<?php

use Phalcon\Http\Request;
use Phalcon\Http\Request\File;

class CrudController extends ControllerBase
{
    public $product;
    public function initialize()
    {
        $this->request = new Request;
        $this->product = new Product;
    }

    public function createProductAction()
    {
        if ($this->request->isPost()) {
            $postarr = $this->request->getPost();

            $this->product->name = $postarr['name'];
            $this->product->description = $postarr['description'];
            $this->product->text = $postarr['text'];

            if ($this->request->hasFiles() == true) {
                $this->product->img = (file_get_contents($this->request->getUploadedFiles()[0]->getTempName ()));
            }
            $this->product->category = $postarr['category'];
            if(!empty($postarr['status'])) {
                $this->product->status = (bool)$postarr['status'];
            } else {
                $this->product->status = false;
            }

            $this->product->save();
        }

    }

    public function readProductAction()
    {
        $res = $this->request->get();
        if($res['type'] == 'по категории'){
            $result = $this->product->find(["category='".$res['name']."'", "status=1"]);
        } else if($res['type'] == 'по имени'){
            $result = $this->product->find(["name='".$res['name']."'", "status=1"]);
        } else {
            $result = $this->product->find();
        }

        $this->view->setVar('vars', $result); //передаём результат во viev
    }

    public function updateProductAction()
    {
        $res = $this->request->getPost();

        if ($this->request->isPost()) {
            $postarr = $this->request->getPost();
            $result = $this->product->find(["name='".$res['name']."'", "limit=1"]);
            if(!empty($result)) {
                $result[0]->name = $postarr['name'];
                $result[0]->description = $postarr['description'];
                $result[0]->text = $postarr['text'];
                if ($this->request->hasFiles() == true) {
                    $result[0]->img = (file_get_contents($this->request->getUploadedFiles()[0]->getTempName ()));
                }
                $result[0]->category = $postarr['category'];
                if(!empty($postarr['status'])) {
                    $result[0]->status = (bool)$postarr['status'];
                } else {
                    $result[0]->status = false;
                }
                $result[0]->save();
            }
        }
    }

    public function delProductAction()
    {
        if ($this->request->isPost()) {
            $res = $this->request->getPost();
            if ($res['type'] == 'продукт по имени') {
                $result = $this->product->find(["name='".$res['name']."'"]);
                if(!empty($result)) {
                    foreach ($result as $el) {
                        $el->delete();
                    }
                }
            } else if($res['type'] == 'удалить категориию') {
                $result = $this->product->find(["category='".$res['name']."'"]);
                if(!empty($result)) {
                    foreach ($result as $el) {
                        $el->category = 'без категории';
                        $el->save();
                    }
                }
            }
        }
    }
}