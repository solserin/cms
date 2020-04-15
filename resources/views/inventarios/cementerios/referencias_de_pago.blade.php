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
   .pagos .tablas{
       width: 100%;
   }
   .pagos{
       border: 1px dashed #000;
       border-right: none;
       border-left: none;
       padding: 20px;
   }
   .logos{
       max-width: 180px;
   }
   .banco{
        display: block;
  margin-left: auto;
   }
   .logo{
        display: block;
  margin-right: auto;
   }

   .concepto{
     font-weight: 550;
       font-size: 1.1em;
       line-height: .8em;
   }
   .mes{
       font-size: 1em;
       font-weight: 550;
       line-height: .5em;
   }

   .venta{
       margin-top: 10px;
       text-align: justify;
   }
   .venta table{
       padding: 1px 0 1px 0;
   }

   .dato{
       font-size: 1em;
   }
   .valor{
       font-size: .9em;
       text-transform: uppercase;
       font-weight: 600;
   }

   .info-header{
     padding: 5px 3px 5px 3px;
     color: #fff;
     background-color: #b18b1e;
     font-weight: 600;
     font-size: 1em;
     line-height: 1em;
     text-transform: uppercase;
   }

   .bg-dato{
     padding: 0 2px 0 2px;
     background-color: #dae1e7;
   }

   .bg-total{
     padding: 0 4px 0 4px;
     background-color: #FE0000;
     color: #fff;
   }

   .cuentas{
     text-align: right;
     padding: 5px 0 5px 0;
   }

   .santader{
     color: #FE0000;
     font-weight: 600;
   }

   .valor-ojo{
       font-size: 1em;
       text-transform: uppercase;
       font-weight: 600;
   }


   .numero-referencia{
     margin-top:-130px !important;
      color: #FE0000;
   }

   .dato-totales{
     font-size: 1.2em;
     line-height: .8em;
   }
   .dato-totales-valor{
     font-size: 1.2em;
     line-height: .8em;
     font-weight: 600;
   }

   .digito{
     padding: 3px 7px 3px 7px;
     border: 1px solid #dae1e7;
     font-size: 1em;
     line-height: 2.3em;
   }
  </style>
</head>
<body>
    <div class="pagos">
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
                        <td width="100%">
                    <span class="dato"> Nombre del Titular:</span> <span class="valor">Hector Raul Cruz Perez</span>
                        </td>
                    </tr>
                </tbody>
        </table>
         <table class="tablas">
                <tbody>
                    <tr>
                       <td width="25%">
                          <span class="dato"> Núm. Venta (Sistemas): </span> <span class="valor">1500</span>
                        </td>
                        <td width="25%" align="right">
                           <span class="dato"> Num. Solicitud: </span> <span class="valor">122</span>
                        </td>
                        <td width="25%" align="right">
                          <span class="dato"> Núm. Convenio: </span> <span class="valor">1500</span>
                        </td>
                        <td width="25%" align="right">
                          <span class="dato"> Núm. Título: </span> <span class="valor">1500</span>
                        </td>
                    </tr>
                </tbody>
        </table>
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
<h3 class="info-header">Información para el Pago: </h3>
        <table class="tablas">
                <tbody>
                    <tr>
                       <td width="15%">
                          <span class="dato">Núm. Pago : </span> <span class="valor">01</span>
                        </td>
                         <td width="40%" align="center">
                          <span class="bg-dato">
                            <span class="dato">Fecha Programada: </span> <span class="valor">10 noviembre 2020</span>
                          </span> 
                        </td>
                        <td width="30%" align="center">
                           <span class="dato"> Tipo Pago: </span> <span class="valor">Enganche</span>
                        </td>
                        <td width="15%" align="right">
                          <span class="dato">Estatus: </span> <span class="valor">Pagado</span>
                        </td>
                    </tr>
                </tbody>
        </table>
        <table class="tablas">
                <tbody>
                    <tr>
                       <td width="50%">
                         <p class="numero-referencia"><span class="dato bg-dato">Número de Referencia</span></p>
                         <p>
                           <span class="digito">0</span>
                           <span class="digito">2</span>
                           <span class="digito">2</span>
                           <span class="digito">0</span>
                           <span class="digito">2</span>
                           <span class="digito">0</span>
                           <span class="digito">0</span>
                           <span class="digito">4</span>
                           <span class="digito">0</span>
                           <span class="digito">2</span>
                           <span class="digito">0</span>
                           <span class="digito">7</span>
                           <span class="digito">8</span>
                           <span class="digito">0</span>
                        </p>
                         <img class="logos banco" src="{{asset('images/code.png')}}" alt="">
                        </td>
                         <td width="45%">
                           <div class="cuentas"><span class="dato">Para pago en sucursales <span class="santader">Santander</span>, </span> <br> <span class="valor-ojo">Número de cuenta: 65-50040187-9</span></div>
                           <div class="cuentas">para transferencias electrónicas, <br> <span class="valor-ojo">Clabe: 0147 3065 5004 0187 96 </span> <br> Beneficiario, <br> <span class="valor-ojo">Servicios Integrales de Sinaloa S.A. de C.V. </span></div>
                           <div class="cuentas"><span class="dato-totales"> Sub Total</span> <span class="dato-totales-valor">$ 5,000.00 Pesos MXN</span></div>
                           <div class="cuentas"><span class="dato-totales"> IVA</span> <span class="dato-totales-valor">$ 5,000.00 Pesos MXN</span></div>
                           <div class="cuentas"><span class="dato-totales"> Descuento</span> <span class="dato-totales-valor">$ 5,000.00 Pesos MXN</span></div>
                           <div class="cuentas"><span class="bg-total"><span class="dato-totales"> Total a Pagar</span> <span class="dato-totales-valor">$ 5,000.00 Pesos MXN</span></span> <br><span class="dato">(cincuenta mil seiscientos setenta y ocho)</span></div>
                        </td>
                    </tr>
                </tbody>
        </table>

    </div>
   
    </div>
    

</body>
</html>