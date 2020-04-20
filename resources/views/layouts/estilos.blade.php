<link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap"
        rel="stylesheet">
<style>
  body {
        font-family: 'Open Sans' !important;
        color: #000 !important;
  }
  @for ($i = 0; $i <= 100; $i++)   
        .px-{{$i}}{
          padding-right:{{($i/3)}}em !important;
          padding-left:{{($i/3)}}em !important;
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
        
  @endfor


   .float-right{
     float:right !important;
    }
    .float-left{
      float: left !important;
    }
  
   .justificar{
      text-align: justify !important;
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
      text-transform: capitalize;
    }

    .uppercase{
      text-transform: uppercase;
    }
    .lowercase{
      text-transform: lowercase;
    }
    /*transform**/
    
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
    
    .bold{
      font-weight: 600 !important;
    }

    .w-normal{
      font-weight: normal !important;
    }

    .underline{
      text-decoration: underline;
    }

    .italic{
     font-style: italic;
    }

    .bg-gray{
      background-color: #{{env('graycolor')}};
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

  </style>