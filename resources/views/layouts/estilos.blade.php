<style>
  @for ($i = 0; $i <= 30; $i++)   
        .px-{{$i}}{
        padding: 0 {{($i/3)}}em 0 {{($i/3)}}em !important;
        }
        .pr-{{$i}}{
        padding-right:{{($i/3)}}em !important;
        }
        .pl-{{$i}}{
        padding-left:{{($i/3)}}em !important;
        }
        .py-{{$i}}{
        padding: {{($i/3)}}em 0 {{($i/3)}}em 0 !important;
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
        margin: 0 {{($i/3)}}em 0 {{($i/3)}}em !important;
        }
        .mr-{{$i}}{
        margin-right:{{($i/3)}}em !important;
        }
        .ml-{{$i}}{
        margin-left:{{($i/3)}}em !important;
        }
        .my-{{$i}}{
        margin: {{($i/3)}}em 0 {{($i/3)}}em 0 !important;
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
        
        /*fin negativos margin*/
  @endfor
  
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

    .underline{
      text-decoration: underline;
    }

    .italic{
     font-style: italic;
    }

    .bg-gray{
      background-color: #{{env('graycolor')}}
    }
  </style>