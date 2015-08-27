<?php namespace App;

use App\Contracts\PermissionUser;
use Illuminate\Database\Eloquent\Model;

class Report extends Model {
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reports';
	
	/**
	 * The primary key that is used by ::get()
	 *
	 * @var string
	 */
	protected $primaryKey = 'report_id';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['reason', 'board_uri', 'post_id', 'ip', 'user_id', 'is_dismissed', 'is_successful'];
	
	
	public function board()
	{
		return $this->belongsTo('\App\Board', 'board_uri');
	}
	
	public function post()
	{
		return $this->belongsTo('\App\Post', 'post_id');
	}
	
	public function user()
	{
		return $this->belongsTo('\App\User', 'user_id');
	}
	
	
	/**
	 * Determines if a user can view this report in any context.
	 * This does not determine if a report is useful in a management view.
	 *
	 * @param  PermissionUser  $user
	 * @return boolean
	 */
	public function canView(PermissionUser $user)
	{
		if (is_null($this->board_uri) && $user->canViewReportsGlobally())
		{
			return true;
		}
		
		if (!is_null($this->board_uri) && $user->canViewReports($this->board_uri))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Determines if the user can dismiss the report.
	 *
	 * @param  PermissionUser  $user
	 * @return boolean
	 */
	public function canDismiss(PermissionUser $user)
	{
		// At the moment, anyone who can view can dismiss.
		return $this->canView($user);
	}
	
	public function scopeWhereOpen($query)
	{
		return $query->where(function($query) {
			$query->where('is_dismissed', false);
			$query->where('is_successful', false);
		});
	}
}