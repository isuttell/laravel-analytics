<?php namespace Isuttell\LaravelAnalytics;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model {
	protected $table = 'pageviews';

	public function analytics()
	{
	    return $this->belongsTo('VisitorAnalytics');
	}
}