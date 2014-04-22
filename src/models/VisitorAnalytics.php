<?php namespace Isuttell\LaravelAnalytics;

use Illuminate\Database\Eloquent\Model;

class VisitorAnalytics extends Model {
	protected $table = 'visitoranalytics';

	public function getIpAttribute($value)
	{
		return long2ip($value);
	}

	public function setIpAttribute($value)
	{
		$this->attributes['ip'] = ip2long($value);
	}

	public function getGeoAttribute($value)
	{
		return json_decode($value);
	}

	public function pageViews()
	{
		return $this->hasMany('PageView');
	}

}
