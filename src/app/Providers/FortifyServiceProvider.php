<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest as CustomLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Validation\ValidationException;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
{
    $this->app->singleton(FortifyLoginRequest::class, CustomLoginRequest::class);

    $this->app->instance(LoginResponseContract::class, new class implements LoginResponseContract {
        public function toResponse($request)
        {
            if (auth()->check() && auth()->user()->admin_status) {
            
            //管理者
            return redirect('/admin/attendance/list'); 
        }

            // 一般ユーザー
            return redirect()->intended('/attendance');
        }
    });

    $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
        public function toResponse($request)
        {
            if ($request->is('admin/*') || $request->is('admin')) {
                return view('admin.auth.login');
            }
        
            return redirect('/login');
        }
    });
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        //Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        //Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        //Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        //Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        //RateLimiter::for('two-factor', function (Request $request) {
            //return Limit::perMinute(5)->by($request->session()->get('login.id'));
        //});

        //RateLimiter::for('passkeys', function (Request $request) {
            //$credentialId = $request->input('credential.id');

            //return Limit::perMinute(10)->by(
                //($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            //);
        //});

        Fortify::registerView(function () {
            return view('general.auth.register');
        });
        //Fortify::loginView(function () {
            //return view('general.auth.login');
        //});

        Fortify::loginView(function (Request $request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return view('admin.auth.login');
            }
        
            return view('general.auth.login');
        });

        Fortify::authenticateUsing(function (Request $request) {
            
            // 1. 管理者ログイン
            if ($request->is('admin/*') || $request->is('admin')) {
                $admin = User::where('email', $request->email)->whereIn('admin_status', [true, 1, 'true'])->first();

                if ($admin && Hash::check($request->password, $admin->password)) {
                    return $admin;
                }

                if (!Hash::check($request->input('password'), $admin->password)) {
                    throw ValidationException::withMessages([
                        'password' => ['パスワードが間違っています。'],
                    ]);
                }
            } 
            // 2. 一般ユーザーログイン
            else {
            $user = User::where('email', $request->email)->whereIn('admin_status', [false, 0, 'false'])->first();

                if ($user && Hash::check($request->password, $user->password)) {
                    return $user;
                }

                if (!Hash::check($request->input('password'), $user->password)) {
                    throw ValidationException::withMessages([
                        'password' => ['パスワードが間違っています。'],
                    ]);
                }
            }

            return null;
        });
        
    }

}