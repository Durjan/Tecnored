@extends('layouts.master')
@section('title') Reportes @endsection
@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />

    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <style>
        .datepicker {
          z-index: 1600 !important; /* has to be larger than 1050 */
          
        }

    </style>    
@endsection
@section('content')
@component('common-components.breadcrumb')
    @slot('pagetitle') Reportes @endslot
    @slot('title') {{ $opcion }} @endslot
@endcomponent
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $opcion }}</h4>
				<p class="card-title-desc">
					Usted se encuentra en el modulo Reporte de {{ $opcion }}.
				</p>
                <form action="{{ route('reportes.pdf') }}" method="post" target="_blank">
                    {{ csrf_field() }}
                    <div class="row">
                        @if($opcion=="Clientes")
                            <div class="col-md-2">
                                <label for="tipo_reporte">Tipo de reporte {{ $opcion }}</label>
                                <select name="tipo_reporte" id="tipo_reporte" class="form-control">
                                    <option value="" >Seleccionar... </option>
                                    <option value="1" >Contratos a vencer</option>
                                    <option value="2" selected>Pago de servicio</option>
                                    <option value="3" >General</option>
                                    <option value="4" >Mbs Vendidos</option>
                                </select>
                            </div>
                        @endif
                        @if($opcion=="Facturas")
                            <div class="col-md-2">
                                <label for="tipo_reporte">Tipo de reporte {{ $opcion }}</label>
                                <select name="tipo_reporte" id="tipo_reporte" class="form-control">
                                    <option value="" >Seleccionar... </option>
                                    <option value="0" >Finalizada</option>
                                    <option value="1" >Anulada</option>
                                    <option value="2" >General</option>
                                </select>
                            </div>
                            <div class="col-md-2" id="div_pago">
                                <label for="example-text-input">Tipo de pago</label>              
                                <select class="form-control" name="tipo_pago" id="tipo_pago">
                                    <option value="" >Seleccionar...</option>
                                    <option value="EFEC" >EFECTIVO</option>
                                    <option value="TRANS" >TRANSFERENCIA</option>
                                    <option value="BITCOIN" >BITCOIN</option>
                                    <option value="DEPO" >DEPOSITO</option>
                                    <option value="POST" >POST</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="example-text-input">Cobrador</label>
                                
                                <select class="form-control" data-live-search="true" name="id_cobrador" id="id_cobrador">
                                    <option value="" >Seleccionar...</option>        
                                    @foreach ($obj_cobradores as $obj_item)
                                    <option value="{{$obj_item->id}}">{{$obj_item->nombre}}</option>          
                                    @endforeach            
                                </select>
                                
                            </div>
                        @endif
                        @if($opcion=="Ordenes")
                            <div class="col-md-2">
                                <label for="tipo_reporte">Tipo de reporte {{ $opcion }}</label>
                                <select name="tipo_reporte" id="tipo_reporte" class="form-control">
                                    <option value="" >Seleccionar... </option>
                                    <option value="1" >Trabajo</option>
                                    <option value="2" >Suspensión</option>
                                    <option value="3" >Reconexión</option>
                                    <option value="4" >Traslado</option>
                                    <option value="5" >Soporte</option>
                                </select>
                            </div>
                        @endif

                        <div class="col-md-2" style="display:none;" id="div_fecha_i">
                            <label for="estado">Desdes </label>
                            <input type="text" class="form-control datepicker" name="fecha_i" id="fecha_i" value="{{ date('d/m/Y') }}" autocomplete="off">
                        </div>
                        <div class="col-md-2" style="display:none;" id="div_fecha_f">
                            <label for="estado">Hasta</label>
                            <input type="text" class="form-control datepicker" name="fecha_f" id="fecha_f" value="{{ date('d/m/Y') }}" autocomplete="off">
                        </div>

                        @if($opcion=="Clientes")
                        <div class="col-md-2" style="display:none;" id="div_dia">
                            <label for="estado">Fecha *</label>
                            <input type="text" class="form-control datepicker" name="fecha" id="fecha" value="{{ date('d/m/Y') }}" autocomplete="off">
                        </div>
                        <div class="col-md-2" style="" id="div_estado_pago">
                            <label for="tipo_reporte">Estado *</label>
                            <select name="estado_pago" id="estado_pago" class="form-control">
                                <option value="1" >Ultima fecha de pago</option>
                                <option value="2" >Vencido</option>
                                <option value="3" >A tiempo</option>
                                <option value="4" selected>Hoy</option>
                            </select>
                        </div>
                        <div class="col-md-2" style="display: none;" id="div_estado_servi">
                            <label for="tipo_reporte">Estado</label>
                            <select name="estado_cliente" id="estado_cliente" class="form-control">
                                <option value="" >General</option>
                                <option value="1" >Activo</option><!-- 1=activo 2=suspendido 0=inactivo -->
                                <option value="0" >Inactivo</option>
                                <option value="2" >Suspendido</option>
                            </select>
                        </div>
                        <div class="col-md-2" style="display: none;" id="div_servicio">
                            <label for="tipo_reporte">Servicio</label>
                            <select name="servicio" id="servicio" class="form-control">
                                <option value="" >General</option><!-- 1=internet 2=tv -->
                                <option value="1" >Internet</option>
                                <option value="2" >Tv</option>
                            </select>
                        </div>
                        @endif
                        @if($opcion=="Ordenes")
                            <div class="col-md-2" id="div_estado_orden" style="display:none;">
                                <label for="orden_estado">Estado</label>
                                <select name="orden_estado" id="orden_estado" class="form-control">
                                    <option value="" >Seleccionar... </option>
                                    <option value="1" >Finalizada</option>
                                    <option value="2" >Pendiente</option>
                                    <option value="3" >General</option>
                                </select>
                            </div>
                        @endif
                        <input type="text" class="form-control" name="opcion" id="opcion" value="{{ $opcion }}" hidden>

                        <div class="col-md-1">
                            <label for="estado">Acción</label>
                            <button type="submit" class="form-control btn btn-primary" > Buscar</button>
                        </div>
                        @if($opcion=="Facturas")
                            <div class="col-md-1">
                                <label for="estado">Acción</label>
                                <button id="ButtonDescarga" onclick="DescargarExcel()" type="button" class="form-control btn btn-success"> <span class="uil-arrow-circle-down" />Excel</button>
                            </div>
                        @endif
                    </div>
                </form>
               
                
            </div>
        </div>
