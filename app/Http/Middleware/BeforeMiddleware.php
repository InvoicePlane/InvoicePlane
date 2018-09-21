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
            if (config('ip.forceHttps') and !$request->secure()) {
                return redirect()->secure($request->getRequestUri());
            }

            // This one needs a little special attention
            $dateFormats = DateFormatter::formats();
            config(['ip.datepickerFormat' => $dateFormats[config('ip.dateFormat')]['datepicker']]);

            // Set the environment timezone to the application specific timezone, if available, otherwise UTC
            date_default_timezone_set((config('ip.timezone') ?: config('app.timezone')));

            $mailPassword = '';

            try {
                $mailPassword = (config('ip.mailPassword')) ? Crypt::decrypt(config('ip.mailPassword')) : '';
            } catch (\Exception $e) {
                if (config('ip.mailDriver') == 'smtp') {
                    session()->flash('error', '<strong>' . trans('ip.error') . '</strong> - ' . trans('ip.mail_hash_error'));
                }
            }

            // Override the framework mail configuration with the values provided by the application
            config(['mail.driver' => (config('ip.mailDriver')) ? config('ip.mailDriver') : 'smtp']);
            config(['mail.host' => config('ip.mailHost')]);
            config(['mail.port' => config('ip.mailPort')]);
            config(['mail.encryption' => config('ip.mailEncryption')]);
            config(['mail.username' => config('ip.mailUsername')]);
            config(['mail.password' => $mailPassword]);
            config(['mail.sendmail' => config('ip.mailSendmail')]);

            if (config('ip.mailAllowSelfSignedCertificate')) {
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
            config(['ip.currency' => Currency::where('code', config('ip.baseCurrency'))->first()]);
        }

        config(['ip.clientCenterRequest' => (($request->segment(1) == 'client_center') ? true : false)]);

        if (!config('ip.clientCenterRequest')) {
            app()->setLocale((config('ip.language')) ?: 'en');
        } elseif (config('ip.clientCenterRequest') and auth()->check() and auth()->user()->client_id) {
            app()->setLocale(auth()->user()->client->language);
        }

        config(['ip.mailConfigured' => (config('ip.mailDriver') ? true : false)]);

        config(['ip.merchant' => json_decode(config('ip.merchant'), true)]);

        return $next($request);
    }
}
