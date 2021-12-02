<?php
   
namespace Modules\Client\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiController as BaseController;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\Clientprofile;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use Ejarnutowski\LaravelApiKey\Models\ApiKey;
use Ejarnutowski\LaravelApiKey\Models\ApiKeyAccessEvent;
use Storage;
   
class RegisterController extends BaseController
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
    *   path="/e-wallet-laravel-admin/public/api/register",
    *   tags={"Auth"},
    *   summary="Register",
    *   operationId="executeRegister",
    * @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    *   @OA\Parameter(
    *      name="phone",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    *   
    *   @OA\Response(
    *      response=200,
    *       description="Success",
    *      @OA\MediaType(
    *           mediaType="application/json",
    *      )
    *   ),
    *   @OA\Response(
    *      response=401,
    *       description="Unauthenticated"
    *   ),
    *   @OA\Response(
    *      response=400,
    *      description="Bad Request"
    *   ),
    *   @OA\Response(
    *      response=404,
    *      description="not found"
    *   ),
    *      @OA\Response(
    *          response=403,
    *          description="Forbidden"
    *      )
    *)
    **/
	
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {   
        $api_id = $this->apiCheck($request->key);
        if(!$api_id){
            return $this->sendError('Invalid Api Key', ['error'=>'Please provide a valid api key!']);
        }else{
            $ip = $request->ip();
            $url = $request->fullUrl();
            $this->apiLog($api_id, $ip, $url);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $client = Client::where('phone', '=', $request->phone)->first();
        if($client){
            if($client->is_activate == false){
        
                $client->phone = $request->phone;
                $client->otp = mt_rand(100000, 999999);
                $client->otp_expired = Carbon::now()->addMinutes(5);
                $client->otp_is_valid = true;
                $client->is_activate = false;
                $client->save();
                
                $success['token'] =  $client->createToken('E-wallet API')->accessToken;
                $success['OTP'] =  $client->otp;
           
                return $this->sendResponse($success, 'Client register successfully. Please enter the OTP within 5 minutes');
            }elseif($client->is_activate == true){
                return $this->sendError('Phone number already taken!', ['error'=>'Phone number already taken!']);
            }
        }else{
            $validator = Validator::make($request->all(), [
                'phone' => 'unique:clients',
            ]);
       
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $client = new Client;
        
            $client->phone = $request->phone;
            $client->otp = mt_rand(100000, 999999);
            $client->otp_expired = Carbon::now()->addMinutes(5);
            $client->otp_is_valid = true;
            $client->is_activate = false;
            $client->save();
            
            $success['token'] =  $client->createToken('E-wallet API')->accessToken;
            $success['OTP'] =  $client->otp;
    
            return $this->sendResponse($success, 'Client register successfully. Please enter the OTP within 5 minutes');
        }
        

        
    }
	
	/**
    * @OA\Post(
    *   path="/e-wallet-laravel-admin/public/api/forgot_pin",
    *   tags={"Auth"},
    *   summary="Forgot Pin",
    *   operationId="executeForgotPin",
    * @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    *   @OA\Parameter(
    *      name="phone",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    *   
    *   @OA\Response(
    *      response=200,
    *       description="Success",
    *      @OA\MediaType(
    *           mediaType="application/json",
    *      )
    *   ),
    *   @OA\Response(
    *      response=401,
    *       description="Unauthenticated"
    *   ),
    *   @OA\Response(
    *      response=400,
    *      description="Bad Request"
    *   ),
    *   @OA\Response(
    *      response=404,
    *      description="not found"
    *   ),
    *      @OA\Response(
    *          response=403,
    *          description="Forbidden"
    *      )
    *)
    **/	

    /**
     * Enter phone no
     *
     * @return \Illuminate\Http\Response
     */
    public function forgotPin(Request $request)
    {
        $api_id = $this->apiCheck($request->key);
        if(!$api_id){
            return $this->sendError('Invalid Api Key', ['error'=>'Please provide a valid api key!']);
        }else{
            $ip = $request->ip();
            $url = $request->fullUrl();
            $this->apiLog($api_id, $ip, $url);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $client = Client::where('phone', '=', $request->phone)->first();

        if($client !=null){
            $client->otp = mt_rand(100000, 999999);
            $client->otp_expired = Carbon::now()->addMinutes(5);
            $client->otp_is_valid = true;
            $client->save();
            
            $success['token'] =  $client->createToken('E-wallet API')->accessToken;
            $success['OTP'] =  $client->otp;
    
            return $this->sendResponse($success, 'Phone number validate!. Please enter your OTP for password reset.');
        }else{
            return $this->sendError('Not registered.', ['error'=>'This phone number is not registered.']);
        }
        
        // echo $request->ip();
        // // server ip

        // echo \Request::ip();
        // // server ip

        // echo \request()->ip();
        // // server ip

        // echo $this->getIp(); //see the method below
        // // clent ip

    }
    
    // public function getIp(){
    //     foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
    //         if (array_key_exists($key, $_SERVER) === true){
    //             foreach (explode(',', $_SERVER[$key]) as $ip){
    //                 $ip = trim($ip); // just to be safe
    //                 if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
    //                     return $ip;
    //                 }
    //             }
    //         }
    //     }
    //     return request()->ip(); // it will return server ip when no client ip found
    // }
    

    /**
     * Enter phone no
     *
     * @return \Illuminate\Http\Response
     */
    public function validatePhone(Request $request)
    {
        $api_id = $this->apiCheck($request->key);
        if(!$api_id){
            return $this->sendError('Invalid Api Key', ['error'=>'Please provide a valid api key!']);
        }else{
            $ip = $request->ip();
            $url = $request->fullUrl();
            $this->apiLog($api_id, $ip, $url);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $client = Client::where('phone', '=', $request->phone)->first();

        if($client !=null){
            return $this->sendResponse('Valid', 'Phone verified');
        }else{
            return $this->sendError('Not registered.', ['error'=>'This phone number is not registered.']);
        }
   
        
    }
	
	/**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/client/otp",
    *      operationId="executeVerifyOtp",
    *      tags={"Auth"},
    *      security={{"bearerAuth":{}}},
    *      summary="Verify OTP",
    *      description="Update OTP status once successfully validate",
	* @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="phone",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="otp",
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
    * Verify OTP
    *
    * @return \Illuminate\Http\Response
    */
    public function verifyOTP(Request $request)
    {
        $api_id = $this->apiCheck($request->key);
        if(!$api_id){
            return $this->sendError('Invalid Api Key', ['error'=>'Please provide a valid api key!']);
        }else{
            $ip = $request->ip();
            $url = $request->fullUrl();
            $this->apiLog($api_id, $ip, $url);
        }

        Auth::guard('client-api')->check();
        if($request->phone){ 
            $client = Client::where('phone', '=', $request->phone)->first();
            //$check = auth()->user()->id;
            //return $this->sendResponse($client  ,'OTP successfully verified.');
            //die;
            if($request->otp){
                if($client->otp == $request->otp){

                    if($client->otp_expired >= Carbon::now()){
    
                        if($client->otp_is_valid == true){
                            $client->otp_is_valid = false;
                            $client->save();
                            return $this->sendResponse('Validation Success','OTP successfully verified.');
                        }else{
                            return $this->sendError('Invalid.', ['error'=>'Otp is Invalid!']);
                        }
                    }else{
                        return $this->sendError('Expired.', ['error'=>'Otp is expired!']);
                    }
                }else{
                    return $this->sendError('Invalid', 'Wrong OTP, please try again.');
                }  
            }else{
                return $this->sendError('Missing', 'Please enter OTP');
            }
        } 
        else{ 
            return $this->sendError('Missing.', ['error'=>'Phone number missing']);
        } 
        return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
    }
	
	/**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/client/refresh_otp",
    *      operationId="executeRefreshOtp",
    *      tags={"Auth"},
    *      security={{"bearerAuth":{}}},
    *      summary="Refresh OTP",
    *      description="Update new OTP number, status, and expired time for each refresh",
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

    /**
     * Refresh OTP
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshOTP(Request $request)
    {
        $api_id = $this->apiCheck($request->key);
        if(!$api_id){
            return $this->sendError('Invalid Api Key', ['error'=>'Please provide a valid api key!']);
        }else{
            $ip = $request->ip();
            $url = $request->fullUrl();
            $this->apiLog($api_id, $ip, $url);
        }

        Auth::guard('client-api')->check();
        $id = auth()->user()->id;
        if($id){ 
            $client = Client::find($id);

            if($client != null){
                $client->otp = mt_rand(100000, 999999);
                $client->otp_expired = Carbon::now()->addMinutes(5);
                $client->otp_is_valid = true;
                $client->save();

                return $this->sendResponse($client,'OTP successfully refreshed, please verify your otp within 5 minute.');
            }
            //die;
            //$client = Client::where('phone', '=', $request->phone)->first();
        } 
        else{ 
            return $this->sendError('Missing.', ['error'=>'Invalid Token']);
        } 
        return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
    }
	
	/**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/client/setup_pin",
    *      operationId="executeVerifyOtp",
    *      tags={"Auth"},
    *      security={{"bearerAuth":{}}},
    *      summary="Register Pin Number",
    *      description="Can be used for register/ forgot pin",
	* @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="pin",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="c_pin",
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
     * Register pin api
     *
     * @return \Illuminate\Http\Response
     */
    public function registerPin(Request $request)
    {
        $api_id = $this->apiCheck($request->key);
        if(!$api_id){
            return $this->sendError('Invalid Api Key', ['error'=>'Please provide a valid api key!']);
        }else{
            $ip = $request->ip();
            $url = $request->fullUrl();
            $this->apiLog($api_id, $ip, $url);
        }

        Auth::guard('client-api')->check();
        $id = auth()->user()->id;
        if($id){ 
            $validator = Validator::make($request->all(), [
                'pin' => 'required|integer',
                'c_pin' => 'required|same:pin',
            ]);
       
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $client = Client::find($id);
            $client->pin = $request->pin;
            if($client->is_activate == false){
                $client->is_activate = true;
            }
            $client->save();
   
            return $this->sendResponse($client, 'Pin setup successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
	
	/**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/client/get_question",
    *      operationId="executeGetQuestion",
    *      tags={"Auth"},
    *      security={{"bearerAuth":{}}},
    *      summary="Get Security Question",
    *      description="Get a list of security question",
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

    /**
     * Get question for user to choose and answer
     *
     * @return \Illuminate\Http\Response
     */
    public function getSecurityQuestion(Request $request)
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
        if($check){ 
            $path = storage_path() . "/json/question.json";
            $json = json_decode(file_get_contents($path), true); 

            return $this->sendResponse($json, 'Question pulled successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
	
	/**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/client/register_question",
    *      operationId="executeRegisterQuestion",
    *      tags={"Auth"},
    *      security={{"bearerAuth":{}}},
    *      summary="Register Security Question",
    *      description="Update Security Question for client",
	* @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="question_id",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="question_answer",
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
     * Submit security question
     *
     * @return \Illuminate\Http\Response
     */
    public function registerSecurityQuestion(Request $request)
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
            
            if($request->question_id){
                $validator = Validator::make($request->all(), [
                    'question_answer' => 'required|min:3|max:191',
                    
                ]);
           
                if($validator->fails()){
                    return $this->sendError('Validation Error.', $validator->errors());       
                }

                $client = Client::find($id);
                $client->question_id = $request->question_id;
                $client->question_answer = $request->question_answer;
                $client->save();

                return $this->sendResponse($client, 'Question pulled successfully.');
            }else{
                return $this->sendError('Empty input', ['error'=>'Input cant be empty.']);        
            }                       
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
	
	/**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/client/verify_question",
    *      operationId="executeVerifyQuestion",
    *      tags={"Auth"},
    *      security={{"bearerAuth":{}}},
    *      summary="Verify Security Question",
    *      description="Used for validate security question for client",
	* @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="question_id",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="question_answer",
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
     * Submit security question
     *
     * @return \Illuminate\Http\Response
     */
    public function verifySecurityQuestion(Request $request)
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
            if($client->question_id == $request->question_id){

                if($client->question_answer == $request->question_answer){
                    return $this->sendResponse($client, 'Verified success!');
                }else{
                    return $this->sendError('Wrong answer', ['error'=>'The answer you submitted is not match']); 
                }
          
            }else{
                return $this->sendError('Wrong question', ['error'=>'The question you choose is not match']);        
            }                       
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
	
	/**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/client/setup_profile",
    *      operationId="executeSetupProfile",
    *      tags={"Auth"},
    *      security={{"bearerAuth":{}}},
    *      summary="Verify Security Question",
    *      description="Client can setup thier profile here",
	* @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="first_name",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="last_name",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="country",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="identity_type",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="identity_no",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="email",
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
    public function registerClientProfile(Request $request)
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
            $checkProfile = Clientprofile::where('client_id', '=', $id)->first(); 
            if($checkProfile == null){
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required|min:2',
                    'last_name' => 'required|min:2',
                    'country' => 'required',
                    'identity_type' => 'required',
                    'identity_no' => 'required|unique:clientprofiles',
                    'email' => 'required|email|unique:clientprofiles',
                    
                ]);
           
                if($validator->fails()){
                    return $this->sendError('Validation Error.', $validator->errors());       
                }
    
                $clientprofile = new Clientprofile();
    
                $clientprofile->client_id = $id;
                $clientprofile->first_name = $request->first_name;
                $clientprofile->last_name = $request->last_name;
                $clientprofile->country = $request->country;
                $clientprofile->identity_type = $request->identity_type;
                $clientprofile->identity_no = $request->identity_no;
                $clientprofile->email = $request->email;
                $clientprofile->save();
    
                return $this->sendResponse($clientprofile, 'Profile register successfully.');
                
            }else{
                return $this->sendError('Duplicate client profile', 0111111125);
            }
            
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    /**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/client/get_profile",
    *      operationId="executeGetProfile",
    *      tags={"Auth"},
    *      security={{"bearerAuth":{}}},
    *      summary="Get Client Profile",
    *      description="To get all client profile based on given token",
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

    /**
     * get client profile
     *
     * @return \Illuminate\Http\Response
     */
    public function getClientProfile(Request $request)
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
            $getProfile = Clientprofile::leftJoin('clients', 'clients.id', '=', 'clientprofiles.client_id')->select('clientprofiles.*', 'clients.phone')->where('client_id', '=', $id)->first(); 
            if($getProfile != null){
                return $this->sendResponse($getProfile, 'Profile pulled successfully.');
            }else{
                return $this->sendError('Profile not created.', ['error'=>'Profile not created']);
            }
            
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
	
	/**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/client/update_profile",
    *      operationId="executeUpdateProfile",
    *      tags={"Auth"},
    *      security={{"bearerAuth":{}}},
    *      summary="Update Client Profile",
    *      description="Client can update thier profile here",
	* @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="first_name",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="last_name",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="country",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="identity_type",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="identity_no",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
	*   @OA\Parameter(
    *      name="email",
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
     * Update client profile
     *
     * @return \Illuminate\Http\Response
     */
    public function updateClientProfile(Request $request)
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
            $clientprofile = Clientprofile::where('client_id', '=', $id)->first(); 
            if($clientprofile != null){
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required|min:2',
                    'last_name' => 'required|min:2',
                    'country' => 'required',
                    'identity_type' => 'required',
                    'identity_no' => 'required',
                    'email' => 'required|email',
                    
                ]);
           
                if($validator->fails()){
                    return $this->sendError('Validation Error.', $validator->errors());       
                }

                $clientprofile->first_name = $request->first_name;
                $clientprofile->last_name = $request->last_name;
                $clientprofile->country = $request->country;
                $clientprofile->identity_type = $request->identity_type;
                $clientprofile->identity_no = $request->identity_no;
                $clientprofile->email = $request->email;
                $clientprofile->save();
    
                return $this->sendResponse($clientprofile, 'Profile updated successfully.');
                
            }else{
                return $this->sendError('Unknown error', ['error'=>'Unknown error']);
            }
            
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

	/**
    * @OA\Post(
    *      path="/e-wallet-laravel-admin/public/api/login",
    *      operationId="executeLogin",
    *      tags={"Auth"},
    * @OA\Parameter(
    *      name="key",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    * @OA\Parameter(
    *      name="phone",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="string"
    *      )
    *   ),
    * @OA\Parameter(
    *      name="pin",
    *      in="query",
    *      required=true,
    *      @OA\Schema(
    *           type="integer"
    *      )
    *   ),
    *      summary="Login for ewallet",
    *      description="return token upon successful login",
    *      @OA\Response(
    *          response=200,
    *          description="Successful operation",
    *          @OA\MediaType(
    *           mediaType="application/json",
    *      )
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthenticated",
    *      ),
    *      @OA\Response(
    *          response=403,
    *          description="Forbidden"
    *      ),
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
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
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
        if(!$check){ 
            $client = Client::where('phone', '=', $request->phone)->first();
            if($client != null){
                
                if($client->pin == $request->pin){
                    $success['token'] =  $client->createToken('LaravelEwallet')-> accessToken; 
                    $success['name'] =  $client->name;
    
                    return $this->sendResponse($success, 'Login successfully.');
                }else{
                    return $this->sendError('Login Error.', ['error'=>'Invalid phone no or pin']);
                } 
                
            }else{
                return $this->sendError('Invalid login information', ['error'=>'Please enter login detail']);
            }
            
        } 
        else{ 
            return $this->sendError('Already login.', ['error'=>'Logged in']);
        } 
    }
}