<?php
use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

Class NewsTemplate
{
   public function render (string $template, array $vars = []):void
   {
      $loader = new FilesystemLoader('templates');
      $twig = new Environment($loader, [
         #'cache' => 'templates/cache',
         #'debug' => true,
      ]);

      echo $twig->render($template, $vars);
   }
}