<link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap"
        rel="stylesheet">
<style>
    thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
        }
  body {
        font-family: 'Open Sans' !important;
        color: #000 !important;
  }
.hidden{
 visibility: hidden;
}
  @for ($i = 0; $i <= 100; $i++)   
        .px-{{$i}}{
          padding-right:{{($i/3)}}em !important;
          padding-left:{{($i/3)}}em !important;
        }
        .px-{{$i}}px{
          padding-right:{{$i}}px !important;
          padding-left:{{$i}}px !important;
        }

        .pr-{{$i}}{
          padding-right:{{($i/3)}}em !important;
        }
        .pl-{{$i}}{
          padding-left:{{($i/3)}}em !important;
        }
        .py-{{$i}}{
          padding-top: {{($i/3)}}em !important;
          padding-bottom: {{($i/3)}}em !important;
        }

        .pt-{{$i}}{
          padding-top: {{($i/3)}}em !important;
        }

        .pb-{{$i}}{
          padding-bottom: {{($i/3)}}em !important;
        }
        .p-{{$i}}{
          padding: {{($i/3)}}em !important;
        }

        .mx-{{$i}}{
          margin-right:{{($i/3)}}em !important;
          margin-left:{{($i/3)}}em !important;
        }
        .mr-{{$i}}{
          margin-right:{{($i/3)}}em !important;
        }
        .ml-{{$i}}{
          margin-left:{{($i/3)}}em !important;
        }
        .my-{{$i}}{
          margin-top: {{($i/3)}}em !important;
          margin-bottom: {{($i/3)}}em !important;
        }
        .mt-{{$i}}{
          margin-top: {{($i/3)}}em !important;
        }
        .mt-{{$i}}px{
          margin-top: {{($i)}}px !important;
        }

        .mb-{{$i}}{
          margin-bottom: {{($i/3)}}em !important;
        }
        .m-{{$i}}{
          margin: {{($i/3)}}em !important;
        }

        /*negativos margin*/
        .-mr-{{$i}}{
        margin-right:-{{($i/3)}}em !important;
        }
        .-ml-{{$i}}{
        margin-left:-{{($i/3)}}em !important;
        }
        .-mt-{{$i}}{
        margin-top: -{{($i/3)}}em !important;
        }

        .-mb-{{$i}}{
        margin-bottom: -{{($i/3)}}em !important;
        }

        .mr-auto{
         margin-right: auto !important;
        }
         .ml-auto{
         margin-left: auto !important;
        }
        
        /*fin negativos margin*/

        .letter-spacing-{{$i}}{
            letter-spacing: {{($i)}}px !important;
        }

        .word-spacing-{{$i}}{
          word-spacing: {{($i)}}px !important;
        }
  
        .w-{{$i}}{
          width: {{($i)}}% !important;
        }
        .h-{{$i}}{
          min-height: {{($i)}}vh !important;
        }

        .h-{{$i}}-p{
          min-height: {{($i)}}% !important;
        }

        .radius-{{$i}}{
          border-radius: {{($i)}}px !important;
        }

        .border-black-{{$i}}{
          border: {{($i)}}px solid #000 !important; 
        }

        .border-top-black-{{$i}}{
          border-top: {{$i}}px solid #000 !important; 
        }

        .border-bottom-black-{{$i}}{
          border-bottom: {{$i}}px solid #000 !important; 
        }

         .border-bottom-primary-{{$i}}{
          border-bottom: {{$i}}px solid #{{env('MAINCOLOR')}} !important;
        }

        .border-right-black-{{$i}}{
          border-right: {{$i}}px solid #000 !important; 
        }


        .border-left-black-{{$i}}{
          border-left: {{$i}}px solid #000 !important; 
        }


          .top-{{$i}}{
              top: {{$i}}%  !important; 
          }

          .bottom-{{$i}}{
              bottom: {{$i}}%  !important; 
          }

          .right-{{$i}}{
              right: {{$i}}%  !important; 
          }
          .left-{{$i}}{
              left: {{$i}}%  !important; 
          }

          .size-{{$i}}{
              font-size: {{$i}}em !important;
          }
           .size-{{$i}}px{
              font-size: {{$i}}px !important;
          }

           .b-spacing-{{$i}}px{
               border-spacing:{{$i}}px !important;
          }

        
  @endfor
      .w-content{
         display: inline-block;
        }

   .float-right{
     float:right !important;
    }
    .float-left{
      float: left !important;
    }
  
   .justificar{
      text-align: justify !important;
    }
