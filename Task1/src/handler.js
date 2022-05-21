
document.addEventListener("DOMContentLoaded", function()
{
   function defineElements ()
   {
      // Дата
      let dateNow = new Date();
      document.querySelector('div.date').innerText = dateNow.getDate() + '.' + (dateNow.getMonth()+1);
      
      return {
         'pogoda': {
            'temp_static': document.querySelector('div.temp_static'),
            'temp': document.querySelector('div.temp'),
            'msg': document.querySelector('div.msg'),
            'data': null
         },
         'currency': {
            'items': document.querySelectorAll('div.currency'),
            'data': null,
         }
      };
   }
   function ajaxGet (url)
   {
      return new Promise(function(resolve) {
         let xhttp = new XMLHttpRequest();
         xhttp.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200)
            {
               return resolve(this.responseText);
            }
         }
         xhttp.open('GET', url, true);
         xhttp.send();
      });
   }
   function getCurrency ()
   {
      return new Promise(function(resolve) {
         //ajaxGet('https://www.cbr.ru/scripts/XML_daily.asp')
         ajaxGet('https://www.cbr-xml-daily.ru/daily_json.js')
         .then(function(data)
         {
            let arr = JSON.parse(data);
            if (typeof arr['Valute'] != "undefined") 
            {
               elems.currency.data = arr['Valute'];
               return resolve(true);
            }
         });
      });
   }
   function getPogodaAPI1 ()
   {
      return new Promise(function(resolve) {
         ajaxGet('https://api.openweathermap.org/data/2.5/weather?q=Moscow&lang=ru&units=metric&appid=465f9fb9b3bdaa7a6a84070021d7e841')
         .then(function(data)
         {
            let arr = JSON.parse(data);
            if (typeof arr['weather'] != "undefined") 
            {
               elems.pogoda.data = arr;
               return resolve(true);
            }
         });
      });
   }
   function getPogodaAPI2 ()
   {
      return new Promise(function(resolve) {
         ajaxGet('http://api.weatherapi.com/v1/current.json?q=Moscow&lang=ru&key=39da7f4669ae417eafb174613221905')
         .then(function(data)
         {
            let arr = JSON.parse(data);
            if (typeof arr['current'] != "undefined") 
            {
               elems.pogoda.data = arr;
               return resolve(true);
            }
         });
      });
   }
   function setResultCurrency ()
   {
      // Валюты
      if(elems.currency.data !== null)
      {
         for(let i = 0; i < elems.currency.items.length; i++)
         {
            let item = elems.currency.items[i];

            if(item.getAttribute('id') == null || item.getAttribute('id') == '')
            {
               item.remove();
               return;
            }

            if(typeof elems.currency.data[item.getAttribute('id')] == "undefined") 
            {
               item.remove();
               return;
            }

            let tmp = elems.currency.data[item.getAttribute('id')];
            item.querySelector('div.change').innerText = '1 ' + tmp.CharCode + ' = ' + tmp.Value + ' RUB';
            item.querySelector('div.name').innerText = tmp.Name;
         }
      }
      else
      {
         console.log('currency.data');
      }
   }
   function setResultPogoraAPI1 ()
   {
      // Погода
      if(elems.pogoda.data !== null)
      {
         elems.pogoda.temp_static.innerText = Math.round(elems.pogoda.data.main.temp) + '°';
         elems.pogoda.temp.innerText = Math.round(elems.pogoda.data.main.feels_like) + '°';
         elems.pogoda.msg.innerText = elems.pogoda.data.weather[0].description;
      }
      else
      {
         console.log('setResultPogoraAPI1');
      }
   }
   function setResultPogoraAPI2 ()
   {
      // Погода
      if(elems.pogoda.data !== null)
      {
         elems.pogoda.temp_static.innerText = Math.round(elems.pogoda.data.current.temp_c) + '°';
         elems.pogoda.temp.innerText = Math.round(elems.pogoda.data.current.feelslike_c) + '°';
         elems.pogoda.msg.innerText = elems.pogoda.data.current.condition.text;
      }
      else
      {
         console.log('setResultPogoraAPI2');
      }
   }
   function start ()
   {
      getCurrency().then(function()
      {
         setResultCurrency();
      });
      // Это API перестало выдавать данные во время разработки, пришлось прикрутить второй вариант.
      getPogodaAPI1().then(function()
      {
         setResultPogoraAPI1();
      })
      getPogodaAPI2().then(function()
      {
         setResultPogoraAPI2();
      })
   }

   document.querySelector('div.update')
   .addEventListener('click', function()
   {
      console.log('update');
      var elems = defineElements();
      start();
   });
   
   var elems = defineElements();
   start();
});