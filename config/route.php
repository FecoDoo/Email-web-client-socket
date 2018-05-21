<?php
return [
	Route::rule('/','index/index/index'),

	Route::rule('/Check.php','index/check/login?email=null&passwd=null'),
	Route::rule('/mail/send','index/mail/send'),
	Route::rule('/mail/receive','index/mail/receive'),
	Route::rule('/mail','index/mail/mail'),

	Route::rule('/password_check/:a','index/index/index'),

	Route::rule('/dashboard','index/ui/dashboard'),
	Route::rule('/inbox','index/ui/inbox'),
	Route::rule('/empty','index/ui/empty'),
	Route::rule('/send','index/ui/send'),

	Route::rule('/test','index/index/test'),
	Route::get('/info','index/index/info'),
];