</div>
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/datatables/datatables.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/jszip/jszip.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/pdfmake/pdfmake.min.js')}}"></script>
    <script src="{{ URL::asset('assets/js/pages/datatables.init.js')}}"></script>

    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>

    <!-- Range slider init js-->
    <script src="{{ URL::asset('assets/js/pages/sweet-alerts.init.js')}}"></script>

    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script> 

    <script>
        function display_notify(typeinfo,msg,process)
      {
	      // Use toastr for notifications get an parameter from other function
	      var infotype=typeinfo;
	      var msg=msg;
        toastr.options = {
          "closeButton": false,
          "debug": false,
          "newestOnTop": false,
          "progressBar": false,
          "positionClass": "toast-top-right",
          "preventDuplicates": false,
          "onclick": null,
          "showDuration": "300",
          "hideDuration": "1000",
          "timeOut": "5000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        }
	      if (infotype=='Success'){
		      toastr.success(msg,infotype);
		      /*if (process=='insert'){
			      cleanvalues();
		      }*/
	      }
	      if (infotype=='Info'){
		      toastr.info(msg,infotype);
	      }
	      if (infotype=='Warning'){
		      toastr.warning(msg,infotype);
	      }
	      if (infotype=='Error'){
		      toastr.error(msg,infotype);
	      }

      }
   

    $( "#tipo_reporte" ).change(function() {
       var tipo_reporte = $("#tipo_reporte").val();
        if($("#opcion").val()=="Clientes"){
            if(tipo_reporte==1){
                $("#div_fecha_i").show();
                $("#div_fecha_f").show();

                $("#div_estado_servi").hide();
                $("#div_dia").hide();
                $("#div_estado_pago").hide();
                $("#div_servicio").hide();

            }
            if(tipo_reporte==2){
                $("#div_dia").show();
                $("#div_estado_pago").show();

                $("#div_estado_servi").hide();
                $("#div_fecha_i").hide();
                $("#div_fecha_f").hide();
                $("#div_servicio").hide();
                

            }
            if(tipo_reporte==3){//GENERAL
                $("#div_fecha_i").show();
                $("#div_fecha_f").show();
                $("#div_estado_servi").show();
                $("#div_servicio").show();

                $("#div_dia").hide();
                $("#div_estado_pago").hide();

            }
            if(tipo_reporte==4){//MEGAS VENDIDOS
                $("#div_fecha_i").show();
                $("#div_fecha_f").show();

                $("#div_estado_servi").hide();
                $("#div_dia").hide();
                $("#div_estado_pago").hide();
                $("#div_servicio").hide();

            }
            if(tipo_reporte==""){
                $("#div_fecha_i").hide();
                $("#div_fecha_f").hide();

                $("#div_estado_servi").hide();
                $("#div_dia").hide();
                $("#div_estado_pago").hide();
                $("#div_servicio").hide();

            }
        }
        if($("#opcion").val()=="Facturas"){
            $("#div_fecha_i").show();
            $("#div_fecha_f").show();
        }
        if($("#opcion").val()=="Ordenes"){
            if(tipo_reporte==5){
                $("#div_fecha_i").show();
                $("#div_fecha_f").show();
                $("#div_estado_orden").hide();
            }
            if(tipo_reporte!=5){
                $("#div_fecha_i").show();
                $("#div_fecha_f").show();
                $("#div_estado_orden").show();
            }
        }
    });

    $( "#estado_pago" ).change(function() {
        var estado_pago = $("#estado_pago").val();

        if(estado_pago==1){
            $("#fecha").prop( "disabled", false );
           

        }

        if(estado_pago==2){
            $("#fecha").prop( "disabled", true );
           

        }

        if(estado_pago==3){
            $("#fecha").prop( "disabled", true );
           

        }

    });

    function eliminar(id){
        Swal.fire({
            title: 'Estas seguro de eliminar el registro?',
            text: 'No podras desaser esta accion',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
            }).then((result) => {
            if (result.value) {
                Swal.fire(
                'Eliminado!',
                'Registro eliminado',
                'success'
                )
                window.location.href = "ordenes/destroy/"+id;
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                'Cancelado',
                'El registro no fue eliminado :)',
                'error'
                )
                
            }
            })      
    }

    $('.datepicker').datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true,
        orientation: "bottom",
    });

    function DescargarExcel(){
        var tipo_reporte=$('#tipo_reporte').val();
        var tipo_pago=$('#tipo_pago').val();
        var id_cobrador=$('#id_cobrador').val();
        if(tipo_pago==""){
            var tipo_pago="0";
        }
        if(id_cobrador==""){
            var id_cobrador="0";
        }
        var fecha_i=fecha_conversion_excel($('#fecha_i').val());
        var fecha_f=fecha_conversion_excel($('#fecha_f').val());
        if(tipo_reporte!=""){
            window.location.href = "{{ url('descargar/excel') }}/"+tipo_reporte+'/'+tipo_pago+'/'+id_cobrador+'/'+fecha_i+'/'+fecha_f;
        }else{
     
        }
    }
    function fecha_conversion(fecha){
            var from = fecha.split("/");
            var f = new Date(from[2], from[1], from[0]);
            var date_string = f.getFullYear() + "-" + f.getMonth() + "-" + f.getDate();
            return date_string;
        }
    function fecha_conversion_excel(fecha){
        var from = fecha.split("/");
        //var f = new Date(from[2], from[1], from[0]);
        var date_string = from[0]+ "-" + from[1] + "-" + from[2];
        return date_string;
    }
    </script>
@endsection