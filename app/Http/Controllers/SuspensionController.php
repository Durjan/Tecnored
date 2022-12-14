<?php

namespace App\Http\Controllers;
use App\Fpdf\FpdfClass;
use App\Models\Suspensiones;
use App\Models\Actividades;
use App\Models\Tecnicos;
use App\Models\Cliente;
use App\Models\Correlativo;
use App\Models\Internet;
use App\Models\Tv;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\DataTables;

class SuspensionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('auth');
    }
    /*public function index()
    {   
        $id_cliente=0;
        $suspensiones = Suspensiones::all();
        return view('suspensiones/index',compact('suspensiones','id_cliente'));
    }*/

    public function index(Request $request)
    {   
        $id_cliente=0;
        if ($request->ajax()) {
            $data = Suspensiones::all();
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('cliente',function($data){
                    return $data->get_cliente->nombre;
                })
                ->addColumn('created_at',function($data){
                    return $data->created_at->format('d/m/Y');
                })
                ->addColumn('tecnico',function($data){
                    if(isset($data->get_tecnico->nombre)){
                        return $data->get_tecnico->nombre;
                    }
                })
                ->addColumn('fecha_trabajo',function($data){
                    if($data->fecha_trabajo == NULL){
                        $fecha= '<div class="col-md-8 badge badge-pill badge-danger ">Pendiente</div>';
                    }else{
                        $fecha= $data->fecha_trabajo->format('d/m/Y');
                    }
                    return $fecha;
                })
                ->addColumn('action', function($data){
                    $btn = 


                    '<div class="btn-group mr-1 mt-2">
                    <button type="button" class="btn btn-primary">Acciones</button>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">';
                    if($data->suspendido==0){
                    $btn.='<a class="dropdown-item" href="#" onclick="suspender('.$data->id.',0)">Suspender</a>';
                    }
                    $btn.='<div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="'.route("suspensiones.imprimir",$data->id).'" target="_blank">Reporte</a>';
                    if(true){
                    $btn.='<a class="dropdown-item" href="'.route("suspensiones.edit",$data->id).'">Editar</a>';
                    }else{
                    $btn.='<a class="dropdown-item" href="">Editar</a>';
                    }
                    $btn.='<a class="dropdown-item" href="#" onclick="eliminar('.$data->id.',0)">Eliminar</a>
                    <div class="dropdown-divider"></div>
                    </div>
                    </div>';
                    return $btn;
                })
                ->rawColumns(['fecha_trabajo','action'])
                ->make(true);
        }
        return view('suspensiones/index',compact('id_cliente'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id_cliente=0;
        $obj_tecnicos = Tecnicos::all();
        return view('suspensiones.create', compact('obj_tecnicos','id_cliente'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->tipo_servicio=="Internet")
        {   $cliente=Cliente::where('id',$request->id_cliente)->where('internet','1')->get();
            if(count($cliente)>0)
            {
                $suspension = new Suspensiones();
                $suspension->id_cliente = $request->id_cliente;
                $suspension->numero = $this->correlativo(9,6);
                $suspension->tipo_servicio = $request->tipo_servicio;
                $suspension->motivo = $request->motivo;
                $suspension->id_tecnico = $request->id_tecnico;
                $suspension->observaciones = $request->observacion;
                $suspension->suspendido = 0;
                $suspension->id_usuario=Auth::user()->id;
                $suspension->save();
                $this->setCorrelativo(9);
                
                //obteniendo la ultima suspension
                $ultimo_suspension = Suspensiones::all()->last();
                $numero = $ultimo_suspension->numero;
                
                $obj_controller_bitacora=new BitacoraController();	
                $obj_controller_bitacora->create_mensaje('Suspension creada: '.$numero);

                flash()->success("Registro creado exitosamente!")->important();
                
                if($request->di==0){

                    return redirect()->route('suspensiones.index');
                }else{
                    return redirect()->route('cliente.suspensiones.index',$request->id_cliente);
                }
            }else
            {
                flash()->error("Cliente No posee Internet activo!")->important();
                if($request->di==0){
        
                    return redirect()->route('suspensiones.create');
                }else{
                    return redirect()->route('cliente.suspensiones.create',$request->id_cliente);
                }
            }
        
        }
        if($request->tipo_servicio=="Tv")
        {   $cliente=Cliente::where('id',$request->id_cliente)->where('tv','1')->get();
            if(count($cliente)>0)
            {
                $suspension = new Suspensiones();
                $suspension->id_cliente = $request->id_cliente;
                $suspension->numero = $this->correlativo(9,6);
                $suspension->tipo_servicio = $request->tipo_servicio;
                $suspension->motivo = $request->motivo;
                $suspension->id_tecnico = $request->id_tecnico;
                $suspension->observaciones = $request->observacion;
                $suspension->suspendido = 0;
                $suspension->id_usuario=Auth::user()->id;
                $suspension->save();
                $this->setCorrelativo(9);

                //obteniendo el ultimo cliente
                $ultimo_suspension = Suspensiones::all()->last();
                $numero = $ultimo_suspension->numero;

                $obj_controller_bitacora=new BitacoraController();	
                $obj_controller_bitacora->create_mensaje('Suspension creada: '.$numero);

                flash()->success("Registro creado exitosamente!")->important();
                
                if($request->di==0){

                    return redirect()->route('suspensiones.index');
                }else{
                    return redirect()->route('cliente.suspensiones.index',$request->id_cliente);
                }
            }else
            {
                flash()->error("Cliente No posee Tv activo!")->important();
                if($request->di==0){
        
                    return redirect()->route('suspensiones.create');
                }else{
                    return redirect()->route('cliente.suspensiones.create',$request->id_cliente);
                }
            }
        
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $suspension = Suspensiones::find($id);
        $obj_tecnicos = Tecnicos::all();
        $id_cliente=0;
        return view("suspensiones.edit",compact('suspension','obj_tecnicos','id_cliente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $suspension=Suspensiones::where('id',$request->id_suspension)->get();
        if($request->tipo_servicio=="Internet")
        {   $cliente=Cliente::where('id',$suspension[0]->id_cliente)->where('internet','1')->get();
            if(count($cliente)>0)
            {
                $fecha_trabajo=null;
                if($request->fecha_trabajo!=""){
                    $fecha_trabajo = Carbon::createFromFormat('d/m/Y', $request->fecha_trabajo);
        
                }
                Suspensiones::where('id',$request->id_suspension)->update([
                    'id_tecnico'=> $request->id_tecnico,
                    'motivo'=>$request->motivo,
                    "fecha_trabajo"=>$fecha_trabajo,
                    "tipo_servicio"=>$request->tipo_servicio,
                    'observaciones'=>$request->observacion
                    ]);
                flash()->success("Registro editado exitosamente!")->important();
                $obj_controller_bitacora=new BitacoraController();	
                $obj_controller_bitacora->create_mensaje('Suspension editada con el n??mero: '. $request->numero);
            
              
                if($request->go_to==0){
        
                    return redirect()->route('suspensiones.index');
                }else{
                    return redirect()->route('cliente.suspensiones.index',$request->go_to);
                }
            }else
            {
                flash()->error("Cliente no posee Internet activo!")->important();
                if($request->di==0){
        
                    return redirect()->route('suspensiones.create');
                }else{
                    return redirect()->route('cliente.suspensiones.create',$request->id_cliente);
                }
            }
        }
        if($request->tipo_servicio=="Tv")
        {   $cliente=Cliente::where('id',$suspension[0]->id_cliente)->where('tv','1')->get();
            if(count($cliente)>0)
            {
                $fecha_trabajo=null;
                if($request->fecha_trabajo!=""){
                    $fecha_trabajo = Carbon::createFromFormat('d/m/Y', $request->fecha_trabajo);
        
                }
                Suspensiones::where('id',$request->id_suspension)->update([
                    'id_tecnico'=> $request->id_tecnico,
                    'motivo'=>$request->motivo,
                    "fecha_trabajo"=>$fecha_trabajo,
                    "tipo_servicio"=>$request->tipo_servicio,
                    'observaciones'=>$request->observacion
                    ]);
                flash()->success("Registro editado exitosamente!")->important();
                $obj_controller_bitacora=new BitacoraController();	
                $obj_controller_bitacora->create_mensaje('Suspension editada con el n??mero: '. $request->numero);
            
              
                if($request->go_to==0){
        
                    return redirect()->route('suspensiones.index');
                }else{
                    return redirect()->route('cliente.suspensiones.index',$request->go_to);
                }
            }else
            {
                flash()->error("Cliente no posee TV activo!")->important();
                if($request->di==0){
        
                    return redirect()->route('suspensiones.create');
                }else{
                    return redirect()->route('cliente.suspensiones.create',$request->id_cliente);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$id_cliente)
    {
        Suspensiones::destroy($id);
        $obj_controller_bitacora=new BitacoraController();	
        $obj_controller_bitacora->create_mensaje('Suspension eliminada con  id: '.$id);
        flash()->success("Registro eliminado exitosamente!")->important();

        if($id_cliente==0){

            return redirect()->route('suspensiones.index');
        }else{
            return redirect()->route('cliente.suspensiones.index',$id_cliente);
        }
       
    }

    // Autocomplete de Cliente
    public function busqueda_cliente(Request $request){
        $term1 = $request->term;
        $results = array();
            
        $queries = Cliente::Where('codigo', 'LIKE', '%'.$term1.'%')->orWhere('nombre', 'LIKE', '%'.$term1.'%')->get();
            
        foreach ($queries as $query){
            $results[] = [ 'id' => $query->id, 'value' => "(".$query->codigo.") ".$query->nombre,'nombre' => $query->nombre];
        }
        return response($results);
    
    }

    private function correlativo($id,$digitos){
        //id correlativo 
        /*
            1 cof
            2 ccf 
            3 cliente
            4 tv 
            5 inter
            6 orden 
            7 traslado
            8 reconexion
            9 suspension
        */

        $correlativo = Correlativo::find($id);
        $ultimo = $correlativo->ultimo+1;

        return $this->get_correlativo($ultimo,$digitos);

    }
    private function setCorrelativo($id){

        //id correlativo 
        /*
            1 cof
            2 ccf 
            3 cliente
            4 tv 
            5 inter
            6 orden 
            7 traslado
            8 reconexion
            9 suspension
        */
        $correlativo = Correlativo::find($id);
        $ultimo = $correlativo->ultimo+1;
        Correlativo::where('id',$id)->update(['ultimo' =>$ultimo]);
    }
    private function get_correlativo($ult_doc,$long_num_fact){
        $ult_doc=trim($ult_doc);
        $len_ult_valor=strlen($ult_doc);
        $long_increment=$long_num_fact-$len_ult_valor;
        $valor_txt="";
        if ($len_ult_valor<$long_num_fact) {
            for ($j=0;$j<$long_increment;$j++) {
            $valor_txt.="0";
            }
        } else {
            $valor_txt="";
        }
        $valor_txt=$valor_txt.$ult_doc;
        return $valor_txt;
    }

    public function suspender($id,$id_cliente){

        $suspension = Suspensiones::find($id);
        $servicio = $suspension->tipo_servicio;
        if($servicio=="Internet")
        {
            Cliente::where('id',$suspension->id_cliente)->update(['internet' =>'2']);
            Internet::where('id_cliente',$suspension->id_cliente)->update(['activo' =>'2']);

        }
        if($servicio=="Tv")
        {
            Cliente::where('id',$suspension->id_cliente)->update(['tv' =>'2']);
            Tv::where('id_cliente',$suspension->id_cliente)->update(['activo' =>'2']);
        }
        Suspensiones::where('id',$id)->update(['suspendido' =>'1']);
        //1= Cliente  activo
        //2=Cliente suspendido
        //0=Cliente sin servicio
        $obj_controller_bitacora=new BitacoraController();	
        $obj_controller_bitacora->create_mensaje('Servicio suspendido con la suspensi??n: '.$suspension->numero);
        flash()->success("Registro suspendido exitosamente!")->important();
        if($id_cliente==0){

            return redirect()->route('suspensiones.index');
        }else{
            return redirect()->route('cliente.suspensiones.index',$id_cliente);

        }
    }

    public function get_Suspensiones(Request $request){
        $columns = array( 
            0 =>'numero',
            1 =>'id_cliente',
            2 =>'created_at',
            3 => 'tipo_servicio',
            4=> 'id_tecnico',
            5=> 'fecha_trabajo',
            6=> 'id'
        );

        $totalData = Suspensiones::all()->count();

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {   
            $posts = Suspensiones::
            offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            //->where('activo','1')
            ->get();
        }else {
            $search = $request->input('search.value');
            $posts =  Suspensiones::orWhere('nombre', 'LIKE',"%{$search}%")
            //->where('activo','1')
            ->orwhere('codigo','LIKE',"%{$search}%")
            //->where('activo','1')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            $totalFiltered = Suspensiones::orwhere('codigo','LIKE',"%{$search}%")
            //->where('activo','1')
            ->orWhere('nombre', 'LIKE',"%{$search}%")
            //->where('activo','1')
            ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                //$show =  route('posts.show',$post->id);
                //$edit =  route('posts.edit',$post->id);

                $nestedData['numero'] = $post->numero;
                $nestedData['cliente'] = $post->id_cliente;
                $nestedData['fecha'] = $post->create_at;
                $nestedData['tipo_servicio'] = $post->tipo_servicio;
                $nestedData['tecnico'] = $post->id_tecnico;
                $nestedData['fecha_trabajo'] = $post->fecha_trabajo;
                $actionBtn = '
                <div class="btn-group dropup mr-1 mt-2">
                    <button type="button" class="btn btn-primary">Acciones</button>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="mdi mdi-chevron-down"></i>
                    </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="'.route("clientes.gen_cargo",$post->id).'">Generar cargo</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="detallesCliente('.$post->id.')">Detalles</a>
                            <a class="dropdown-item" href="'.route("clientes.edit",$post->id).'">Editar</a>
                            <a class="dropdown-item" href="#" onclick="eliminar('.$post->id.')">Eliminar</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="'.route("clientes.contrato",$post->id).'">Contrato</a>
                            <a class="dropdown-item" href="'.route("cliente.estado_cuenta.index",$post->id).'">Estado de cuenta</a>
                            <a class="dropdown-item" href="'.route("cliente.ordenes.index",$post->id).'">Ordenes</a>
                            <a class="dropdown-item" href="'.route("cliente.suspensiones.index",$post->id).'">Suspenciones</a>
                            <a class="dropdown-item" href="'.route("cliente.reconexiones.index",$post->id).'">Reconexiones</a>
                            <a class="dropdown-item" href="'.route("cliente.traslados.index",$post->id).'">Traslados</a>
                        </div>
                </div>';
                $nestedData['action']=$actionBtn;

                //$nestedData['created_at'] = date('j M Y h:i a',strtotime($post->created_at));
                //$nestedData['options'] = "&emsp;<a href='{$show}' title='SHOW' ><span class='glyphicon glyphicon-list'></span></a>
                //                    &emsp;<a href='{$edit}' title='EDIT' ><span class='glyphicon glyphicon-edit'></span></a>";
                $data[] = $nestedData;

            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );

        echo json_encode($json_data); 
    }

    public function imprimir($id)
    {
        $suspension= suspensiones::find($id);
        
        $velocidad="";
        $mac="";
        $marca="";
        $ip="";
        $dia_c="";
        if($suspension->tipo_servicio=="Internet")
        {
           
            $i= Internet::where('id_cliente',$suspension->id_cliente)->where('activo','1')->get();
            if(count($i)>0)
            {   
                $velocidad=$i[0]->velocidad;
                $mac=$i[0]->mac;
                $marca=$i[0]->marca;
                $ip=$i[0]->ip;
                $dia_c=$i[0]->dia_gene_fact;
            }
        }
        if($suspension->tipo_servicio=="Tv")
        {
            
            $tv=Tv::where('id_cliente',$suspension->id_cliente)->where('activo','1')->get();
            if(count($tv)>0)
            {
                $dia_c=$tv[0]->dia_gene_fact;
            }
            
        }

        /*
        colilla roja=internet=1
        colilla verde=cable=2
        colilla amarilla=paquete=3
        */
        if($suspension->get_cliente->colilla=="1"){$colilla="Roja";}
        if($suspension->get_cliente->colilla=="2"){$colilla="Verde";}
        if($suspension->get_cliente->colilla=="3"){$colilla="Amarilla";}

        $fpdf = new FpdfClass('P','mm', 'Letter');
        
        $fpdf->AliasNbPages();
        $fpdf->AddPage();
        $fpdf->SetTitle('SUSPENSION   | UNINET');

        $fpdf->SetXY(175,22);
        $fpdf->SetFont('Arial','',15);
        $fpdf->SetTextColor(194,8,8);
        $fpdf->Cell(20,10,$suspension->numero);
        $fpdf->SetTextColor(0,0,0);
        $fpdf->SetFont('Arial','B',12);
        $fpdf->SetXY(80,35);
        $fpdf->cell(50,5,'SUSPENSION DE SERVICIO',0);
        $fpdf->SetXY(165,22);
        $fpdf->SetFont('Arial','',14);
        $fpdf->SetTextColor(194,8,8);
        $fpdf->Cell(30,10,utf8_decode('N??.'));
        $fpdf->SetTextColor(0,0,0);


        $fpdf->SetFont('Arial','',11);
        $fpdf->SetXY(10,40);
        $fpdf->Cell(25,5,utf8_decode("Dia de cobro: "),0,0,'L');
        $fpdf->SetXY(35,40);
        $fpdf->Cell(10,5,utf8_decode($dia_c),'B',0,'L');
        $fpdf->SetXY(85,40);
        $fpdf->Cell(40,5,utf8_decode($suspension->get_cliente->nodo),0,0,'C');
        $fpdf->SetXY(165,40);
        $fpdf->Cell(40,5,utf8_decode($suspension->created_at),'B',0,'C');

        $fpdf->SetXY(10,50);
        $fpdf->Cell(15,5,utf8_decode("C??digo: "),0,0,'L');
        $fpdf->SetXY(25,50);
        $fpdf->Cell(15,5,utf8_decode($suspension->get_cliente->codigo),'B',0,'L');
        $fpdf->SetXY(60,50);
        $fpdf->Cell(20,5,utf8_decode("Nombre: "),0,0,'L');
        $fpdf->SetXY(80,50);
        $fpdf->Cell(85,5,utf8_decode($suspension->get_cliente->nombre),'B',0,'L');


        $fpdf->SetXY(10,60);
        $fpdf->Cell(20,5,utf8_decode("Direcci??n: "),0,1,'L');
        $fpdf->SetXY(30,60);
        $fpdf->MultiCell(175, 5, substr(utf8_decode($suspension->get_cliente->dirreccion_cobro),0,255), 'B', 'L');
        
        $fpdf->SetXY(10,75);
        $fpdf->Cell(40,5,utf8_decode("Motivo de Suspensi??n: "),0,0,'L');
        $fpdf->SetXY(50,75);
        $fpdf->Cell(50,5,utf8_decode($suspension->motivo),'B',0,'L');
        $fpdf->SetXY(100,75);
        $fpdf->Cell(20,5,utf8_decode("T??cnico: "),0,0,'L');
        $fpdf->SetXY(120,75);
        $fpdf->Cell(50,5,utf8_decode($suspension->get_tecnico->nombre),'B',0,'L');

        $fpdf->SetXY(10,82);
        $fpdf->Cell(20,5,utf8_decode("T??lefono: "),0,0,'L');
        $fpdf->SetXY(30,82);
        $fpdf->Cell(40,5,utf8_decode($suspension->get_cliente->telefono1.'/'.$suspension->get_cliente->telefono2),'B',0,'L');
        $fpdf->SetXY(73,82);
        $fpdf->Cell(8,5,utf8_decode("Rx:"),0,0,'L');
        $fpdf->SetXY(81,82);
        $fpdf->Cell(12,5,utf8_decode($suspension->recepcion),'B',0,'L');
        $fpdf->SetXY(94,82);
        $fpdf->Cell(8,5,utf8_decode("tx:"),0,0,'L');
        $fpdf->SetXY(102,82);
        $fpdf->Cell(12,5,utf8_decode($suspension->tx),'B',0,'L');
        $fpdf->SetXY(120,82);
        $fpdf->Cell(20,5,utf8_decode("Velocidad:"),0,0,'L');
        $fpdf->SetXY(140,82);
        $fpdf->Cell(15,5,utf8_decode($velocidad),'B',0,'L');
        $fpdf->SetXY(160,82);
        $fpdf->Cell(15,5,utf8_decode("Colilla:"),0,0,'L');
        $fpdf->SetXY(175,82);
        $fpdf->Cell(20,5,utf8_decode($colilla),'B',0,'L');

        $fpdf->SetXY(10,89);
        $fpdf->Cell(10,5,utf8_decode("Mac: "),0,0,'L');
        $fpdf->SetXY(20,89);
        $fpdf->Cell(35,5,utf8_decode($mac),'B',0,'L');
        $fpdf->SetXY(60,89);
        $fpdf->Cell(15,5,utf8_decode("Marca:"),0,0,'L');
        $fpdf->SetXY(75,89);
        $fpdf->Cell(25,5,utf8_decode($marca),'B',0,'L');
        $fpdf->SetXY(100,89);
        $fpdf->Cell(25,5,utf8_decode("Coordenadas:"),0,0,'L');
        $fpdf->SetXY(125,89);
        $fpdf->Cell(35,5,utf8_decode($suspension->get_cliente->cordenada),'B',0,'L');
        $fpdf->SetXY(160,89);
        $fpdf->Cell(5,5,utf8_decode("IP:"),0,0,'L');
        $fpdf->SetXY(165,89);
        $fpdf->Cell(35,5,utf8_decode($ip),'B',0,'L');


        $fpdf->SetXY(10,96);
        $fpdf->Cell(40,5,utf8_decode("Observaciones:"),0,0,'L');
        $fpdf->SetXY(40,96);
        $fpdf->MultiCell(165, 5, substr(utf8_decode($suspension->observaciones),0,255), 'B', 'L');
        
        $fpdf->SetXY(10,115);
        $fpdf->Cell(30,5,utf8_decode("Fecha realizado:"),0,0,'L');
        $fpdf->SetXY(40,115);
        if($suspension->fecha_trabajo!=''){$fpdf->Cell(30,5,utf8_decode($suspension->fecha_trabajo->format('d/m/Y')),'B',0,'L');}
        else{$fpdf->Cell(30,5,' /  /  ','B',0,'L');}
        $fpdf->SetXY(70,115);
        $fpdf->Cell(30,5,utf8_decode("Servicio:".$suspension->tipo_servicio),0,0,'L');

        $fpdf->SetXY(10,125);
        $fpdf->Cell(40,5,utf8_decode("_________________"),0,0,'L');
        $fpdf->SetXY(90,125);
        $fpdf->Cell(40,5,utf8_decode("_________________"),0,0,'L');
        $fpdf->SetXY(165,125);
        $fpdf->Cell(40,5,utf8_decode("_________________"),0,0,'L');
        $fpdf->SetXY(10,130);
        $fpdf->Cell(40,5,utf8_decode("Cliente"),0,0,'C');
        $fpdf->SetXY(90,130);
        $fpdf->Cell(40,5,utf8_decode("T??cnico"),0,0,'C');
        $fpdf->SetXY(165,130);
        $fpdf->Cell(40,5,utf8_decode("Autorizado"),0,0,'C');
        $fpdf->SetXY(10,135);
        if(isset($suspension->get_usuario->name)){
            $fpdf->Cell(40,5,utf8_decode("Creado por: ".$suspension->get_usuario->name),0,0,'L');
        }
        $fpdf->Line(10,140,205,140,225,140);
  
        $fpdf->Output();
        exit;

    }
}
