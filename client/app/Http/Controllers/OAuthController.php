<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
// use GuzzleHttp\Client;

class OAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $queries = http_build_query([
            'client_id' => '4',
            'redirect_uri' => 'http://client.local/oauth/callback',
            'response_type' => 'code',
            'scope' => 'view-posts'
            // 'scope' => 'view-posts view-user'
        ]);

        return redirect('http://server.local/oauth/authorize?' . $queries);
    }

    public function callback(Request $request)
    {
        $response = Http::post('http://server.local/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => '4',
            'client_secret' => 'zHimZetZeqIWaNpQVCqRrTwYRHAxMyj5UScKhI1w',
            'redirect_uri' => 'http://client.local/oauth/callback',
            'code' => $request->code,
        ]);

        $response = $response->json();
        // dd($response);
        $request->user()->token()->delete();

        $request->user()->token()->create([
            'access_token' => $response['access_token'],
            'expires_in' => $response['expires_in'],
            'refresh_token' => $response['refresh_token']
        ]);

        return redirect('/home');
    }

    public function refresh(Request $request)
    {
        $response = Http::post('http://server.local/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->user()->token->refresh_token,
            'client_id' => '4',
            'client_secret' => 'zHimZetZeqIWaNpQVCqRrTwYRHAxMyj5UScKhI1w',
            'redirect_uri' => 'http://client.local/oauth/callback',
            'scope' => 'view-posts'
        ]);

        // if ($response->status() !== 200) {
        //     $request->user()->token()->delete();
        //     return redirect('/home')->withStatus('Authorization failed from OAuth server.');
        // }

        $response = $response->json();
        $request->user()->token()->update([
            'access_token' => $response['access_token'],
            'expires_in' => $response['expires_in'],
            'refresh_token' => $response['refresh_token']
        ]);

        return redirect('/home');
    }

    // public function redirect(Request $request)
    // {
    //     $request->session()->put('state', $state = Str::random(40));

    //     $queries = http_build_query([
    //         'client_id' => '4',
    //         'redirect_uri' => 'http://client.local/oauth/callback',
    //         'response_type' => 'code',
    //         'state' => $state,
    //         'scope' => '',
    //     ]);

    //     return redirect('http://server.local/oauth/authorize?' . $queries);
    // }

    // composer require guzzlehttp/guzzle
    // public function callback(Request $request)
    // {
    //     // dd($request->code);
    //     $state = $request->session()->pull('state');

    //     throw_unless(
    //         strlen($state) > 0 && $state === $request->state,
    //         InvalidArgumentException::class
    //     );

    //     $http = new \GuzzleHttp\Client;

    //     $response = $http->post('http://server.local/oauth/token', [
    //         'form_params' => [
    //             'grant_type' => 'authorization_code',
    //             'client_id' => config('services.oauth_server.client_id'),
    //             'client_secret' => config('services.oauth_server.client_secret'),
    //             'redirect_uri' => config('services.oauth_server.redirect'),
    //             'code' => $request->code,
    //         ]
    //     ]);

    //     $temp = json_decode((string) $response->getBody(), true);
    //     dd($temp);
    // }

    // public function callback(Request $request)
    // {
    //     $response = Http::post('http://server.local/oauth/token', [
    //         'grant_type' => 'authorization_code',
    //         'client_id' => '4',
    //         'client_secret' => 'zHimZetZeqIWaNpQVCqRrTwYRHAxMyj5UScKhI1w',
    //         'redirect_uri' => 'http://client.local/oauth/callback',
    //         'code' => $request->code,
    //     ]);

    //     dd($response->json());
    // }
}