.img-block{
    display: block;
}
.img-center{
    display: block;
    margin-left: auto;
    margin-right: auto;
}
    .center{
      text-align: center !important;
    }
    .right{
      text-align: right !important;
    }
    .left{
      text-align: left !important;
    }
    /*alineacion**/

    /*transform**/
    .capitalize{
      text-transform: capitalize !important;
    }

    .uppercase{
      text-transform: uppercase;
    }
    .lowercase{
      text-transform: lowercase;
    }
    /*transform**/
 .texto-lg{
      font-size: 1.2em !important;
    }
     .texto-base{
      font-size: 1em !important;
    }
    .texto-sm{
      font-size: .9em !important;
    }
    .texto-xs{
      font-size: .8em !important;
    }

 .texto-xs2{
      font-size: .7em !important;
    }


    .line-0{
      line-height: 0em !important;
    } 

    .line-lg{
      line-height: 1.9em !important;
    }

    .line-base{
      line-height: 1.5em !important;
    }

    .line-small{
      line-height: 1.3em !important;
    }

    .line-xs{
      line-height: 1.1em !important;
    }

    .line-xxs{
      line-height: .7em !important;
    }
    
    .bold{
      font-weight: 700 !important;
    }

    .light{
     font-weight: 300;
    }

    .thin{
        font-weight: 200;
    }

    .semibold{
     font-weight: 600;
    }

    .w-normal{
      font-weight: normal !important;
    }
    .font-hairline{
      font-weight: 100;
    }	
    .font-extrabold	{
      font-weight: 800;
    }
    .font-black	{
      font-weight: 900;
    }


    .underline{
      text-decoration: underline;
    }

    .italic{
     font-style: italic;
    }



.bg-white{
      background-color: #fff !important;
    }


    .bg-primary{
      background-color: #{{env('MAINCOLOR')}} !important;
    }


    .bg-gray{
      background-color: #E5E8E8 !important;
    }
    .bg-gray-dark{
      background-color: #BFBFBF !important;
    }
 
.bg-666 {
    border: 1px solid #ddd;
    color: #fff !important;
    background-color: #666666 !important;
}
.pagos_tabla thead tr {
    border: 1px solid #ddd;
    color: #fff !important;
    background-color: #666666 !important;
}

.pagos_tabla tbody {
    border: 0px !important;
    border-top: 2px solid #fff !important;
}

.pagos_tabla {
    border-collapse: collapse;
}

.pagos_tabla tr th,
.pagos_tabla td {
    font-weight: 500 !important;
    border: 1px solid #ddd;
}

.pagos_tabla tr:nth-child(even) {
    background-color: #f2f2f2 !important;
}


   
.bg-header{
    color: #fff !important;
    background-color: #666666 !important;
}
     .bg-black{
      background-color: #000 !important;
    }

    .bg-info{
      background-color: #0061ff !important;
    }

    .bg-white{
      background-color:#fff !important;
    }

    .border-top{
      border-top: 1px solid #000 !important; 
    }

    .text-white{
      color: #fff !important;
    }

    .text-danger{
      color: #EA5455 !important;
    }

    .text-success{
      color: #28C76F !important;
    }

     .text-black{
      color: #000 !important;
    }


    .text-primary{
      color: #{{env('MAINCOLOR')}} !important;
    }


 

  .watermark{
      display: inline-block;
      opacity: 1 !important;
      transform: rotate(-45deg);
      text-align: center;
      z-index: 1000;
      transform: rotate(-45deg) !important;
      /* Legacy vendor prefixes that you probably don't need... */
      /* Safari */
      -webkit-transform: rotate(-45deg) !important;
      /* Firefox */
      -moz-transform: rotate(-45deg) !important;
      /* IE */
      -ms-transform: rotate(-45deg) !important;
      /* Opera */
      -o-transform: rotate(-45deg) !important;
      /* Internet Explorer */
      filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
  }


  .watermark-danger {
    border: 3px dashed #dc3545 !important;
    color: #dc3545 !important;
  }

  .watermark-success {
      border: 3px dashed #22BB33 !important;
      color: #22BB33 !important;
  }
  /*positions*/
  .absolute{
    position: absolute !important;
  }

  .relative{
    position: relative !important;
  }

  .fixed{
    position: fixed !important;
  }

   .static{
    position: static !important;
  }

  .sticky{
    position: sticky !important;
  }

.bg-gray-light{
background-color:#e8e8e8 !important;
}


.bg-table-header{
background-color:#666666 !important;
} 

.color-semidark{
color:#666666 !important;
}

.tablas-collapsed{
 border-collapse: collapse;
border: 0px !important;
  border-top: 2px solid #fff !important;
}


.tablas-collapsed tr:nth-child(even){
    background-color: #f2f2f2 !important;
}


.collapse{
border-collapse: collapse;
}
.collapse tr, .collapse td {
  border-bottom: 1px solid #ddd;
}


  </style>