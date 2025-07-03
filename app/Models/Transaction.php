<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'bank_balance',
        'cash_in_hand',
        'sales_ledger_id',
        'expense_ledger_id',
        'reason',
    ];

    protected $searchableFields = ['*'];

    public function salesLedger()
    {
        return $this->belongsTo(SalesLedger::class);
    }

    public function expenseLedger()
    {
        return $this->belongsTo(ExpenseLedger::class);
    }
}
