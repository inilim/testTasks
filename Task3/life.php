<?php
function createMx (int $m, int $n)
{
   $mn = [];
   $print = '';
   foreach(range(1,$m) as $r)
   {
      foreach(range(1,$n) as $c)
      {
         # 0 Мертвая - 1 Активная
         $mn[$r][$c] = mt_rand(0,1);
      }
      $print .= implode(' ', $mn[$r]) . PHP_EOL;
   }
   print_r($print);
   echo '------------------' . PHP_EOL;
   return $mn;
}
function createDeathMx (int $m, int $n)
{
   $mn = [];
   foreach(range(1,$m) as $r)
   {
      foreach(range(1,$n) as $c)
      {
         $mn[$r][$c] = 0;
      }
   }
   return $mn;
}
# активация
function activation (array $c, array &$mx)
{
   # Только мертвая клетка
   if($mx[$c['rows']][$c['col']] === 1)
   {
      return;
   }
   $neighbors = defineNeighbors($c['rows'], $c['col'], $mx);
   $activeNeighbors = array_sum($neighbors);
   # мертвая клетка, у которой ровно три активных соседа, становится активной в следующем состоянии поля
   if($activeNeighbors === 3)
   {
      $mx[$c['rows']][$c['col']] = 1;
   }
}
# перезагрузка
function overload (array $c, array &$mx)
{
   # Только живая клетка
   if($mx[$c['rows']][$c['col']] === 0)
   {
      return;
   }
   $neighbors = defineNeighbors($c['rows'], $c['col'], $mx);
   $activeNeighbors = array_sum($neighbors);
   # активная клетка, у которой активных соседей четыре или больше, «умрет» в следующем состоянии поля
   if($activeNeighbors >= 4)
   {
      $mx[$c['rows']][$c['col']] = 0;
   }
}
# изоляция
function isolation (array $c, array &$mx)
{
   # Только живая клетка
   if($mx[$c['rows']][$c['col']] === 0)
   {
      return;
   }
   $neighbors = defineNeighbors($c['rows'], $c['col'], $mx);
   $activeNeighbors = array_sum($neighbors);
   # Активная клетка, у которой активных соседей один или меньше, «умрет» в следующем состоянии поля.
   if($activeNeighbors <= 1)
   {
      $mx[$c['rows']][$c['col']] = 0;
   }
}
# вымирание
function extinction (array $c, array &$mx)
{
   # Только живая клетка
   if($mx[$c['rows']][$c['col']] === 0)
   {
      return;
   }
   $neighbors = defineNeighbors($c['rows'], $c['col'], $mx);
   $activeNeighbors = array_sum($neighbors);
   # Активная клетка останется такой, только если у неё ровно 2 или 3 активных соседа, иначе «умрет» в следующем состоянии поля
   if( !($activeNeighbors === 2 || $activeNeighbors === 3) )
   {
      $mx[$c['rows']][$c['col']] = 0;
   }
}
# определить соседей
function defineNeighbors (int $r, int $c, array $mx):array
{
   $res = [];
   foreach($mx as $kr => $vr)
   {
      if($kr === ($r-1) || $kr === $r || $kr === ($r+1))
      {
         foreach($vr as $kc => $neighbor)
         {
            if($kc === ($c-1) || $kc === ($c+1))
            {
               $res[] = $neighbor;
            }
            if($kr !== $r && $kc === $c)
            {
               $res[] = $neighbor;
            }
         }
      }
   }
   return $res;
}
# выбрать случайную клетку для операции
function randomCell (array $mx)
{
   return [
      'rows' => mt_rand(1, sizeof($mx)),
      'col' => mt_rand(1, sizeof(current($mx))),
   ];
}
# последовательно берем клетку для операции
function counterCell (?array $current, int $mrows, int $mcol)
{
   if($current === null)
   {
      $current = [
         'rows' => 1,
         'col' => 1,
      ];
      print_r($current);
      return $current;
   }
   if($current['col'] === $mcol)
   {
      if($current['rows'] !== $mrows)
      {
         $current['rows']++;
         $current['col'] = 1;
      }
   }
   else
   {
      $current['col']++;
   }
   print_r($current);
   return $current;
}

$rows = 5;
$col = 5;

$mx = createMx($rows,$col);
$dmx = createDeathMx($rows,$col);
$tick = 0;


while(1)
{
   $tick++;

   $c = counterCell($c ?? null, $rows, $col);
   #$c = randomCell($mx);
   activation($c, $mx);

   $c = counterCell($c, $rows, $col);
   #$c = randomCell($mx);
   overload($c, $mx);

   $c = counterCell($c, $rows, $col);
   #$c = randomCell($mx);
   isolation($c, $mx);

   $c = counterCell($c, $rows, $col);
   #$c = randomCell($mx);
   extinction($c, $mx);

   if($dmx === $mx) break;

}


echo 'Количество итераций: ' . $tick . PHP_EOL;
#print_r($deathMx);