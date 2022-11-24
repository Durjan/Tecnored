<table>
    <thead>
        <tr><th align="center" colspan="8">TECNORED S.A de C.V.</th></tr>
        <tr><th align="center" colspan="8">SERVICIO DE TELECOMUNICACIONES</th></tr>
        <tr>
            <th colspan="8">Generado por {{$usuario.' '.date('d/m/Y h:i:s a')}}                
            </th>
        </tr>
        <tr>
            <th colspan="8">{{$sucursal}}</th>
        </tr>
        <tr>
            <th colspan="8" align="center">CORTE DE FACTURACION</th>
        </tr>
        <tr>
            <th colspan="8" align="center">desde {{$fecha_inicio_s}} hasta {{$fecha_fin_s}}</th>
        </tr>
        <tr >
            <th>Cobrador</th>
            <th>Documento</th>
            <th>Código</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Servicio</th>
            <th>Cantidad</th>
            <th>Pago</th>

        </tr>
    </thead>
    <tbody>
    @php
    $suma=0.00;
    @endphp
    @foreach($facturas as $item)
        @php
            if($item->tipo_documento==1){$tipo='FAC';}
            if($item->tipo_documento==2){$tipo='CRE';}
            if($item->tipo_servicio==1){$servicio='I';}
            if($item->tipo_servicio==2){$servicio='Tv';}
            if($item->tipo_servicio==0){$servicio='-';}
        @endphp
        @if($tipo_pago==$item->tipo_pago OR $tipo_pago=="0")
            @if($item->get_cobrador->id==$id_cobrador OR $id_cobrador=="0")
                <tr>
                    <td>{{ $item->get_cobrador->nombre }}</td>
                    <td> {{ $tipo.'-'.$item->numero_documento }}</td>
                    <td>{{ $item->get_cliente->codigo }}</td>
                    <td>{{ $item->get_cliente->nombre }}</td>
                    <td>{{ $item->created_at->format("d/m/Y")}}</td>
                    <td align="center">{{ $servicio }}</td>
                    @if($item->anulada==0)
                        <td align="center">{{ $item->total }}</td>
                        @php
                            $suma+= $item->total;
                        @endphp
                    @else
                        <td style="color: red" align="center">ANULADA</td>
                    @endif
                    <td>{{ $item->tipo_pago }}</td>
                </tr>
            @endif
        @endif
    @endforeach
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>{{number_format($suma,2)}}</th>
            <th></th>
        </tr>
    </tbody>
</table>