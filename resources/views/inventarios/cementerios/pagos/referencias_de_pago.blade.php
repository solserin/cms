<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap"
    rel="stylesheet">
  <title>Reportes</title>
  <style>
    body {
      font-family: 'Open Sans' !important;
    }


    /*#header,#header section table{
      margin-top: -30px !important;
      width: 100%;
      padding-top: 0px;
    }

    #header section table {
      border-collapse: collapse;
      color: #{{env('TEXTOSCOLOR')}};
    }

    #header section table, #header section table th, #header section table td {
      border-bottom: 1px solid #{{env('MAINCOLOR')}};
    }

     #header section table td {
       padding:10px;
     }

    .logo{
      max-width: 270px;
    }

    h1{
      font-size: .8em;
      line-height: 1.2em !important;
      text-transform: uppercase;
      text-align: right;
      color:  #{{env('TEXTOSCOLOR')}};
    }

    .datos{
      text-align: right !important;
      font-size: .9em;
      line-height: .6em !important;
      text-transform: capitalize !important;
    }



     .data table thead tr th{
        padding: 6px;
     }
    .data table thead tr th{
      font-size: .9em;
      text-align: center;
      color:  #{{env('MAINCOLOR')}};
    }

    .data table tr td{
      text-align: center;
    }

    .data table {
      margin-top: 30px !important;
      width: 100%;
      border-collapse: collapse;
    }

    .data table th,  .data table tr,.data table td {
      border: 1px solid #{{env('SECONDCOLOR')}};
    }
    */
    .pagos .tablas {
      width: 100%;
    }

    .pagos {
      border: 1px dashed #000;
      border-right: none;
      border-left: none;
      padding: 6px 20px 0px 20px;
      min-height: 121mm !important;
      max-height: 121mm !important;
    }

    .logos {
      max-width: 160px;
    }

    .banco {
      display: block;
      margin-left: auto;
    }

    .logo {
      display: block;
      margin-right: auto;
    }

    .concepto {
      font-weight: 500;
      font-size: 1em;
      line-height: .8em;
    }

    .mes {
      font-size: 1.1em;
      font-weight: 550;
      line-height: .5em;
      text-transform: uppercase;
    }

    .venta {
      margin-top: 10px;
      text-align: justify;
    }

    .venta table {
      padding: 1px 0 1px 0;
    }

    .dato {
      font-size: 1em;
    }

    .valor {
      font-size: .9em;
      text-transform: uppercase;
      font-weight: 600;
    }

    .info-header {
      padding: 5px 3px 5px 3px;
      color: #fff;
      margin-top: 3px !important;
      background-color: #b18b1e;
      font-weight: 600;
      font-size: 1em;
      line-height: 1em;
      text-transform: uppercase;
    }

    .bg-dato {
      padding: 0 2px 0 2px;
      background-color: #dae1e7;
    }

    .bg-total {
      padding: 0 4px 0 4px;
      background-color: #FE0000;
      color: #fff;
    }

    .cuentas {
      text-align: right;
      padding: 0px 0 10px 0;
    }

    .santader {
      color: #FE0000;
      font-weight: 600;
    }

    .pendiente {
      color: #FE0000;
    }

    .impuestos {
      color: #FE0000;
    }

    .valor-ojo {
      font-size: 1em;
      text-transform: uppercase;
      font-weight: 600;
    }


    .numero-referencia {
      text-transform: uppercase;
      color: #FE0000;
    }

    .dato-totales {
      font-size: 1.2em;
      line-height: .8em;
    }

    .dato-totales-valor {
      font-size: 1.2em;
      line-height: .8em;
      font-weight: 600;
    }

    .digitos {
      width: 90% !important;
      margin: 0 auto 0 auto;
    }

    .digito {
      padding: 3px 7px 3px 7px;
      border: 1px solid #dae1e7;
      font-size: 1em;
      line-height: 2.3em;
    }

    .barcode div {
      min-height: 30px !important;
    }

    .barcode {
      width: 60%;
      margin: 10px auto 0 auto;
    }

    .ojo {
      font-size: .8em;
      margin-top: 25px;
    }

    .sello {
      margin-top: 40px !important;
      text-align: center;
      margin: 0 auto 0 auto;
      width: 50%;
      font-size: 1em !important;
      line-height: 1.3em !important;
      padding: 0 0 10px 0;
      border-bottom-width: 1px;
      border-bottom-style: solid;
      border-bottom-color: black;
    }


    .pagado {
      color: #22BB33;
    }

    
  </style>
</head>

