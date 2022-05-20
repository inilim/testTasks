<?php
Class Counter
{
   static $operations = [
      'activation' => 0,
      'overload' => 0,
      'isolation' => 0,
      'extinction' => 0,
   ];
}
# создаем матрицу со случайными состояниями
function createMx (int $m, int $n):array
{
   $mn = [];
   foreach(range(1,$m) as $r)
   {
      foreach(range(1,$n) as $c)
      {
         # 0 Мертвая - 1 Активная
         $mn[$r][$c] = mt_rand(0,1);
      }
   }
   return $mn;
}
function viewPrint (array $mx):string
{
   $str = '';
   foreach($mx as $r)
   {
      $str .= implode(' ', $r) . PHP_EOL;
   }
   $str .= '------------------' . PHP_EOL;
   return $str;
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
      Counter::$operations['activation']++;
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
      Counter::$operations['overload']++;
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
      Counter::$operations['isolation']++;
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
      Counter::$operations['extinction']++;
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
function randomCell (array $mx):array
{
   return [
      'rows' => mt_rand(1, sizeof($mx)),
      'col' => mt_rand(1, sizeof(current($mx))),
   ];
}
# последовательно берем клетки для операции
function counterCell (?array $current, int $mrows, int $mcol):array
{
   if($current === null)
   {
      $current = [
         'rows' => 1,
         'col' => 1,
      ];
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

   if($current['rows'] === $mrows && $current['col'] === $mcol)
   {
      $current = [
         'rows' => 1,
         'col' => 1,
      ];
   }
   return $current;
}

$rows = 15;
$col = 15;

$cntCell = $rows * $col;

$mx = createMx($rows,$col);

print_r(viewPrint($mx));

$tick = 0;
$noChange = 0;
$cntChange = 0;
$dblMx = $mx;

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

   #print_r(Counter::$operations);

   #exit();

   if($dblMx === $mx)
   {
      $noChange++;
      if($noChange === $cntCell)
      {
         echo 'Клетки не активны. Без изменений.' . PHP_EOL;
         break;
      }
   }
   else
   {
      $dblMx = $mx;
      $cntChange++;
      $noChange = 0;
   }
}


echo 'Количество итераций: ' . $tick . PHP_EOL;
echo 'Изменений: ' . $cntChange . PHP_EOL;
print_r(viewPrint($mx));
print_r(Counter::$operations);