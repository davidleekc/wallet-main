<?php
    namespace Modules\Client\Entities;

	use App\Models\BaseModel;
	use Illuminate\Foundation\Auth\User as Authenticatable;


	class Clientprofile extends BaseModel
	{
		protected $fillable = [
			'client_id','first_name', 'last_name', 'country', 'identity_type','identity_no','email',
		];

		public function client()
        {
            return $this->belongsTo('Modules\Client\Entities\Client', 'client_id');
        }

		
	}