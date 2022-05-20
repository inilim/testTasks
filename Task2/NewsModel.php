<?php
use \Envms\FluentPDO\Query as Query;

class NewsModel
{
   private $id;
   private $title;
   private $preview;
   private $text;
   private $tags;
   private $datePublishing;
   private $query;

   public function __construct()
   {
      # Мне не понятно зачем нужно устанавливать в конструкторе id, если он генерируется в базе через autoincrement
      $this->id = 0;
      $this->datePublishing = date('d.m.Y H:i:s');
      # Подключение
      $pdo = new PDO('sqlite:db.sqlite');
      $this->query = new Query($pdo);
   }

   public function addNews (array $values)
   {
      $res = $this->query->insertInto('news')->values($values)->execute();
      if($res !== false)
      {
         return $res;
      }
   }

   public function deleteNews (int $id)
   {
      $res = $this->query->deleteFrom('news')->where('id', $id)->execute();
      if($res !== false)
      {
         return $res;
      }
   }

   public function getNewsById (int $id)
   {
      if(!($this->query instanceof Query))
      {
         return [];
      }
      $res = $this->query->from('news')->where('id', $id)->fetch();
      if($res !== false)
      {
         return $res;
      }
      return [];
   }

   public function getNewsByTitle (string $title)
   {
      if(!($this->query instanceof Query))
      {
         return [];
      }
      $res = $this->query->from('news')->where('title', $title)->fetch();
      if($res !== false)
      {
         return $res;
      }
      return [];
   }

   public function getId()
   {
      return $this->id;
   }

   public function getDatePublishing()
   {
      return $this->datePublishing;
   }
   
   public function getTitle()
   {
      return $this->title;
   }
   
   public function setTitle(string $title)
   {
      $this->title = $title;
   }
   
   public function getPreview()
   {
      return $this->preview;
   }
   
   public function setPreview(string $preview)
   {
      $this->preview = $preview;
   }

   public function getText()
   {
      return $this->text;
   }
   
   public function setText(string $text)
   {
      $this->text = $text;
   }

   public function getTags()
   {
      return $this->tags;
   }
   
   public function setTags(array $tags)
   {
      $this->tags = $tags;
   }
}