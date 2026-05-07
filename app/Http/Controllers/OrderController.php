<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function lookup(Request $request)
    {
        $request->validate([
            'client_num' => 'required|string',
            'invoice'    => 'required|string',
        ]);

        $order = Order::where('num_cliente', $request->client_num)
                      ->where('num_factura', $request->invoice)
                      ->whereNull('deleted_at')
                      ->first();

        if (!$order) {
            return back()->with('lookup_error', 'No se encontró ningún pedido con esos datos.');
        }

        return back()->with('lookup_result', [
            'invoice'        => $order->num_factura,
            'client_name'    => $order->razon_social,
            'status'         => $order->status,
            'delivery_photo' => $order->delivery_photo
                                    ? Storage::url($order->delivery_photo)
                                    : null,
        ]);
    }

    public function index(Request $request)
    {
        $query = Order::with('creator')->whereNull('deleted_at');

        if ($q = $request->invoice)    $query->where('num_factura',  'like', "%$q%");
        if ($q = $request->client_num) $query->where('num_cliente',  'like', "%$q%");
        if ($q = $request->status)     $query->where('status', $q);
        if ($q = $request->date)       $query->whereDate('fecha_hora', $q);

        $orders = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Orders/Index', [
            'orders'  => $orders,
            'filters' => $request->only(['invoice', 'client_num', 'status', 'date']),
        ]);
    }

    public function deleted()
    {
        $orders = Order::with('creator')->onlyTrashed()->latest()->paginate(15);
        return Inertia::render('Orders/Deleted', ['orders' => $orders]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'num_cliente'      => 'required|string|max:50',
            'razon_social'     => 'required|string|max:200',
            'datos_fiscales'   => 'nullable|string',
            'fecha_hora'       => 'required|date',
            'direccion_entrega'=> 'required|string',
            'notas'            => 'nullable|string',
        ]);

        $data['num_factura'] = Order::nextInvoice();
        $data['status']      = 'Ordered';
        $data['created_by']  = auth()->id();

        Order::create($data);

        return back()->with('success', 'Pedido creado correctamente.');
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'num_cliente'      => 'sometimes|string|max:50',
            'razon_social'     => 'sometimes|string|max:200',
            'datos_fiscales'   => 'nullable|string',
            'fecha_hora'       => 'sometimes|date',
            'direccion_entrega'=> 'sometimes|string',
            'notas'            => 'nullable|string',
        ]);

        $order->update($data);
        return back()->with('success', 'Pedido actualizado.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Ordered,In Process,In Route,Delivered',
        ]);

        $order->update(['status' => $request->status]);
        return back()->with('success', 'Estado actualizado.');
    }

    public function uploadPhoto(Request $request, Order $order)
    {
        $request->validate([
            'type'  => 'required|in:route,delivery',
            'photo' => 'required|image|max:5120',
        ]);

        $path = $request->file('photo')->store('order-photos', 'public');

        if ($request->type === 'route') {
            $order->update(['route_photo' => $path]);
        } else {
            $order->update(['delivery_photo' => $path, 'status' => 'Delivered']);
        }

        return back()->with('success', 'Foto subida correctamente.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Pedido eliminado.');
    }

    public function restore($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->restore();
        return back()->with('success', 'Pedido restaurado.');
    }
}