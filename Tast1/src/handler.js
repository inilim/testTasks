
jQuery(document).ready(function(e)
{
   function defineElements ()
   {
      // Дата
      let dateNow = new Date();
      $('div.date').text(dateNow.getDate() + '.' + (dateNow.getMonth()+1));
      
      return {
         'pogoda': {
            'temp_static': $('div.temp_static'),
            'temp': $('div.temp'),
            'msg': $('div.msg'),
            'data': null
         },
         'currency': {
            'items': $('div.currency'),
            'data': null,
         }
      };
   }
   function getCurrency ()
   {
      return new Promise(function(resolve) {
         $.ajax({
            type: 'GET',
            // url: 'https://www.cbr.ru/scripts/XML_daily.asp'
            url: 'https://www.cbr-xml-daily.ru/daily_json.js'
         }).done(function(data) {
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
         $.ajax({
            type: 'GET',
            url: 'https://api.openweathermap.org/data/2.5/weather?q=Moscow&lang=ru&units=metric&appid=465f9fb9b3bdaa7a6a84070021d7e841'
         }).done(function(data) {
            //console.log(data);
            if (typeof data['weather'] != "undefined") 
            {
               elems.pogoda.data = data;
               return resolve(true);
            }
         });
      });
   }
   function getPogodaAPI2 ()
   {
      return new Promise(function(resolve) {
         $.ajax({
            type: 'GET',
            url: 'http://api.weatherapi.com/v1/current.json?q=Moscow&lang=ru&key=39da7f4669ae417eafb174613221905'
         }).done(function(data) {
            //console.log(data);
            if (typeof data['current'] != "undefined") 
            {
               elems.pogoda.data = data;
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
         $.each(elems.currency.items, function(k, v)
         {
            
            if (v.getAttribute('id') == null || v.getAttribute('id') == '') 
            {
               v.remove();
               return;
            }

            if (typeof elems.currency.data[v.getAttribute('id')] == "undefined") 
            {
               v.remove();
               return;
            }

            let tmp = elems.currency.data[v.getAttribute('id')];
            v.querySelector('div.change').innerText = '1 ' + tmp.CharCode + ' = ' + tmp.Value + ' RUB';
            v.querySelector('div.name').innerText = tmp.Name;
         });
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
         elems.pogoda.temp_static.text(Math.round(elems.pogoda.data.main.temp) + '°');
         elems.pogoda.temp.text(Math.round(elems.pogoda.data.main.feels_like) + '°');
         elems.pogoda.msg.text(elems.pogoda.data.weather[0].description);
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
         elems.pogoda.temp_static.text(Math.round(elems.pogoda.data.current.temp_c) + '°');
         elems.pogoda.temp.text(Math.round(elems.pogoda.data.current.feelslike_c) + '°');
         elems.pogoda.msg.text(elems.pogoda.data.current.condition.text);
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

   $('div.update').on('click', function()
   {
      console.log('update');
      var elems = defineElements();
      start();
   });
   
   var elems = defineElements();
   start();
});