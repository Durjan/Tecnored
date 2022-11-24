<?php

namespace App\Exports;

use App\Models\Factura;
use Google\Service\AndroidPublisher\Timestamp;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpParser\Node\Expr\Cast\String_;

class FacturaExport implements FromView,ShouldAutoSize
{
    use Exportable;
    public function __construct(int $tipo_reporte,string $tipo_pago,int $id_cobrador,String $fecha_i, String $fecha_f, String $fecha_i_s, String $fecha_f_s)
    {                           
        $this->tipo_reporte = $tipo_reporte;
        $this->tipo_pago = $tipo_pago;
        $this->id_cobrador = $id_cobrador;
        $this->fecha_inicio = $fecha_i;
        $this->fecha_fin = $fecha_f;
        $this->fecha_inicio_s = $fecha_i_s;
        $this->fecha_fin_s = $fecha_f_s;
         
    }


    public function view(): View
    {
        $usuario=Auth::user()->name;
        $sucursal=Auth::user()->get_sucursal->nombre;
        if($this->tipo_reporte==0){
            return view('reportes/excel_factura', [
                'facturas' => Factura::select('id_cliente','id_cobrador','total','tipo_pago','tipo_documento','numero_documento','anulada','tipo_servicio','created_at')->where('id_sucursal',Auth::user()->id_sucursal)->where('anulada',0)->whereBetween('created_at',[$this->fecha_inicio,$this->fecha_fin])->orderBy('numero_documento', 'ASC')->get(),
                'tipo_pago'=>$this->tipo_pago,
                'id_cobrador'=>$this->id_cobrador,
                'fecha_inicio_s'=>$this->fecha_inicio_s,
                'fecha_fin_s'=>$this->fecha_fin_s,
                'usuario'=>$usuario,
                'sucursal'=>$sucursal
            ]);
        }
        if($this->tipo_reporte==1){
            return view('reportes/excel_factura', [
                'facturas' => Factura::select('id_cliente','id_cobrador','total','tipo_pago','tipo_documento','numero_documento','anulada','tipo_servicio','created_at')->where('id_sucursal',Auth::user()->id_sucursal)->where('anulada',1)->whereBetween('created_at',[$this->fecha_inicio,$this->fecha_fin])->orderBy('numero_documento', 'ASC')->get(),
                'tipo_pago'=>$this->tipo_pago,
                'id_cobrador'=>$this->id_cobrador,
                'fecha_inicio_s'=>$this->fecha_inicio_s,
                'fecha_fin_s'=>$this->fecha_fin_s,
                'usuario'=>$usuario,
                'sucursal'=>$sucursal

            ]);
            
        }
        if($this->tipo_reporte==2){
            return view('reportes/excel_factura', [
                'facturas' => Factura::select('id_cliente','id_cobrador','total','tipo_pago','tipo_documento','numero_documento','anulada','tipo_servicio','created_at')->where('id_sucursal',Auth::user()->id_sucursal)->whereBetween('created_at',[$this->fecha_inicio,$this->fecha_fin])->orderBy('numero_documento', 'ASC')->get(),
                'tipo_pago'=>$this->tipo_pago,
                'id_cobrador'=>$this->id_cobrador,
                'fecha_inicio_s'=>$this->fecha_inicio_s,
                'fecha_fin_s'=>$this->fecha_fin_s,
                'usuario'=>$usuario,
                'sucursal'=>$sucursal
            ]);
        }

    }
    
}
