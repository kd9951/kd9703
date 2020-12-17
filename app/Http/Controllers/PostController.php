<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kd9703\Eloquents\Twitter\Post;
use Kd9703\Eloquents\Twitter\PostRecipient;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Sort\Inputs as SortInputs;
use Kd9703\Resources\Interfaces\Post\Post as PostResource;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PostResource $postResource)
    {
        $account = Auth::user()->getAccount();

        $posts = $postResource->getCommunications($account,
            null,
            null,
            null,
            new PaginateInput([
                'per_page' => 5,
                'page'     => 1,
            ]), new SortInputs([[
                'key'   => 'posted_at',
                'order' => 'desc',
            ]]));

        // SELECT
        //     account_id,
        //     MAX(posted_at) last_posted_at
        // FROM
        //     (SELECT
        //         to_account_id account_id,
        //         posted_at
        //     FROM post_recipients
        //     WHERE from_account_id = 1259724960664174592
        // UNION ALL
        //     SELECT
        //         from_account_id account_id,
        //         posted_at
        //     FROM post_recipients
        //     WHERE to_account_id = 1259724960664174592
        //     ) SUB
        // ORDER BY last_posted_at DESC

        dd($posts->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post                   $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $postRecipients = PostRecipient::where('post_id', $post->post_id)->get();

        dd($post->toArray(), $postRecipients->toArray());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post                   $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  \App\Post                   $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post                   $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
