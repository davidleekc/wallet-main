<?php
   
namespace Modules\Deposit\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiController as BaseController;
use Modules\Client\Entities\Client;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use Storage;
use DB;
use Ejarnutowski\LaravelApiKey\Models\ApiKey;
use Ejarnutowski\LaravelApiKey\Models\ApiKeyAccessEvent;

   
class DepositController extends BaseController
{
    public function apiCheck($key)
    {
        //api key check
        if($key){
            $apiKey = ApiKey::getByKey($key);

            if ($apiKey != null) {
                return true;
            }else{
                return false;
            }
            
        }else{
            return false;
        }
    }

    protected function apiLog($api_id, $ip, $url)
    {
        $event = new ApiKeyAccessEvent;
        $event->api_key_id = $api_id;
        $event->ip_address = $ip;
        $event->url        = $url;
        $event->save();
    }

    /**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/deposit/reload",
    *      operationId="executeClientReload",
    *      tags={"Deposit"},
    *      security={{"bearerAuth":{}}},
    *      summary="reload wallet",
    *      description="Reload wallet balance",
	* @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="reload",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    *   @OA\Response(
    *      response=200,
    *      description="Successful operation",
    *      @OA\MediaType(
    *      		mediaType="application/json",
    *   	)
    *      ),
    *   @OA\Response(
    *      response=401,
    *      description="Unauthenticated",
    *   ),
    *   @OA\Response(
    *      response=403,
    *      description="Forbidden"
    *   ),
    * @OA\Response(
    *      response=400,
    *      description="Bad Request"
    *   ),
    * @OA\Response(
    *      response=404,
    *      description="not found"
    *   ),     
	*  )
    */
    
    /**
     * Submit client profile
     *
     * @return \Illuminate\Http\Response
     */
    public function reload(Request $request)
    {
        $api_id = $this->apiCheck($request->key);
        if(!$api_id){
            return $this->sendError('Invalid Api Key', ['error'=>'Please provide a valid api key!']);
        }else{
            $ip = $request->ip();
            $url = $request->fullUrl();
            $this->apiLog($api_id, $ip, $url);
        }

        $check = Auth::guard('client-api')->check();
        $id = auth()->user()->id;
        if($check){
            $validator = Validator::make($request->all(), [
                'reload' => 'required|integer|between:1000,100000',
            ]);
       
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }     
            $client = Client::find($id);
            $client->deposit($request->reload,['title'=>'reload','payment_method'=>'Banking / Card', 'r_balance' => $client->balance+$request->reload]);



            return $this->sendResponse($client->balanceFloat, 'Reload successful!.');
            
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    /**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/deposit/check_balance",
    *      operationId="executeClientBalance",
    *      tags={"Deposit"},
    *      security={{"bearerAuth":{}}},
    *      summary="Check client balance",
    *      description="Get client balance based on the token given",
	* @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    *   @OA\Response(
    *      response=200,
    *      description="Successful operation",
    *      @OA\MediaType(
    *      		mediaType="application/json",
    *   	)
    *      ),
    *   @OA\Response(
    *      response=401,
    *      description="Unauthenticated",
    *   ),
    *   @OA\Response(
    *      response=403,
    *      description="Forbidden"
    *   ),
    * @OA\Response(
    *      response=400,
    *      description="Bad Request"
    *   ),
    * @OA\Response(
    *      response=404,
    *      description="not found"
    *   ),     
	*  )
    */

    public function checkBalance(Request $request)
    {
        $api_id = $this->apiCheck($request->key);
        if(!$api_id){
            return $this->sendError('Invalid Api Key', ['error'=>'Please provide a valid api key!']);
        }else{
            $ip = $request->ip();
            $url = $request->fullUrl();
            $this->apiLog($api_id, $ip, $url);
        }

        $check = Auth::guard('client-api')->check();
        $id = auth()->user()->id;
        if($check){
            $client = Client::find($id);
            return $this->sendResponse($client->balanceFloat, 'Balance retrieved successful.');
            
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    /**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/deposit/check_transaction",
    *      operationId="executeClientTransaction",
    *      tags={"Deposit"},
    *      security={{"bearerAuth":{}}},
    *      summary="Check client transaction",
    *      description="Get all client transaction based on the token given",
	* @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    *   @OA\Response(
    *      response=200,
    *      description="Successful operation",
    *      @OA\MediaType(
    *      		mediaType="application/json",
    *   	)
    *      ),
    *   @OA\Response(
    *      response=401,
    *      description="Unauthenticated",
    *   ),
    *   @OA\Response(
    *      response=403,
    *      description="Forbidden"
    *   ),
    * @OA\Response(
    *      response=400,
    *      description="Bad Request"
    *   ),
    * @OA\Response(
    *      response=404,
    *      description="not found"
    *   ),     
	*  )
    */

    public function checkTransaction(Request $request)
    {
        $api_id = $this->apiCheck($request->key);
        if(!$api_id){
            return $this->sendError('Invalid Api Key', ['error'=>'Please provide a valid api key!']);
        }else{
            $ip = $request->ip();
            $url = $request->fullUrl();
            $this->apiLog($api_id, $ip, $url);
        }

        $check = Auth::guard('client-api')->check();
        $id = auth()->user()->id;
        if($check){
            $transactions = Transaction::select('type','amount','confirmed','meta','created_at')->where('payable_id', $id)->get();

            return $this->sendResponse($transactions, 'Transactions retrieved successful.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    /**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/deposit/transfer",
    *      operationId="executeClientTransfer",
    *      tags={"Deposit"},
    *      security={{"bearerAuth":{}}},
    *      summary="Transfer to another phone number",
    *      description="Transfer amount to other phone number",
	* @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    * @OA\Parameter(
    *      name="pay_to_phone",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    * @OA\Parameter(
    *      name="amount",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    *   @OA\Response(
    *      response=200,
    *      description="Successful operation",
    *      @OA\MediaType(
    *      		mediaType="application/json",
    *   	)
    *      ),
    *   @OA\Response(
    *      response=401,
    *      description="Unauthenticated",
    *   ),
    *   @OA\Response(
    *      response=403,
    *      description="Forbidden"
    *   ),
    * @OA\Response(
    *      response=400,
    *      description="Bad Request"
    *   ),
    * @OA\Response(
    *      response=404,
    *      description="not found"
    *   ),     
	*  )
    */

    public function transfer(Request $request)
    {
        $api_id = $this->apiCheck($request->key);
        if(!$api_id){
            return $this->sendError('Invalid Api Key', ['error'=>'Please provide a valid api key!']);
        }else{
            $ip = $request->ip();
            $url = $request->fullUrl();
            $this->apiLog($api_id, $ip, $url);
        }

        $check = Auth::guard('client-api')->check();
        $id = auth()->user()->id;
        if($check){
            $payUser = Client::find($id); 
            $receiveUser = Client::where('phone', '=', $request->pay_to_phone)->first();

            if($payUser->transfer($receiveUser, $request->amount, ['title' => 'transfer', 'r_balance' => $receiveUser->balance+$request->amount, 'p_balance'=>$payUser->balance-$request->amount])){
                return $this->sendResponse('Transfer Successfully!', 'Transactions retrieved successful.');
            }else{
                return $this->sendError('Insuffecient Fund.', ['error'=>'Insuffecient Fund']);
            }         
            
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}