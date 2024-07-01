<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Models\Account;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Nette\Utils\Random;

class AccountController extends Controller
{
    use HttpResponses;

    public function getAccounts(){
        $accounts = Account::all();
        return $this->success($accounts);
    }

    public function createAccount(){
        $created = Account::create([
            'id' => Str::uuid(),
            'user_id' => Auth::user()->id,
            'accountNo' => $this->generateAccountNumber(),
            'lastTransactionAt' => now()
        ]);

        return $this->success($created, 'Account Created Successfully');
    }

    private function generateAccountNumber(): int
    {
        return Random::generate(8, '0-9');
    }
}
