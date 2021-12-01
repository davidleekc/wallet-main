<?php
    namespace Modules\Client\Entities;

	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
    use Laravel\Passport\HasApiTokens;
	use Bavix\Wallet\Traits\HasWalletFloat;
	use Bavix\Wallet\Interfaces\WalletFloat;
	use Bavix\Wallet\Interfaces\Wallet;
	use DB;



	class Client extends Authenticatable implements Wallet, WalletFloat
	{
		use HasApiTokens, Notifiable;
		use HasWalletFloat;

	    protected $guard = "client";
		/**
		 * The attributes that are mass assignable.
		 *
		 * @var array
		 */
		protected $fillable = [
			'phone', 'otp', 'question_id', 'question_answer','secret_key',
		];
		/**
		 * The attributes that should be hidden for arrays.
		 *
		 * @var array
		 */
		protected $hidden = [
			'pin', 'remember_token',
		];

		public function getTableColumns()
		{
			$table_info_columns = DB::select(DB::raw('SHOW COLUMNS FROM '.$this->getTable()));
	
			return $table_info_columns;
		}
		
		//public function transactions(){
		//	return $this->hasMany(App\Transaction::class);
		//}

		//public function account(){
		//	return $this->belongsTo(Account::class);
		//}
	}