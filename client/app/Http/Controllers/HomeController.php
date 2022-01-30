<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $posts = [];

        if (auth()->user()->token) {

            // dd(auth()->user()->token->access_token);

            if (auth()->user()->token->hasExpired()) {
                return redirect('/oauth/refresh');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . auth()->user()->token->access_token,
                'Accept' => 'application/json',
            ])->get('http://server.local/api/posts');

            if ($response->status() === 200) {
                $posts = $response->json();
                // dd($posts);
            } else {
                $posts = array(
                    array(
                        'title' => 'Python dars',
                        'body' => 'Squirty cheese feta taleggio cow gouda cut the cheese lancashire croque monsieur. Manchego squirty cheese goat danish fontina st. agur blue cheese cheese slices cream cheese cottage cheese'
                    ),
                    array(
                        'title' => 'PHP dars',
                        'body' => 'Manchego squirty cheese goat danish fontina st. agur blue cheese cheese slices cream cheese cottage cheese'
                    )
                );
            }
        }

        return view('home', [
            'posts' => $posts
        ]);
    }
}
