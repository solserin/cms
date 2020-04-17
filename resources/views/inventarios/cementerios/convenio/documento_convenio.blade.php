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
      width: 100% !important;
      padding-top: 0px;
    }

    #header section table {
      border-collapse: collapse !important;
    }
   


    .logo{
      max-width: 100% !important;
    }


    h1{
      font-size: 1em;
      line-height: .8em !important;
      text-transform: uppercase;
      text-align: center;
    }

    .datos-header{
      text-align: center !important;
      font-size: .9em;
      line-height: 0.7em !important;
      text-transform: uppercase !important;
    }

    .numeros-contrato{
      width: 100% !important;
    }

    .numeros-contrato .control{
      text-align: center;
      text-transform: uppercase !important;
      font-size: .8em;
      line-height: 1.9em !important;
      font-weight: 600 !important;
    }

    .control-valor{
      text-align: center;
      font-size: .9em;
      line-height: .3em !important;
      text-transform: uppercase;
    }

    /*parrafos**/
     .contenido{
      padding: 10px 0 0 0 !important;
      margin: 0 !important;
    }
    /*fin de parrafos*/
  </style>
</head>
<body>
@include('layouts.estilos') 
  <header id="header">
    <section>
      <table>
        <tr>
          <td style="width:23%;">
          <img src="{{public_path(env('LOGOJPG'))}}" alt="" class="logo">
          </td>
          <td style="width:53%;">
            <h1>
               {{$empresa->razon_social}}
            </h1>
            <p class="datos-header">
              r.f.c. {{$empresa->rfc}}
            </p>
            <p class="datos-header">
              {{strtolower($empresa->calle)}} Núm. Ext {{$empresa->num_ext}}
            </p>
            <p class="datos-header">
             Col. {{strtolower($empresa->colonia)}}. cp. {{$empresa->cp}}.  {{$empresa->ciudad}}  {{$empresa->estado}}
            </p>
            <p class="datos-header">
              Tel. {{$empresa->telefono}}, fax {{$empresa->fax}}
            </p>
          </td>
           <td style="width:25%;">
            <div class="numeros-contrato">
              <div class="control bg-gray">
                solicitud de servicio
              </div>
              <p class="control-valor">
                  4560707059549777777
                </p>

               <div style=""></div>
              <div class="control bg-gray">
                Número de convenio
              </div>
              <p class="control-valor">
                  pfaf-020-0020
                </p>
            </div>
          </td>
        </tr>
      </table>
    </section>
  </header>
  <p class="fecha capitalize right">
      {{$empresa->ciudad}}, {{$empresa->estado}}, a <span class="bg-gray bold uppercase  pl-2 pr-1">{{fechahora_completa()}}</span>.
    </p>
  <div class="contenido parrafo1">
    <p class="texto-base justificar line-base">
    Convenio para el otorgamiento del derecho de uso mortuorio a perpetuidad con reserva de dominio, 
    que celebran por una parte <span class="bold uppercase"><span class="texto-sm">{{$empresa->razon_social}}</span></span>, 
    con domicilio en
    <span class="uppercase texto-sm bold">{{$empresa->calle}}, {{$empresa->num_ext}}, Col. {{$empresa->colonia}} C.P {{$empresa->cp}}</span>, de esta ciudad; a quien en lo sucesivo se le denominara la <span class="bold uppercase texto-sm">"La Empresa"</span>, 
    y por la otra parte, por su propio derecho, El (La) C. 
    <span class="uppercase texto-sm bold bg-gray px-1">hector raul cruz perez</span>, 
    quien en lo sucesivo se denominara <span class="uppercase texto-sm bold">"El cliente"</span> y será el Titular del presente convenio, 
    el cual ambas partes se comprometen a firmar, de conformidad con las siguiente declaraciones y 
    cláusulas:
    </p>
  </div>

   

   <div class="contenido parrafo2">
     <h1 class="texto-base bold underline">
      declaraciones
  </h1>
    <p class="texto-base justificar line-base">
    <span class="uppercase bold">I. </span> Declara el representante legal de “La empresa”, que su representada está legalmente constituida conforme a las leyes mexicanas, 
    según consta en escritura pública número <span class="bold texto-sm">4761</span> (<span class="uppercase bold texto-sm">cuatro mil setecientos sesenta y uno</span>) del volumen 
    <span class="uppercase bold texto-sm">xxxix</span> (<span class="uppercase bold texto-sm">trigésimo noveno</span>), pasada en la ciudad de <span class="uppercase bold texto-sm">{{$empresa->registro_publico['ciudad_np']}}</span>, <span class="uppercase bold texto-sm">{{$empresa->registro_publico['estado_np']}}</span>, 
    ante el protocolo a cargo del notario público número 9 (<span class="uppercase bold texto-sm">nueve</span>), licenciado <span class="uppercase bold texto-sm">{{$empresa->registro_publico['fe_lic']}}</span>.
    </p>
     <p class="texto-base justificar line-base">
    <span class="uppercase bold">II. </span>
    Sigue declarando “La empresa” que los servicios de inhumación amparados por este convenio, 
     se realizaran en el cementerio denominado <span class="uppercase bold texto-sm">{{$empresa->cementerio['cementerio']}}</span>, ubicado en <span class="uppercase bold texto-sm">{{$empresa->cementerio->calle}}, {{$empresa->cementerio->num_ext}}, Col. {{$empresa->cementerio->colonia}} C.P {{$empresa->cementerio->cp}}</span>, 
    en el municipio de
    <span class="uppercase bold texto-sm">{{$empresa->cementerio->ciudad}}, {{$empresa->cementerio->estado}}</span>.
  </p>

   <p class="texto-base justificar line-base">
    <span class="uppercase bold">III. </span>
   Declara “El Cliente” tener el interés y capacidad legal para celebrar este convenio, y declara tener (<span class="uppercase bold texto-sm">56</span>) años de edad 
   y su domicilio en: <span class="uppercase bold texto-sm">
