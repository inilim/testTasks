<?php

Class NewsController
{
   private $objModel;

   public function __construct()
   {
      $this->objModel = new NewsModel();
   }
   
   public function create ()
   {
      if($_SERVER['REQUEST_METHOD'] === 'POST')
      {
         $values = [
            'title' => $_POST['title'],
            'preview' => $_POST['preview'],
            'text' => $_POST['text'],
            'tags' => $_POST['tags'],
            'datePublishing' => $this->objModel->getDatePublishing(),
         ];
         $id = $this->objModel->addNews($values);
         header('Location: /news/id/' . $id); exit();
      }
      else
      {
         (new NewsTemplate)->render('add.twig');
      }
   }

   public function delete (int $id)
   {
      $this->objModel->deleteNews($id);
      echo 'ok';
   }

   public function render (string|int $value)
   {
      if(preg_match('#^[0-9]+$#', $value) && strpos($_SERVER['REQUEST_URI'], '/news/id/') === 0)
      {
         $res = $this->objModel->getNewsById($value);
      }
      else
      {
         $res = $this->objModel->getNewsByTitle($value);
      }
      (new NewsTemplate)->render('news.twig', ['data' => $res]);
   }
}