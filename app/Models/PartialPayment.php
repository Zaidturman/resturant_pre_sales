<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartialPayment extends Model
{
    use HasFactory;

    // تحديد الأعمدة التي يمكن ملؤها
    protected $fillable = [
        'customer_id',
        'amount',
    'invoice_id', // إضافة هذا

        'payment_date',
         'discount_amount',
        'payment_method',
    ];
// علاقة مع الفاتورة
public function invoices()
{
    return $this->belongsToMany(Invoice::class, 'invoice_partial_payment')
                ->withPivot('amount')
                ->withTimestamps();
}
    // إنشاء علاقة الربط مع نموذج Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
