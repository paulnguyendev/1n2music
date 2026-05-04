<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class PurchasedModel extends Model
{
    protected $table = 'rrt_purchased';
    protected $primaryKey = 'id';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree'];
    protected $fillable = ['id', 'user_id', 'order_id', 'type', 'name','cycle', 'price', 'status'];
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
    public function addData($type, $data = [])
    {
        return self::create([
            'user_id' => $data['user_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'type' => $type ?? null,
            'name' => $data['name'] ?? null,
            'cycle'=> $data['cycle'] ?? null,
            'price' => $data['price'] ?? 0,
            'status' => 'active',
        ]);
    }
    public function syncData()
    {
        $this->syncOrders(SubscriptionOrderModel::class, 'subscription');

        $this->syncOrders(PlanOrderModel::class, 'plan');
    }

    protected function syncOrders($modelClass, $type)
    {
        $existingOrderIds = self::where('type', $type)->pluck('order_id')->toArray();
        $orders = $modelClass::where('status', 'active')
            ->whereNotIn('id', $existingOrderIds)
            ->get();
        foreach ($orders as $order) {
            $this->addData($type, [
                'user_id' => $order->user_id??null,
                'order_id' => $order->id??null,
                'name' => $order->{$type}->name,
                'price' => $order->{$type}->price ?? $order->{$type}->pricing_annually ?? 0,
            ]);
        }
    }
}
