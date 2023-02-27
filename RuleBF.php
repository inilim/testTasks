<?php

/**
 * Единые правила для бекенда и фронта
 */
Class RuleBF
{
   public array $front = [];
   public array $back = [];

   /**
    * name="value"
    *
    * @var string
    */
   private string $name = '';

   static ?RuleBF $obj = null;

   function __construct()
   {
      self::$obj = $this;
   }

   public function returnRulesBack (string $name, bool $rArray):string|array
   {
      $this->name = $this->strtolower($name);
      unset($name);

      if(!isset($this->back[ $this->name ])) return $rArray ? [] : '';

      return $this->formationBack($rArray);
   }

   public function returnRulesFront (string $name):string
   {
      $this->name = $this->strtolower($name);
      unset($name);

      if(!isset($this->front[ $this->name ])) return '';

      return $this->formationFront();
   }

   private function strtolower (string $value):string
   {
      return mb_strtolower($value, 'UTF-8');
   }

   private function formationFront ():string
   {
      $str = [];
      $str[] = 'name="' . $this->name . '"';

      foreach($this->front[ $this->name ] as $rule => $value)
      {
         $str[] = str_replace('=""', '', ($rule . '="' . $value . '"') );
      }

      return implode(' ', $str);
   }

   private function formationBack (bool $rArray):string|array
   {
      $arr = [];
      foreach($this->back[ $this->name ] as $rule => $value)
      {
         $arr[] = trim( ($rule . ':' . $value), ':');
      }

      return $rArray ? $arr : implode('|', $arr);
   }

   static function setDataRuleBack (array $data):void
   {
      if(is_null(self::$obj)) new RuleBF;
      self::$obj->back = $data;
   }

   static function setDataRuleFront (array $data):void
   {
      if(is_null(self::$obj)) new RuleBF;
      self::$obj->front = $data;
   }

   static function getRulesBack (string $name, bool $rArray = false):string|array
   {
      if(is_null(self::$obj)) new RuleBF;
      return self::$obj->returnRulesBack($name, $rArray);
   }

   static function getRulesFront (string $name):string
   {
      if(is_null(self::$obj)) new RuleBF;
      return self::$obj->returnRulesFront($name);
   }
}



# example

RuleBF::setDataRuleFront([
   'title_site' => [
      'required' => '',
      'minlength' => 1,
      'maxlength' => 100,
      'autocomplete' => 'off'
   ],
]);

RuleBF::setDataRuleBack([
   'title_site' => [
      'required' => '',
      'min' => 1,
      'max' => 100,
   ],
]);


print_r(RuleBF::getRulesBack('title_site', true));
echo PHP_EOL;
print_r(RuleBF::getRulesFront('title_site'));