<body>
  @include('layouts.estilos')
  @foreach ($datos['pagos_programados'] as $pago)
  @php
  //checa cuanto se ha pagado por cada pago programado
  $total_pagado_por_programado=0;
  @endphp
  @foreach ($pago['pagos_realizados'] as $pagado)
  @if ($pagado['status']==1)
  @php
  $total_pagado_por_programado+=$pagado['total'];
  @endphp
  @endif
  @endforeach
  @php
  $restante_pagar_de_este_pago=$pago['total']-$total_pagado_por_programado;
  @endphp


  <div class="pagos relative">
    <table class="tablas">
      <thead>
        <tr>
          <th>
            <img class="logo logos" src="{{asset('images/aeternus/LogoEmp.jpg')}}" alt="">
          </th>
          <th>
            <h1 class="concepto">
              RECIBO DE PAGO DE PLAN FUNERARIO EN CEMENTERIO
            </h1>
            <h2 class="mes">
              @if ($pago['tipo_pagos_id']==1)
              ENGANCHE INICIAL
              @elseif($pago['tipo_pagos_id']==2)
              mensualidad número {{$pago['num_pago']}} / {{mes_from_fecha($pago['fecha_programada'])}}
              @else
              Pago único de liquidación
              @endif
            </h2>
          </th>
          <th>
            <img class="logos banco" src="{{asset('images/santander.png')}}" alt="">
          </th>
        </tr>
      </thead>
    </table>

    <div class="venta">
      <table class="tablas">
        <tbody>
          <tr>
            <td width="70%">
              <span class="dato"> Nombre del Titular:</span> <span class="valor">{{$datos['nombre']}}</span>
            </td>
            <td width="30%" align="right">
              <span class="dato"> Fecha Impresión:</span> <span class="valor">{{fecha_completa()}}</span>
            </td>
          </tr>
        </tbody>
      </table>
      <table class="tablas">
        <tbody>
          <tr>
            <td width="25%">
              <span class="dato"> Núm. Venta (Sistemas): </span> <span class="valor">{{$datos['id']}}</span>
            </td>
            <td width="25%" align="right">
              <span class="dato"> Num. Solicitud: </span> <span class="valor">{{$datos['numero_solicitud']}}</span>
            </td>
            <td width="25%" align="right">
              <span class="dato"> Núm. Convenio: </span> <span class="valor">{{$datos['numero_convenio']}}</span>
            </td>
            <td width="25%" align="right">
              <span class="dato"> Núm. Título: </span> <span class="valor">{{$datos['numero_titulo']}}</span>
            </td>
          </tr>
        </tbody>
      </table>
      <!--
        <table class="tablas">
                <tbody>
                    <tr>
                       <td width="30%">
                          <span class="dato">Tipo de Venta : </span> <span class="valor">uso inmediato</span>
                        </td>
                        <td width="40%" align="center">
                           <span class="dato"> Ubicación: </span> <span class="valor">Terraza 2, Fila A Lote 3</span>
                        </td>
                        <td width="30%" align="right">
                          <span class="dato">Fecha Venta: </span> <span class="valor">10 enero 2020</span>
                        </td>
                    </tr>
                </tbody>
        </table>
      -->
      <h3 class="info-header">Información para el Pago: </h3>
      <table class="tablas">
        <tbody>
          <tr>
            <td width="15%">
              <span class="dato">Núm. Pago : </span> <span class="valor">{{$pago['num_pago']}}</span>
            </td>
            <td width="40%" align="center">
              <span class="bg-dato">
                <span class="dato">Pago puntual antes de: </span> <span
                  class="valor">{{fecha_no_day($pago['fecha_programada'])}}</span>
              </span>
            </td>
            <td width="27%" align="center">
              <span class="dato"> Tipo Pago: </span> <span class="valor">{{$pago['tipo_pago']['tipo']}}</span>
            </td>
            <td width="18%" align="right">
              <span class="dato">Estatus: </span> <span class="valor">

                @if ($restante_pagar_de_este_pago>0)
                <span class="pendiente">Pendiente</span>
                @else
                <span class="pagado">Pagado</span>
                 <span class="watermark watermark-success top-45 right-30 px-5 uppercase size-2 w-normal absolute">
                    pagado
                </span>
                @endif
              </span>
            </td>
          </tr>
        </tbody>
      </table>
      <table class="tablas">
        <tbody>
          <tr>
            <td width="50%">
              <p class="numero-referencia"><span class="dato bg-dato">Número de Referencia</span></p>
              <p class="digitos">
                @for($i = 0; $i < strlen($pago['referencia_pago']); $i++) <span class="digito">
                  {{$pago['referencia_pago'][$i]}}</span>
                  @endfor
              </p>
              <!--<div class="barcode">
                            {!!DNS1D::getBarcodeHTML("02202004020780", "C128")!!}
                        </div>
                      -->
              <!--<p class="sello">
                        sello del banco
                      </p>-->
              <p class="ojo">
                <strong>NOTA</strong>: Por ningun motivo será considerado como comprobante de pago sin el sello del
                banco o el número de operación que resulte de hacer el pago por cualquier otro medio. <strong>El titular
                  deberá guardar los recibos para futuras aclaraciones</strong>.
              </p>

            </td>
            <td width="45%">
              <div class="cuentas"><span class="dato bg-dato">Para pago en sucursales <span
                    class="santader">Santander</span>, </span> <br> <span class="valor-ojo">Número de cuenta:
                  {{$empresa->cuenta}}</span></div>
              <div class="cuentas"><span class="bg-dato">Para transferencias electrónicas</span>, <br> <span
                  class="valor-ojo">Clabe: {{$empresa->clabe}} </span> <br> <span class="bg-dato"> Beneficiario</span>,
                <br> <span class="valor-ojo">{{$empresa->razon_social}} </span></div>
              <!--<div class="cuentas"><span class="dato-totales"> Sub Total</span> <span class="dato-totales-valor">$ 5,000.00 Pesos MXN</span></div>
                           <div class="cuentas"><span class="dato-totales"> IVA</span> <span class="dato-totales-valor">$ 5,000.00 Pesos MXN</span></div>
                           <div class="cuentas"><span class="dato-totales"> Descuento</span> <span class="dato-totales-valor">$ 5,000.00 Pesos MXN</span></div>
                           -->
              <div class="cuentas"><span class="bg-total"><span class="dato-totales"> Total a Pagar</span> <span
                    class="dato-totales-valor">$ {{number_format($pago['total'],2)}} Pesos MXN</span></span> <br><span
                  class="dato">({{numeros_a_letras($pago['total'])}} Pesos MXN, incluye IVA y descuentos
                  aplicados, <span><strong>NO</strong> intereses</span>)</span></div>
            </td>
          </tr>
        </tbody>
      </table>

    </div>

  </div>
  @endforeach

</body>

</html>