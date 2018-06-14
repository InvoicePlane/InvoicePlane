<?php

namespace IP\Http\Middleware;

use Closure;
use IP\Modules\Currencies\Models\Currency;
use IP\Modules\Settings\Models\Setting;
use IP\Support\DateFormatter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class BeforeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('app.debug')) {
            DB::enableQueryLog();
        }

        // Set the application specific settings under fi. prefix (fi.settingName)
        if (Setting::setAll()) {
            if (config('fi.forceHttps') and !$request->secure()) {
                return redirect()->secure($request->getRequestUri());
            }

            // This one needs a little special attention
            $dateFormats = DateFormatter::formats();
            config(['fi.datepickerFormat' => $dateFormats[config('fi.dateFormat')]['datepicker']]);

            // Set the environment timezone to the application specific timezone, if available, otherwise UTC
            date_default_timezone_set((config('fi.timezone') ?: config('app.timezone')));

            $mailPassword = '';

            try {
                $mailPassword = (config('fi.mailPassword')) ? Crypt::decrypt(config('fi.mailPassword')) : '';
            } catch (\Exception $e) {
                if (config('fi.mailDriver') == 'smtp') {
                    session()->flash('error', '<strong>' . trans('ip.error') . '</strong> - ' . trans('ip.mail_hash_error'));
                }
            }

            // Override the framework mail configuration with the values provided by the application
            config(['mail.driver' => (config('fi.mailDriver')) ? config('fi.mailDriver') : 'smtp']);
            config(['mail.host' => config('fi.mailHost')]);
            config(['mail.port' => config('fi.mailPort')]);
            config(['mail.encryption' => config('fi.mailEncryption')]);
            config(['mail.username' => config('fi.mailUsername')]);
            config(['mail.password' => $mailPassword]);
            config(['mail.sendmail' => config('fi.mailSendmail')]);

            if (config('fi.mailAllowSelfSignedCertificate')) {
                config([
                    'mail.stream.ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);
            }

            // Force the mailer to use these settings
            (new \Illuminate\Mail\MailServiceProvider(app()))->register();

            // Set the base currency to a config value
            config(['fi.currency' => Currency::where('code', config('fi.baseCurrency'))->first()]);
        }

        config(['fi.clientCenterRequest' => (($request->segment(1) == 'client_center') ? true : false)]);

        if (!config('fi.clientCenterRequest')) {
            app()->setLocale((config('fi.language')) ?: 'en');
        } elseif (config('fi.clientCenterRequest') and auth()->check() and auth()->user()->client_id) {
            app()->setLocale(auth()->user()->client->language);
        }

        config(['fi.mailConfigured' => (config('fi.mailDriver') ? true : false)]);

        config(['fi.merchant' => json_decode(config('fi.merchant'), true)]);

        return $next($request);
    }
}
