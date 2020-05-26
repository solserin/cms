<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- <link rel="icon" href="<%= BASE_URL %>favicon.ico"> -->

  <title>SIIGA | Aeternus Funerales</title>
  <meta name="description"
    content="Sistema Integral de Información y Gerencia Administrativa de Funeraria Aeternus Mazatlán Sinaloa. | SOLSERIN.">
  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset(('css/main.css')) }}">
  <link rel="stylesheet" href="{{ asset(('css/iconfont.css')) }}">
  <link rel="stylesheet" href="{{ asset(('css/material-icons/material-icons.css')) }}">
  <link rel="stylesheet" href="{{ asset(('css/vuesax.css')) }}">
  <link rel="stylesheet" href="{{ asset(('css/prism-tomorrow.css')) }}">
  <link rel="stylesheet" href="{{ asset(('css/app.css')) }}">

  <!-- Favicon -->
  <link rel="shortcut icon" href="{{ asset('images/logo/icono.png') }}">
</head>

<body>
  <noscript>
    <strong>We're sorry but Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template doesn't work properly without
      JavaScript enabled. Please enable it to continue.</strong>
  </noscript>
  <div id="app">
  </div>

  <!-- <script src="js/app.js"></script> -->
  <!-- <script src="{{ asset(('js/app.js')) }}"></script> -->
  <script src="{{ mix('js/app.js') }}"></script>

</body>

</html>