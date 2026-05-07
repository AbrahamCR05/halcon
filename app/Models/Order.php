<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pedidos';

    protected $fillable = [
        'num_factura',
        'num_cliente',
        'razon_social',
        'datos_fiscales',
        'fecha_hora',
        'direccion_entrega',
        'notas',
        'status',
        'eliminado',
        'route_photo',
        'delivery_photo',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function nextInvoice(): string
    {
        $last = self::withTrashed()->latest('id')->first();
        $next = $last
            ? ((int) str_replace('FAC-', '', $last->num_factura)) + 1
            : 1;
        return 'FAC-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}