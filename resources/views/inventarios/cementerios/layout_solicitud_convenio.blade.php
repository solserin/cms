<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">
  <title>Reportes</title>
  <style>
    body{
      font-family: 'Open Sans' !important;
      color: #000 !important;
    }

    #header,#header section table{
      width: 100%;
      padding-top: 0px;
    }

    #header section table {
      border-collapse: collapse;
    }


    .logo{
      max-width: 100% !important;
    }

    h1{
      font-size: 1.1em;
      line-height: .8em !important;
      text-transform: uppercase;
      text-align: center;
    }

    .datos{
      text-align: center !important;
      font-size: .9em;
      line-height: 0.5em !important;
      text-transform: uppercase !important;
    }

  </style>
</head>
<body>
  <header id="header">
    <section>
      <table>
        <tr>
          <td style="width:20%;">
          <img src="{{public_path(env('LOGOJPG'))}}" alt="" class="logo">
          </td>
          <td style="width:60%;">
            <h1>
             <!-- @yield('title')-->
               {{$empresa->razon_social}}
            </h1>
            <p class="datos">
              r.f.c. {{$empresa->rfc}}
            </p>
            <p class="datos">
              {{strtolower($empresa->calle)}} Núm. Ext {{$empresa->num_ext}}
            </p>
            <p class="datos">
             Col. {{strtolower($empresa->colonia)}}. cp. {{$empresa->cp}}.  {{$empresa->ciudad}}  {{$empresa->estado}}
            </p>
            <p class="datos">
              Tel. {{$empresa->telefono}}, fax {{$empresa->fax}}
            </p>
          </td>
           <td style="width:20%;">
          <img src="{{public_path(env('LOGOJPG'))}}" alt="" class="logo">
          </td>
        </tr>
      </table>
    </section>
  </header>
  @yield('contenido')
</body>
</html>