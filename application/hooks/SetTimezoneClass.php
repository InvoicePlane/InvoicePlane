<?php 
class  SetTimezoneClass {
	public function setTimezone()
	{
		date_default_timezone_set(date_default_timezone_get());
	}
}