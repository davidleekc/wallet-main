<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	/**
	* @OA\Info(
	*      version="1.0.0",
	*      title="Laravel Ewallet",
	*      description="The api for third party ewallet application",
	*      @OA\Contact(
	*          email="admin@admin.com"
	*      ),
	*      @OA\License(
	*          name="Apache 2.0",
	*          url="http://www.apache.org/licenses/LICENSE-2.0.html"
	*      )
	* )
	*
	*
	* @OA\SecurityScheme(
	*     type="http",
	*     description="Login with email and password to get the authentication token",
	*     name="Token based Based",
	*     in="header",
	*     scheme="bearer",
	*     bearerFormat="JWT",
	*     securityScheme="bearerAuth",
	* )
	*/
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
}
