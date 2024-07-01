<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Requests\WithdrawRequest;
use App\Models\Account;
use App\Models\Transaction;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    use HttpResponses;

    public function showTransactions(Request $request){
        $data = $request->validate([
            'sort' => 'in:asc,desc',
            'type' => 'nullable|string|in:WITHDRAW,DEPOSIT,TRANSFER',
            'pageSize' => 'integer:min:1',
            'page' => 'integer:min:1'
        ]);
        $sort = $data['sort'] ?? 'desc';
        $type = $data['type'] ?? null;
        $pageSize = $data['pageSize'] ?? 5;

        $query = DB::table('transactions')->orderBy('created_at', $sort);
        if($type){
            $query->where('transactionType',$type);
        }

        $transactions = $query->paginate($pageSize);
        return $this->success($transactions, 'Transactions Retrieved Successfully');
    }

    public function deposit(DepositRequest $request)
    {
        $account = Account::where('user_id',Auth::id())->first();
        if($account == null){
            return $this->error(404, "User does not have an account");
        }
        $created = DB::transaction(function () use($account, $request){
            $account->accountBalance += $request->amount;
            $account->lastTransactionAt = now();
            $account->save();

            return Transaction::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'accountNo' => $account->accountNo,
                'amount' => $request->amount,
                'transactionType' => 'DEPOSIT'
            ]);
        });
        return $this->success($created, 'Transaction Successful');
    }

    public function withdraw(WithdrawRequest $request){
        $account = Account::where('user_id',Auth::id())->first();

        if($account == null){
            return $this->error(404, "User does not have an account");
        }
        if($account->accountBalance < $request->amount){
            return $this->error(400, "Insufficient Balance");
        }
        $created = DB::transaction(function () use($account, $request){
            $account->accountBalance -= $request->amount;
            $account->lastTransactionAt = now();
            $account->save();

            return Transaction::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'accountNo' => $account->accountNo,
                'amount' => $request->amount,
                'transactionType' => 'WITHDRAW'
            ]);
        });
        if(!$created){
            return $this->error(400, "Something Went Wrong");
        }
        return $this->success($created, 'Transaction Successful');
    }

    public function transfer(TransferRequest $request){
        $senderAccount = Account::where('user_id',Auth::id())->first();
        $receiverAccount = Account::where('accountNo', $request->accountNo)->first();
        if($senderAccount == null){
            return $this->error(404, "User does not have an account");
        }
        if($receiverAccount == null){
            return $this->error(404, "Invalid Account Number");
        }
        if($senderAccount->accountBalance < $request->amount){
            return $this->error(400, "Insufficient Balance");
        }

        $created = DB::transaction(function() use($senderAccount,$receiverAccount, $request){
            $senderAccount->accountBalance -= $request->amount;
            $senderAccount->save();
            $receiverAccount->accountBalance += $request->amount;
            $receiverAccount->save();

            return Transaction::create([
                'id' => Str::uuid(),
                'user_id' => Auth::id(),
                'accountNo' => $receiverAccount->accountNo,
                'amount' => $request->amount,
                'transactionType' => 'TRANSFER'
            ]);
        });
        if(!$created){
            return $this->error(400, "Something Went Wrong");
        }
        return $this->success($created, 'Transaction Successful');

    }
}