CARRETERA INTERNACIONAL, 58, COL. LÓPEZ MATEOS C.P 8140</span>, Tel. <span class="uppercase bold texto-sm">(669) 983 15 77</span>, Cel. <span class="uppercase bold texto-sm">(669) 983 15 77</span> y correo electrónico <span class="lowercase bold">administracion@aeternus.com.mx</span>
    para efecto de notificaciones y demás efectos legales de este convenio.
  </p>
  </div>

  
   <div class="contenido parrafo3">
   <p class="texto-base justificar line-base">
  Hechas las aclaraciones anteriores. “La Empresa” y “El Cliente” proceden a la celebración del presente convenio, al tenor de las siguientes:
  </p>
  </div>

  <div class="contenido parrafo4">
     <h1 class="texto-base bold underline">
      cláusulas
  </h1>
    <p class="texto-base justificar line-base">
    <span class="uppercase bold texto-sm">primera.- </span>
    “El Cliente" adquiere de "La Empresa", el derecho de uso mortuorio a perpetuidad con reserva de 
    dominio de <span class="uppercase bold texto-sm">1</span> Terreno(s) <span class="uppercase bold texto-sm">cuadriplex</span>, ubicado en la 
    <span class="uppercase bold texto-sm">"Teraza 7"</span>, 
    <span class="uppercase bold texto-sm">"Fila E"</span>, 
    <span class="uppercase bold texto-sm">"Lote 7"</span>, 
    en el <span class="uppercase bold texto-sm">{{$empresa->cementerio['cementerio']}}</span>, 
    con una capacidad de <span class="uppercase bold texto-sm">1</span> gavetas.
    </p>

    <p class="texto-base justificar line-base">
    <span class="uppercase bold texto-sm">Segunda.- </span>
    “La Empresa” se compromete a:
    </p>
    <div class="lista pl-11 -mt-1">
      <p class="texto-base justificar line-base">
          <span class="lowercase bold texto-sm -ml-6">a) </span>
           <span class="ml-2">
              Proporcionar un título de aportación que otorga el derecho de uso mortuorio a perpetuidad al titular de este convenio, 
              o en caso del fallecimiento de este, a cualquiera de los beneficiarios del mismo, 
              dentro de los treinta días siguientes a aquel en que se haya cubierto en forma total el pago de las 
              aportaciones mencionadas en la cláusula tercera de este convenio.
          </span>
      </p>
      <p class="texto-base justificar line-base">
          <span class="lowercase bold texto-sm -ml-6">b) </span>
           <span class="ml-2">
              Garantizar que las gavetas mencionadas en la cláusula primera de este convenio fueron construidas con 
              los materiales aprobados por las autoridades competentes, y cuenten con los cierres y sellamientos necesarios.
          </span>
      </p>
       <p class="texto-base justificar line-base">
          <span class="lowercase bold texto-sm -ml-6">c) </span>
           <span class="ml-2">
              Arreglar el lugar del sepelio, proporcionando el equipo necesario y adecuado para el mismo.
          </span>
      </p>
       <p class="texto-base justificar line-base">
          <span class="lowercase bold texto-sm -ml-6">d) </span>
           <span class="ml-2">
              Proporcionar e instalar en el espacio mortuorio amparado por este convenio una lápida de mármol, 
              en el que se grabara su nombre, el año de nacimiento y el año de fallecimiento de 
              cada una de las personas a inhumarse en el lote mencionado en la primera 
              cláusula de dicho convenio. (<span class="texto-xs bold italic">solo aplica a terrenos Dúplex y Cuádruplex</span>).
          </span>
      </p>
      <p class="texto-base justificar line-base">
          <span class="lowercase bold texto-sm -ml-6">e) </span>
           <span class="ml-2">
             Conservar y mantener el parque funerario, incluyendo todos sus jardines y tomas de agua, mediante la aportación de un fondo especial establecido para dicho fin.
          </span>
      </p>
    </div>

    <p class="texto-base justificar line-base">
    <span class="uppercase bold texto-sm">Tercera.- </span>
    En contraparte, “El Cliente”, se compromete a pagar por concepto de aportaciones la cantidad de $<span class="bold texto-sm bg-gray px-2">5,000.00</span>.
    </p>

  </div>
  

</body>
</html>
   <span class="uppercase bold texto-sm"></span>


