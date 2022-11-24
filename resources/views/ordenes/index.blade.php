@extends('layouts.master')
@section('title') Ordenes @endsection
@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
@component('common-components.breadcrumb')
    @slot('pagetitle') Clientes @endslot
    @slot('title') Gestión de ordenes @endslot
@endcomponent
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($id_cliente==0)
                    <h4 class="card-title">Ordenes</h4>
                @else
                    <h4 class="card-title">Ordenes de {{ $nombre_cliente }}</h4>
                @endif
				<p class="card-title-desc">
					Usted se encuentra en el modulo Gestión de Ordenes.
				</p>
                <div class="text-right">
                    @if($id_cliente!=0)
                    <a href="{{route('clientes.index')}}"> 
						<button type="button" class="btn btn-primary waves-effect waves-light">
							Regresar <i class="fa fa-undo" aria-hidden="true"></i>

						</button>
					</a>
                    <a href="{{ route('cliente.ordenes.create',$id_cliente) }}">
                        <button type="button" class="btn btn-primary waves-effect waves-light">
                            Agregar <i class="uil uil-arrow-right ml-2"></i> 
                        </button>

                    </a>
                    @else
                    <a href="{{ route('ordenes.create') }}">
                        <button type="button" class="btn btn-primary waves-effect waves-light">
                            Agregar <i class="uil uil-arrow-right ml-2"></i> 
                        </button>

                    </a>
                    @endif


                </div>
				<br>
                @include('flash::message')
                <div class="table-responsive">

					<table  class="table table-bordered dt-responsive nowrap yajra-datatable" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr>
								<th>Numero</th>
								<th>Cliente</th>
                                <th>Fecha</th>
								<th>Servicio</th>
                                <th>Actividad</th>
                                <th>Técnico</th>
                                <th>Fecha Realizado</th>
								<th>Acciones</th>
							
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
                
                
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

    <script>
        function eliminar(id,id_cliente){
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
                    window.location.href = "{{ url('ordenes/destroy') }}/"+id+"/"+id_cliente;
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                    'Cancelado',
                    'El registro no fue eliminado :)',
                    'error'
                    )
                    
                }
                })      
        }
        $(document).ready(function() {
            var id_cliente={{$id_cliente}};
            if(id_cliente==0){
                $('.yajra-datatable').DataTable({
                    "order": [ [0, "desc"] ],
                    "language":{url:'https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json'},
                    "processing": true,
                    "serverSide": false,
                    pageLength: 10,
                    "ajax":{
                        "url": "{{ route('ordenes.index')  }}",
                        "dataType": "json",
                        "type": "GET",
                        "data":{ _token: "{{csrf_token()}}"}
                    },
                    "columns": [
                    {data: 'numero'},
                    {data: 'cliente'},
                    {data: 'created_at', orderable: false,  searchable: false},
                    {data: 'tipo_servicio', orderable: false,  searchable: false},
                    {data: 'actividad', orderable: false,  searchable: false},
                    {data: 'tecnico'},
                    {data: 'fecha_trabajo', orderable: false,  searchable: false},    
                    {data: 'action', orderable: false, searchable: false},    
                    ]	 
                });
            }else
            {   $('.yajra-datatable').DataTable({
                    "order": [ [0, "desc"] ],
                    "language":{url:'https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json'},
                    "processing": true,
                    "serverSide": false,
                    pageLength: 10,
                    "ajax":{
                        "url": "{{ route('cliente.ordenes_cliente')}}",
                        "dataType": "json",
                        "type": "GET",
                        "data":{ _token: "{{csrf_token()}}",id_cliente:id_cliente}
                    },
                    "columns": [
                    {data: 'numero'},
                    {data: 'cliente'},
                    {data: 'created_at', orderable: false,  searchable: false},
                    {data: 'tipo_servicio', orderable: false,  searchable: false},
                    {data: 'actividad', orderable: false,  searchable: false},
                    {data: 'tecnico'},
                    {data: 'fecha_trabajo', orderable: false,  searchable: false},    
                    {data: 'action', orderable: false, searchable: false},    
                    ]	 
                });
            }   
            

        } );
        
    </script>
@endsection